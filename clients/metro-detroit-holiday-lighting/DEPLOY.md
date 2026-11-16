# Metro Detroit Holiday Lighting — Path A staging

- **Local:** `http://localhost/webstar-business-solutions/clients/metro-detroit-holiday-lighting/`
- **Staging:** `https://webstarbusinessservices.com/clients/metro-detroit-holiday-lighting/`
- **Live:** `https://metrodetroitholidaylighting.com`
- **Gate:** On for Webstar hosts; off on localhost and `metrodetroitholidaylighting.com`.

## Docs (strategy)

- [docs/COMPETITOR-ANALYSIS.md](docs/COMPETITOR-ANALYSIS.md) — competitor tiers + implementation checklist
- [docs/CLIENT-INPUTS.md](docs/CLIENT-INPUTS.md) — Steven approvals (testimonials, pricing, naming)
- [docs/GBP-AND-LIVE-PARITY.md](docs/GBP-AND-LIVE-PARITY.md) — Google Business Profile + live domain checklist

## Deploy

1. Edit under `clients/metro-detroit-holiday-lighting/`.
2. Push to **https://github.com/imcdon/webstar** (`main`).
3. cPanel → Webstar Git → **Update from Remote** → **Deploy HEAD Commit**.
4. Portal → Open preview (uses the `/clients/...` URL). Staging prompts for Client Portal credentials.
5. After live deploy, complete [docs/GBP-AND-LIVE-PARITY.md](docs/GBP-AND-LIVE-PARITY.md).

## Photos

Drop `.jpg` / `.jpeg` / `.png` / `.webp` files into `assets/img/gallery/` and push. The Gallery page and home preview list them automatically. Prefer wide, well-lit night photos.

## Testimonials

Add real quotes to `$testimonials` in `includes/config.php`. The home section stays hidden while the array is empty. Use the paste format in [docs/CLIENT-INPUTS.md](docs/CLIENT-INPUTS.md).

## Contact form

Submissions email `metrodetroitholidaylighting@gmail.com`. On Path A staging under the Webstar tree, PHPMailer uses `library/mail-config.php` when present; otherwise PHP `mail()` is used. On the live domain, configure host mail / SMTP as needed.
