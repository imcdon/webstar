# DJ's Tackle — deploy

**Local:** http://localhost/webstar-business-solutions/clients/djstackle/  
**Staging (Path A):** https://webstarbusinessservices.com/clients/djstackle/  
**Live:** https://djstackle.com (document root = this folder or a synced copy)

## One-time setup

1. Copy `includes/shop-config.example.php` → `includes/shop-config.php` (gitignored).
2. Create a [Square Developer](https://developer.squareup.com/) app:
   - **Application ID** → `square_application_id` (required for on-site Web Payments SDK)
   - Access token → `square_token`
   - Sandbox location ID → `square_location_id`
   - Set `square_environment` to `sandbox` while testing
3. Seed catalog:
   - **Without Square yet:** `php bin/seed-local.php` (loads `seed/catalog.php`)
   - **With Square:** `php ../shared/shop/bin/seed-sandbox.php --replace` then `php bin/sync-catalog.php`  
     Creates items, Size/Color options, Michigan 6% sales tax, and stock.  
     Once the owner manages items in the Square Dashboard, stop running the seed — just `sync-catalog.php`.
4. Webhooks (sandbox or production): subscribe to  
   `payment.updated`, `inventory.count.updated`, `catalog.version.updated`  
   Notification URL must match `square_webhook_notification_url` exactly. Paste signature key into config.
5. **SMTP in `shop-config.php`** — required for real order emails (customer confirmation + owner alert).  
   Square’s Payments API does **not** email receipts (Payment Links did). Without SMTP, messages are written to `data/mail.log` only.
6. cPanel cron (hourly):  
   `php $HOME/.../clients/djstackle/bin/abandoned-carts.php`
7. Gift cards: enable Square Gift Cards in the Dashboard (selling + accepting). Purchase URL (optional) → `gift_card_purchase_url`.
8. **Apple Pay (production / HTTPS only):**  
   Developer Console → app → Apple Pay → add `djstackle.com` → download the association file → save as  
   `clients/djstackle/.well-known/apple-developer-merchantid-domain-association` (no extension).  
   Cannot be tested on localhost HTTP.

## Checkout

Checkout is **on-site** (no Square redirect). Card, Apple Pay, Google Pay, Cash App Pay, and gift cards via the Web Payments SDK. Orders still fulfill in the Square Dashboard.

**Coupons:** create a Discount in Square Dashboard → Items → Discounts. The discount **name** is the coupon code buyers enter (case-insensitive).

## Deploy via Webstar

1. Push to `main` on the Webstar repo.
2. cPanel → Git → Update + Deploy (`.cpanel.yml` creates `clients/djstackle`).
3. On the server, ensure `includes/shop-config.php` exists (never deploy secrets from git).
4. Staging is HTTP Basic gated via Client Portal credentials; `djstackle.com` is public.

## Production cutover

1. Switch `square_environment` to `production` and use production Application ID / token / location / webhook key.
2. Point `site_url` and webhook URL to `https://djstackle.com`.
3. Re-register webhooks for production.
4. Register Apple Pay domain + deploy the `.well-known` association file.
5. Change admin password hash.
6. Run [SECURITY-CHECKLIST.md](../SECURITY-CHECKLIST.md).

## Sandbox test values

| Method | Value |
|--------|--------|
| Card | `4111 1111 1111 1111`, CVV `111`, any future expiry, any ZIP |
| Card (API token) | `cnon:card-nonce-ok` |
| Card declined | `cnon:card-nonce-declined` |
| Gift card | `7783 3200 0000 0000` (or token `cnon:gift-card-nonce-ok`) |
| Cash App (API) | `wnon:cash-app-ok` |

`php bin/e2e-smoke.php` places a real sandbox charge with `cnon:card-nonce-ok`.

## Orders & fulfillment

**Square Dashboard is the system of record** for payments, receipts, packing, shipping labels, and pickup. This site does not run a fulfillment admin — only catalog sync, reviews, cart recovery, and on-site checkout.

## Demo mode

If `square_token` is `REPLACE_ME`, checkout creates a local demo payment (no Square charge). Coupon `DEMO10` applies 10% off in demo mode.
