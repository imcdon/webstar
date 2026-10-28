# Business card — print

## Files

| File | Purpose |
|------|---------|
| [assets/business-card-webstar.html](assets/business-card-webstar.html) | Front + back preview (3.5 × 2 in faces) |
| [assets/webstar-qr-home.png](assets/webstar-qr-home.png) | QR → `https://www.webstarbusinessservices.com/` |
| Front photo | `assets/images/business-card/fabrizio-the-frog.webp` (source: `_raw/images/business-card/fabrizio-the-frog.jpg`) |
| Star mark | Repo `assets/images/star-icon-gold.png` |

Brand tokens: [BRAND.md](BRAND.md).

## Design

- **Front:** full-bleed photo background (or dark placeholder), larger gold star + company name, bottom band with Ian McDonnell / Owner / phone. Light text on dark. No gold divider; no email/domain on front.
- **Back:** night teal panel; QR to homepage on the left; service bullets on the right (free consults, custom websites for any niche, booking and ecommerce, SEO and GEO with Google Analytics, Serving Metro Detroit and Nationwide); phone · email · domain along the bottom.

## Swapping assets

1. **Front photo:** drop the original in `_raw/images/business-card/`, run `npm run media:images` from the repo root, then point the HTML at the new WebP under `assets/images/business-card/` if the filename changes. Cover crop works best at ~3.5×2 aspect.
2. **QR:** regenerate `webstar-qr-home.png` only if the homepage URL changes.

## Print / PDF

1. Open `assets/business-card-webstar.html` in Chrome (confirm star + QR load).  
2. **Ctrl+P** → Save as PDF → Letter → Background graphics **On**.  
3. Send the PDF to a card printer, or crop each face to **3.5 × 2 in** for upload to Vistaprint / local print shop.

Bleed: if the shop requires 0.125 in bleed, ask them to extend the background or regenerate with bleed margins.
