# Client ops workflow — agreements, Drive, eSignature

Source templates live in this repo. Day-to-day editing and signing happen in **Google Drive / Docs**. Do not commit signed client PDFs to git.

## Gate before production

Do **not** start build work until:

1. Agreement accepted (eSignature **or** clear written “I agree” email with PDF attached), and  
2. Required deposit / full payment received (Stripe), and  
3. Enough intake assets to begin (logo + basics).

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
| `docs/client-ops/agreements/*.md` | Paste into Google Docs under `Templates/Agreements/` |
| `docs/client-ops/assets/letterhead-webstar.pdf` | `Templates/Letterhead/` |
| `docs/client-ops/email-signature.html` | Keep local + paste into Gmail; optional copy in `Templates/Email signature/` |
| `docs/client-ops/BRAND.md` | Optional Doc for reference |

---

## Per-client checklist

1. **Intake received** (package form or email) — note `{INTAKE_DATE}`.  
2. **Pick annex** matching package: marketing / simple-website / business-marketing / custom-growth.  
3. **Duplicate** Google Doc from master Service Agreement template.  
4. **Paste annex** content into the Doc (or append as “Exhibit A”).  
5. **Replace all placeholders** (`{CLIENT_LEGAL_NAME}`, `{PRICE}`, etc.). See MASTER for the list.  
6. **Optional:** Apply letterhead header/footer per [LETTERHEAD.md](../LETTERHEAD.md).  
7. **Send for signature** (see below).  
8. **Send Stripe invoice** for `{DEPOSIT}` (or full amount).  
9. **File** signed PDF + payment confirmation under `Clients/{ClientName}/`.  
10. **Send** [SCOPE-SUMMARY](../SCOPE-SUMMARY.md) email confirming next steps.  
11. **Start** production queue / portal project.

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
2. Email from `info@webstarbusinessservices.com` with SCOPE-SUMMARY + PDF.  
3. Ask client to reply: **“I agree to the Service Agreement dated {EFFECTIVE_DATE}.”**  
4. Save the email PDF/print and the agreement PDF in the client folder.

Use Option B for tiny jobs if eSignature is unavailable; prefer Option A for $200+ website work.

---

## Stripe (payment)

- Brand Stripe with logo/colors separately (Dashboard branding).  
- Invoice language should match `{PRICE}` / `{DEPOSIT}` / `{BALANCE}` in the agreement.  
- Payouts → business checking (not personal).

---

## Package → annex map

| Site package | Annex file |
|--------------|------------|
| Marketing ($50) | `ANNEX-marketing.md` |
| Simple Website ($200) | `ANNEX-simple-website.md` |
| Business Website + Marketing ($500–$1,000) | `ANNEX-business-marketing.md` |
| Custom Growth ($1,000+) | `ANNEX-custom-growth.md` |

---

## Related

- [BRAND.md](../BRAND.md) — NAP, colors, logos  
- [SERVICE-AGREEMENT-MASTER.md](SERVICE-AGREEMENT-MASTER.md)  
- [EMAIL-SIGNATURE.md](../EMAIL-SIGNATURE.md)  
- [LETTERHEAD.md](../LETTERHEAD.md)
