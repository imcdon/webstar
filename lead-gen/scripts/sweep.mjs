#!/usr/bin/env node
/**
 * Corridor sweep: find EVERY place along a traced road (no type filter), store website + location.
 *
 *   node lead-gen/scripts/sweep.mjs [--cell waterford-dixie-hwy] [--dry-run] [--reset-tiles] [--max-depth 3]
 *                                   [--deepen]   # next month: split still-saturated leaf tiles (use with --max-depth 4)
 *
 * - Tiles the corridor polyline with circles, calls Nearby Search (rankPreference DISTANCE, 20 max,
 *   Enterprise field mask so websiteUri + location come back in the same call — no Place Details).
 * - A tile that returns 20 results is split into 4 smaller circles (adaptive), down to --min-radius.
 * - Only places within `bufferMeters` of the road are stored (in_corridor = 1).
 * - Resumable: tiles live in sweep_tiles; stops at LEAD_GEN_NEARBY_BUDGET for the month and resumes next run.
 */
import { parseArgs, sleep } from "./lib/cli.mjs";
import { corridorCells, getCell, loadExclusions, loadGeography, loadScoring } from "./lib/config.mjs";
import { getNearbyBudget } from "./lib/env.mjs";
import {
  circleTouchesCorridor,
  pointToPolyline,
  polylineLengthMeters,
  subdivideCircle,
  tilesAlongPolyline,
} from "./lib/geo.mjs";
import { mapSweepRow, searchNearbyAll } from "./lib/places.mjs";
import { getSkuCount, incrementSku, monthKey, openDb, parseFlags } from "./lib/db.mjs";
import { computeLeadScore } from "./lib/score.mjs";
import { classifyWebsiteUrl } from "./lib/url-flags.mjs";

const SKU = "nearby_enterprise";
const args = parseArgs();
const dryRun = Boolean(args["dry-run"]);
const resetTiles = Boolean(args["reset-tiles"]);
const maxDepth = args["max-depth"] ? parseInt(args["max-depth"], 10) : 3;
const minRadius = args["min-radius"] ? parseInt(args["min-radius"], 10) : 100;
const baseRadius = args["radius"] ? parseInt(args["radius"], 10) : 480;
const stepMeters = args["step"] ? parseInt(args["step"], 10) : 500;

const geography = loadGeography();
const scoring = loadScoring();
const exclusions = loadExclusions();
const budget = getNearbyBudget();
const db = openDb();

let cells;
if (args.cell) {
  const c = getCell(geography, args.cell);
  if (!Array.isArray(c.polyline) || c.polyline.length < 2) {
    console.error(`Cell "${c.id}" has no polyline; add one to geography.json before sweeping.`);
    process.exit(1);
  }
  cells = [c];
} else {
  cells = corridorCells(geography);
  if (!cells.length) {
    console.error("No enabled cells with a polyline in geography.json.");
    process.exit(1);
  }
}

let sweepExcludedTypes = exclusions.sweepExcludedTypes ?? [];

const stmts = {
  pendingCount: db.prepare("SELECT COUNT(*) c FROM sweep_tiles WHERE geo_cell = ? AND status = 'pending'"),
  anyTiles: db.prepare("SELECT COUNT(*) c FROM sweep_tiles WHERE geo_cell = ?"),
  insertTile: db.prepare(
    "INSERT INTO sweep_tiles (geo_cell, lat, lng, radius, depth, parent_id, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')",
  ),
  nextTile: db.prepare(
    "SELECT * FROM sweep_tiles WHERE geo_cell = ? AND status = 'pending' ORDER BY depth ASC, id ASC LIMIT 1",
  ),
  doneTile: db.prepare("UPDATE sweep_tiles SET status = 'done', result_count = ?, fetched_at = ? WHERE id = ?"),
  failTile: db.prepare("UPDATE sweep_tiles SET status = 'failed', fetched_at = ? WHERE id = ?"),
  deleteTiles: db.prepare("DELETE FROM sweep_tiles WHERE geo_cell = ?"),
  existing: db.prepare("SELECT audit_flags, website_url FROM leads WHERE place_id = ?"),
  upsert: db.prepare(`
    INSERT INTO leads (
      place_id, geo_cell, segment, discovered_at, display_name, google_maps_uri, primary_type, types_json,
      website_url, lat, lng, location_fetched_at, distance_to_road_m, road_position_m, in_corridor,
      user_rating_count, audit_flags, lead_score, last_details_fetched_at
    ) VALUES (?, ?, 'corridor', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?, ?, ?)
    ON CONFLICT(place_id) DO UPDATE SET
      geo_cell = excluded.geo_cell,
      display_name = COALESCE(excluded.display_name, leads.display_name),
      google_maps_uri = COALESCE(excluded.google_maps_uri, leads.google_maps_uri),
      primary_type = COALESCE(excluded.primary_type, leads.primary_type),
      types_json = excluded.types_json,
      website_url = excluded.website_url,
      lat = excluded.lat,
      lng = excluded.lng,
      location_fetched_at = excluded.location_fetched_at,
      distance_to_road_m = excluded.distance_to_road_m,
      road_position_m = excluded.road_position_m,
      in_corridor = 1,
      user_rating_count = excluded.user_rating_count,
      audit_flags = excluded.audit_flags,
      lead_score = excluded.lead_score,
      last_details_fetched_at = excluded.last_details_fetched_at,
      last_audit_at = CASE WHEN IFNULL(leads.website_url, '') = IFNULL(excluded.website_url, '') THEN leads.last_audit_at ELSE NULL END
  `),
};

