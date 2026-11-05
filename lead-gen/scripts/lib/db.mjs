import fs from "node:fs";
import { DatabaseSync } from "node:sqlite";
import { DATA_DIR, DB_PATH } from "./paths.mjs";

export function openDb() {
  fs.mkdirSync(DATA_DIR, { recursive: true });
  const db = new DatabaseSync(DB_PATH);
  db.exec("PRAGMA journal_mode = WAL;");
  db.exec(`
    CREATE TABLE IF NOT EXISTS meta (
      key TEXT PRIMARY KEY,
      value TEXT NOT NULL
    );
    CREATE TABLE IF NOT EXISTS leads (
      place_id TEXT PRIMARY KEY,
      geo_cell TEXT NOT NULL,
      segment TEXT NOT NULL,
      discovered_at TEXT NOT NULL,
      display_name TEXT,
      google_maps_uri TEXT,
      website_url TEXT,
      primary_type TEXT,
      audit_flags TEXT NOT NULL DEFAULT '{}',
      psi_performance REAL,
      psi_seo REAL,
      psi_accessibility REAL,
      lead_score INTEGER NOT NULL DEFAULT 0,
      notes TEXT NOT NULL DEFAULT '',
      last_details_fetched_at TEXT,
      last_audit_at TEXT,
      last_psi_at TEXT
    );
    CREATE INDEX IF NOT EXISTS idx_leads_segment ON leads(segment);
    CREATE INDEX IF NOT EXISTS idx_leads_geo ON leads(geo_cell);
    CREATE INDEX IF NOT EXISTS idx_leads_score ON leads(lead_score);
  `);
  // Column migrations for DBs created by earlier versions (ALTER fails harmlessly when present)
  const migrations = [
    "display_name TEXT",
    "lat REAL",
    "lng REAL",
    "location_fetched_at TEXT",
    "distance_to_road_m REAL",
    "road_position_m REAL",
    "in_corridor INTEGER NOT NULL DEFAULT 0",
    "types_json TEXT",
    "user_rating_count INTEGER",
    "exclude_reason TEXT",
    "exclude_override TEXT",
  ];
  for (const col of migrations) {
    try {
      db.exec(`ALTER TABLE leads ADD COLUMN ${col}`);
    } catch {
      // column already exists
    }
  }
  db.exec(`
    CREATE TABLE IF NOT EXISTS sweep_tiles (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      geo_cell TEXT NOT NULL,
      lat REAL NOT NULL,
      lng REAL NOT NULL,
      radius REAL NOT NULL,
      depth INTEGER NOT NULL DEFAULT 0,
      parent_id INTEGER,
      status TEXT NOT NULL DEFAULT 'pending',
      result_count INTEGER,
      fetched_at TEXT
    );
    CREATE INDEX IF NOT EXISTS idx_tiles_cell_status ON sweep_tiles(geo_cell, status);
    CREATE INDEX IF NOT EXISTS idx_leads_exclude ON leads(exclude_reason);
  `);
  return db;
}

/** Month key for per-SKU counters, e.g. 2026-11 */
export function monthKey(d = new Date()) {
  return d.toISOString().slice(0, 7);
}

export function getSkuCount(db, sku, month = monthKey()) {
  return parseInt(getMeta(db, `sku:${sku}:${month}`, "0"), 10) || 0;
}

export function incrementSku(db, sku, n = 1, month = monthKey()) {
  const next = getSkuCount(db, sku, month) + n;
  setMeta(db, `sku:${sku}:${month}`, next);
  return next;
}

export function getMeta(db, key, fallback = "0") {
  const row = db.prepare("SELECT value FROM meta WHERE key = ?").get(key);
  return row ? row.value : fallback;
}

export function setMeta(db, key, value) {
  db.prepare("INSERT INTO meta (key, value) VALUES (?, ?) ON CONFLICT(key) DO UPDATE SET value = excluded.value").run(
    key,
    String(value),
  );
}

export function incrementApiCalls(db, n = 1) {
  const cur = parseInt(getMeta(db, "places_api_calls_month", "0"), 10) || 0;
  setMeta(db, "places_api_calls_month", cur + n);
  return cur + n;
}

export function upsertDiscoveredLead(db, { placeId, geoCell, segment, googleMapsUri, primaryType, displayName }) {
  const now = new Date().toISOString();
  db.prepare(
    `INSERT INTO leads (place_id, geo_cell, segment, discovered_at, display_name, google_maps_uri, primary_type, audit_flags)
     VALUES (?, ?, ?, ?, ?, ?, ?, '{}')
     ON CONFLICT(place_id) DO UPDATE SET
       display_name = COALESCE(excluded.display_name, leads.display_name),
       google_maps_uri = COALESCE(excluded.google_maps_uri, leads.google_maps_uri),
       primary_type = COALESCE(excluded.primary_type, leads.primary_type)`,
  ).run(
    placeId,
    geoCell,
    segment,
    now,
    displayName || null,
    googleMapsUri ?? null,
    primaryType ?? null,
  );
}

export function parseFlags(raw) {
  if (!raw) return {};
  try {
    return JSON.parse(raw);
  } catch {
    return {};
  }
}

export function saveFlags(db, placeId, flags) {
  db.prepare("UPDATE leads SET audit_flags = ? WHERE place_id = ?").run(JSON.stringify(flags), placeId);
}

export function mergeFlags(db, placeId, patch) {
  const row = db.prepare("SELECT audit_flags FROM leads WHERE place_id = ?").get(placeId);
  const flags = { ...parseFlags(row?.audit_flags), ...patch };
  saveFlags(db, placeId, flags);
  return flags;
}
