# Agents — Webstar Business Services

Ian McDonnell operates **Webstar Business Services LLC**: custom PHP websites for Metro Detroit and surrounding Michigan communities. This monorepo holds the marketing site, client portal, Path A client staging sites, and shared Square shop module.

## Startup protocol (before your first edit)

Read these files in order (parallel reads are fine):

1. [docs/agent/CORE.md](docs/agent/CORE.md) — NAP, pricing, live slugs, constraints
2. [docs/agent/ARCHITECTURE.md](docs/agent/ARCHITECTURE.md) — where code and data live
3. [docs/AGENT-INDEX.md](docs/AGENT-INDEX.md) — doc map and task router
4. **One playbook** from the INDEX task table:
   - [docs/agent/PLAYBOOK-main-site.md](docs/agent/PLAYBOOK-main-site.md)
   - [docs/agent/PLAYBOOK-clients.md](docs/agent/PLAYBOOK-clients.md)
   - [docs/agent/PLAYBOOK-content.md](docs/agent/PLAYBOOK-content.md)

Then open playbook-listed deep docs and target code only. Do not bulk-read `library/seo-pages-batch.php`, client `vendor/`, or `clients/shared/shop/vendor/` unless the playbook names one slug or section.

## Manifest

Full agent doc index: [docs/agent/README.md](docs/agent/README.md)

Human content revamp plan: `~/.cursor/plans/webstar_articles_seo_revamp_79e845a2.plan.md` (status only — facts live in CORE and `docs/content-revamp/`).

Validate CORE slug list: `php bin/agent-context-validate.php`