#!/usr/bin/env node
/**
 * Recompress existing WebPs under assets/img for LCP/CWV.
 * Hero max width 1600 @ q72; other images max 1400 @ q75.
 *   node scripts/optimize-webps.mjs
 */
import { readdir, stat, writeFile, rename, unlink, copyFile } from "node:fs/promises";
import path from "node:path";
import sharp from "sharp";

const ROOT = path.resolve(process.cwd(), "assets/img");
const HERO_DIRS = new Set(["hero"]);

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
    } else if (/\.webp$/i.test(entry.name) && !entry.name.endsWith(".tmp.webp")) {
      out.push(full);
    }
  }
  return out;
}

function isHeroPath(filePath) {
  const rel = path.relative(ROOT, filePath).split(path.sep);
  return HERO_DIRS.has(rel[0]);
}

async function replaceFile(file, buf) {
  const tmp = file + ".tmp.webp";
  await writeFile(tmp, buf);
  try {
    await unlink(file);
  } catch {
    /* may be locked; try overwrite via copy */
  }
  try {
    await rename(tmp, file);
  } catch {
    await copyFile(tmp, file);
    await unlink(tmp);
  }
}

const files = await walk(ROOT);
console.log(`Optimizing ${files.length} WebP(s)…`);

for (const file of files) {
  const before = (await stat(file)).size;
  const hero = isHeroPath(file);
  const maxW = hero ? 1600 : 1400;
  const quality = hero ? 72 : 75;
  let buf = await sharp(file)
    .rotate()
    .resize({ width: maxW, withoutEnlargement: true })
    .webp({ quality, effort: 6 })
    .toBuffer();

  if (buf.length >= before && before < 180 * 1024) {
    console.log(`skip (already small): ${path.relative(ROOT, file).replace(/\\/g, "/")} ${(before / 1024).toFixed(0)}KB`);
    continue;
  }

  if (buf.length >= before) {
    buf = await sharp(file)
      .rotate()
      .resize({ width: hero ? 1400 : 1200, withoutEnlargement: true })
      .webp({ quality: hero ? 68 : 70, effort: 6 })
      .toBuffer();
    if (buf.length >= before) {
      console.log(`skip (no gain): ${path.relative(ROOT, file).replace(/\\/g, "/")} ${(before / 1024).toFixed(0)}KB`);
      continue;
    }
  }

  try {
    await replaceFile(file, buf);
    console.log(
      `${path.relative(ROOT, file).replace(/\\/g, "/")}  ${(before / 1024).toFixed(0)}KB → ${(buf.length / 1024).toFixed(0)}KB`,
    );
  } catch (err) {
    console.warn(`FAIL ${path.relative(ROOT, file).replace(/\\/g, "/")}: ${err.message}`);
  }
}
console.log("Done.");
