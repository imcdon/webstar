# Playbook — content and SEO

Use for `/resources/` guides, `articles-data.php`, SEO landings, internal links, FAQs, JSON-LD dates.

## Pre-edit (mandatory gate)

Read [CONTENT-SEO.md](../CONTENT-SEO.md) — people-first, Who/How/Why, staggered honest dates, FAQ visible = schema. Optional upstream SEO/GEO skill guardrails: [SEO-GEO-SKILLS-EVAL.md](SEO-GEO-SKILLS-EVAL.md).

Check [resource-topics.csv](../content-revamp/resource-topics.csv) for slug/title approval.

## New or updated Resource article

1. Add row to `library/articles-data.php` (meta, `primary_keyword`, `published`, `updated`, `faq`, `related_landings`).
2. Create `library/articles/{slug}.php` — body is HTML with `h2`/`p`/`ul`; links via:

   ```php
   <?php echo webstar_h(webstar_url('resources/other-slug/')); ?>
   ```

3. Match voice: Michigan owner audience, minimal boilerplate, one pricing pointer to `/packages/` unless article is about cost.
4. Update [CORE.md](CORE.md) slug list and CSV status when shipping.

## SEO landing rewrite

1. Find slug in [SEO.md](../../SEO.md) — do not merge without owner 301 approval.
2. Load **one** slug from `seo-pages-data.php` OR `seo-pages-batch.php`.
3. Target shorter MC, unique local Experience — [phase0-summary.md](../content-revamp/phase0-summary.md).
4. Link to relevant **one** guide instead of duplicating long package essays.

## Phase 2 revamp (pending)

- 10 landings: light edit in `seo-pages-data.php`
- 22 landings: heavy rewrite in `seo-pages-batch.php` (one slug per session)

Plan: `~/.cursor/plans/webstar_articles_seo_revamp_79e845a2.plan.md`

## Post-edit

- [ ] Title ~60 chars `| Webstar`; meta 140–155 chars
- [ ] FAQ answers match visible text
- [ ] `published` / `updated` ISO dates align with JSON-LD
- [ ] No duplicate head term vs another live URL
- [ ] Run `php bin/agent-context-validate.php` if new slug shipped