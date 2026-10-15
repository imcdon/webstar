# Legitimacy checklist (owner)

Business-side items that make Webstar Business Services LLC look and operate like an established agency. Work top to bottom; tick as done. Nothing here lives on the website except where noted.

## Entity and money

- [ ] **Michigan LLC** in good standing; file the **annual statement** with LARA each year (due Feb 15; set a calendar reminder).
- [ ] **EIN** on file; W-9 ready to send clients who ask.
- [ ] **Business checking** in the LLC's name; no personal account for client payments.
- [ ] **Stripe** account in the LLC's name, payouts to business checking, branding set (logo, colors, statement descriptor `WEBSTAR BUS SVCS`).
- [ ] Stripe **Products**: Simple Website, Business Website, Business Growth Website (one-time, variable amount); **Website as a Service** $150/mo; **Care Plan Essential / Standard / Premium** $49 / $99 / $179 per month. Smart Retries + failed-payment emails on.
- [ ] Sales tax review: Michigan does not tax web design services as of this writing; confirm annually with an accountant.
- [ ] Bookkeeping system (Wave / QuickBooks) connected to Stripe and the bank.

## Insurance and legal

- [ ] **General liability** policy (≈ $1M/$2M).
- [ ] **Professional liability / E&O** policy (≈ $500k–$1M) — clients increasingly ask; Master §11 caps liability accordingly.
- [ ] Agreements in use: Master + CONTRACT-* / WaaS / Care Plan ([agreements/WORKFLOW.md](agreements/WORKFLOW.md)). Have an attorney review once; re-review when prices or terms change.
- [ ] Public **Privacy**, **Terms**, **Accessibility** pages live and linked in the footer (done — `/privacy/`, `/terms/`, `/accessibility/`).
- [ ] Registered agent and principal office address current with LARA.

## Address and identity

- [ ] **Mailing address** for invoices and contracts (virtual office or registered agent address is fine; avoid home address on public pages).
- [ ] Google Business Profile for Webstar: service-area business, hours, services list matching the three tiers + plans, logo, cover photo, booking/contact link.
- [ ] Consistent NAP (name, address, phone) across GBP, website footer, Stripe, invoices, email signature ([BRAND.md](BRAND.md)).
- [ ] Phone: business line or Google Voice number with voicemail greeting that names the company.

## Email and domain

- [ ] Google Workspace on `webstarbusinessservices.com`; all client mail from `ian@webstarbusinessservices.com` (no Gmail/Hotmail).
- [ ] **SPF, DKIM, DMARC** published and passing ([GOOGLE-WORKSPACE-DNS.md](GOOGLE-WORKSPACE-DNS.md)); DMARC at `p=quarantine` after a clean month of reports.
- [ ] Email signature deployed ([EMAIL-SIGNATURE.md](EMAIL-SIGNATURE.md)).
- [ ] Domain auto-renew on; registrar and Cloudflare accounts have 2FA.

## Proof and reputation

- [ ] **Google reviews:** ask every client at project close and at the first check-in; link is in the footer/testimonials block once `google_review_url` is set in `library/site-config.php`.
- [ ] Add each approved client quote to `library/testimonials-data.php` (quote, name, role, business, URL).
- [ ] Examples page shows live links and a one-line outcome for every launched site.
- [ ] Case-study note (problem → build → result) kept per client in Drive for later use.
- [ ] LinkedIn company page and personal profile list the company and the three services.

## Operations

- [ ] Password manager with a vault per client; no credentials in email, repo, or portal notes.
- [ ] Uptime monitoring on every Path B site; backup restore tested quarterly ([HOSTING-STANDARD.md](HOSTING-STANDARD.md)).
- [ ] Weekly status email during active projects; 7–14 day post-launch check-in; Care Plan offered once.
- [ ] Client portal credentials changed after each handoff.
- [ ] Review pricing ranges and Care Plan placeholders ($49 / $99 / $179) after the first five paying plan clients.
