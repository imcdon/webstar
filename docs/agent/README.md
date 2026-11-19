# Webstar agent knowledge pack

Versioned onboarding for Cursor and other coding agents. Maintained when pricing, slugs, or architecture change.

| File | Load when |
|------|-----------|
| [CORE.md](CORE.md) | Every session (mandatory) |
| [ARCHITECTURE.md](ARCHITECTURE.md) | Every session (mandatory) |
| [../AGENT-INDEX.md](../AGENT-INDEX.md) | Every session (mandatory) |
| [PLAYBOOK-main-site.md](PLAYBOOK-main-site.md) | Main site, packages, portal, deploy |
| [PLAYBOOK-clients.md](PLAYBOOK-clients.md) | `clients/` Path A, Square shop |
| [PLAYBOOK-content.md](PLAYBOOK-content.md) | `/resources/`, SEO landings |
| [SEO-GEO-SKILLS-EVAL.md](SEO-GEO-SKILLS-EVAL.md) | Upstream SEO/GEO skills fit — cherry-pick list and client-promise guardrails |

Entry point for humans and agents: [../../AGENTS.md](../../AGENTS.md).

**Validate:** `php bin/agent-context-validate.php` from repo root — compares live article slugs in `library/articles-data.php` to the list in CORE.md.