# SEO keyword map & ops

One winning URL per head term. Do not retarget losers to compete for the same primary phrase.

## Keyword map

| Intent | Winning URL | Notes / losers |
|--------|-------------|----------------|
| Brand / general Metro Detroit home | `/` | Title/H1 emphasize Metro Detroit web design & marketing. Keep “affordable” light; do not lead with statewide “affordable website design.” |
| Affordable website design **Michigan** | `/affordable-website-design-michigan/` | Owns the statewide affordability intent. |
| Web design **Metro Detroit** (services) | `/web-design-services-metro-detroit/` | Services-focused landing; distinct from Home. |
| Web design **Detroit** (city) | `/web-design-services-detroit/` | City-specific only. |
| Oakland County | `/web-design-services-oakland-county/` | Unchanged. |
| Macomb County | `/web-design-services-macomb-county/` | Unchanged. |
| Nail salons | `/web-design-services-nail-salons/` | Industry niche. |
| Hair salons | `/web-design-services-hair-salons/` | Industry niche. |
| Contractors | `/web-design-services-contractors/` | Industry niche. |
| Landscaping | `/web-design-services-landscaping/` | Industry niche. |
| Marketing package Michigan | `/small-business-marketing-package-michigan/` | Unchanged. |
| Package pricing hub | `/packages/` | Compare tiers; no city keyword fight. |
| Per-package detail | `/packages/{slug}/` | e.g. `/packages/simple-website/` |

### Home vs Michigan affordable (locked)

- **Home** primary: Metro Detroit websites & marketing (brand + local hub).
- **Affordable Michigan** primary: statewide one-time / affordable website design.
- Do not restore Home title/H1 to lead with “Affordable Website Design Michigan.”

## Ops checklist

1. **Google Search Console** — Verify `webstarbusinessservices.com`. Submit `https://webstarbusinessservices.com/sitemap.php`.
2. **Google Business Profile** — Create/claim as a **service-area** business (Metro Detroit / Michigan). Add website and primary category when ready.
3. **After each deploy** — Spot-check clean URLs (`/packages/`, one SEO landing, one package detail) and that footer SEO links resolve.
4. **Optional** — In GSC, request indexing for home and `/packages/` after a meaningful deploy.

### Crawl notes

- `404` and portal pages use `noindex` (portal also `nofollow`). Keep them out of the sitemap.
- Fill `sameAs` in `library/site-config.php` when public LinkedIn / Facebook / GBP URLs exist.
