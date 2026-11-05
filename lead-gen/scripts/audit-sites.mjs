#!/usr/bin/env node
/**
 * Fetch homepage + HTML heuristics + optional PSI.
 *   node lead-gen/scripts/audit-sites.mjs [--limit 20 | --all] [--skip-psi] [--stale-days 90] [--cell id] [--include-excluded] [--retry-errors]
 *
 * By default only retained rows (exclude_reason IS NULL) that have never been audited are fetched.
 */
import { parseArgs, sleep } from "./lib/cli.mjs";
import { loadScoring } from "./lib/config.mjs";
import { fetchPage } from "./lib/fetch-page.mjs";
import { auditHtml } from "./lib/html-audit.mjs";
import { runPsi } from "./lib/psi.mjs";
import { openDb, parseFlags } from "./lib/db.mjs";
import { computeLeadScore } from "./lib/score.mjs";

const args = parseArgs();
const all = Boolean(args.all);
const limit = args.limit ? parseInt(args.limit, 10) : 20;
const skipPsi = Boolean(args["skip-psi"]);
const staleDays = args["stale-days"] ? parseInt(args["stale-days"], 10) : null;
const scoring = loadScoring();
const db = openDb();

let sql = `SELECT place_id, website_url, audit_flags FROM leads
     WHERE website_url IS NOT NULL AND TRIM(website_url) != ''`;
const params = [];
if (args["retry-errors"]) {
  // Re-fetch sites that errored or blocked us last time (regardless of last_audit_at)
  sql += ` AND (audit_flags LIKE '%"site_error":true%' OR audit_flags LIKE '%"fetch_blocked":true%')`;
} else if (staleDays != null) {
  sql += ` AND (last_audit_at IS NULL OR last_audit_at < ?)`;
  params.push(new Date(Date.now() - staleDays * 86400000).toISOString());
} else {
  sql += ` AND last_audit_at IS NULL`;
}
if (!args["include-excluded"]) sql += ` AND exclude_reason IS NULL`;
if (args.cell) {
  sql += ` AND geo_cell = ?`;
  params.push(args.cell);
}
sql += ` ORDER BY last_details_fetched_at DESC`;
if (!all) {
  sql += ` LIMIT ?`;
  params.push(limit);
}
const leads = db.prepare(sql).all(...params);
console.log(`Auditing ${leads.length} site(s)${skipPsi ? " (HTML only)" : ""}…`);

let done = 0;
for (const row of leads) {
  const existing = parseFlags(row.audit_flags);
  // Social-only links are graded by URL alone; builder subdomains are still fetched so they get a full grade.
  if (existing.no_website || existing.weak_url) {
    db.prepare("UPDATE leads SET last_audit_at = ? WHERE place_id = ?").run(
      new Date().toISOString(),
      row.place_id,
    );
    continue;
  }
  const page = await fetchPage(row.website_url);
  // Fresh fetch supersedes last time's error/blocked state
  delete existing.site_error;
  delete existing.fetch_blocked;
  let flags = { ...existing, ...page.flags };
  if (page.ok && page.html) {
    flags = { ...flags, ...auditHtml(page.html, { finalUrl: page.finalUrl, lastModifiedHeader: page.lastModified, scoring }) };
  }

  let psi_performance = null;
  let psi_seo = null;
  let psi_accessibility = null;
  let last_psi_at = null;

  if (!skipPsi && page.ok && page.finalUrl.startsWith("http")) {
    try {
      const psi = await runPsi(page.finalUrl, "mobile");
      flags = { ...flags, ...psi.flags };
      psi_performance = psi.scores.performance;
      psi_seo = psi.scores.seo;
      psi_accessibility = psi.scores.accessibility;
      last_psi_at = new Date().toISOString();
      await sleep(3000);
    } catch (err) {
      console.warn(`PSI skip ${row.place_id}: ${err.message}`);
    }
  }

  flags.psi_performance = psi_performance;
  flags.psi_seo = psi_seo;
  flags.psi_accessibility = psi_accessibility;

  const score = computeLeadScore(flags, scoring);
  const now = new Date().toISOString();
  db.prepare(
    `UPDATE leads SET audit_flags = ?, psi_performance = ?, psi_seo = ?, psi_accessibility = ?,
      lead_score = ?, last_audit_at = ?, last_psi_at = COALESCE(?, last_psi_at),
      website_url = ?
     WHERE place_id = ?`,
  ).run(
    JSON.stringify(flags),
    psi_performance,
    psi_seo,
    psi_accessibility,
    score,
    now,
    last_psi_at,
    page.finalUrl || row.website_url,
    row.place_id,
  );
  done++;
  if (done % 25 === 0) console.log(`  …${done}/${leads.length}`);
  await sleep(300);
}

console.log(`Audited ${done} site(s).`);
