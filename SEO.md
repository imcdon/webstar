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

## Locked NAP (use identically everywhere)

```text
Webstar Business Services · Metro Detroit & surrounding Michigan communities · 248-564-3663 · https://www.webstarbusinessservices.com · info@webstarbusinessservices.com
```

- **Never** list a personal cell on public listings.
- GBP Maps: https://maps.app.goo.gl/ZUKh1nB9Q5wcub5F6
- Match this name, phone, website, and email on Bing, Apple, Facebook, LinkedIn, Yelp, and the site footer/contact.

## Ops checklist

1. **Google Search Console** — www property verified; indexing requested for priority URLs. Sitemap submit still retrying (“couldn’t fetch”).
2. **Google Business Profile** — service-area listing created; phone + website set. Confirm photos; add Workspace email when ready.
3. **After each deploy** — Spot-check clean URLs (`/packages/`, one SEO landing, one package detail) and that footer SEO links resolve.
4. **Optional** — Retry sitemap submit; Domain property when DNS is available at the registrar.

### Crawl notes

- `404` and portal pages use `noindex` (portal also `nofollow`). Keep them out of the sitemap.
- `robots.txt` disallows `/clients/` (staging) and `/portal/`.
- Default social/OG image is `assets/images/og-default.jpg` (1200×630).
- `sameAs` includes GBP; add LinkedIn / Facebook when those pages exist.
- Business phone lives in `library/site-config.php` (`phone_display` / `phone_tel`) and surfaces in header, footer, contact, home schema, and Weddings.
