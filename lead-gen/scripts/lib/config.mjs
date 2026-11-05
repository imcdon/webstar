import fs from "node:fs";
import path from "node:path";
import { CONFIG_DIR } from "./paths.mjs";

function readJson(name) {
  const p = path.join(CONFIG_DIR, name);
  return JSON.parse(fs.readFileSync(p, "utf8"));
}

export function loadGeography() {
  return readJson("geography.json");
}

export function loadSegments() {
  return readJson("segments.json");
}

export function loadScoring() {
  return readJson("scoring.json");
}

export function getCell(geography, cellId) {
  const cell = geography.cells.find((c) => c.id === cellId);
  if (!cell) {
    throw new Error(`Unknown cell id "${cellId}". Available: ${geography.cells.map((c) => c.id).join(", ")}`);
  }
  return cell;
}

/** Cells with `enabled !== false` (default on). */
export function enabledCells(geography) {
  return geography.cells.filter((c) => c.enabled !== false);
}

/** Cells that have a traced road polyline (sweepable). */
export function corridorCells(geography, { enabledOnly = true } = {}) {
  return geography.cells.filter(
    (c) => Array.isArray(c.polyline) && c.polyline.length >= 2 && (!enabledOnly || c.enabled !== false),
  );
}

/** Map every legacy cell id → current cell id (identity for current ids). */
export function cellIdAliases(geography) {
  const map = new Map();
  for (const c of geography.cells) {
    map.set(c.id, c.id);
    for (const old of c.legacyCellIds ?? []) map.set(old, c.id);
  }
  return map;
}

export function loadExclusions() {
  return readJson("exclusions.json");
}

export function getSegment(segmentsConfig, segmentId) {
  const seg = segmentsConfig.segments[segmentId];
  if (!seg) {
    throw new Error(
      `Unknown segment "${segmentId}". Available: ${Object.keys(segmentsConfig.segments).join(", ")}`,
    );
  }
  return seg;
}

/** City name for text query templates (before comma in label). */
export function cityFromLabel(label) {
  const part = label.split(",")[0]?.trim();
  return part || label;
}

/** Build text search strings for a segment + geography cell. */
export function textQueriesForCell(segment, cell) {
  const expand = (tmpl) =>
    tmpl
      .replaceAll("{city}", cityFromLabel(cell.label))
      .replaceAll("{street}", cell.streetHint ?? "")
      .replaceAll("{address}", cell.address ?? cityFromLabel(cell.label))
      .replace(/\s+,/g, ",")
      .replace(/\s{2,}/g, " ")
      .trim();

  const base = [...(segment.textQueries ?? [])];
  if (cell.textQueries?.length) {
    return cell.textQueries.map(expand);
  }
  const queries = base.map(expand);
  if (cell.streetHint && segment.streetTextQueries?.length) {
    queries.push(...segment.streetTextQueries.map(expand));
  }
  return [...new Set(queries.filter(Boolean))];
}
