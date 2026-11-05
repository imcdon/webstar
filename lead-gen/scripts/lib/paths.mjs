import path from "node:path";
import { fileURLToPath } from "node:url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));

export const LEAD_GEN_ROOT = path.resolve(__dirname, "../..");
export const CONFIG_DIR = path.join(LEAD_GEN_ROOT, "config");
export const DATA_DIR = path.join(LEAD_GEN_ROOT, "data");
export const DB_PATH = path.join(DATA_DIR, "leads.sqlite");
