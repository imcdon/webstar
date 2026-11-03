#!/usr/bin/env node
/**
 * Download self-hosted Inter + Fraunces woff2 files from Google Fonts.
 *   node scripts/download-fonts.mjs
 */
import { mkdir, writeFile } from "node:fs/promises";
import path from "node:path";

const OUT = path.resolve(process.cwd(), "assets/fonts");
await mkdir(OUT, { recursive: true });

const cssUrl =
  "https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap";

const res = await fetch(cssUrl, {
  headers: {
    // Request woff2 (modern browsers)
    "User-Agent":
      "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
  },
});
if (!res.ok) throw new Error(`CSS fetch failed: ${res.status}`);
const css = await res.text();

const urls = [...css.matchAll(/url\((https:\/\/fonts\.gstatic\.com\/[^)]+)\)/g)].map((m) => m[1]);
const unique = [...new Set(urls)];
console.log(`Found ${unique.length} font file(s)`);

const renamed = new Map();
let i = 0;
for (const fontUrl of unique) {
  i += 1;
  const isFraunces = fontUrl.includes("fraunces") || fontUrl.includes("Fraunces");
  const family = isFraunces ? "fraunces" : "inter";
  const name = `${family}-${i}.woff2`;
  const dest = path.join(OUT, name);
  const bin = await fetch(fontUrl);
  if (!bin.ok) throw new Error(`Font fetch failed: ${fontUrl}`);
  await writeFile(dest, Buffer.from(await bin.arrayBuffer()));
  renamed.set(fontUrl, `./${name}`);
  console.log(`saved ${name}`);
}

let localCss = css;
for (const [remote, local] of renamed) {
  localCss = localCss.split(remote).join(local);
}
// Drop google preconnect leftovers; keep @font-face blocks
localCss = localCss.replace(/\/\*[\s\S]*?\*\//g, "").trim() + "\n";
await writeFile(path.join(OUT, "fonts.css"), localCss);
console.log("Wrote assets/fonts/fonts.css");
