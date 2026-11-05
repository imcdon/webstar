# Webstar shared shop module

PHP 8.1+ Square-backed ecommerce used by client sites (first: DJ's Tackle).

## Install

```bash
cd clients/shared/shop
php ../../../composer.phar install
```

`vendor/` is committed for cPanel deploys that do not run Composer.

## Client wiring

1. `includes/shop-config.php` (from example) — needs `square_application_id`, `square_token`, `square_location_id`
2. `require` `clients/shared/shop/bootstrap.php` with `$shop_config_path` set
3. Pages call `Catalog`, `Cart`, `Checkout`, etc.

**Fulfillment stays in Square** (Dashboard / app): receipts, packing, shipping, pickup. This module syncs catalog/stock, runs on-site Web Payments checkout, and site extras (reviews, cart recovery, magic-link accounts).

## Checkout (Web Payments SDK)

`Checkout::calculate()` / `Checkout::pay()` create a Square Order with auto-applied catalog taxes, shipping as a service charge, optional discount (coupon → CatalogDiscount by name), and SHIPMENT/PICKUP fulfillment. The browser tokenizes via Square.js; the server charges with the Payments API (gift card + card split uses PayOrder).

## Bin scripts

| Script | Purpose |
|--------|---------|
| `bin/seed-sandbox.php [--replace]` | Push client `seed/catalog.php` into Square (items, options, MI tax, stock). `--replace` wipes existing items/options/categories/taxes first |
| `bin/replay-webhook.php` | Replay a saved webhook JSON |
| Client `bin/seed-local.php` | Load `seed/catalog.php` into SQLite without Square (demo mode) |
| Client `bin/sync-catalog.php` | Pull Square → SQLite (items, options, images, stock) |
| Client `bin/set-inventory.php [--qty=N]` | Push stock counts to Square for every synced variation |
| Client `bin/export-products-csv.php` | Regenerate `seed/products*.csv` from `seed/catalog.php` |
| Client `bin/abandoned-carts.php` | Cron reminders |
| Client `bin/e2e-smoke.php` | Place a sandbox/demo order via `Checkout::pay` |

### Catalog definition (`seed/catalog.php`)

One entry per listing. `options` is an ordered map of option name → values; every combination
becomes a variation with its own SKU (`sku_prefix` + values) and stock (`quantity`). Price resolves
from `prices["Val1 / Val2"]`, then `price_by[Option][Value]`, then `price_cents`.

See [docs/SQUARE-PHP-REFERENCE.md](../../../docs/SQUARE-PHP-REFERENCE.md).
