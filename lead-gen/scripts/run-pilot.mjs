#!/usr/bin/env node
/**
 * Full pilot: discover all geography cells → enrich → audit → score → export.
 *   node lead-gen/scripts/run-pilot.mjs [--segment home-services] [--discover-limit 50]
 */
import { spawnSync } from "node:child_process";
import path from "node:path";
import { fileURLToPath } from "node:url";
import { parseArgs } from "./lib/cli.mjs";
import { loadGeography } from "./lib/config.mjs";
import { LEAD_GEN_ROOT } from "./lib/paths.mjs";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const REPO_ROOT = path.resolve(LEAD_GEN_ROOT, "..");
const SCRIPTS = path.join(LEAD_GEN_ROOT, "scripts");

const args = parseArgs();
const segment = args.segment ?? "home-services";
const discoverLimit = args["discover-limit"] ? parseInt(args["discover-limit"], 10) : 50;
const enrichLimit = args["enrich-limit"] ? parseInt(args["enrich-limit"], 10) : 200;
const auditLimit = args["audit-limit"] ? parseInt(args["audit-limit"], 10) : 30;
const minScore = args["min-score"] ? parseInt(args["min-score"], 10) : 35;
const cellFilter = args.cells ? args.cells.split(",").map((s) => s.trim()) : null;

function runStep(label, scriptName, scriptArgs) {
  console.log(`\n=== ${label} ===\n`);
  const scriptPath = path.join(SCRIPTS, scriptName);
  const r = spawnSync(process.execPath, [scriptPath, ...scriptArgs], {
    cwd: REPO_ROOT,
    stdio: "inherit",
  });
  if (r.status !== 0) {
    console.error(`\nStopped: ${scriptName} exited with code ${r.status}`);
    process.exit(r.status ?? 1);
  }
}

// Preflight
runStep("Setup check", "check-setup.mjs", []);

const geography = loadGeography();
let cells = geography.cells.map((c) => c.id);
if (cellFilter?.length) {
  cells = cells.filter((id) => cellFilter.includes(id));
  const missing = cellFilter.filter((id) => !geography.cells.some((c) => c.id === id));
  if (missing.length) {
    console.error(`Unknown cell(s): ${missing.join(", ")}`);
    process.exit(1);
  }
}

console.log(`\nPilot: segment=${segment}, ${cells.length} cell(s), discover limit ${discoverLimit} each\n`);

for (const cellId of cells) {
  runStep(`Discover — ${cellId}`, "discover.mjs", [
    "--segment",
    segment,
    "--cell",
    cellId,
    "--limit",
    String(discoverLimit),
  ]);
}

runStep("Enrich (Place Details)", "enrich.mjs", ["--limit", String(enrichLimit), "--segment", segment]);
runStep("Audit sites (HTML only, no PSI)", "audit-sites.mjs", [
  "--limit",
  String(auditLimit),
  "--skip-psi",
  "--segment",
  segment,
]);
runStep("Score leads", "score-leads.mjs", []);
runStep("Export CSV", "export.mjs", [
  "--min-score",
  String(minScore),
  "--segment",
  segment,
  "--out",
  "exports/pilot.csv",
]);

console.log("\n=== Done ===");
console.log(`Open: ${path.join(LEAD_GEN_ROOT, "exports", "pilot.csv")}\n`);
