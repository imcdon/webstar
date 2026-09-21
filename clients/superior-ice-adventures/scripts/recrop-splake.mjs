import sharp from "sharp";
import fs from "node:fs";
import path from "node:path";

const root = process.cwd();
const raw = path.join(root, "_raw/images/services/inland-splake-fishing.jpeg");
const heroOut = path.join(root, "assets/img/hero/inland-splake-fishing.webp");
const serviceOut = path.join(root, "assets/img/services/inland-splake-fishing.webp");
const galleryOut = path.join(root, "assets/img/gallery/inland-splake-fishing.webp");

const meta = await sharp(raw).metadata();
const w = meta.width;
const h = meta.height;

// Bias toward the bottom (fish): take lower ~82% of the frame
const extractH = Math.round(h * 0.82);
const top = h - extractH;

await sharp(raw)
  .rotate()
  .extract({ left: 0, top, width: w, height: extractH })
  .resize({ width: Math.min(w, 2400), withoutEnlargement: true })
  .webp({ quality: 82 })
  .toFile(heroOut);

fs.copyFileSync(heroOut, serviceOut);
fs.copyFileSync(heroOut, galleryOut);
console.log({ w, h, top, extractH, out: (await sharp(heroOut).metadata()).height });
