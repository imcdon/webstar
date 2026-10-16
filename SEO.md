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
| Fishing guides | `/web-design-services-fishing-guides/` | Nationwide guide services; booking + trip showcase intent. |
| Service area hub | `/service-areas/` | Index of city + regional landings. |
| City web design (Troy, Royal Oak, Sterling Heights, Warren, Livonia, Dearborn, Novi, Farmington Hills, Southfield, Ann Arbor) | `/web-design-services-{city}/` | One URL per city; no duplicate blog posts for same phrase. |
| Industry hub | `/industries/` | Index of trade-specific landings. |
| Restaurants, dentists, plumbers/HVAC, auto repair, cleaning, gyms, real estate, law firms | `/web-design-services-{trade}/` | Industry pages. |
| Website cost Michigan | `/website-cost-michigan/` | Commercial intent landing (ranges + WaaS). |
| Website redesign | `/website-redesign-services/` | Intent landing. |
| Wix / Squarespace alternative | `/wix-squarespace-alternative/` | Intent landing. |
| Google Business Profile setup service | `/google-business-profile-setup/` | Intent landing. |
| Resources / guides hub | `/resources/` | Blog index; informational queries only here. |
| Guide articles | `/resources/{slug}/` | One primary keyword per article (see `library/articles-data.php`). |
| Package pricing hub | `/packages/` | Compare the three website tiers; no city keyword fight. |
| Per-package detail | `/packages/{slug}/` | `simple-website`, `business-website`, `business-growth-website` |
| Monthly website / care plans | `/plans/` | Website as a Service ($150/mo) + Care Plans. Owns "monthly website plan" / "website maintenance plan Michigan" intent. |
| How we work | `/process/` | HowTo schema; supports trust, not a keyword target. |
| Legal / trust | `/privacy/`, `/terms/`, `/accessibility/` | Indexable, low priority in sitemap. |

### Retired URLs (301 in `.htaccess`)

| Old | New |
|-----|-----|
| `/packages/marketing/` | `/packages/` |
| `/packages/get-me-on-google/` | `/packages/simple-website/` |
| `/packages/business-website-marketing/` | `/packages/business-website/` |
| `/packages/custom-growth/` | `/packages/business-growth-website/` |
| `/small-business-marketing-package-michigan/` | `/packages/business-website/` |

After deploy, request indexing for the three new package URLs and `/plans/`; let the old URLs drop out via the redirects. Do not recreate a standalone marketing-package landing — Google services are inside every tier now.

### Home vs Michigan affordable (locked)

- **Home** primary: Metro Detroit websites & marketing (brand + local hub).
- **Affordable Michigan** primary: statewide one-time / affordable website design.
- Do not restore Home title/H1 to lead with “Affordable Website Design Michigan.”

## Locked NAP (use identically everywhere)

```text
Webstar Business Services · Metro Detroit & surrounding Michigan communities · 248-564-3663 · https://www.webstarbusinessservices.com · ian@webstarbusinessservices.com
```

- **Never** list a personal cell on public listings.
- GBP Maps: https://maps.app.goo.gl/ZUKh1nB9Q5wcub5F6
- Match this name, phone, website, and email on Bing, Apple, Facebook, LinkedIn, Yelp, and the site footer/contact.

## Ops checklist

1. **Google Search Console** — www property verified; submit **`https://www.webstarbusinessservices.com/sitemap.xml`** (rewrites to `sitemap.php` with `lastmod` + `priority`). Request indexing for `/service-areas/`, `/industries/`, `/resources/`, new city/industry/intent landings after deploy.
2. **Google Business Profile** — service-area listing created; phone + website set. Confirm photos; add Workspace email when ready.
3. **After each deploy** — Spot-check clean URLs (`/packages/`, one SEO landing, one package detail) and that footer SEO links resolve.
4. **Optional** — Retry sitemap submit; Domain property when DNS is available at the registrar.

### Crawl notes

- `404` and portal pages use `noindex` (portal also `nofollow`). Keep them out of the sitemap.
- `robots.txt` disallows `/clients/` (staging) and `/portal/`.
- Default social/OG image is `assets/images/og-default.jpg` (1200×630).
- `sameAs` includes GBP; add LinkedIn / Facebook when those pages exist.
- Home `priceRange` is `$350–$2500+`; package pages emit `Offer` with `PriceSpecification` min/max from the range string in `packages-data.php`. Keep prices in one place.
- Business phone lives in `library/site-config.php` (`phone_display` / `phone_tel`) and surfaces in header, footer, contact, home schema, and Weddings.
- SEO landings emit FAQPage JSON-LD when FAQ data exists; portal pages skip canonical while remaining noindex.
- Site-wide `Organization` + `WebSite` JSON-LD on every page via `library/seo-global.php`.
- GA4 events (configure in GTM if duplicate): `phone_click`, `intake_submit`, `contact_submit`, `intake_cta_click` via `dataLayer` in `scripts/scripts.js`.

## Off-site growth (owner)

- Complete GBP services list matching three tiers + plans; weekly posts; seeded Q&A; photos.
- Create LinkedIn company page, Facebook page, Yelp, Bing Places, Apple Business Connect; add URLs to `library/site-config.php` `sameAs`.
- NAP identical to locked block above on every profile.
- Ask each client for a Google review at project close; set `google_review_url` when available.
- See `docs/client-ops/LEGITIMACY-CHECKLIST.md` for insurance, Stripe products, and directories.

## Monthly SEO log (fill in)

| Month | Impressions (GSC) | Clicks | Top 3 queries | Actions taken |
|-------|-------------------|--------|---------------|---------------|
| YYYY-MM | | | | |
