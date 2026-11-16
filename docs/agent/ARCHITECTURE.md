# Webstar architecture — for agents

## Repository layout

| Area | Path | Role |
|------|------|------|
| Marketing pages | `*.php` (root) | `index.php`, `packages.php`, `contact.php`, etc. |
| Shared library | `library/` | Config, helpers, data arrays, article bodies |
| Assets | `assets/` | CSS, JS, images |
| Scripts | `scripts/scripts.js` | GA `dataLayer` events |
| Portal | `portal/` | Client portal (noindex) |
| Clients | `clients/{slug}/` | Path A staging sites |
| Shared shop | `clients/shared/shop/` | Square catalog PHP module |
| Deploy | `.cpanel.yml` | cPanel rsync tasks |
| Bin | `bin/` | CLI utilities (content audit, agent validate) |

## URL routing (`.htaccess`)

- `/packages/{slug}/` → `package.php?slug=`
- `/resources/{slug}/` → `article.php?slug=`
- `/resources/feed/` → `resources-feed.php`
- `/{landing-slug}/` → `landing.php?slug=` (excludes reserved segments: `packages`, `clients`, `portal`, `library`, …)
- `sitemap.xml` → `sitemap.php`
- Local subdirectory: same rules under `/webstar-business-solutions/`

## Data-driven content

| Content type | Metadata | Body / MC |
|--------------|----------|-----------|
| Resource article | `library/articles-data.php` | `library/articles/{slug}.php` (HTML fragments) |
| SEO landing (10) | `library/seo-pages-data.php` | `body` / sections in same array |
| SEO landing (22) | `library/seo-pages-batch.php` | Per-slug entries in batch return |
| Package tier | `library/packages-data.php` | Rendered by `package.php` |
| Monthly plans | `library/plans-data.php` | `plans.php` |

Loaders: `webstar_articles()`, `webstar_article_by_slug()`, `webstar_seo_by_slug()`, `webstar_package_by_slug()` in `library/helpers.php`.

Article body path: `library/articles/{slug}.php` (included by `article.php`).

## Config and SEO globals

- `library/site-config.php` — base URL hints, `sameAs`, phone
- `library/seo-global.php` — Organization / WebSite JSON-LD on pages
- `library/mail-config.php` — **server-only** (from example)

## Where to change X

| Goal | Files |
|------|--------|
| Article title, meta, FAQ, dates | `library/articles-data.php` |
| Article prose | `library/articles/{slug}.php` |
| Landing meta or short MC | `seo-pages-data.php` or one entry in `seo-pages-batch.php` |
| Package price or includes | `packages-data.php` |
| Package page template | `package.php` + includes |
| Clean URL / 301 | `.htaccess` + update [SEO.md](../../SEO.md) if slug retires |
| Footer SEO links | Driven from seo data via helpers |
| Sitemap lastmod | `sitemap.php` reads article/seo dates |

## Rendering pipeline

1. Page sets `$currentPage`, `$pageTitle`, `$pageDescription`, `$canonicalPath`.
2. `includes/header.php` / `footer.php` (pattern varies by `headerVariant`).
3. `article.php`: loads metadata, includes body file, JSON-LD via `article-helpers.php`, author aside, related links.
4. `landing.php`: SEO landing template + FAQ schema when present.

## Clients and shop

- Each client: own `index.php`, `includes/config.php`, optional `assets/`.
- Square: `clients/shared/shop/` — consumed by DJ's Tackle; config in gitignored `shop-config.php` from example.
- Staging banner: `clients/shared/webstar-staging-banner.php`.

## Deploy

GitHub `main` → cPanel pull → `.cpanel.yml` deploy to document root. Details: [DEPLOY.md](../../DEPLOY.md).

Production: `https://www.webstarbusinessservices.com`