# Raw photos

Drop originals here, then from the repo root run:

```bash
npm install
npm run media:images
```

`_raw/` is gitignored. The site serves only the WebP files written to `assets/images/`.

| Folder | Output |
|--------|--------|
| `_raw/images/hero/` | Two files per photo: `assets/images/{name}.webp` (max 1920 px, under 200 KB) and `assets/images/{name}-768.webp` (max 768 px). Use these for full-bleed backgrounds. |
| `_raw/images/` and other folders | One WebP, max width 1600, quality 80, same relative path under `assets/images/`. |
| `_raw/images/archive/` | Kept on disk and not processed. Camera rolls and unused originals go here. |

Icons and SVG stay in `assets/images/` and do not go through this script.

The homepage background source is `_raw/images/hero/hero-home.jpg`.

Business card front photo: `_raw/images/business-card/fabrizio-the-frog.jpg` → `assets/images/business-card/fabrizio-the-frog.webp`.
