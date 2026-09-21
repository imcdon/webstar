import sharp from "sharp";
import fs from "node:fs";

const src = "assets/img/textures/lake-superior.png";
const { data, info } = await sharp(src).ensureAlpha().raw().toBuffer({ resolveWithObject: true });

// Brand red fill on opaque lake pixels
for (let i = 0; i < data.length; i += 4) {
  if (data[i + 3] > 10) {
    data[i] = 113;
    data[i + 1] = 15;
    data[i + 2] = 16;
  }
}

// Tight bounding box so the lake fills the icon
let minX = info.width;
let minY = info.height;
let maxX = 0;
let maxY = 0;
for (let y = 0; y < info.height; y++) {
  for (let x = 0; x < info.width; x++) {
    if (data[(y * info.width + x) * 4 + 3] > 10) {
      if (x < minX) minX = x;
      if (y < minY) minY = y;
      if (x > maxX) maxX = x;
      if (y > maxY) maxY = y;
    }
  }
}

const bw = maxX - minX + 1;
const bh = maxY - minY + 1;
const pad = Math.round(Math.max(bw, bh) * 0.06);
const side = Math.max(bw, bh) + pad * 2;
const canvas = Buffer.alloc(side * side * 4, 0);
const ox = Math.floor((side - bw) / 2);
const oy = Math.floor((side - bh) / 2);

for (let y = 0; y < bh; y++) {
  for (let x = 0; x < bw; x++) {
    const si = ((minY + y) * info.width + (minX + x)) * 4;
    const di = ((oy + y) * side + (ox + x)) * 4;
    canvas[di] = data[si];
    canvas[di + 1] = data[si + 1];
    canvas[di + 2] = data[si + 2];
    canvas[di + 3] = data[si + 3];
  }
}

const square = sharp(canvas, { raw: { width: side, height: side, channels: 4 } });

await square.clone().resize(64, 64).png().toFile("assets/img/favicon.png");
await square.clone().resize(180, 180).png().toFile("assets/img/apple-touch-icon.png");

const b64 = fs.readFileSync("assets/img/favicon.png").toString("base64");
fs.writeFileSync(
  "assets/img/favicon.svg",
  `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><image href="data:image/png;base64,${b64}" width="64" height="64"/></svg>\n`,
);

console.log({ bw, bh, side, favicon: await sharp("assets/img/favicon.png").metadata() });
