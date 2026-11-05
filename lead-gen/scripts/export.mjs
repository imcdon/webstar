#!/usr/bin/env node
/**
 * Export businesses CSV for outreach (internal use).
 *
 *   node lead-gen/scripts/export.mjs [--out exports/corridor-businesses.csv] [--cell id] [--segment id]
 *                                    [--qualified-only] [--min-score 35] [--include-excluded]
 *
 * Default: every retained business (exclude_reason IS NULL) in enabled geography cells, each with a
 * website grade. Within a corridor rows are ordered worst web presence first, then north→south along
 * the road (road_position_m). Corridors with the most businesses come first.
 *
 * --qualified-only reproduces the old lead-only export (lead_score >= min and isQualified).
 */
import fs from "node:fs";
import path from "node:path";
import { parseArgs } from "./lib/cli.mjs";
import { enabledCells, loadGeography, loadScoring } from "./lib/config.mjs";
import { LEAD_GEN_ROOT } from "./lib/paths.mjs";
import { openDb } from "./lib/db.mjs";
import { buildPainSummary, isQualified, flagsFromRow, rankWebsite } from "./lib/score.mjs";

const args = parseArgs();
const scoring = loadScoring();
const geography = loadGeography();
const qualifiedOnly = Boolean(args["qualified-only"]);
const includeExcluded = Boolean(args["include-excluded"]);
const minScore = args["min-score"] ? parseInt(args["min-score"], 10) : scoring.qualify.minScore;
const outRel = args.out || (qualifiedOnly ? `exports/leads-${new Date().toISOString().slice(0, 10)}.csv` : "exports/corridor-businesses.csv");
const outPath = path.isAbsolute(outRel) ? outRel : path.join(LEAD_GEN_ROOT, outRel);

fs.mkdirSync(path.dirname(outPath), { recursive: true });
const db = openDb();

const where = [];
const params = [];
if (args.cell) {
  where.push("geo_cell = ?");
  params.push(args.cell);
} else {
  const ids = enabledCells(geography).map((c) => c.id);
  if (ids.length) {
    where.push(`geo_cell IN (${ids.map(() => "?").join(",")})`);
    params.push(...ids);
  }
}
if (args.segment) {
  where.push("segment = ?");
  params.push(args.segment);
}
if (!includeExcluded) where.push("exclude_reason IS NULL");
if (qualifiedOnly) {
  where.push("lead_score >= ?");
  params.push(minScore);
}

const sql = `SELECT * FROM leads${where.length ? ` WHERE ${where.join(" AND ")}` : ""}`;
let rows = db.prepare(sql).all(...params);
if (qualifiedOnly) rows = rows.filter((row) => isQualified(flagsFromRow(row), row.lead_score, scoring));

function esc(v) {
  const s = String(v ?? "");
  if (/[",\n]/.test(s)) return `"${s.replace(/"/g, '""')}"`;
  return s;
}

const header = [
  "geo_cell",
  "place_name",
  "website_need",
  "website_grade",
  "website_score",
  "website_status",
  "pain_summary",
  "website_url",
  "distance_to_road_m",
  "primary_type",
  "google_maps_uri",
  "place_id",
  "lead_score",
  ...(includeExcluded ? ["exclude_reason"] : []),
  "notes",
].join(",");

const ranked = rows.map((row) => {
  const flags = flagsFromRow(row);
  return { row, flags, site: rankWebsite(flags, scoring) };
});

const cellCounts = new Map();
for (const item of ranked) {
  const cell = item.row.geo_cell || "";
  cellCounts.set(cell, (cellCounts.get(cell) || 0) + 1);
}

const needOrder = { high: 0, medium: 1, unknown: 2, low: 3 };
const gradeOrder = { none: 0, F: 1, D: 2, C: 3, "?": 4, B: 5, A: 6 };

ranked.sort((a, b) => {
  const cellA = a.row.geo_cell || "";
  const cellB = b.row.geo_cell || "";
  const countA = cellCounts.get(cellA) || 0;
  const countB = cellCounts.get(cellB) || 0;
  if (countA !== countB) return countB - countA; // densest corridor first
  if (cellA !== cellB) return cellA.localeCompare(cellB);
  const na = needOrder[a.site.website_need] ?? 9;
  const nb = needOrder[b.site.website_need] ?? 9;
  if (na !== nb) return na - nb; // worst web presence first
  const ga = gradeOrder[a.site.website_grade] ?? 9;
  const gb = gradeOrder[b.site.website_grade] ?? 9;
  if (ga !== gb) return ga - gb;
  const pa = a.row.road_position_m ?? Number.MAX_SAFE_INTEGER;
  const pb = b.row.road_position_m ?? Number.MAX_SAFE_INTEGER;
  if (pa !== pb) return pa - pb; // then a drive down the road (polyline start → end)
  return (b.row.lead_score ?? 0) - (a.row.lead_score ?? 0);
});

const lines = [header];
for (const { row, flags, site } of ranked) {
  lines.push(
    [
      esc(row.geo_cell),
      esc(row.display_name || ""),
      esc(site.website_need),
      esc(site.website_grade),
      esc(site.website_score),
      esc(site.website_status),
      esc(buildPainSummary(flags, row, scoring)),
      esc(row.website_url),
      esc(row.distance_to_road_m ?? ""),
      esc(row.primary_type),
      esc(row.google_maps_uri),
      esc(row.place_id),
      esc(row.lead_score),
      ...(includeExcluded ? [esc(row.exclude_reason ?? "")] : []),
      esc(row.notes),
    ].join(","),
  );
}

fs.writeFileSync(outPath, lines.join("\n") + "\n", "utf8");

const concentration = [...cellCounts.entries()]
  .sort((a, b) => b[1] - a[1] || a[0].localeCompare(b[0]))
  .map(([cell, n]) => `${n} ${cell}`)
  .join(", ");
const grades = new Map();
for (const { site } of ranked) grades.set(site.website_grade, (grades.get(site.website_grade) || 0) + 1);
const gradeSummary = Object.keys(gradeOrder)
  .filter((g) => grades.has(g))
  .map((g) => `${g}: ${grades.get(g)}`)
  .join(", ");

console.log(`Exported ${ranked.length} ${qualifiedOnly ? "qualified leads" : "businesses"} → ${outPath}`);
console.log(`By location: ${concentration || "none"}`);
console.log(`Website grades — ${gradeSummary || "none"}`);
