#!/usr/bin/env node
/**
 * Place Details (Enterprise) — websiteUri + URL tier-0 flags.
 *   node lead-gen/scripts/enrich.mjs [--limit 50] [--segment home-services] [--refresh-names]
 */
import { parseArgs, sleep } from "./lib/cli.mjs";
import { loadScoring } from "./lib/config.mjs";
import { getApiCallBudget } from "./lib/env.mjs";
import { classifyWebsiteUrl } from "./lib/url-flags.mjs";
import { placeDetails } from "./lib/places.mjs";
import { openDb, incrementApiCalls, getMeta, parseFlags, mergeFlags } from "./lib/db.mjs";
import { computeLeadScore } from "./lib/score.mjs";

const args = parseArgs();
const limit = args.limit ? parseInt(args.limit, 10) : 50;
const segmentFilter = args.segment ?? null;
const refreshNames = Boolean(args["refresh-names"]);
const budget = getApiCallBudget();
const scoring = loadScoring();
const db = openDb();

let sql = refreshNames
  ? `SELECT place_id FROM leads WHERE display_name IS NULL OR TRIM(display_name) = ''`
  : `SELECT place_id FROM leads WHERE last_details_fetched_at IS NULL`;
const params = [];
if (segmentFilter) {
  sql += ` AND segment = ?`;
  params.push(segmentFilter);
}
sql += ` ORDER BY discovered_at DESC LIMIT ?`;
params.push(limit);

const rows = db.prepare(sql).all(...params);
let done = 0;

for (const row of rows) {
  const total = parseInt(getMeta(db, "places_api_calls_month", "0"), 10) || 0;
  if (total >= budget) {
    console.warn(`API budget (${budget}) reached.`);
    break;
  }
  incrementApiCalls(db, 1);
  try {
    const detail = await placeDetails(row.place_id);
    const website = detail.websiteUri?.trim() || "";
    const displayName = detail.displayName?.text?.trim() || "";
    const urlFlags = classifyWebsiteUrl(website, scoring);
    if (!website) urlFlags.no_website = true;
    const prev = db.prepare("SELECT audit_flags FROM leads WHERE place_id = ?").get(row.place_id);
    const merged = { ...parseFlags(prev?.audit_flags), ...urlFlags };
    delete merged.site_error;

    const now = new Date().toISOString();
    const leadScore = computeLeadScore(merged, scoring);
    db.prepare(
      `UPDATE leads SET
         display_name = COALESCE(NULLIF(?, ''), display_name),
         website_url = ?,
         google_maps_uri = COALESCE(?, google_maps_uri),
         primary_type = COALESCE(?, primary_type),
         last_details_fetched_at = ?,
         audit_flags = ?,
         lead_score = ?
       WHERE place_id = ?`,
    ).run(
      displayName,
      website || null,
      detail.googleMapsUri ?? null,
      detail.primaryType ?? null,
      now,
      JSON.stringify(merged),
      leadScore,
      row.place_id,
    );
    done++;
  } catch (err) {
    console.warn(`Enrich failed ${row.place_id}: ${err.message}`);
    mergeFlags(db, row.place_id, { site_error: true });
  }
  await sleep(200);
}

console.log(`Enriched ${done} / ${rows.length} leads.`);