function used() {
  return getSkuCount(db, SKU);
}

/** --deepen: re-split saturated leaf tiles (done, 20 results, no children) so a later run with a fresh budget digs deeper. */
function deepenTiles(cell) {
  const leaves = db
    .prepare(
      `SELECT t.* FROM sweep_tiles t
       WHERE t.geo_cell = ? AND t.status = 'done' AND t.result_count >= 20
         AND NOT EXISTS (SELECT 1 FROM sweep_tiles c WHERE c.parent_id = t.id)`,
    )
    .all(cell.id);
  let added = 0;
  for (const tile of leaves) {
    const childRadius = Math.round(tile.radius * 0.72);
    if (tile.depth >= maxDepth || childRadius < minRadius) continue;
    for (const child of subdivideCircle({ lat: tile.lat, lng: tile.lng }, tile.radius)) {
      if (!circleTouchesCorridor(child, child.radius, cell.polyline, cell.bufferMeters ?? 400)) continue;
      stmts.insertTile.run(cell.id, child.lat, child.lng, child.radius, tile.depth + 1, tile.id);
      added++;
    }
  }
  return added;
}

function seedTiles(cell) {
  if (resetTiles) stmts.deleteTiles.run(cell.id);
  if (stmts.anyTiles.get(cell.id).c > 0) {
    if (args.deepen) {
      const n = deepenTiles(cell);
      console.log(`Deepen: queued ${n} child tile(s) under saturated leaves (max depth ${maxDepth}).`);
    }
    return 0;
  }
  const base = tilesAlongPolyline(cell.polyline, stepMeters);
  for (const t of base) stmts.insertTile.run(cell.id, t.lat, t.lng, baseRadius, 0, null);
  return base.length;
}

function storePlaces(cell, places, now) {
  let kept = 0;
  let outside = 0;
  let skipped = 0;
  for (const p of places) {
    const row = mapSweepRow(p);
    if (!row || row.lat == null || row.lng == null) {
      skipped++;
      continue;
    }
    const { distance, position } = pointToPolyline([row.lat, row.lng], cell.polyline);
    if (distance > (cell.bufferMeters ?? 400)) {
      outside++;
      continue;
    }
    const prev = stmts.existing.get(row.placeId);
    const urlFlags = classifyWebsiteUrl(row.websiteUri, scoring);
    if (!row.websiteUri) urlFlags.no_website = true;
    const prevFlags = parseFlags(prev?.audit_flags);
    // If the website changed, old HTML audit flags are stale — start from URL flags only.
    const sameSite = (prev?.website_url ?? "") === (row.websiteUri || "");
    const merged = sameSite ? { ...prevFlags, ...urlFlags } : { ...urlFlags };
    if (row.websiteUri) delete merged.no_website;
    delete merged.site_error;
    const score = computeLeadScore(merged, scoring);
    stmts.upsert.run(
      row.placeId,
      cell.id,
      now,
      row.displayName || null,
      row.googleMapsUri,
      row.primaryType,
      JSON.stringify(row.types),
      row.websiteUri || null,
      row.lat,
      row.lng,
      now,
      Math.round(distance),
      Math.round(position),
      row.userRatingCount,
      JSON.stringify(merged),
      score,
      now,
    );
    kept++;
  }
  return { kept, outside, skipped };
}

