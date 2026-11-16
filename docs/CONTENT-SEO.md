# Content & SEO editorial standards

**Agents:** see [AGENTS.md](../AGENTS.md) and [agent/PLAYBOOK-content.md](agent/PLAYBOOK-content.md).

Operational rules for Resources guides and SEO landing pages. Primary references:

- [People-first helpful content](https://developers.google.com/search/docs/fundamentals/creating-helpful-content)
- [Generative AI on your website](https://developers.google.com/search/docs/fundamentals/using-gen-ai-content)
- [Scaled content abuse](https://developers.google.com/search/docs/essentials/spam-policies#scaled-content-abuse)
- Keyword map: [SEO.md](../SEO.md)

## Who, How, Why

| | Webstar |
|---|---------|
| **Who** | Ian McDonnell — operator; byline on guides; landings tied to real intake/process. |
| **How** | AI may draft or outline; **Ian edits and fact-checks** before publish. |
| **Why** | Help Michigan small business owners choose and launch a site they own — not to game rankings. |

## Pre-publish gate (every URL)

1. **People-first** — Useful to the intended audience without Search; reader can complete one clear goal.
2. **One job per URL** — Matches winning URL in SEO.md; no duplicate head terms.
3. **Originality** — At least one paragraph or section that does not appear on other Webstar pages (Experience).
4. **Boilerplate budget** — One pricing pointer + link to `/packages/` unless the page is about cost/WaaS.
5. **FAQs** — Unique Q&A; visible text matches FAQ schema.
6. **Meta** — Title ≤ ~60 chars with `| Webstar`; description 140–155 chars.
7. **Dates** — Guides use **staggered publish**: each rewrite’s `published` = the deploy day that guide goes live; `updated` matches on first ship, then bumps on later edits. Article JSON-LD and sitemap aligned. No backdating.
8. **Facts** — Pricing bands, process, Google steps match packages and agreements; no invented clients or results.

## Landings: keep vs rewrite vs merge

See [content-revamp/phase0-summary.md](content-revamp/phase0-summary.md). **Merge only with owner approval** — 301 + SEO.md update.

## Resources topics

**Owner picks** guide topics (title, slug, primary keyword). Do not add guides without an entry in SEO.md / keyword map and owner approval. Landings and guides must not compete for the same head term.

Topic backlog: `docs/content-revamp/resource-topics.csv` (when filled).

## Regenerate inventory

```bash
php bin/content-phase0-audit.php > docs/content-revamp/phase0-inventory.csv
```
