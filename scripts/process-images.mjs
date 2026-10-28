#!/usr/bin/env node
// Bulk-optimize photos for the Webstar site.
// Drop originals in _raw/images/ and run `npm run media:images`.
// Content photos: one WebP, max width 1600, quality 80.
// Files under _raw/images/hero/: 1920 and 768 WebPs, each kept under 200 KB.
// _raw/images/archive/ is ignored.

import { mkdir, readFile, readdir, stat } from "node:fs/promises";
import path from "node:path";
import { createRequire } from "node:module";
import sharp from "sharp";

const require = createRequire(import.meta.url);
let _heicConvert;
function loadHeicConvert() {
  if (!_heicConvert) {
    _heicConvert = require("heic-convert");
  }
  return _heicConvert;
}

const IN_ROOT = path.resolve(process.cwd(), "_raw/images");
const OUT_ROOT = path.resolve(process.cwd(), "assets/images");
const CONTENT_MAX_WIDTH = 1600;
const CONTENT_QUALITY = 80;
const HERO_MAX_BYTES = 200 * 1024;

const INPUT_EXT_REGEX = /\.(jpe?g|png|tiff?|webp|hei[cf])$/i;

function extPriority(filePath) {
  const ext = path.extname(filePath).toLowerCase();
  if (ext === ".jpg" || ext === ".jpeg") return 100;
  if (ext === ".png") return 90;
  if (ext === ".webp") return 80;
  if (ext === ".heic" || ext === ".heif") return 70;
  if (ext === ".tif" || ext === ".tiff") return 65;
  return 50;
}

function toPosix(filePath) {
  return filePath.split(path.sep).join("/");
}

function dedupeKey(relPath) {
  const norm = toPosix(relPath);
  const dir = path.posix.dirname(norm);
  const stem = path.posix.basename(norm, path.extname(norm)).toLowerCase();
  return dir === "." ? stem : `${dir}/${stem}`;
}

function isArchive(relPath) {
  const norm = toPosix(relPath);
  return norm === "archive" || norm.startsWith("archive/");
}

function isHero(relPath) {
  const norm = toPosix(relPath);
  return norm.startsWith("hero/");
}

function contentOutputPath(relPath) {
  const dirRel = path.dirname(relPath);
  const stem = path.basename(relPath, path.extname(relPath)).toLowerCase();
  const name = `${stem}.webp`;
  return dirRel === "." ? path.join(OUT_ROOT, name) : path.join(OUT_ROOT, dirRel, name);
}

function heroOutputSpecs(relPath) {
  const stem = path.basename(relPath, path.extname(relPath)).toLowerCase();
  return [
    { outPath: path.join(OUT_ROOT, `${stem}.webp`), maxWidth: 1920, startQuality: 72 },
    { outPath: path.join(OUT_ROOT, `${stem}-768.webp`), maxWidth: 768, startQuality: 70 },
  ];
}

function compareCandidates(a, b) {
  const dp = extPriority(b.path) - extPriority(a.path);
  if (dp !== 0) return dp;
  if (b.mtimeMs !== a.mtimeMs) return b.mtimeMs - a.mtimeMs;
  return a.path.localeCompare(b.path);
}

async function walk(dir) {
  const out = [];
  let entries;
  try {
    entries = await readdir(dir, { withFileTypes: true });
  } catch (e) {
    if (e.code === "ENOENT") return out;
    throw e;
  }
  for (const entry of entries) {
    const full = path.join(dir, entry.name);
    if (entry.isDirectory()) {
      out.push(...(await walk(full)));
    } else if (INPUT_EXT_REGEX.test(entry.name)) {
      const rel = path.relative(IN_ROOT, full);
      if (!isArchive(rel)) out.push(full);
    }
  }
  return out;
}

async function encodeWebp(source, outPath, maxWidth, quality) {
  const meta = await sharp(source).metadata();
  const width = Math.min(meta.width ?? maxWidth, maxWidth);
  await mkdir(path.dirname(outPath), { recursive: true });
  await sharp(source)
    .rotate()
    .resize({ width, withoutEnlargement: true })
    .webp({ quality })
    .toFile(outPath);
}

async function encodeUnderBudget(source, outPath, maxWidth, startQuality, maxBytes) {
  let quality = startQuality;
  let size = Infinity;
  while (quality >= 45) {
    await encodeWebp(source, outPath, maxWidth, quality);
    size = (await stat(outPath)).size;
    if (size <= maxBytes) break;
    quality -= 6;
  }
  return { quality, size };
}

async function sourceBuffer(inputPath) {
  const extLow = path.extname(inputPath).toLowerCase();
  if (extLow !== ".heic" && extLow !== ".heif") {
    return inputPath;
  }
  try {
    await sharp(inputPath).metadata();
    return inputPath;
  } catch {
    const rel = path.relative(IN_ROOT, inputPath);
    console.warn(`Sharp HEIC decode failed (${toPosix(rel)}); using heic-convert fallback`);
    const convert = loadHeicConvert();
    const jpegBuf = await convert({
      buffer: await readFile(inputPath),
      format: "JPEG",
      quality: 1,
    });
    return jpegBuf;
  }
}

async function processFile(inputPath) {
  const rel = path.relative(IN_ROOT, inputPath);
  const source = await sourceBuffer(inputPath);
  const inSize = (await stat(inputPath)).size;
  const lines = [];

  if (isHero(rel)) {
    for (const spec of heroOutputSpecs(rel)) {
      const result = await encodeUnderBudget(
        source,
        spec.outPath,
        spec.maxWidth,
        spec.startQuality,
        HERO_MAX_BYTES,
      );
      lines.push(
        `${toPosix(path.relative(OUT_ROOT, spec.outPath))}  ${(result.size / 1024).toFixed(0)}KB  q${result.quality}`,
      );
    }
  } else {
    const outPath = contentOutputPath(rel);
    await encodeWebp(source, outPath, CONTENT_MAX_WIDTH, CONTENT_QUALITY);
    const outSize = (await stat(outPath)).size;
    lines.push(`${toPosix(path.relative(OUT_ROOT, outPath))}  ${(outSize / 1024).toFixed(0)}KB`);
  }

  console.log(`${toPosix(rel)}  ${(inSize / 1024 / 1024).toFixed(2)}MB`);
  for (const line of lines) console.log(`  → ${line}`);
}

const discovered = await walk(IN_ROOT);
if (discovered.length === 0) {
  console.log(`No images found in ${IN_ROOT}. Drop raw photos there and re-run.`);
  process.exit(0);
}

const byKey = new Map();
for (const full of discovered) {
  const rel = path.relative(IN_ROOT, full);
  const key = dedupeKey(rel);
  let list = byKey.get(key);
  if (!list) {
    list = [];
    byKey.set(key, list);
  }
  const s = await stat(full);
  list.push({ path: full, mtimeMs: s.mtimeMs });
}

const toProcess = [];
for (const [key, candidates] of byKey) {
  candidates.sort(compareCandidates);
  const winner = candidates[0];
  toProcess.push(winner.path);
  if (candidates.length > 1) {
    const skipped = candidates.slice(1).map((c) => toPosix(path.relative(IN_ROOT, c.path)));
    console.warn(
      `Deduped (${key}): using ${toPosix(path.relative(IN_ROOT, winner.path))}; skipped: ${skipped.join(", ")}`,
    );
  }
}

console.log(`Processing ${toProcess.length} image(s)…`);
for (const filePath of toProcess) {
  try {
    await processFile(filePath);
  } catch (e) {
    console.error(`Failed: ${filePath}`, e);
    process.exitCode = 1;
  }
}
console.log("Done.");