async function queryTile(tile) {
  const center = { lat: tile.lat, lng: tile.lng };
  try {
    return await searchNearbyAll({ center, radiusMeters: tile.radius, excludedTypes: sweepExcludedTypes });
  } catch (err) {
    // Unsupported type in excludedTypes → drop API-side exclusions for the rest of the run (classify.mjs still filters)
    if (sweepExcludedTypes.length && /type/i.test(err.message)) {
      console.warn(`excludedTypes rejected (${err.message}); retrying without API-side exclusions.`);
      sweepExcludedTypes = [];
      return searchNearbyAll({ center, radiusMeters: tile.radius, excludedTypes: [] });
    }
    throw err;
  }
}

for (const cell of cells) {
  const lengthM = polylineLengthMeters(cell.polyline);
  const baseTiles = tilesAlongPolyline(cell.polyline, stepMeters);
  console.log(`\n=== ${cell.id} — ${(lengthM / 1000).toFixed(1)} km, buffer ${cell.bufferMeters ?? 400} m ===`);
  console.log(`Base tiles: ${baseTiles.length} (r=${baseRadius} m, step ${stepMeters} m), max depth ${maxDepth}, min radius ${minRadius} m`);

  if (dryRun) {
    let perTile = 0;
    for (let d = 0; d <= maxDepth; d++) perTile += 4 ** d;
    console.log(`Dry run: ${baseTiles.length} calls minimum; ~${Math.round(baseTiles.length * 3)} typical; ${baseTiles.length * perTile} worst case (every tile saturates to depth ${maxDepth}).`);
    console.log(`Nearby Enterprise calls used ${monthKey()}: ${used()} / ${budget}. No API calls made.`);
    continue;
  }

  const seeded = seedTiles(cell);
  if (seeded) console.log(`Seeded ${seeded} tiles.`);
  else console.log(`Resuming: ${stmts.pendingCount.get(cell.id).c} pending tile(s).`);

  let calls = 0;
  let kept = 0;
  let outside = 0;
  let split = 0;
  let stoppedForBudget = false;

  for (;;) {
    const tile = stmts.nextTile.get(cell.id);
    if (!tile) break;
    if (used() >= budget) {
      stoppedForBudget = true;
      break;
    }
    incrementSku(db, SKU, 1);
    calls++;
    let data;
    try {
      data = await queryTile(tile);
    } catch (err) {
      console.warn(`Tile ${tile.id} failed: ${err.message}`);
      stmts.failTile.run(new Date().toISOString(), tile.id);
      await sleep(500);
      continue;
    }
    const places = data.places ?? [];
    const now = new Date().toISOString();
    const r = storePlaces(cell, places, now);
    kept += r.kept;
    outside += r.outside;
    stmts.doneTile.run(places.length, now, tile.id);

    const saturated = places.length >= 20;
    const childRadius = Math.round(tile.radius * 0.72);
    if (saturated && tile.depth < maxDepth && childRadius >= minRadius) {
      let added = 0;
      for (const child of subdivideCircle({ lat: tile.lat, lng: tile.lng }, tile.radius)) {
        if (!circleTouchesCorridor(child, child.radius, cell.polyline, cell.bufferMeters ?? 400)) continue;
        stmts.insertTile.run(cell.id, child.lat, child.lng, child.radius, tile.depth + 1, tile.id);
        added++;
      }
      split++;
      process.stdout.write(`  tile ${tile.id} d${tile.depth} r${Math.round(tile.radius)}: ${places.length} results (saturated → +${added} children), kept ${r.kept}\n`);
    } else {
      process.stdout.write(`  tile ${tile.id} d${tile.depth} r${Math.round(tile.radius)}: ${places.length} results, kept ${r.kept}, outside buffer ${r.outside}\n`);
    }
    await sleep(150);
  }

  const inCorridor = db.prepare("SELECT COUNT(*) c FROM leads WHERE geo_cell = ? AND in_corridor = 1").get(cell.id).c;
  const pending = stmts.pendingCount.get(cell.id).c;
  console.log(`\n${cell.id}: ${calls} calls this run, ${split} tiles split, ${kept} place-hits in buffer (${outside} outside).`);
  console.log(`Unique places in corridor: ${inCorridor}. Pending tiles: ${pending}.`);
  if (stoppedForBudget) {
    console.warn(`Stopped: Nearby Enterprise budget ${budget} reached for ${monthKey()} (${used()} used). Re-run next month or raise LEAD_GEN_NEARBY_BUDGET.`);
  }
}

console.log(`\nNearby Enterprise calls used ${monthKey()}: ${used()} / ${budget}`);
