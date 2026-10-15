# Client ops workflow — agreements, Drive, eSignature

Source templates live in this repo. Day-to-day editing and signing happen in **Google Drive / Docs**. Do not commit signed client PDFs to git.

## Gate before production

Do **not** start build work until:

1. Agreement accepted (eSignature **or** clear written “I agree” email with PDF attached), and  
2. **First payment** received — 50% deposit, pay-in-full, first milestone (Growth), or the **first $150 subscription payment** (Website as a Service) — via Stripe, and  
3. Enough intake assets to begin (logo + basics), and  
4. Exact `{PRICE}` written in the agreement (published prices are ranges).

**Before launch / final handoff:** second payment (remaining 50%) or final milestone must clear, unless the client paid in full at signing. Website as a Service launches on approval with the subscription running. Keep card on file until the full invoice amount is paid (see Master §5).

**After launch:** 30-day warranty clock starts (Master §8). Complete Google services (GBP, GA4, GSC) under the client’s Google account and send the first check-in.

---

## Google Drive folder structure

Create once under your Workspace Drive:

```text
Webstar/
  Client ops/
    Templates/          ← masters (Docs + letterhead PDF + signature HTML)
      Letterhead/
      Agreements/
      Email signature/
    Clients/
      {ClientName}/     ← duplicate agreement, signed PDF, Stripe notes
        Agreement/
        Invoices/
        Assets/
```

Copy from repo:

| Repo path | Drive destination |
|-----------|-------------------|
| `docs/client-ops/agreements/CONTRACT-*.md` | Paste into Google Docs under `Templates/Agreements/` (preferred) |
| `docs/client-ops/agreements/WEBSITE-AS-A-SERVICE-AGREEMENT.md`, `CARE-PLAN-AGREEMENT.md` | Monthly plan agreements → `Templates/Agreements/` |
| `docs/client-ops/agreements/SERVICE-AGREEMENT-MASTER.md` + `ANNEX-*.md` | Optional if using Master + Annex instead of standalone CONTRACT |
| `docs/client-ops/assets/letterhead-webstar.pdf` | `Templates/Letterhead/` |
| `docs/client-ops/email-signature.html` | Keep local + paste into Gmail; optional copy in `Templates/Email signature/` |
| `docs/client-ops/BRAND.md` | Optional Doc for reference |

---

## Per-client checklist

1. **Intake received** (package form, `/plans/` form, or email) — note `{INTAKE_DATE}`.  
2. **Scope call** (20–30 min): confirm tier or Website as a Service, pages, booking tool, hosting path (client cPanel vs Webstar managed), client Google account. Set exact `{PRICE}`.  
3. **Pick contract** (see map below). Prefer standalone `CONTRACT-*.md`; monthly plans use their own agreement.  
4. **Duplicate** Google Doc from that template.  
5. **Replace all placeholders** and fill project blanks (`{CLIENT_LEGAL_NAME}`, `{PRICE}`, payment plan and hosting checkboxes, page list, etc.).  
6. **Optional:** Apply letterhead header/footer per [LETTERHEAD.md](../LETTERHEAD.md).  
7. **Send for signature** (see below).  
8. **Stripe:** invoice `{DEPOSIT}` (or full amount), or start the **subscription** (WaaS $150/mo · Care Plan tier). Confirm **card on file** until paid in full.  
9. **File** signed PDF + payment confirmation under `Clients/{ClientName}/`.  
10. **Send** [SCOPE-SUMMARY](../SCOPE-SUMMARY.md) email confirming next steps; issue portal login.  
11. **Start** production per the package WBS; weekly status update while active.  
12. At **build complete**, invoice `{BALANCE}` (if 50/50) or final milestone, then **launch / handoff** after it clears. WaaS: launch on approval.  
13. **Post-launch:** Google services completed under client account; 30-day warranty; first check-in at 7–14 days; mention Care Plans once; request a Google review.  
14. **Handoff:** credentials via password manager share; final backup archived to client Drive folder; Webstar access removed or downgraded to manager per [HOSTING-STANDARD.md](../HOSTING-STANDARD.md).

---

## How to send for signature

### Option A — Google Workspace eSignature (preferred when available)

Eligible on Business Standard/Plus, Enterprise, Workspace Individual, etc. (often **not** on Business Starter). Admin: Apps → Google Workspace → Drive and Docs → **eSignature**.

**From a Google Doc:**

1. Open the filled agreement Doc.  
2. **Tools → eSignature**.  
3. Add signer (client email); place signature / date / name fields.  
4. **Request signature**.  
5. Track status in Drive; download final signed PDF to `Clients/{ClientName}/Agreement/`.

**From a PDF** (letterhead export):

1. Upload PDF to Drive.  
2. Open PDF → ⋮ menu → **eSignature** (wording may vary).  
3. Same field + request flow.

Help: [Send signature requests with eSignature](https://support.google.com/docs/answer/12315692).

### Option B — Email acceptance (fallback)

1. Export Doc as PDF (on letterhead if possible).  
2. Email from `ian@webstarbusinessservices.com` with SCOPE-SUMMARY + PDF.  
3. Ask client to reply: **“I agree to the Service Agreement dated {EFFECTIVE_DATE}.”**  
4. Save the email PDF/print and the agreement PDF in the client folder.

Prefer Option A (eSignature) for all website and subscription work; Option B is the fallback.

---

## Stripe (payment)

- Brand Stripe with logo/colors separately (Dashboard branding).  
- **One-time packages:** invoices matching `{PRICE}` / `{DEPOSIT}` / `{BALANCE}` and the chosen payment plan.  
- **Subscriptions:** Products → "Website as a Service" ($150/mo) and "Care Plan — Essential / Standard / Premium" ($49 / $99 / $179). Start the subscription on the signing day; enable Smart Retries and failed-payment emails; note the 6th-payment date for WaaS ownership transfer.  
- Keep payment method on file until the project is paid in full (subscriptions keep it by default).  
- Payouts → business checking in the LLC’s name (not personal).

---

## Offer → agreement map

| Offer | Agreement (preferred) | Annex (optional) | WBS |
|-------|-----------------------|------------------|-----|
| Simple Website ($350–$500) | `CONTRACT-simple-website.md` | `ANNEX-simple-website.md` | `wbs/WBS-simple-website.md` |
| Business Website ($900–$1,800) | `CONTRACT-business-website.md` | `ANNEX-business-website.md` | `wbs/WBS-business-website.md` |
| Business Growth Website ($2,500+) | `CONTRACT-business-growth-website.md` | `ANNEX-business-growth-website.md` | `wbs/WBS-business-growth-website.md` |
| Website as a Service ($150/mo, 6-mo min) | `WEBSITE-AS-A-SERVICE-AGREEMENT.md` | — | `wbs/WBS-website-as-a-service.md` + tier WBS |
| Care Plan ($49 / $99 / $179 per mo) | `CARE-PLAN-AGREEMENT.md` | — | `wbs/WBS-care-plan.md` |

Every website tier includes Google services (GBP, GA4, Search Console). Retired offers (Marketing Package, Get Me On Google) are archived under `agreements/archive/` and `wbs/archive/` for reference only.

---

## Related

- [BRAND.md](../BRAND.md) — NAP, colors, logos  
- [SERVICE-AGREEMENT-MASTER.md](SERVICE-AGREEMENT-MASTER.md)  
- [EMAIL-SIGNATURE.md](../EMAIL-SIGNATURE.md)  
- [LETTERHEAD.md](../LETTERHEAD.md)
