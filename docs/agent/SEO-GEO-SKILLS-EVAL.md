# SEO/GEO Cursor skills — Webstar fit evaluation

**Date:** 2026-11-19  
**Scope:** Aaron He Zhu SEO/GEO agent skills vs Webstar Low-and-Slow GEO, [CONTENT-SEO.md](../CONTENT-SEO.md), and personal Cursor skills.  
**Verdict:** **Partial adopt** — cherry-pick maintained skills as internal QA; never as a client-facing GEO product line.

Public stance (do not contradict in client copy or agent promises): [low-and-slow-seo-geo-2026](../../library/articles/low-and-slow-seo-geo-2026.php) — GEO is consistent crawlable facts, not “#1 in AI” packages.

## Bundle decision

| Source | Status | Webstar choice |
|--------|--------|----------------|
| [seo-geo-cursor-skills](https://github.com/aaron-he-zhu/seo-geo-cursor-skills) / [seo-geo-claude-skills](https://github.com/aaron-he-zhu/seo-geo-claude-skills) on `main` | Signpost only; standalone 20-skill line frozen at tag **`v9.9.12`** | **Skip** unless you need zero upstream churn |
| [aaron-marketing-skills](https://github.com/aaron-he-zhu/aaron-marketing-skills) | Actively maintained; 16 SEO/GEO skills + CORE-EEAT (80) + CITE (40) | **Use this**; install **individual** skills only |
| Full 120-skill marketing umbrella | Seven disciplines | **Do not** install wholesale |

Install pattern (**global** user skills — do not commit into the monorepo):

```bash
npx skills add aaron-he-zhu/aaron-marketing-skills -g -y `
  -s content-quality-auditor -s serp-markup-builder `
  -s on-page-seo-checker -s technical-seo-checker
# optional for single-landing revamp sessions:
# npx skills add aaron-he-zhu/aaron-marketing-skills -g -y -s serp-analysis
```

`skills` CLI places copies under `~/.agents/skills/`. For Cursor agent discovery alongside Webstar skills, also mirror into `~/.cursor/skills/` (same four folder names). Do **not** leave project-scoped `.agents/` or an empty `skills-lock.json` in this repo.

Host entrypoint when available: `/aaron-marketing:seo-geo`.

## What Webstar already owns

| Layer | Path | Role |
|-------|------|------|
| Editorial gate | [CONTENT-SEO.md](../CONTENT-SEO.md) | 8-point pre-publish checklist + CORE-EEAT veto crosswalk |
| Content routing | [PLAYBOOK-content.md](PLAYBOOK-content.md) | Articles + one landing slug per session |
| Keyword law | [SEO.md](../../SEO.md) | One winning URL per head term |
| Competitive intel | `~/.cursor/skills/competitor-analysis/` | Local discovery; no invented rankings |
| Project skill | `~/.cursor/skills/webstar-project/` | Monorepo startup protocol |
| Technical backlog | [SEO-AUDIT.md](../SEO-AUDIT.md) | Oct 2026 crawl / PageSpeed notes |

Upstream skills are an **optional layer under** CONTENT-SEO — never a replacement.

## Framework alignment (CORE-EEAT / CITE)

**Use (ideas / vetoes):**

- **C01** — Title promise = delivery (one job per URL).
- **R10** — No material internal factual contradictions (pricing, FAQ = schema).
- **T04** — Affiliate disclosure if material relationship (rare on Webstar; N/A when none).
- EEAT Experience / Exclusivity — original Experience paragraph, Ian byline.
- CORE Organization / Direct answer — answer upfront for humans and grounding tools.

**Apply selectively (friction):**

- Citation-density quotas — do not force outbound cites that bloat Michigan-owner guides.
- Long-tail / “query coverage” that implies new near-duplicate URLs — conflicts with SEO.md and Low-and-Slow anti city-clone rules.
- Numeric GEO/SEO scores — **internal advisory only**; never publish or pitch to clients ([CORE.md](CORE.md)).
- **CITE** — marketing site and **live** client domains only; not Path A `/clients/` (robots-disallowed staging).

## Skill cherry-pick table (16 maintained SEO/GEO skills)

| Skill | Verdict | Guardrails |
|-------|---------|------------|
| `content-quality-auditor` | **Partial** | CONTENT-SEO first; then veto trio + useful C/O/R/E items; no score theater in client copy |
| `serp-markup-builder` | **Adopt** | FAQ / Article JSON-LD must match visible FAQ text |
| `on-page-seo-checker` | **Partial** | Title ≤ ~60 with `\| Webstar`; meta 140–155 |
| `technical-seo-checker` | **Partial** | Complements SEO-AUDIT; crawl, schema, LCP — no invented metrics |
| `site-structure-optimizer` | **Partial** | Internal links only; no competing head terms |
| `geo-content-optimizer` | **Partial** | Clarity, NAP, definitions — not “win AI search” language |
| `keyword-research` | **Partial** | Map to existing SEO.md / owner-approved CSV rows only |
| `serp-analysis` | **Partial** | One landing slug per session (phase0); optional install |
| `content-gap-analysis` | **Partial** | Gaps ≠ permission to ship thin new URLs |
| `content-writer` | **Skip** | Who/How/Why: Ian edits; avoid scaled-content tone |
| `page-play-builder` | **Skip** | Risks package-boilerplate landings |
| `competitor-analysis` | **Skip install** | Keep Webstar-tuned personal skill |
| `domain-authority-auditor` | **Partial** | Main site + live domains only |
| `offsite-signal-analyzer` | **Partial** | Citations unverified until measured; no fiction |
| `rank-tracker` | **Partial** | Observed GSC/tool data only |
| `performance-monitor` | **Partial** | Observed PSI/GSC only |

**Do not install:** full umbrella, frozen `v9.9.12` line, duplicate `competitor-analysis`, unguarded `content-writer`.

## Do not use for client promises

Aligned with Low-and-Slow GEO:

- No guaranteed rankings, “#1 in AI,” or overnight GEO packages.
- No invented traffic, positions, review counts, or case-study numbers.
- Do not rename Care Plan / build work as an “AEO/GEO SKU.”
- Agent audits are **publish-readiness QA**, not a sold methodology brand.

## Suggested agent prompts

**Resources guide (CONTENT-SEO first, then vetoes):**

```text
Audit resources/{slug}/ using docs/CONTENT-SEO.md first (all 8 gates).
Then apply CORE-EEAT vetoes only: C01 (title = delivery), R10 (no factual contradiction),
T04 (affiliate disclosure if applicable). Skip numeric GEO/SEO score rollups for client-facing notes.
Respect SEO.md — do not propose competing URLs.
```

**Single SEO landing:**

```text
Revamp one landing slug from seo-pages-data.php OR seo-pages-batch.php (not both files wholesale).
Run on-page checks (title/meta lengths) and FAQ/schema match.
Use serp-analysis only for competitive SERP context; do not add city-clone pages.
Link to at most one relevant Resources guide.
```

**Technical pass:**

```text
Compare findings to docs/SEO-AUDIT.md. Prefer fixing known issues (meta truncation, LCP, schema)
over inventing a new audit score for the client.
```

## Smoke-test log (post-install)

| Target | Type | Result |
|--------|------|--------|
| `low-and-slow-seo-geo-2026` | Resources | **Pass** gates + vetoes; meta short of 140–155 |
| `web-design-services-troy` | Landing (batch) | **Pass** vetoes; meta over 155 chars |

### Smoke notes (2026-11-19)

Lightweight CONTENT-SEO + CORE-EEAT **veto-only** pass after installing the four skills globally (not a full 80-item score). Skills used as checklists: `content-quality-auditor` (vetoes), `on-page-seo-checker` (meta), `serp-markup-builder` (FAQ = schema expectation).

**`resources/low-and-slow-seo-geo-2026/`**

| Check | Result |
|-------|--------|
| People-first / one job | Pass — explains Low-and-Slow GEO; clear consult CTA |
| Originality | Pass — brisket / two-speed framing unique vs other guides |
| Boilerplate | Pass — single `/packages/` pointer |
| FAQ visible themes | Pass — GEO≠product line; no overnight results (matches body) |
| Meta title (~60 + `\| Webstar`) | Pass — ~43 with suffix |
| Meta description 140–155 | **Partial** — ~125 chars (under band) |
| Facts / no fake metrics | Pass — explicitly rejects “#1 AI” promises |
| **C01** title = delivery | Pass |
| **R10** no material contradiction | Pass — FAQ and body agree |
| **T04** affiliate disclosure | N/A |

**`web-design-services-troy` (seo-pages-batch)**

| Check | Result |
|-------|--------|
| One job / local Experience | Pass — Troy landmarks and Simple package path |
| FAQ present | Pass — five Q&As; keep schema in sync when rendering |
| Meta title | Pass — ~42 with `\| Webstar` |
| Meta description 140–155 | **Fail band** — ~169 chars (truncate risk; matches SEO-AUDIT theme) |
| **C01** | Pass — Troy web design promise delivered |
| **R10** | Pass — pricing/process aligns with Simple Website story |
| **T04** | N/A |

**Install verification:** `content-quality-auditor`, `serp-markup-builder`, `on-page-seo-checker`, `technical-seo-checker` present under `%USERPROFILE%\.agents\skills\` and mirrored to `%USERPROFILE%\.cursor\skills\`. Project `.agents/` removed.

## Related docs

- Crosswalk in [CONTENT-SEO.md](../CONTENT-SEO.md) (§ CORE-EEAT veto crosswalk)
- [PLAYBOOK-content.md](PLAYBOOK-content.md)
- [AGENT-INDEX.md](../AGENT-INDEX.md)
