#!/usr/bin/env node
/**
 * Discover businesses via Places (Pro field mask — no websiteUri).
 *   node lead-gen/scripts/discover.mjs --segment home-services --cell waterford-walton-blvd [--limit 100]
 */
import { getCell, getSegment, loadGeography, loadSegments, textQueriesForCell } from "./lib/config.mjs";
import { parseArgs, sleep } from "./lib/cli.mjs";
import { getApiCallBudget } from "./lib/env.mjs";
import { geocodeAddress, mapPlaceRow, searchNearby, searchText } from "./lib/places.mjs";
import { openDb, incrementApiCalls, upsertDiscoveredLead, getMeta } from "./lib/db.mjs";

const args = parseArgs();
const segmentId = args.segment;
const cellId = args.cell;
const limit = args.limit ? parseInt(args.limit, 10) : 200;

if (!segmentId || !cellId) {
  console.error("Usage: discover.mjs --segment <id> --cell <id> [--limit N]");
  process.exit(1);
}

const geography = loadGeography();
const segmentsConfig = loadSegments();
const cell = getCell(geography, cellId);
const segment = getSegment(segmentsConfig, segmentId);
const db = openDb();

let center = cell.center;
if ((!center?.lat || cell.geocodeOnRun) && cell.address) {
  if (!budgetOk()) process.exit(0);
  trackCall();
  center = await geocodeAddress(cell.address);
}

const budget = getApiCallBudget();
let added = 0;

function budgetOk() {
  const total = parseInt(getMeta(db, "places_api_calls_month", "0"), 10) || 0;
  if (total >= budget) {
    console.warn(`API call budget (${budget}) reached. Stopping.`);
    return false;
  }
  return true;
}

function trackCall() {
  incrementApiCalls(db, 1);
}

async function ingestPlaces(places) {
  for (const p of places) {
    if (added >= limit) return;
    const row = mapPlaceRow(p);
    if (!row) continue;
    upsertDiscoveredLead(db, {
      placeId: row.placeId,
      geoCell: cellId,
      segment: segmentId,
      googleMapsUri: row.googleMapsUri,
      primaryType: row.primaryType,
      displayName: row.displayName,
    });
    added++;
  }
}

for (const includedType of segment.nearbyTypes) {
  if (added >= limit) break;
  let pageToken;
  do {
    if (!budgetOk()) process.exit(0);
    trackCall();
    let data;
    try {
      data = await searchNearby({
        center,
        radiusMeters: cell.radiusMeters ?? 8000,
        includedType,
        pageToken,
      });
    } catch (err) {
      // Table B / invalid types (e.g. general_contractor) are not valid includedTypes
      console.warn(`Nearby skip type=${includedType}: ${err.message}`);
      break;
    }
    await ingestPlaces(data.places ?? []);
    pageToken = data.nextPageToken;
    if (pageToken) await sleep(1500);
  } while (pageToken && added < limit);
}

for (const textQuery of textQueriesForCell(segment, cell)) {
  if (added >= limit) break;
  let pageToken;
  do {
    if (!budgetOk()) process.exit(0);
    trackCall();
    const data = await searchText({
      textQuery,
      center,
      radiusMeters: cell.radiusMeters ?? 8000,
      pageToken,
    });
    await ingestPlaces(data.places ?? []);
    pageToken = data.nextPageToken;
    if (pageToken) await sleep(1500);
  } while (pageToken && added < limit);
}

const total = db.prepare("SELECT COUNT(*) AS c FROM leads").get().c;
console.log(`Discover done. Upserted up to ${added} rows this run. Total leads in DB: ${total}.`);
console.log(`Places API calls (meta counter): ${getMeta(db, "places_api_calls_month")}`);
