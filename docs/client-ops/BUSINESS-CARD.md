# Business card — print

## Files

| File | Purpose |
|------|---------|
| [assets/business-card-webstar.html](assets/business-card-webstar.html) | Front + back preview (3.5 × 2 in faces) |
| [assets/webstar-qr-home.png](assets/webstar-qr-home.png) | QR → `https://www.webstarbusinessservices.com/` |
| `assets/business-card-front-bg.jpg` | Front photo (optional); dark CSS placeholder until added |
| Star mark | Repo `assets/images/star-icon-gold.png` |

Brand tokens: [BRAND.md](BRAND.md).

## Design

- **Front:** full-bleed photo background (or dark placeholder), larger gold star + company name, bottom band with Ian McDonnell / Owner / phone. Light text on dark. No gold divider; no email/domain on front.
- **Back:** night teal panel; QR to homepage on the left; service bullets on the right (packages, GBP & Analytics, SEO and GEO, Ongoing Support, Metro Detroit and Nationwide); phone · email · domain along the bottom.

## Swapping assets

1. **Front photo:** save as `docs/client-ops/assets/business-card-front-bg.jpg` (cover crop works best at ~3.5×2 aspect). Refresh the HTML preview.
2. **QR:** regenerate `webstar-qr-home.png` only if the homepage URL changes.

## Print / PDF

1. Open `assets/business-card-webstar.html` in Chrome (confirm star + QR load).  
2. **Ctrl+P** → Save as PDF → Letter → Background graphics **On**.  
3. Send the PDF to a card printer, or crop each face to **3.5 × 2 in** for upload to Vistaprint / local print shop.

Bleed: if the shop requires 0.125 in bleed, ask them to extend the background or regenerate with bleed margins.
