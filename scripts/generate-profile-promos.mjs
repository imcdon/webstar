#!/usr/bin/env node
/**
 * Composite profile promo graphics from _raw/profile-promo photos.
 *   npm run profile-promos
 *
 * Outputs square (1200×1200) and wide (1024×576) WebP + JPG under
 * assets/images/profile-promo/.
 */

import { mkdir, readFile } from "node:fs/promises";
import path from "node:path";
import sharp from "sharp";

const ROOT = process.cwd();
const RAW = path.join(ROOT, "_raw", "profile-promo");
const OUT = path.join(ROOT, "assets", "images", "profile-promo");
const LOGO = path.join(ROOT, "assets", "images", "star-icon-teal.png");
const FONT = path.join(ROOT, "assets", "fonts", "galano", "GalanoClassicAltSemiBold.otf");

const BUSINESS_NAME = "Webstar Business Services";

const SIZES = {
  square: { width: 1200, height: 1200 },
  wide: { width: 1024, height: 576 },
};

const PROMOS = [
  {
    slug: "consultation",
    source: "flowers.jpg",
    headline: "Book a free 1-on-1 consultation",
    mood: "bright",
    // Prefer sky/flowers; slight upward crop for square
    position: { square: "centre", wide: "centre" },
  },
  {
    slug: "grow",
    source: "fabrizio-frog.jpg",
    headline: "Discover ways to grow your business",
    mood: "dark",
    // Keep frog lower-right visible
    position: { square: "entropy", wide: "entropy" },
  },
  {
    slug: "tailored",
    source: "water.jpg",
    headline: "Tailored to your business needs",
    mood: "dark",
    position: { square: "centre", wide: "centre" },
  },
];

function escapeXml(value) {
  return String(value)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}

function wrapWords(text, maxCharsPerLine) {
  const words = text.split(/\s+/);
  const lines = [];
  let current = "";
  for (const word of words) {
    const next = current ? `${current} ${word}` : word;
    if (next.length > maxCharsPerLine && current) {
      lines.push(current);
      current = word;
    } else {
      current = next;
    }
  }
  if (current) {
    lines.push(current);
  }
  return lines;
}

function fontFaceCss(fontBase64) {
  if (!fontBase64) {
    return "";
  }
  return `@font-face {
  font-family: "Galano Classic Alt";
  src: url("data:font/otf;base64,${fontBase64}") format("opentype");
  font-weight: 600;
  font-style: normal;
}`;
}

/**
 * @param {{
 *   width: number,
 *   height: number,
 *   headline: string,
 *   mood: 'bright' | 'dark',
 *   sizeKey: 'square' | 'wide',
 *   fontBase64: string | null,
 *   slug: string,
 * }} opts
 */
