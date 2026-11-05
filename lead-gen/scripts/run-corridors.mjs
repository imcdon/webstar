#!/usr/bin/env node
/**
 * Full corridor pipeline: sweep (budgeted) → classify → audit every retained site → score → export.
 *   node lead-gen/scripts/run-corridors.mjs [--cell waterford-dixie-hwy] [--out exports/dixie-hwy-businesses.csv] [--with-psi]
 */
import { spawnSync } from "node:child_process";
import path from "node:path";
import { parseArgs } from "./lib/cli.mjs";
import { corridorCells, loadGeography } from "./lib/config.mjs";
import { LEAD_GEN_ROOT } from "./lib/paths.mjs";

const REPO_ROOT = path.resolve(LEAD_GEN_ROOT, "..");
const SCRIPTS = path.join(LEAD_GEN_ROOT, "scripts");

const args = parseArgs();
const geography = loadGeography();
const cells = args.cell ? [args.cell] : corridorCells(geography).map((c) => c.id);
if (!cells.length) {
  console.error("No enabled corridor cells (with polyline) in geography.json.");
  process.exit(1);
}
const out = args.out || (cells.length === 1 ? `exports/${cells[0]}-businesses.csv` : "exports/corridor-businesses.csv");

function runStep(label, scriptName, scriptArgs) {
  console.log(`\n=== ${label} ===\n`);
  const r = spawnSync(process.execPath, [path.join(SCRIPTS, scriptName), ...scriptArgs], {
    cwd: REPO_ROOT,
    stdio: "inherit",
  });
  if (r.status !== 0) {
    console.error(`\nStopped: ${scriptName} exited with code ${r.status}`);
    process.exit(r.status ?? 1);
  }
}

runStep("Setup check", "check-setup.mjs", []);
for (const cellId of cells) runStep(`Sweep — ${cellId}`, "sweep.mjs", ["--cell", cellId]);
runStep("Classify (chains, large firms, excluded types)", "classify.mjs", []);
for (const cellId of cells) {
  const auditArgs = ["--all", "--cell", cellId];
  if (!args["with-psi"]) auditArgs.push("--skip-psi");
  runStep(`Audit every retained website — ${cellId}`, "audit-sites.mjs", auditArgs);
  runStep(`Retry errored / blocked sites — ${cellId}`, "audit-sites.mjs", [...auditArgs, "--retry-errors"]);
}
// Second pass picks up enterprise_nav (multi-location / franchise nav) found by the audit
runStep("Classify again (large-firm signals from audits)", "classify.mjs", []);
runStep("Score", "score-leads.mjs", []);
const exportArgs = ["--out", out];
if (args.cell) exportArgs.push("--cell", args.cell);
runStep("Export CSV", "export.mjs", exportArgs);

console.log("\n=== Done ===");
console.log(`Open: ${path.join(LEAD_GEN_ROOT, out)}\n`);
