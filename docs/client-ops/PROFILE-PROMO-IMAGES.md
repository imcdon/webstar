# Profile promo images

Branded photos for Google Business Profile and social. Sources live in `_raw/profile-promo/` (gitignored). Finished exports are in `assets/images/profile-promo/`.

## Regenerate

```bash
npm run profile-promos
```

Requires the three JPGs in `_raw/profile-promo/` (`flowers.jpg`, `fabrizio-frog.jpg`, `water.jpg`) and `assets/images/star-icon-teal.png`.

## Exports

| Concept | Headline | Square (posts) | Wide (cover) |
|---------|----------|----------------|--------------|
| Consultation | Book a free 1-on-1 consultation | `consultation-square.jpg` / `.webp` | `consultation-wide.jpg` / `.webp` |
| Grow | Discover ways to grow your business | `grow-square.jpg` / `.webp` | `grow-wide.jpg` / `.webp` |
| Tailored | Tailored to your business needs | `tailored-square.jpg` / `.webp` | `tailored-wide.jpg` / `.webp` |

Sizes: **1200×1200** square, **1024×576** wide.

## Where to upload

| Asset | Use |
|-------|-----|
| `*-wide.jpg` | Google Business Profile **cover** (rotate seasonally or A/B) |
| `*-square.jpg` | GBP **posts / updates**, Instagram, Facebook posts |

Link posts and CTAs to the site booking page: `/contact/` (Book a free 1-on-1).

## Notes

- Prefer JPG for Google/Facebook upload; WebP is fine for the website if reused later.
- Do not regenerate photos with AI — only composite logo, name, and headline over Ian’s photography.
