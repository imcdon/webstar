# Lead-gen — Google Maps Platform compliance

Internal Webstar tooling only. Not for reselling Google data.

## What we store in `data/leads.sqlite`

**Allowed long-term**

- `place_id` (Google Place ID — [exempt from caching restrictions](https://developers.google.com/maps/documentation/places/web-service/place-id))
- Our **derived** fields: `audit_flags` JSON, PSI scores, `lead_score`, website grade/need, `exclude_reason`, `distance_to_road_m`, `road_position_m`, `notes`
- `google_maps_uri` as a link back to the listing (refresh via Place Details / sweep if stale)
- Pipeline-only `website_url` (used to run audits; do not bulk-export for unrelated CRMs)

**Internal-only, short-lived cache (refresh from Places; do not resell)**

- `display_name` for outreach CSVs — re-fetch with a fresh sweep (or `enrich.mjs --refresh-names`) before large campaigns
- `primary_type` / `types_json` — used only to apply our exclusion policy
- `user_rating_count` — used only for the large-firm heuristic

**Coordinates (≤ 30 days)**

Per [Maps Service Specific Terms](https://cloud.google.com/maps-platform/terms/maps-service-terms), lat/lng from Places may be cached for at most **30 consecutive days**. The sweep stores `lat`, `lng`, `location_fetched_at` per place. Re-run the sweep monthly (`npm run lead-gen:sweep -- --reset-tiles`) to refresh; anything with `location_fetched_at` older than 30 days must not be displayed on a map.

**Do not**

- Scrape Google Maps HTML
- Publish or sell static CSVs of Google business names, phones, or addresses without [live Place Details](https://developers.google.com/maps/documentation/places/web-service/place-details) and [required attribution](https://developers.google.com/maps/documentation/places/web-service/policies)

## Corridor geometry (OpenStreetMap)

Road centerlines in `config/geography.json` (`polyline`) were traced from OpenStreetMap via the Overpass API. OSM data is © OpenStreetMap contributors, [ODbL](https://www.openstreetmap.org/copyright). Any map that shows these corridors or uses OSM tiles must carry the OSM attribution.

## Cost

- Sweep: Nearby Search **Enterprise** SKU (because the field mask includes `websiteUri`) — 1,000 free calls/month, tracked per month in `meta` as `sku:nearby_enterprise:YYYY-MM`; the sweep stops at `LEAD_GEN_NEARBY_BUDGET` (default 1000) and resumes next run.
- Site audits, classification, scoring, export: no Google calls.

## Official links

- [Places policies](https://developers.google.com/maps/documentation/places/web-service/policies)
- [Usage and billing](https://developers.google.com/maps/documentation/places/web-service/usage-and-billing)
- [Pricing](https://developers.google.com/maps/billing-and-pricing/pricing)
