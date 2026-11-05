#!/usr/bin/env node
/**
 * Recompute lead_score from audit_flags for all rows.
 *   node lead-gen/scripts/score-leads.mjs
 */
import { loadScoring } from "./lib/config.mjs";
import { openDb, parseFlags } from "./lib/db.mjs";
import { computeLeadScore } from "./lib/score.mjs";

const scoring = loadScoring();
const db = openDb();
const rows = db.prepare("SELECT place_id, audit_flags FROM leads").all();
let n = 0;
for (const row of rows) {
  const flags = parseFlags(row.audit_flags);
  const score = computeLeadScore(flags, scoring);
  db.prepare("UPDATE leads SET lead_score = ? WHERE place_id = ?").run(score, row.place_id);
  n++;
}
console.log(`Scored ${n} leads.`);