function buildOverlaySvg(opts) {
  const { width, height, headline, mood, sizeKey, fontBase64, slug } = opts;
  const pad = Math.round(width * 0.055);
  const logoSize = Math.round(width * (sizeKey === "square" ? 0.09 : 0.08));
  const nameSize = Math.round(width * (sizeKey === "square" ? 0.038 : 0.042));
  const headlineSize = Math.round(width * (sizeKey === "square" ? 0.055 : 0.048));
  const maxChars = sizeKey === "square" ? 22 : 28;
  const lines = wrapWords(headline, maxChars);
  const lineHeight = Math.round(headlineSize * 1.2);
  const fontFamily = fontBase64
    ? '"Galano Classic Alt", Arial, Helvetica, sans-serif'
    : "Arial, Helvetica, sans-serif";

  const gradientStops =
    mood === "bright"
      ? `
      <stop offset="0%" stop-color="#04191a" stop-opacity="0"/>
      <stop offset="45%" stop-color="#0a2f2c" stop-opacity="0.35"/>
      <stop offset="100%" stop-color="#04191a" stop-opacity="0.82"/>`
      : `
      <stop offset="0%" stop-color="#04191a" stop-opacity="0.55"/>
      <stop offset="40%" stop-color="#04191a" stop-opacity="0.2"/>
      <stop offset="100%" stop-color="#04191a" stop-opacity="0.72"/>`;

  // Brand strip: optical vertical center with the star (alphabetic baseline)
  const logoCenterY = pad + logoSize / 2;
  const brandY = Math.round(logoCenterY + nameSize * 0.35);
  const nameX = pad + logoSize + Math.round(pad * 0.4);

  // Headline placement
  let headlineAnchor = "start";
  let headlineX = pad;
  let headlineBlockTop;
  if (mood === "bright") {
    headlineBlockTop = height - pad - lines.length * lineHeight;
  } else if (slug === "tailored" && sizeKey === "wide") {
    headlineAnchor = "end";
    headlineX = width - pad;
    headlineBlockTop = height - pad - lines.length * lineHeight;
  } else {
    headlineBlockTop = height - pad - lines.length * lineHeight;
  }

  const headlineTspans = lines
    .map((line, i) => {
      const dy = i === 0 ? 0 : lineHeight;
      return `<tspan x="${headlineX}" dy="${dy}">${escapeXml(line)}</tspan>`;
    })
    .join("");

  return `<?xml version="1.0" encoding="UTF-8"?>
<svg width="${width}" height="${height}" viewBox="0 0 ${width} ${height}" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <style type="text/css"><![CDATA[
${fontFaceCss(fontBase64)}
    ]]></style>
    <linearGradient id="veil" x1="0" y1="0" x2="0" y2="1">
      ${gradientStops}
    </linearGradient>
    <linearGradient id="topBar" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0%" stop-color="#04191a" stop-opacity="${mood === "bright" ? 0.45 : 0.65}"/>
      <stop offset="100%" stop-color="#04191a" stop-opacity="0"/>
    </linearGradient>
  </defs>
  <rect width="${width}" height="${height}" fill="url(#veil)"/>
  <rect width="${width}" height="${Math.round(height * 0.28)}" fill="url(#topBar)"/>
  <text
    x="${nameX}"
    y="${brandY}"
    fill="#ffffff"
    font-family="${escapeXml(fontFamily)}"
    font-size="${nameSize}"
    font-weight="600"
  >${escapeXml(BUSINESS_NAME)}</text>
  <text
    x="${headlineX}"
    y="${headlineBlockTop}"
    fill="#ffffff"
    font-family="${escapeXml(fontFamily)}"
    font-size="${headlineSize}"
    font-weight="700"
    text-anchor="${headlineAnchor}"
  >${headlineTspans}</text>
  <rect
    x="${headlineAnchor === "end" ? headlineX - Math.round(width * 0.12) : headlineX}"
    y="${headlineBlockTop + lines.length * lineHeight + Math.round(pad * 0.25)}"
    width="${Math.round(width * 0.12)}"
    height="${Math.max(3, Math.round(height * 0.006))}"
    rx="2"
    fill="#c9a227"
  />
</svg>`;
}

async function prepareBackground(inputPath, width, height, position) {
  return sharp(inputPath)
    .rotate()
    .resize(width, height, {
      fit: "cover",
      position: position || "centre",
    })
    .toBuffer();
}

async function renderPromo(promo, sizeKey, logoBuffer, fontBase64) {
  const { width, height } = SIZES[sizeKey];
  const inputPath = path.join(RAW, promo.source);
  const bg = await prepareBackground(inputPath, width, height, promo.position[sizeKey]);

  const logoSize = Math.round(width * (sizeKey === "square" ? 0.09 : 0.08));
  const pad = Math.round(width * 0.055);
  const logoResized = await sharp(logoBuffer)
    .resize(logoSize, logoSize, { fit: "contain", background: { r: 0, g: 0, b: 0, alpha: 0 } })
    .png()
    .toBuffer();

  const svg = buildOverlaySvg({
    width,
    height,
    headline: promo.headline,
    mood: promo.mood,
    sizeKey,
    fontBase64,
    slug: promo.slug,
  });

  const flat = await sharp(bg)
    .composite([
      { input: Buffer.from(svg), top: 0, left: 0 },
      { input: logoResized, top: pad, left: pad },
    ])
    .png()
    .toBuffer();

  const baseName = `${promo.slug}-${sizeKey}`;
  const webpPath = path.join(OUT, `${baseName}.webp`);
  const jpgPath = path.join(OUT, `${baseName}.jpg`);

  await sharp(flat).webp({ quality: 82 }).toFile(webpPath);
  await sharp(flat).jpeg({ quality: 85, mozjpeg: true }).toFile(jpgPath);

  return { webpPath, jpgPath };
}

async function main() {
  await mkdir(OUT, { recursive: true });

  const logoBuffer = await readFile(LOGO);
  let fontBase64 = null;
  try {
    fontBase64 = (await readFile(FONT)).toString("base64");
  } catch {
    console.warn("Galano font not found; falling back to Arial.");
  }

  for (const promo of PROMOS) {
    for (const sizeKey of Object.keys(SIZES)) {
      const { webpPath, jpgPath } = await renderPromo(promo, sizeKey, logoBuffer, fontBase64);
      console.log(`Wrote ${path.relative(ROOT, webpPath)}`);
      console.log(`Wrote ${path.relative(ROOT, jpgPath)}`);
    }
  }
}

main().catch((err) => {
  console.error(err);
  process.exit(1);
});
