# Agent index — doc map and task router

Use after [AGENTS.md](../AGENTS.md) startup: CORE + ARCHITECTURE, then pick a row below.

## Documentation map

| Path | Purpose |
|------|---------|
| [agent/CORE.md](agent/CORE.md) | Invariants — NAP, tiers, article slugs, clients, never-commit |
| [agent/ARCHITECTURE.md](agent/ARCHITECTURE.md) | Request routing, data files, where to change what |
| [agent/PLAYBOOK-main-site.md](agent/PLAYBOOK-main-site.md) | Packages, portal, deploy, schema |
| [agent/PLAYBOOK-clients.md](agent/PLAYBOOK-clients.md) | Path A staging, shop, per-client DEPLOY |
| [agent/PLAYBOOK-content.md](agent/PLAYBOOK-content.md) | Resources articles, SEO landings, editorial gate |
| [CONTENT-SEO.md](CONTENT-SEO.md) | Pre-publish checklist (people-first, dates, FAQs) |
| [content-revamp/resource-topics.csv](content-revamp/resource-topics.csv) | Article backlog (live vs proposed) |
| [content-revamp/phase0-summary.md](content-revamp/phase0-summary.md) | 32 landings keep/rewrite disposition |
| [SEO-AUDIT.md](SEO-AUDIT.md) | Meta length, technical SEO notes |
| [../SEO.md](../SEO.md) | Keyword map — one URL per head term |
| [../DEPLOY.md](../DEPLOY.md) | GitHub → cPanel, local XAMPP |
| [client-ops/README.md](client-ops/README.md) | Agreements, WBS, brand (ops — not first read) |
| [SQUARE-PHP-REFERENCE.md](SQUARE-PHP-REFERENCE.md) | Square API notes for shop work |
| [../clients/README.md](../clients/README.md) | Path A clients overview |
| [../portal/README.md](../portal/README.md) | Client portal auth and deploy |

## Task router

| If you are… | Playbook | Then read (L3) | Avoid |
|-------------|----------|----------------|-------|
| Packages, plans, contact, homepage, schema | [PLAYBOOK-main-site](agent/PLAYBOOK-main-site.md) | `library/packages-data.php`, `process.php`, target `*.php` | Whole `helpers.php` |
| Portal, mail, deploy | PLAYBOOK-main-site | `DEPLOY.md`, `portal/README.md`, `mail-config.example.php` | client-ops agreements |
| New/edit Resource guide | PLAYBOOK-content | `CONTENT-SEO.md`, `articles-data.php`, `library/articles/{slug}.php` | All `library/articles/` |
| SEO landing rewrite | PLAYBOOK-content | `SEO.md` row, one slug in `seo-pages-data.php` or `seo-pages-batch.php`, phase0 CSV row | Entire batch file |
| DJ's Tackle / shop | PLAYBOOK-clients | `clients/djstackle/DEPLOY.md`, `shared/shop/README.md`, `shop-config.example.php` | `vendor/` trees |
| MDHL / Superior Ice | PLAYBOOK-clients | `clients/{slug}/DEPLOY.md`, `PATH-A.md`, `SECURITY-CHECKLIST.md` | Unrelated clients |
| Pricing or tier copy truth | PLAYBOOK-main-site | `packages-data.php`, `plans-data.php` | Marketing research docs |

## Token discipline

- Prefer `Grep` / `Glob` with a path over repo-wide search.
- Landings: **one slug per session** from batch file.
- Windows shell: chain with `;` not `&&`.
- Git: commit only when the user asks; never commit secrets (see CORE).

## Local dev

- Main: `http://localhost/webstar-business-solutions/`
- Resources: `http://localhost/webstar-business-solutions/resources/{slug}/`
- Client: `http://localhost/webstar-business-solutions/clients/{slug}/`