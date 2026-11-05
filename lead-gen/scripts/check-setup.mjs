#!/usr/bin/env node
/**
 * Preflight before first lead-gen run.
 *   node lead-gen/scripts/check-setup.mjs
 */
import fs from "node:fs";
import path from "node:path";
import { loadGeography } from "./lib/config.mjs";
import { LEAD_GEN_ROOT, DATA_DIR } from "./lib/paths.mjs";

// Load .env (same as other scripts)
await import("./lib/env.mjs");

const steps = [];
let ok = true;

function pass(msg) {
  steps.push({ ok: true, msg });
  console.log(`  OK  ${msg}`);
}

function fail(msg) {
  steps.push({ ok: false, msg });
  console.log(`  FAIL  ${msg}`);
  ok = false;
}

console.log("Lead-gen setup check\n");

const nodeMajor = parseInt(process.versions.node.split(".")[0], 10);
if (nodeMajor >= 20) pass(`Node ${process.versions.node} (20+ required)`);
else fail(`Node ${process.versions.node} — upgrade to Node 20+`);

const envPath = path.join(LEAD_GEN_ROOT, ".env");
if (!fs.existsSync(envPath)) {
  fail(`Missing ${envPath} — copy .env.example and add GOOGLE_MAPS_API_KEY`);
} else {
  const raw = fs.readFileSync(envPath, "utf8").trim();
  const key = process.env.GOOGLE_MAPS_API_KEY?.trim();
  if (!key) {
    fail(
      `${envPath} exists but GOOGLE_MAPS_API_KEY is empty. Add a line: GOOGLE_MAPS_API_KEY=your_key`
    );
  } else {
    pass("GOOGLE_MAPS_API_KEY is set");
  }
  if (raw && !key && !raw.includes("GOOGLE_MAPS_API_KEY")) {
    fail(".env must use GOOGLE_MAPS_API_KEY=... (not the raw key alone on one line)");
  }
}

try {
  const geo = loadGeography();
  const enabled = geo.cells.filter((c) => c.enabled !== false);
  const corridors = enabled.filter((c) => Array.isArray(c.polyline) && c.polyline.length >= 2);
  pass(`${geo.cells.length} geography cells loaded (${enabled.length} enabled, ${corridors.length} with road polyline)`);
} catch (e) {
  fail(e.message);
}

for (const dir of ["data", "exports"]) {
  const p = path.join(LEAD_GEN_ROOT, dir);
  fs.mkdirSync(p, { recursive: true });
  pass(`Folder ${dir}/`);
}

const cheerioPath = path.resolve(LEAD_GEN_ROOT, "../node_modules/cheerio/package.json");
if (fs.existsSync(cheerioPath)) pass("npm dependencies installed (repo root)");
else fail("Run `npm install` from repo root (c:\\xampp\\htdocs\\webstar-business-solutions)");

console.log("");
if (ok) {
  console.log("Ready. Next:");
  console.log("  npm run lead-gen:sweep -- --dry-run     # tile count / call estimate, no API calls");
  console.log("  npm run lead-gen:corridors              # sweep → classify → audit → score → export");
  process.exit(0);
} else {
  console.log("Fix the FAIL items above, then run this check again:");
  console.log("  npm run lead-gen:check");
  process.exit(1);
}
