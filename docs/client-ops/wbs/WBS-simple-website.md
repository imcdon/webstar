# Internal WBS — Simple Website

**Audience:** Webstar ops only. Do not send to clients.  
**Public version:** “How we work” on `/packages/simple-website/`.  
**Annex:** [ANNEX-simple-website.md](../agreements/ANNEX-simple-website.md) · Hosting: [HOSTING-STANDARD.md](../HOSTING-STANDARD.md)

Phases: Planning → Building → Deployment → Google.

---

## 1.1 Planning

- 1.1.1 Intake: logo, photos, copy notes, preferred colors, CTA goals (call / book link)
- 1.1.2 Scope call: lock 3-page sitemap (e.g. Home / Services or About / Contact); set exact `{PRICE}` inside $350–$500
- 1.1.3 Confirm hosting path (client cPanel host vs Webstar managed under Care Plan) and that the domain is in the client’s name; move DNS to Cloudflare
- 1.1.4 Confirm the client’s Google account (or have them create one) for GBP / GA4 / GSC
- 1.1.5 Agreement + 50% deposit gate; Stripe invoice; portal login issued
- 1.1.6 Scope summary email ([SCOPE-SUMMARY.md](../SCOPE-SUMMARY.md))
- 1.1.7 Tech notes: stack (PHP static pattern), contact form recipient, book/call button targets

## 1.2 Building

- 1.2.1 Repo/scaffold from client template; config, paths, security checklist
- 1.2.2 Design system: tokens, type, mobile-first layout shell
- 1.2.3 Page 1 — Home (brand, offer, primary CTAs)
- 1.2.4 Page 2 — Services or About (per sitemap)
- 1.2.5 Page 3 — Contact (form + tel + map/hours as provided)
- 1.2.6 Contact form submit path + honeypot/spam basics; GA4 events on form submit and tel clicks
- 1.2.7 Call / Book Now buttons (links only; no custom booker)
- 1.2.8 SEO basics: titles, meta, LocalBusiness schema, sitemap.xml, robots, favicon, OG image
- 1.2.9 Accessibility pass (headings, labels, contrast, focus, alt text); mobile QA; form test
- 1.2.10 Client review rounds (2 included); content freeze before launch

## 1.3 Deployment

- 1.3.1 Staging preview (auth gate on Webstar host)
- 1.3.2 Build complete notice → balance invoice (50/50 projects)
- 1.3.3 Production deploy per hosting path; SSL/DNS checklist; Cloudflare proxy on
- 1.3.4 Smoke test live (pages, form delivery, CTAs, 404, redirects)
- 1.3.5 Handoff: credentials via password manager share; client owns domain/hosting; archive final backup to client Drive folder
- 1.3.6 Start 30-day warranty clock; log launch date in portal

## 1.4 Google

- 1.4.1 Create or claim GBP under client’s Google account; complete NAP, categories, description, hours, services, attributes; upload photos
- 1.4.2 Start Google verification; document method; follow up until verified or blocked by Google
- 1.4.3 GA4 property + data stream; install tag; mark form submit + tel click as key events
- 1.4.4 Search Console property (domain or URL prefix); submit sitemap; confirm indexing request for home
- 1.4.5 Confirm client is primary owner on GBP/GA4/GSC; Webstar as manager only
- 1.4.6 First check-in 7–14 days after launch: one-page plain-English notes + 2–3 next actions; mention Care Plans once, no pressure
- 1.4.7 Close project; request Google review if client is happy
