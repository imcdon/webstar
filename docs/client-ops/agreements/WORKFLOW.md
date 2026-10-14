# Client ops workflow — agreements, Drive, eSignature

Source templates live in this repo. Day-to-day editing and signing happen in **Google Drive / Docs**. Do not commit signed client PDFs to git.

## Gate before production

Do **not** start build work until:

1. Agreement accepted (eSignature **or** clear written “I agree” email with PDF attached), and  
2. **First payment** received (50% deposit) **or** paid in full (Stripe), and  
3. Enough intake assets to begin (logo + basics).

**Before deploy / final handoff:** second payment (remaining 50%) must clear, unless the client paid in full at signing. Keep card on file until the full invoice amount is paid (see Master §5).

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
| `docs/client-ops/agreements/SERVICE-AGREEMENT-MASTER.md` + `ANNEX-*.md` | Optional if using Master + Annex instead of standalone CONTRACT |
| `docs/client-ops/assets/letterhead-webstar.pdf` | `Templates/Letterhead/` |
| `docs/client-ops/email-signature.html` | Keep local + paste into Gmail; optional copy in `Templates/Email signature/` |
| `docs/client-ops/BRAND.md` | Optional Doc for reference |

---

## Per-client checklist

1. **Intake received** (package form or email) — note `{INTAKE_DATE}`.  
2. **Pick contract** matching package (see map below). Prefer standalone `CONTRACT-*.md`.  
3. **Duplicate** Google Doc from that template.  
4. **Replace all placeholders** and fill project blanks (`{CLIENT_LEGAL_NAME}`, `{PRICE}`, payment plan checkbox, etc.).  
5. **Optional:** Apply letterhead header/footer per [LETTERHEAD.md](../LETTERHEAD.md).  
6. **Send for signature** (see below).  
7. **Send Stripe invoice** for `{DEPOSIT}` (or full amount). Confirm **card on file** until paid in full.  
8. **File** signed PDF + payment confirmation under `Clients/{ClientName}/`.  
9. **Send** [SCOPE-SUMMARY](../SCOPE-SUMMARY.md) email confirming next steps.  
10. **Start** production / portal project.  
11. At **build complete**, invoice `{BALANCE}` (if 50/50), then **deploy / handoff** after it clears.

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

Use Option B for tiny jobs if eSignature is unavailable; prefer Option A for $200+ website work.

---

## Stripe (payment)

- Brand Stripe with logo/colors separately (Dashboard branding).  
- Invoice language should match `{PRICE}` / `{DEPOSIT}` / `{BALANCE}` and the chosen payment plan.  
- Keep payment method on file until the project is paid in full.  
- Payouts → business checking (not personal).

---

## Package → contract map

| Site package | Standalone contract (preferred) | Annex (optional) |
|--------------|----------------------------------|------------------|
| Marketing ($50) | `CONTRACT-marketing.md` | `ANNEX-marketing.md` |
| Simple Website ($200) | `CONTRACT-simple-website.md` | `ANNEX-simple-website.md` |
| Business Website + Marketing ($500–$1,000) | `CONTRACT-business-website-marketing.md` | `ANNEX-business-marketing.md` |
| Custom Growth ($1,000+) | `CONTRACT-custom-growth.md` | `ANNEX-custom-growth.md` |
| Get Me On Google ($200) | *(no CONTRACT yet)* | `ANNEX-get-me-on-google.md` + Master |

---

## Related

- [BRAND.md](../BRAND.md) — NAP, colors, logos  
- [SERVICE-AGREEMENT-MASTER.md](SERVICE-AGREEMENT-MASTER.md)  
- [EMAIL-SIGNATURE.md](../EMAIL-SIGNATURE.md)  
- [LETTERHEAD.md](../LETTERHEAD.md)
