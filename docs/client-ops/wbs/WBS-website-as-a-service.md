# Internal WBS — Website as a Service ($150/mo, 6-month minimum)

**Audience:** Webstar ops only. Do not send to clients.  
**Public version:** `/plans/`.  
**Agreement:** [WEBSITE-AS-A-SERVICE-AGREEMENT.md](../agreements/WEBSITE-AS-A-SERVICE-AGREEMENT.md) · Hosting: [HOSTING-STANDARD.md](../HOSTING-STANDARD.md)

Reuse the build WBS for the scope tier chosen (Simple / Business / Growth). This file covers what is different.

---

## 0.1 Onboarding

- 0.1.1 Scope call: pick tier, lock pages/features in the agreement fill-in (this is the contract scope — no "we'll see")
- 0.1.2 Stripe: create customer, attach payment method, start the **$150/mo subscription** with billing day = signing day; note first payment date as term start
- 0.1.3 Domain: client registers (or transfers) in their own name; Webstar added as technical contact; DNS to Cloudflare
- 0.1.4 Create cPanel account under Webstar reseller for the client domain; pin PHP; AutoSSL; JetBackup schedule
- 0.1.5 Agreement eSigned; scope summary email; portal login; ownership-transfer date (6th payment) recorded in portal notes

## 0.2 Build and launch

- 0.2.1 Follow the tier WBS (Planning → Building → Deployment → Google)
- 0.2.2 Launch on Webstar managed hosting; no balance invoice (subscription continues)
- 0.2.3 Warranty clock starts at launch; Standard Care service starts same day

## 0.3 Monthly operations (Standard Care)

- 0.3.1 Monitor Stripe: failed payment → retry → 15-day notice → suspend page at 30 days per agreement
- 0.3.2 Track edit minutes (60/mo) in the client log; 15-minute increments
- 0.3.3 Monthly report (GA4 + GSC + GBP) sent first week of month
- 0.3.4 GBP check: photos/posts/hours/Q&A
- 0.3.5 Patch/update window; verify backups restore quarterly

## 0.4 Month 6 and after

- 0.4.1 On 6th successful payment: send ownership-transfer confirmation email; store a dated export in the client Drive folder
- 0.4.2 Offer: stay at $150/mo, move to a Care Plan, or export + DNS repoint (no charge)
- 0.4.3 If cancelled: 30-day notice, export within 10 business days, remove Webstar access, close cPanel account after DNS confirms new host
- 0.4.4 Early-cancel request before month 6: invoice remaining minimum-term payments; transfer on payment
