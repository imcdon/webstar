# Lead-gen runbook — Dixie Hwy (Waterford)

## Before first run

- [ ] `GOOGLE_MAPS_API_KEY` in `lead-gen/.env`
- [ ] GCP billing alert (e.g. $25 / $50)
- [ ] `config/geography.json`: `waterford-dixie-hwy` is enabled and has a `polyline`; Walton Blvd / M-59 cells are `enabled: false` until traced
- [ ] `npm run lead-gen:check`

**Cell IDs:** `waterford-dixie-hwy` (active), `auburn-hills-walton-blvd`, `pontiac-walton-blvd`, `waterford-walton-blvd`, `waterford-m59` (disabled)

## Run

```bash
npm run lead-gen:sweep -- --dry-run          # no API calls: tiles + estimate
npm run lead-gen:corridors                   # full pipeline, writes exports/waterford-dixie-hwy-businesses.csv
```

Step by step, if you prefer:

```bash
node lead-gen/scripts/sweep.mjs --cell waterford-dixie-hwy
node lead-gen/scripts/classify.mjs --show
node lead-gen/scripts/audit-sites.mjs --all --skip-psi --cell waterford-dixie-hwy
node lead-gen/scripts/classify.mjs
node lead-gen/scripts/score-leads.mjs
node lead-gen/scripts/export.mjs --cell waterford-dixie-hwy --out exports/waterford-dixie-hwy-businesses.csv
```

## First Dixie Hwy sweep (Nov 2026)

- 11.0 km corridor, 400 m buffer, 23 base tiles → 860 Nearby Enterprise calls (dense strip retail saturates even 180 m circles)
- 1,018 unique places inside the buffer; ~710 retained after exclusions
- Next month: `npm run lead-gen:sweep -- --deepen --max-depth 4` picks up the spots that still returned 20 results at 180 m

## Review

- Open the CSV; `website_grade` = `none` / `F` / `D` are the outreach targets (`website_need` = high)
- Spot-check `exclude_reason` with `node lead-gen/scripts/export.mjs --include-excluded --out exports/review.csv`
- Fix a wrong exclusion with `exclude_override` (see README), then re-run classify + export
- Add qualitative `notes` in DB only after conversations (not in bulk export)

## Monthly

- Re-sweep (`--reset-tiles`) to refresh coordinates (30-day cache rule) and catch new businesses
- `audit-sites.mjs --all --skip-psi --stale-days 90` to refresh website grades
