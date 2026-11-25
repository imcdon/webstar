# Playbook — main site

Use for packages, plans, process, contact, homepage, footer, schema, sitemap, `.htaccess`, portal (high level).

## Pre-edit

1. Confirm fact in [CORE.md](CORE.md) or `library/packages-data.php` — not memory.
2. Keyword conflict check: [SEO.md](../../SEO.md) — one head term per URL.
3. If changing prices or tier includes, **only** `packages-data.php` (and mirrored copy on package pages if not data-driven).

## Common tasks

| Task | Files |
|------|--------|
| New package bullet | `packages-data.php`, verify `package.php` displays field |
| Plan pricing | `plans-data.php` |
| Contact form / mail | `contact.php`, `library/mail-config.example.php` (not live secrets) |
| Book 1-on-1 | `book.php`, `library/process-consultation.php`, calendar config examples |
| Intake flow | `intake.php`, `library/package-intake-form.php`, `library/process-package-intake.php` |
| Redirect | `.htaccess` + SEO.md retired table |
| OG / default image | `assets/images/og-default.jpg`, header partials |
| GA events | `scripts/scripts.js` (`phone_click`, `contact_submit`, etc.) |

## Portal and mail

Read [portal/README.md](../../portal/README.md) and [DEPLOY.md](../../DEPLOY.md) for auth and server-only files.

## Post-edit

- [ ] NAP unchanged unless Ian requested ([CORE.md](CORE.md))
- [ ] No secrets in diff
- [ ] If URL or slug changed: sitemap + SEO.md
- [ ] Local smoke: one clean URL at `http://localhost/webstar-business-solutions/...`