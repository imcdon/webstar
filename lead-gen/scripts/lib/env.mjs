import dotenv from "dotenv";
import fs from "node:fs";
import path from "node:path";
import { LEAD_GEN_ROOT } from "./paths.mjs";

const candidates = [
  path.join(LEAD_GEN_ROOT, ".env"),
  path.resolve(LEAD_GEN_ROOT, "../.env"),
];

for (const p of candidates) {
  if (fs.existsSync(p)) {
    dotenv.config({ path: p });
    break;
  }
}

export function getGoogleKey() {
  const key = process.env.GOOGLE_MAPS_API_KEY?.trim();
  if (!key) {
    const envFile = path.join(LEAD_GEN_ROOT, ".env");
    let hint = "Add GOOGLE_MAPS_API_KEY=your_key to lead-gen/.env (see .env.example).";
    if (fs.existsSync(envFile) && !fs.readFileSync(envFile, "utf8").includes("GOOGLE_MAPS_API_KEY=")) {
      hint = "lead-gen/.env must include the line GOOGLE_MAPS_API_KEY=... (not the key by itself).";
    }
    throw new Error(`Missing GOOGLE_MAPS_API_KEY. ${hint}`);
  }
  return key;
}

export function getPsiKey() {
  return process.env.PSI_API_KEY?.trim() || getGoogleKey();
}

export function getApiCallBudget() {
  const n = parseInt(process.env.LEAD_GEN_API_CALL_BUDGET ?? "500", 10);
  return Number.isFinite(n) ? n : 500;
}

/** Monthly cap for Nearby Search Enterprise calls (free tier is 1,000/month). */
export function getNearbyBudget() {
  const n = parseInt(process.env.LEAD_GEN_NEARBY_BUDGET ?? "1000", 10);
  return Number.isFinite(n) ? n : 1000;
}
