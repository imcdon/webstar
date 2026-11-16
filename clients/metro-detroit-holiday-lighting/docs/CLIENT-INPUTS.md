# MDHL — client inputs (Phase 1 blockers)

Steven / Ian: fill these before publishing testimonials, dollar ranges, or named client claims beyond what is already on the Path A site.

## Decisions applied on the site today (no Steven reply yet)

| Item | Decision until you change it | Where |
|------|------------------------------|--------|
| Price framing | **Custom quote only** — no dollar band on the site | FAQ in `includes/config.php` |
| Light ownership / storage | Company provides the seasonal display materials; install, mid-season care, take-down, and **off-season storage** are included | FAQ + process steps |
| October promo | **None** advertised until Steven confirms a real offer | — |
| Testimonials | **Empty** — homepage section stays hidden | `$testimonials` in `includes/config.php` |
| Named commercial captions | Scope-only copy; Soaring Eagle already used in hero alt; Ford House / dealership filenames used as **photo labels**, not endorsement quotes | Landing pages + gallery |

## Still need from Steven

### 1. Testimonials (3–5)

Paste real quotes only. Format for `includes/config.php`:

```php
$testimonials = [
    [
        'quote' => '…',
        'name' => 'First name or initials',
        'detail' => 'City, MI', // optional
    ],
];
```

Optional: Google review profile URL for Ian to link from the homepage later.

### 2. Price band (optional)

If you want public pricing like competitors ($500–$2,500 or $1,600–$3,500+ style):

- Most residential installs: $____ – $____
- Typical commercial / HOA: “custom” or a range: $____ – $____

If you prefer **no numbers**, keep the current custom-quote FAQ.

### 3. Confirm ownership wording

Reply yes/no:

- [ ] Lights are **leased / company-owned** for the season (client never stores bins)
- [ ] Or another model: _______________________

Current site copy assumes full-season service with **off-season storage** by MDHL.

### 4. October incentive (optional)

- Offer text: _______________________
- Dates: _______________________

### 5. Commercial / HOA names you may publish

Check which properties may be named in captions or case blurbs:

- [ ] Soaring Eagle (already on hero alt)
- [ ] Ford House (filename only today)
- [ ] GMC / dealership installs (filenames only)
- [ ] Other: _______________________

## After Steven replies

1. Update `$testimonials` and any pricing FAQ in `includes/config.php`.
2. Mirror pricing FAQ on `homeowners` / `businesses` in `includes/landing-pages.php` if needed.
3. Deploy Path A staging, then live domain (see `DEPLOY.md`).
