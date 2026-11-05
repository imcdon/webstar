# Square PHP reference (Rip Dip Fishing + DJ's Tackle shops)

Pulled Nov 4, 2026 from Square's GitHub repos, cloned locally to `%USERPROFILE%\square-reference\`:

- [square/square-php-sdk](https://github.com/square/square-php-sdk): **v47.0.0.20260916**, PHP ^8.1. Full method reference is `square-php-sdk/reference.md`.
- [square/connect-api-examples](https://github.com/square/connect-api-examples), `connect-examples/v2/php_payment`: card-form payment sample.

## Read this first: the official PHP example is out of date

`php_payment` pins `square/square` **18.0.0 (2022)** and uses the old interface (`$client->getPaymentsApi()`, `Square\Models\...`, `$response->isSuccess()`). SDK v41+ replaced it. The old classes still ship under `Square\Legacy\...`, but new code should use the current interface below. Use the example only to understand the flow, not to copy code.

It also shows the wrong approach for us: it builds an on-page card form (Web Payments SDK + Payments API) and charges a hard-coded $1.00. For our shops, **Square-hosted checkout via payment links** is simpler: no card form on our pages, Apple Pay / Google Pay / Cash App Pay built in, and Square creates the order and decrements stock.

## Local environment

| Need | Status |
|------|--------|
| PHP ^8.1 | XAMPP has **PHP 8.2.12** |
| `curl`, `json`, `openssl`, `mbstring` | Enabled |
| Composer | **Not installed** — install from [getcomposer.org](https://getcomposer.org/download/), then `composer require square/square` |

## Credentials (Square Developer Dashboard → your app)

| Variable | Where |
|----------|-------|
| `SQUARE_TOKEN` | Credentials tab, access token (sandbox and production are different) |
| `SQUARE_ENVIRONMENT` | `sandbox` while building, `production` at launch |
| `SQUARE_LOCATION_ID` | Locations tab |
| `SQUARE_WEBHOOK_SIGNATURE_KEY` | Webhooks → subscription → signature key |

Each shop is its own Square seller account, so each site gets its own set. Keep them in a gitignored `.env`; the SDK reads `SQUARE_TOKEN` automatically if no token is passed.

## Client setup

```php
use Square\SquareClient;
use Square\Environments;

$square = new SquareClient(
    token: getenv('SQUARE_TOKEN'),
    options: [
        'baseUrl' => getenv('SQUARE_ENVIRONMENT') === 'production'
            ? Environments::Production->value
            : Environments::Sandbox->value,
    ],
);
```

Errors throw `Square\Exceptions\SquareApiException` (`getMessage()`, `getCode()`, `getBody()`). List endpoints return an iterator that pages automatically.

## How the shop maps to Square

| Shop step | Square API | SDK call |
|-----------|-----------|----------|
| Product grid / product page | Catalog | `$square->catalog->list(...)` or `catalog->searchItems(...)` |
| Color / weight choices | Catalog item variations | Nested in each `ITEM` (`itemData->variations`); `CatalogItemVariation` has `name`, `sku`, `priceMoney`, `trackInventory`, `itemOptionValues`, `imageIds` |
| Product photos | Catalog images | `ITEM` / variation `imageIds` → `IMAGE` objects (`catalog->batchGet`) |
| "In stock" / "Sold out" | Inventory | `$square->inventory->batchGetCounts(...)` (`InventoryCount->quantity`, `state` = `IN_STOCK`) |
| Checkout | Checkout (payment links) | `$square->checkout->paymentLinks->create(...)` with an `Order` |
| Stock decrement + order record | Orders + Inventory | Automatic when line items reference `catalogObjectId` |
| Order notifications | Webhooks | `Square\Utils\WebhooksHelper::verifySignature(...)` |
| Gift cards (Rip Dip) | Gift Cards | Sold from the Square Dashboard (eGift cards); `$square->giftCards->...` if needed |

## 1. Load the catalog

```php
use Square\Catalog\Requests\ListCatalogRequest;

$items = $square->catalog->list(new ListCatalogRequest([
    'types' => 'ITEM,IMAGE,CATEGORY',
]));

foreach ($items as $object) {
    if (!$object->isItem()) continue;
    $item = $object->asItem()->getItemData();
    // $item->getName(), $item->getDescriptionHtml(), $item->getVariations()
}
```

`CatalogObject` is a union type in v47: check with `isItem()` / `isImage()` etc., then unwrap with `asItem()` / `asImage()`.

Cache the result (file or SQLite) and refresh on a schedule or from a `catalog.version.updated` webhook — don't call Square on every page view. Page size is 100; the iterator handles cursors.

## 2. Stock for variations

```php
use Square\Types\BatchGetInventoryCountsRequest;

$counts = $square->inventory->batchGetCounts(new BatchGetInventoryCountsRequest([
    'catalogObjectIds' => $variationIds,
    'locationIds'      => [getenv('SQUARE_LOCATION_ID')],
]));
// each: getCatalogObjectId(), getState() === 'IN_STOCK', getQuantity() (string)
```

Only variations with `trackInventory` on have counts. Owners set stock per color/weight in the Square Dashboard or app.

## 3. Checkout from our cart

```php
use Square\Checkout\PaymentLinks\Requests\CreatePaymentLinkRequest;
use Square\Types\Order;
use Square\Types\OrderLineItem;
use Square\Types\CheckoutOptions;
use Square\Types\ShippingFee;
use Square\Types\Money;
use Square\Types\Currency;

$lineItems = array_map(fn ($row) => new OrderLineItem([
    'catalogObjectId' => $row['variation_id'], // Square price/name come from the catalog, not the browser
    'quantity'        => (string) $row['qty'],  // must be a string
]), $cart);

$response = $square->checkout->paymentLinks->create(new CreatePaymentLinkRequest([
    'idempotencyKey' => bin2hex(random_bytes(16)),
    'order' => new Order([
        'locationId' => getenv('SQUARE_LOCATION_ID'),
        'lineItems'  => $lineItems,
    ]),
    'checkoutOptions' => new CheckoutOptions([
        'redirectUrl'           => 'https://ripdipfishing.com/thank-you',
        'askForShippingAddress' => true,
        'shippingFee'           => new ShippingFee([
            'name'   => 'USPS Ground Advantage',
            'charge' => new Money(['amount' => 500, 'currency' => Currency::Usd->value]),
        ]),
        'enableCoupon'          => true,
        'merchantSupportEmail'  => 'Ripdipfishing@gmail.com',
    ]),
]));

header('Location: ' . $response->getPaymentLink()->getUrl());
```

Notes from the type definitions:

- `CheckoutOptions` fields: `allowTipping`, `customFields`, `redirectUrl`, `merchantSupportEmail`, `askForShippingAddress`, `acceptedPaymentMethods`, `shippingFee`, `enableCoupon`, `enableLoyalty`, `subscriptionPlanId`, `appFeeMoney`.
- `shippingFee` is **one flat fee per link**. For "free over $X" or local pickup, compute it in our cart before creating the link (omit the fee, or offer a pickup button that creates a link without shipping).
- Sales tax: set up the Michigan 6% tax in the Square Dashboard on the items; it applies automatically.
- `PrePopulatedData` (`buyerEmail`, `buyerPhoneNumber`, `buyerAddress`) can prefill checkout.
- Every create needs a fresh `idempotencyKey`.

## 4. Webhooks (order emails, stock sync)

```php
use Square\Utils\WebhooksHelper;

$body = file_get_contents('php://input');
$ok = WebhooksHelper::verifySignature(
    requestBody: $body,
    signatureHeader: $_SERVER['HTTP_X_SQUARE_HMACSHA256_SIGNATURE'] ?? '',
    signatureKey: getenv('SQUARE_WEBHOOK_SIGNATURE_KEY'),
    notificationUrl: 'https://ripdipfishing.com/square-webhook.php', // must match the subscription URL exactly
);
if (!$ok) { http_response_code(403); exit; }
```

The SDK README writes this as `WebhooksHelper.verifySignature(...)` (JavaScript syntax); in PHP it is the static call `WebhooksHelper::verifySignature(...)`.

Useful events: `payment.updated` (status `COMPLETED`), `order.updated`, `inventory.count.updated`, `catalog.version.updated` (refresh our cache).

## Testing (sandbox)

- Sandbox card: `4111 1111 1111 1111`, CVV `111`, any future expiry, any valid US ZIP.
- More test values: [developer.squareup.com/docs/testing/test-values](https://developer.squareup.com/docs/testing/test-values).
- Live credentials with a real card **will charge it**.

## Moving products from Ecwid

Owners export the Ecwid catalog CSV (see `ECOMMERCE-ALTERNATIVES.md`). Either reshape it into Square's item-library import spreadsheet (one row per variation) in the Dashboard, or write a one-time script that calls `$square->catalog->batchUpsert(...)` with `ITEM` + `ITEM_VARIATION` objects, then `catalog->images->create(...)` for photos and `inventory->batchCreateChanges(...)` for starting stock.

## Docs

- [Square PHP SDK](https://developer.squareup.com/docs/sdks/php)
- [Checkout API: Square order checkout](https://developer.squareup.com/docs/checkout-api/square-order-checkout)
- [Optional checkout configurations](https://developer.squareup.com/docs/checkout-api/optional-checkout-configurations)
- [Catalog API](https://developer.squareup.com/docs/catalog-api/what-it-does)
- [Inventory API](https://developer.squareup.com/docs/inventory-api/what-it-does)
- [Webhooks](https://developer.squareup.com/docs/webhooks/overview)
