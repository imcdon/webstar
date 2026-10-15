# Internal WBS — Care Plans (Essential $49 · Standard $99 · Premium $179)

**Audience:** Webstar ops only.  
**Public version:** `/plans/#care-plans`.  
**Agreement:** [CARE-PLAN-AGREEMENT.md](../agreements/CARE-PLAN-AGREEMENT.md) · Hosting: [HOSTING-STANDARD.md](../HOSTING-STANDARD.md)

---

## Onboarding

- Agreement eSigned (plan + hosting option checked); Stripe subscription started; billing day noted
- If moving to Webstar hosting: create cPanel account under reseller; migrate (cPanel full backup or rsync); test on temp URL; Cloudflare DNS switch; verify forms/SSL
- If managing client host: collect cPanel/host login via password manager share; enable backups there; add uptime monitor
- Add site to uptime monitor and backup schedule; record PHP version and dependencies
- Add Webstar as manager on GBP / GA4 / GSC (owner stays client)
- Portal: add plan tier, start date, minutes allowance to client record

## Monthly

- Payment check (Stripe) — failed → retry → 15-day notice → pause at 30 days
- Updates: PHP minor, dependencies, security patches; smoke test after
- Backup verification (restore test quarterly)
- Edit requests: log minutes in 15-min increments; stop at allowance and quote the rest
- Standard/Premium: monthly report (GA4 + GSC + GBP) first week; GBP refresh
- Premium: quarterly strategy call scheduled; seasonal updates planned

## Cancellation

- 30-day notice logged; export (files + DB + instructions) within 10 business days
- Repoint DNS with client; confirm new host live; remove Webstar access; close cPanel account; Stripe subscription cancelled at period end
