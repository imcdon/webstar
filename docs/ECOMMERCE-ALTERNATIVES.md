# Free ecommerce alternatives to Ecwid (Lightspeed)

Researched Nov 4, 2026. Verify prices on the vendor pages before quoting a client.

## Why look

Ecwid (owned by Lightspeed since 2021) **no longer has a free plan**. The "Free Forever" tier was replaced in March 2025 by **Starter at $5/mo**: 10 products, 2 categories, no custom domain, no CSV export, no REST API. Paid tiers rose again on March 2, 2026: Venture $35/mo, Business $65/mo, Unlimited $149/mo ([Ecwid help center](https://support.ecwid.com/hc/en-us/articles/25122701806108-Changes-to-the-Ecwid-plan-pricing-after-March-2-2026)). Square and Clover POS sync are on Unlimited only.

The Ecwid use case we need to replace: **drop a store or cart into a site we already built** (custom PHP), without moving the client to a new site builder.

## What "free" really means

Nothing removes card processing (about 2.9% + 30¢ in the US). "Free" below means **$0/month platform fee**. Some options add a per-sale platform fee on top of processing.

## Options

### Embed into an existing custom site (closest to Ecwid)

| Option | Monthly | Per sale | Catalog | Fit |
|--------|---------|----------|---------|-----|
| **Square Payment Links / Buy buttons** | $0 | 3.3% + 30¢ (Square Free) | One button per item; no browsable cart | Few products, services, deposits, gift cards; best if client already uses Square POS |
| **Stripe Checkout + our own PHP cart** | $0 | 2.9% + 30¢ | Unlimited (we build catalog + cart) | Best long-term Webstar fit: our design, our SEO, no platform fee |
| **Stripe Payment Links** | $0 | 2.9% + 30¢ | One link per product or fixed bundle (up to 20 items) | Simplest no-code buy buttons |
| **Payhip Free** | $0 | **5%** + processing | Unlimited, embeddable cart | Digital goods; physical works but 5% adds up |
| Snipcart | $20 min under $1k sales/mo, else 2% | + processing | HTML attributes on our pages | Not free; nice overlay cart |
| Foxy Starter | $30 (100 orders) | 1% capped at 35¢ | Unlimited | Not free; strong for subscriptions |
| Shopify Starter (buy button) | $5 | 5% | Buy buttons only | Not free |

### Hosted free stores (client gets a separate store site)

| Option | Monthly | Limits |
|--------|---------|--------|
| **Square Online Free** | $0 | Unlimited products, Square branding, square.site address on free; pickup/delivery; syncs with Square POS |
| **Shift4Shop End-to-End** | $0 | US only, must use Shift4 Payments; unlimited products |
| **Big Cartel Gold** | $0 | 5 products, 1 image each |

### Self-hosted open source (we host it)

| Option | Notes |
|--------|-------|
| **OpenCart** | Plain PHP/MySQL, runs on XAMPP-style hosting, web installer, GPL. Simplest standalone store. |
| **WooCommerce** | Free plugin, but requires WordPress. Only if the client is on WordPress. |
| PrestaShop / Bagisto / Sylius | Heavier; Laravel or Symfony. Overkill for small local clients. |

Self-hosting is free in license only: we own updates, security patches, backups, and PCI scope stays small only if checkout is Stripe/Square-hosted.

## Recommendation for Webstar clients

1. **Default: Stripe Checkout with a small PHP cart we build once and reuse.** $0/month, standard processing only, unlimited products, lives on the client's own domain (good for SEO), checkout is Stripe-hosted (no card data on our server). We need a product list (JSON or the existing CMS), a session cart, one endpoint that creates a Checkout Session, and a `checkout.session.completed` webhook to email the owner and decrement stock.
2. **Client already on Square POS:** use **Square Payment Links / buy buttons** on the site (or Square Online Free if they want a full catalog with inventory sync). Higher online rate (3.3% + 30¢) but inventory and payouts stay in Square.
3. **Under about 5 products or services:** plain **Stripe Payment Links** (no code) are enough.
4. **Digital-only sellers:** Payhip Free is the fastest, but the 5% fee costs more than Stripe once sales pass a few hundred dollars a month.

Avoid for "free" positioning: Ecwid (paid now), Snipcart ($20 floor), Foxy ($30), Shopify Starter (5% fee).

## Client fit: Rip Dip Fishing and DJ's Tackle

Both are new Webstar projects, checked Nov 4, 2026. Both currently run on **Ecwid Instant Site** (`generator: ec-instant-site`) and are related: DJ's sources some plastics from Rip Dip.

| | [ripdipfishing.com](https://ripdipfishing.com) | [djstackle.com](https://djstackle.com) |
|--|--|--|
| Business | Custom soft plastics, Commerce Twp, MI; also sold at Lakes Area Bait Shop (Wolverine Lake) | Walleye jigs, stinger hooks, bottom bouncers, spinners, "River Ready" boxes; Sterling Heights, MI |
| Catalog | ~22 baits ($5.75–$6.25), gift card, hoodie, "Rip Dip Custom" | ~19 products ($2–$100), options by weight (1/2 oz to 8 oz) |
| Features in use | Color variants, sale price ("was $6.25"), gift card, custom orders | Weight variants, product reviews, custom colors on request |
| Contact | 248-904-9289, Gmail | 810-627-9974, Gmail |
| Likely Ecwid cost now | 10+ products means at least Venture ($35/mo); variant inventory needs Business ($65/mo) | Same |

### What both need from a replacement

- Product options (color for Rip Dip, weight for DJ's) with per-option price and stock.
- Light, cheap shipping: flat rate (e.g. USPS Ground Advantage) plus free-over threshold; local pickup.
- Michigan sales tax (6%) on Michigan orders.
- An easy way for the owner to add colors and mark items sold out.
- Nice to have: gift cards (Rip Dip), reviews (DJ's), discount codes.

### Recommendation

**One shared Webstar shop module (PHP + Stripe Checkout), deployed on both sites.** Built once, used twice, $0/month each (saves roughly $420–$780 per client per year versus Ecwid Venture or Business).

- Catalog and options in our CMS (JSON or SQLite), with an owner admin page for products, options, stock and sold-out toggles.
- Session cart on our pages; checkout is Stripe-hosted (cards, Apple Pay, Google Pay; no card data on our server).
- Shipping: Stripe Checkout supports up to 5 fixed shipping options (flat, free over $X, local pickup). Rates can't change by address on the hosted page, which is fine for small tackle packages.
- Tax: Stripe manual tax rates (free) for 6% Michigan, or Stripe Tax (+0.5%) if they start selling heavily out of state.
- Fulfillment via the `checkout.session.completed` webhook: email the owner, decrement stock, log the order.
- Discount codes: Stripe promotion codes (free).
- Gaps to plan for: gift cards (Stripe has no native gift cards; issue one-time promotion codes as a workaround) and reviews (build simple reviews or use Google reviews links).

**Fallback if the owners want a no-code admin and accept a hosted store:** Square Online Free (unlimited products, variants, inventory, pickup; 3.3% + 30¢ per online sale, Square branding, square.site address unless they pay). Weaker fit for a custom Webstar design and SEO.

### Less-custom option: Shopify Basic

If building our own cart and admin is too much, Shopify Basic does the store work and our site just shows it.

- **Cost:** $39/mo, or $29/mo billed yearly, per store. Shopify Payments 2.9% + 30¢ with no extra transaction fee (2% surcharge if another processor is used). Starter ($5) is closed to new stores.
- **API:** the Storefront API and Headless channel are free with Basic and up; no per-call charge.
- **Included:** variants (up to 3 option types per product, enough for color or weight), per-variant inventory, gift cards, discount codes, shipping rates and labels, sales tax, abandoned-checkout emails, order admin and a phone app for the owners. Reviews through a free Shopify app.
- **Ways to connect to a Webstar site:**
  1. **Buy Button** (no code): paste embed code per product or collection; has a cart. Apple Pay isn't offered through Buy Button and styling is limited.
  2. **Storefront API** (moderate): our PHP pages pull products from Shopify, add to a Shopify cart, then hand off to Shopify checkout. Our design and SEO, Shopify's admin and inventory.
  3. **Full Shopify theme**: the whole site moves to Shopify. Least code, least design control.
- **Migration:** Ecwid CSV maps to Shopify's product CSV (options become Option1–3, variants grouped by handle; fill blank variant prices first or they import as $0). The Vostari "CSV Converter: Ecwid" Shopify app does the mapping and builds collections from categories.
- **Versus Ecwid:** about the same as Venture ($35) and cheaper than Business ($65), which Ecwid requires for per-variant stock.

### Square vs Stripe for these two sites

**Square is less work here**, because it already has the catalog pieces Stripe lacks.

| | Square | Stripe |
|--|--|--|
| Products with options (color, weight) | Yes: items with variations in the free Square Dashboard | No: we build it |
| Per-option stock, auto-decrement on sale | Yes (Inventory API) | No: we build it |
| Owner admin and phone app | Yes, free | No: we build it |
| Gift cards | Yes (Square eGift cards) | No |
| In-person sales (shows, bait shop) | Yes, same catalog and stock | Separate setup |
| Our site's job | Read catalog via Catalog API, show pages, send cart to Square checkout | Catalog, cart, admin, stock, webhooks |
| Monthly cost | $0 (Square Free) | $0 |
| Online card fee | 3.3% + 30¢ (Square Free online / checkout links); 2.9% + 30¢ via Payments API | 2.9% + 30¢ |
| Developer experience | Good PHP SDK; sandbox | Best-in-class docs and SDK |

Ecwid's CSV can be reshaped into Square's item-library import spreadsheet (one row per variation).

### Migrating the catalog off Ecwid

Ecwid store IDs: Rip Dip `84739518`, DJ's Tackle `100207139`.

- Export is on Venture and up. Both stores list more than 10 products, so neither is on Starter and both can export.
- Owner exports from **Catalog → Data Import & Export → Export Catalog**. One CSV includes products, categories, prices, sale prices, SKUs, stock, weights, descriptions, options (`product_option_*` rows, e.g. color or weight) and variations (`product_variation` rows with their own price, SKU and stock).
- Stores created before May 24, 2023 may still have the **legacy** export, which skips variations. Switch in **Settings → What's New** (or ask Ecwid support) before exporting. Rip Dip's older store ID suggests it may be legacy.
- Images come as URLs, not files. Our import script downloads every image (main, gallery, per-variation) **before** the Ecwid subscription is cancelled.
- Not in the catalog export: product reviews (DJ's), outstanding gift card balances (Rip Dip), customer list and order history. Export customers and orders separately, copy reviews, and carry gift card balances over as one-time Stripe promotion codes.
- Cancel Ecwid only after the new shop is live and checked against the export.

### Open questions for the owners

- Who updates products and stock, and how often?
- Do they already have a Stripe or Square account (e.g. for shows or the bait shop)?
- How many gift cards and reviews do they actually use? Are they must-haves?
- Current shipping rules and carrier.
- Any wholesale pricing between DJ's and Rip Dip, or for Lakes Area Bait Shop?

## Sources

- [Ecwid pricing change, March 2026](https://support.ecwid.com/hc/en-us/articles/25122701806108-Changes-to-the-Ecwid-plan-pricing-after-March-2-2026)
- [Square buy button](https://squareup.com/us/en/payment-links/buy-button), [Square fees](https://squareup.com/us/en/payments/our-fees), [Square Online](https://squareup.com/us/en/online-store)
- [Stripe Payment Links](https://stripe.com/resources/more/payment-links)
- [Payhip pricing](https://payhip.com/sell-widgets)
- [Big Cartel plans](https://www.bigcartel.com/resources/help/article/unlock-premium-features)
- [Foxy pricing](https://www.foxy.io/pricing)
- [OpenCart vs Bagisto vs Drupal Commerce](https://repoly.dev/guides/bagisto-vs-opencart-vs-drupal-commerce/)
