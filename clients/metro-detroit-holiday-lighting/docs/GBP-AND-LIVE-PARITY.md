# Google Business Profile + live site parity

Off-site checklist after Path A deploy. Do this on Google Business Profile and the live document root — not inventable in PHP alone.

## Messaging to mirror (Path A source of truth)

Use copy consistent with `includes/config.php` and `index.php`:

- **Name:** Metro Detroit Holiday Lighting
- **Tagline / positioning:** Owner-operated holiday lighting for homes, storefronts, and HOA entrances — one expert from design through takedown
- **Support:** Fully insured · 20 years · Holiday magic, simplified
- **Phone / text:** (248) 525-4715
- **Email:** metrodetroitholidaylighting@gmail.com
- **Website:** https://metrodetroitholidaylighting.com
- **Quote CTA:** Text a photo of the property + city

## Google Business Profile

- [ ] Business name exact match (no keyword stuffing)
- [ ] Primary category appropriate (e.g. Lighting contractor / Holiday lighting if available)
- [ ] Service areas: Oakland, Wayne, Macomb — align with cities in `includes/cities.php` (priority: Birmingham, Bloomfield Hills, Troy, Rochester Hills, Rochester, Royal Oak, Novi, Livonia, Northville, Sterling Heights)
- [ ] Description uses owner-operated + full-season service (design, install, mid-season, take-down, storage)
- [ ] Phone matches site; enable messaging if used for photo quotes
- [ ] Website URL points to live domain (not Webstar staging `/clients/...`)
- [ ] Photos: hero / gallery samples (night installs, commercial entrance, Steven at work)
- [ ] Products/services listed: residential, commercial, HOA; wreaths; tree wraps; high-roof
- [ ] Review ask after takedown (Steven); do not invent star counts on the website
- [ ] Hours: reflect seasonal reality or “by appointment” if that is accurate

## Live site parity (`metrodetroitholidaylighting.com`)

Path A staging is richer than a prior minimal live homepage fetch. After deploy:

- [ ] Confirm live document root serves this Path A tree (or sync the same hero, FAQ, audiences, and CTAs)
- [ ] Homepage shows owner-operated H1 / lead and **Text a photo** CTA
- [ ] FAQ includes pricing (custom quote), ownership/storage, and mid-season care
- [ ] `/homeowners/`, `/businesses/`, `/hoa-property-managers/` and priority `/service-areas/{city}/` respond 200
- [ ] Contact page matches text-a-photo CTA
- [ ] Staging gate **off** on live host; **on** for Webstar `/clients/...` hosts
- [ ] No Client Portal auth prompt on the public live domain
- [ ] `robots.txt` / sitemap appropriate for live (staging remains disallowed under Webstar tree)

## Paid search (later)

Defer until testimonials and (optional) price band are filled. Non-brand ads need matching landings (`homeowners/` or city pages).

## Owner

| Task | Who |
|------|-----|
| GBP edits + review asks | Steven (with Ian assist) |
| Deploy Path A → live | Ian (see `DEPLOY.md`) |
| Testimonials / price band approval | Steven → then Ian updates `config.php` |
