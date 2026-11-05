#!/usr/bin/env node
/**
 * Offline (free) classification: mark chains, large firms, excluded types, and out-of-corridor rows.
 *   node lead-gen/scripts/classify.mjs [--cell waterford-dixie-hwy] [--show]
 *
 * Writes leads.exclude_reason (NULL = retained). A manual exclude_override ('keep' | 'exclude') always wins.
 */
import { parseArgs } from "./lib/cli.mjs";
import { cellIdAliases, corridorCells, loadExclusions, loadGeography } from "./lib/config.mjs";
import { makeChainFilter } from "./lib/chain-filter.mjs";
import { openDb, parseFlags } from "./lib/db.mjs";

const args = parseArgs();
const geography = loadGeography();
const exclusions = loadExclusions();
const db = openDb();
const filter = makeChainFilter(exclusions);
const aliases = cellIdAliases(geography);
const polylineCellIds = new Set(corridorCells(geography, { enabledOnly: false }).map((c) => c.id));

// Re-tag legacy cell ids (e.g. waterford-dixie-hwy-north → waterford-dixie-hwy)
for (const [oldId, newId] of aliases) {
  if (oldId !== newId) db.prepare("UPDATE leads SET geo_cell = ? WHERE geo_cell = ?").run(newId, oldId);
}

let sql = "SELECT * FROM leads";
const params = [];
if (args.cell) {
  sql += " WHERE geo_cell = ?";
  params.push(aliases.get(args.cell) ?? args.cell);
}
const rows = db.prepare(sql).all(...params);

// Same registrable domain on many listings = multi-location / franchise (shared platforms like facebook.com don't count)
const domainCounts = new Map();
for (const r of db.prepare("SELECT website_url FROM leads WHERE website_url IS NOT NULL").all()) {
  const d = filter.countableDomain(r.website_url);
  if (d) domainCounts.set(d, (domainCounts.get(d) || 0) + 1);
}

const update = db.prepare("UPDATE leads SET exclude_reason = ? WHERE place_id = ?");
const counts = new Map();
let retained = 0;

for (const row of rows) {
  let reason = null;
  if (row.exclude_override === "exclude") {
    reason = "manual";
  } else if (row.exclude_override === "keep") {
    reason = null;
  } else if (polylineCellIds.has(row.geo_cell) && !row.in_corridor) {
    reason = "outside_corridor";
  } else {
    let types = [];
    try {
      types = JSON.parse(row.types_json || "[]");
    } catch {
      types = [];
    }
    reason = filter.reasonFor(
      {
        display_name: row.display_name,
        website_url: row.website_url,
        primary_type: row.primary_type,
        types,
        user_rating_count: row.user_rating_count,
        flags: parseFlags(row.audit_flags),
      },
      { domainCounts },
    );
  }
  update.run(reason, row.place_id);
  if (reason) counts.set(reason, (counts.get(reason) || 0) + 1);
  else retained++;
  if (args.show && reason && reason !== "outside_corridor") {
    console.log(`  ${reason.padEnd(22)} ${row.display_name ?? "?"}  ${row.website_url ?? ""}`);
  }
}

console.log(`Classified ${rows.length} rows${args.cell ? ` in ${args.cell}` : ""}: ${retained} retained.`);
for (const [reason, n] of [...counts.entries()].sort((a, b) => b[1] - a[1])) {
  console.log(`  ${String(n).padStart(4)}  ${reason}`);
}
