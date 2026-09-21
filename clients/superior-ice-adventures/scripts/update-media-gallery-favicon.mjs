import sharp from "sharp";
import fs from "node:fs";
import path from "node:path";

const root = process.cwd();

async function cropSplake() {
  const src = path.join(root, "assets/img/hero/inland-splake-fishing.webp");
  const meta = await sharp(src).metadata();
  const w = meta.width;
  const h = meta.height;
  const extractH = Math.round(h * 0.78);
  const tmp = path.join(root, "assets/img/hero/inland-splake-fishing-new.webp");
  await sharp(src)
    .extract({ left: 0, top: 0, width: w, height: extractH })
    .resize(w, h, { fit: "cover", position: "top" })
    .webp({ quality: 82 })
    .toFile(tmp);
  fs.renameSync(tmp, src);
  fs.copyFileSync(src, path.join(root, "assets/img/services/inland-splake-fishing.webp"));
  const leftover = path.join(root, "assets/img/hero/inland-splake-fishing-tmp.webp");
  if (fs.existsSync(leftover)) fs.unlinkSync(leftover);
  console.log("splake cropped");
}

function fillGallery() {
  const galleryDir = path.join(root, "assets/img/gallery");
  fs.mkdirSync(galleryDir, { recursive: true });
  // Clear old numbered placeholders
  for (const f of fs.readdirSync(galleryDir)) {
    if (/\.(webp|jpg|jpeg|png)$/i.test(f)) fs.unlinkSync(path.join(galleryDir, f));
  }
  const map = [
    ["assets/img/hero/atlantic.webp", "atlantic.webp"],
    ["assets/img/hero/burbot.webp", "burbot.webp"],
    ["assets/img/hero/inland-splake-fishing.webp", "inland-splake-fishing.webp"],
    ["assets/img/hero/lake-superior-lake-trout.webp", "lake-superior-lake-trout.webp"],
    ["assets/img/hero/ice-shack-rentals.webp", "ice-shack-rentals.webp"],
    ["assets/img/resuperioriceadventures - Copy/antlanticshanty.webp", "atlantic-shanty.webp"],
    ["assets/img/resuperioriceadventures - Copy/brooktrout.webp", "brook-trout.webp"],
    ["assets/img/resuperioriceadventures - Copy/burbotclient.webp", "burbot-client.webp"],
    ["assets/img/resuperioriceadventures - Copy/ice.webp", "ice.webp"],
    ["assets/img/resuperioriceadventures - Copy/laketroutsmile.webp", "lake-trout-smile.webp"],
  ];
  for (const [from, to] of map) {
    const abs = path.join(root, from);
    if (!fs.existsSync(abs)) {
      console.log("missing", from);
      continue;
    }
    fs.copyFileSync(abs, path.join(galleryDir, to));
    console.log("gallery", to);
  }
}

async function makeFavicon() {
  const lake = path.join(root, "assets/img/textures/lake-superior.png");
  const { data, info } = await sharp(lake).ensureAlpha().raw().toBuffer({ resolveWithObject: true });
  for (let i = 0; i < data.length; i += 4) {
    if (data[i + 3] > 10) {
      data[i] = 113; // #710F10
      data[i + 1] = 15;
      data[i + 2] = 16;
    }
  }
  const side = Math.max(info.width, info.height);
  const top = Math.floor((side - info.height) / 2);
  const bottom = Math.ceil((side - info.height) / 2);
  const left = Math.floor((side - info.width) / 2);
  const right = Math.ceil((side - info.width) / 2);
  const pngPath = path.join(root, "assets/img/favicon.png");
  await sharp(data, { raw: { width: info.width, height: info.height, channels: 4 } })
    .extend({ top, bottom, left, right, background: { r: 0, g: 0, b: 0, alpha: 0 } })
    .resize(64, 64)
    .png()
    .toFile(pngPath);

  const b64 = fs.readFileSync(pngPath).toString("base64");
  const svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
  <image href="data:image/png;base64,${b64}" width="64" height="64"/>
</svg>
`;
  fs.writeFileSync(path.join(root, "assets/img/favicon.svg"), svg);
  console.log("favicon ok", fs.statSync(pngPath).size);
}

await cropSplake();
fillGallery();
await makeFavicon();
