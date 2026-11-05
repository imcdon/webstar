<?php

declare(strict_types=1);

namespace Webstar\Shop;

use RuntimeException;
use Square\GiftCards\Requests\GetGiftCardFromNonceRequest;
use Square\Orders\Requests\CalculateOrderRequest;
use Square\Orders\Requests\GetOrdersRequest;
use Square\Orders\Requests\PayOrderRequest;
use Square\Payments\Requests\CreatePaymentRequest;
use Square\Types\Address;
use Square\Types\CreateOrderRequest;
use Square\Types\Currency;
use Square\Types\Fulfillment;
use Square\Types\FulfillmentPickupDetails;
use Square\Types\FulfillmentPickupDetailsScheduleType;
use Square\Types\FulfillmentRecipient;
use Square\Types\FulfillmentShipmentDetails;
use Square\Types\FulfillmentType;
use Square\Types\Money;
use Square\Types\Order;
use Square\Types\OrderLineItem;
use Square\Types\OrderLineItemDiscount;
use Square\Types\OrderLineItemDiscountScope;
use Square\Types\OrderPricingOptions;
use Square\Types\OrderServiceCharge;
use Square\Types\OrderServiceChargeCalculationPhase;

/**
 * On-site checkout via Square Web Payments SDK + Orders/Payments APIs.
 */
final class Checkout
{
    public function __construct(
        private readonly Config $config,
        private readonly Coupons $coupons,
    ) {
    }

    public static function make(Config $config): self
    {
        return new self($config, Coupons::make($config));
    }

    /**
     * Form payload from checkout page / API.
     *
     * @param array{
     *   email?:string,phone?:string,name?:string,
     *   shipping_method?:string,
     *   address?:array{line1?:string,line2?:string,city?:string,state?:string,postal?:string,country?:string},
     *   coupon?:string,
     *   gift_card_token?:string,
     * } $form
     * @return array{
     *   subtotal_cents:int,shipping_cents:int,tax_cents:int,discount_cents:int,
     *   gift_applied_cents:int,total_cents:int,due_cents:int,
     *   shipping_method:string,shipping_label:string,
     *   coupon:?array{id:string,name:string},
     *   currency:string
     * }
     */
    public function calculate(Cart $cart, array $form): array
    {
        $detailed = $this->requireCart($cart);
        $form = $this->normalizeForm($form, $detailed);
        $detailed = $this->applyShippingToDetailed($detailed, $form);

        if ($this->config->demoMode) {
            return $this->demoTotals($detailed, $form);
        }

        $order = $this->buildOrder($detailed, $form, $cart->cartId());
        $square = SquareClientFactory::make($this->config);
        $response = $square->orders->calculate(new CalculateOrderRequest(['order' => $order]));
        $calc = $response->getOrder();
        if ($calc === null) {
            throw new RuntimeException('Square could not calculate the order.');
        }

        $subtotal = (int) ($calc->getTotalMoney()?->getAmount() ?? 0)
            - (int) ($calc->getTotalTaxMoney()?->getAmount() ?? 0)
            - (int) ($calc->getTotalServiceChargeMoney()?->getAmount() ?? 0)
            + (int) ($calc->getTotalDiscountMoney()?->getAmount() ?? 0);
        // Prefer line-item totals for a clearer subtotal
        $lineSub = 0;
        foreach ($calc->getLineItems() ?? [] as $li) {
            $lineSub += (int) ($li->getBasePriceMoney()?->getAmount() ?? 0) * (int) $li->getQuantity();
        }
        if ($lineSub > 0) {
            $subtotal = $lineSub;
        }

        $tax = (int) ($calc->getTotalTaxMoney()?->getAmount() ?? 0);
        $shipping = (int) ($calc->getTotalServiceChargeMoney()?->getAmount() ?? 0);
        $discount = (int) ($calc->getTotalDiscountMoney()?->getAmount() ?? 0);
        $total = (int) ($calc->getNetAmountDueMoney()?->getAmount()
            ?? $calc->getTotalMoney()?->getAmount()
            ?? 0);

        $giftApplied = 0;
        $giftToken = trim((string) ($form['gift_card_token'] ?? ''));
        if ($giftToken !== '') {
            $balance = $this->giftCardBalanceCents($giftToken);
            $giftApplied = min($balance, $total);
        }

        return [
            'subtotal_cents' => $subtotal,
            'shipping_cents' => $shipping,
            'tax_cents' => $tax,
            'discount_cents' => $discount,
            'gift_applied_cents' => $giftApplied,
            'total_cents' => $total,
            'due_cents' => max(0, $total - $giftApplied),
            'shipping_method' => $form['shipping_method'],
            'shipping_label' => $detailed['shipping_label'],
            'coupon' => $form['coupon_resolved'] ?? null,
            'currency' => $this->config->currency,
        ];
    }

    /**
     * Charge the cart. Returns local order + redirect path.
     *
     * @param array{
     *   email?:string,phone?:string,name?:string,
     *   shipping_method?:string,
     *   address?:array{line1?:string,line2?:string,city?:string,state?:string,postal?:string,country?:string},
     *   coupon?:string,
     *   gift_card_token?:string,
     *   source_id?:string,
     *   verification_token?:string,
     *   account_id?:?string,
     * } $form
     * @return array{order_number:string,order_id:string,square_order_id:?string,redirect:string}
     */
    public function pay(Cart $cart, array $form): array
    {
        $detailed = $this->requireCart($cart);
        $form = $this->normalizeForm($form, $detailed);
        $detailed = $this->applyShippingToDetailed($detailed, $form);
        $this->validatePayForm($form);

        if ($this->config->demoMode) {
            return $this->demoPay($cart, $detailed, $form);
        }

        $sourceId = trim((string) ($form['source_id'] ?? ''));
        $giftToken = trim((string) ($form['gift_card_token'] ?? ''));
        if ($sourceId === '' && $giftToken === '') {
            throw new RuntimeException('Payment method is required.');
        }

        $cartId = $cart->cartId();
        $order = $this->buildOrder($detailed, $form, $cartId);
        $square = SquareClientFactory::make($this->config);

        $created = $square->orders->create(new CreateOrderRequest([
            'idempotencyKey' => bin2hex(random_bytes(16)),
            'order' => $order,
        ]));
        $squareOrder = $created->getOrder();
        if ($squareOrder === null || !$squareOrder->getId()) {
            throw new RuntimeException('Square did not create an order.');
        }
        $squareOrderId = (string) $squareOrder->getId();
        $due = (int) ($squareOrder->getNetAmountDueMoney()?->getAmount()
            ?? $squareOrder->getTotalMoney()?->getAmount()
            ?? 0);

        $paymentIds = [];
        $primaryPayment = null;
        $buyerEmail = strtolower((string) $form['email']);
        $shippingAddress = $this->squareAddress($form);

        // Split tender: gift card covers what it can; card/wallet covers the rest
        $giftApplied = 0;
        if ($giftToken !== '' && $due > 0) {
            $balance = $this->giftCardBalanceCents($giftToken);
            $giftApplied = min($balance, $due);
            if ($giftApplied > 0) {
                $giftPay = $square->payments->create(new CreatePaymentRequest([
                    'sourceId' => $giftToken,
                    'idempotencyKey' => bin2hex(random_bytes(16)),
                    'amountMoney' => new Money([
                        'amount' => $giftApplied,
                        'currency' => Currency::Usd->value,
                    ]),
                    'orderId' => $squareOrderId,
                    'locationId' => $this->config->squareLocationId,
                    'autocomplete' => $sourceId !== '' && ($due - $giftApplied) > 0 ? false : true,
                    'buyerEmailAddress' => $buyerEmail,
                    'referenceId' => $cartId,
                ]));
                $gp = $giftPay->getPayment();
                if ($gp === null || !$gp->getId()) {
                    throw new RuntimeException('Gift card payment failed.');
                }
                $paymentIds[] = (string) $gp->getId();
                $primaryPayment = $gp;
                $due -= $giftApplied;
            }
        }

        if ($due > 0) {
            if ($sourceId === '') {
                throw new RuntimeException('Card or wallet payment is required for the remaining balance.');
            }
            $autocomplete = $paymentIds === [];
            $cardPay = $square->payments->create(new CreatePaymentRequest([
                'sourceId' => $sourceId,
                'idempotencyKey' => bin2hex(random_bytes(16)),
                'amountMoney' => new Money([
                    'amount' => $due,
                    'currency' => Currency::Usd->value,
                ]),
                'orderId' => $squareOrderId,
                'locationId' => $this->config->squareLocationId,
                'autocomplete' => $autocomplete,
                'buyerEmailAddress' => $buyerEmail,
                'buyerPhoneNumber' => $form['phone'] !== '' ? $form['phone'] : null,
                'shippingAddress' => $shippingAddress,
                'verificationToken' => !empty($form['verification_token'])
                    ? (string) $form['verification_token']
                    : null,
                'referenceId' => $cartId,
            ]));
            $cp = $cardPay->getPayment();
            if ($cp === null || !$cp->getId()) {
                throw new RuntimeException('Payment failed.');
            }
            $paymentIds[] = (string) $cp->getId();
            $primaryPayment = $cp;
        }

        // Split tender: both payments were created with autocomplete=false — finalize
        if (count($paymentIds) > 1) {
            $square->orders->pay(new PayOrderRequest([
                'orderId' => $squareOrderId,
                'idempotencyKey' => bin2hex(random_bytes(16)),
                'paymentIds' => $paymentIds,
            ]));
            $squareOrder = $square->orders->get(new GetOrdersRequest(['orderId' => $squareOrderId]))->getOrder()
                ?? $squareOrder;
        }

        $local = Orders::make($this->config)->createFromSquareOrder(
            $squareOrder,
            $primaryPayment,
            $cartId,
            $form['account_id'] ?? null,
            $form,
        );

        $cart->attachPaymentLink('', '', $squareOrderId);
        $cart->markConverted((string) $local['id']);
        $cart->clear();

        if (!empty($form['account_id']) && ($form['shipping_method'] ?? '') === 'flat' && !empty($form['address'])) {
            try {
                Accounts::make($this->config)->updateProfile(
                    (string) $form['account_id'],
                    $form['name'] !== '' ? $form['name'] : null,
                    $form['phone'] !== '' ? $form['phone'] : null,
                    $form['address'],
                );
            } catch (\Throwable) {
                // non-fatal
            }
        }

        $this->sendOrderEmails($local);

        return [
            'order_number' => (string) $local['order_number'],
            'order_id' => (string) $local['id'],
            'square_order_id' => $squareOrderId,
            'redirect' => 'checkout/return.php?order=' . rawurlencode((string) $local['order_number']),
        ];
    }

    /**
     * @param array{lines:list<array>,shipping_cents:int,shipping_method:string,shipping_label:string,subtotal_cents:int,total_cents:int} $detailed
     * @param array{email:string,phone:string,name:string,shipping_method:string,address:array,coupon_resolved:?array,gift_card_token:string} $form
     */
    public function buildOrder(array $detailed, array $form, string $cartId): Order
    {
        $lineItems = [];
        foreach ($detailed['lines'] as $line) {
            $squareId = $line['square_id'] ?? null;
            if (!$squareId || str_starts_with((string) $squareId, 'local_')) {
                throw new RuntimeException(
                    'Cart contains local/demo variations. Sync the Square catalog before checkout.'
                );
            }
            $lineItems[] = new OrderLineItem([
                'catalogObjectId' => (string) $squareId,
                'quantity' => (string) $line['qty'],
            ]);
        }
        if ($lineItems === []) {
            throw new RuntimeException('Cart is empty.');
        }

        $serviceCharges = [];
        if (($form['shipping_method'] ?? 'flat') === 'flat' && (int) $detailed['shipping_cents'] > 0) {
            $serviceCharges[] = new OrderServiceCharge([
                'name' => (string) ($detailed['shipping_label'] ?? 'Shipping'),
                'amountMoney' => new Money([
                    'amount' => (int) $detailed['shipping_cents'],
                    'currency' => Currency::Usd->value,
                ]),
                'calculationPhase' => OrderServiceChargeCalculationPhase::TotalPhase->value,
                'taxable' => false,
            ]);
        }

        $discounts = [];
        if (!empty($form['coupon_resolved']['id']) && !str_starts_with((string) $form['coupon_resolved']['id'], 'demo_')) {
            $discounts[] = new OrderLineItemDiscount([
                'catalogObjectId' => (string) $form['coupon_resolved']['id'],
                'name' => (string) $form['coupon_resolved']['name'],
                'scope' => OrderLineItemDiscountScope::Order->value,
            ]);
        }

        $displayName = trim((string) ($form['name'] ?? ''));
        if ($displayName === '') {
            $displayName = trim((string) ($form['email'] ?? ''));
        }
        if ($displayName === '') {
            $displayName = 'Customer';
        }
        $recipient = new FulfillmentRecipient([
            'displayName' => $displayName,
            'emailAddress' => ($form['email'] ?? '') !== '' ? $form['email'] : null,
            'phoneNumber' => ($form['phone'] ?? '') !== '' ? $form['phone'] : null,
            'address' => ($form['shipping_method'] ?? '') === 'flat' ? $this->squareAddress($form) : null,
        ]);

        if (($form['shipping_method'] ?? 'flat') === 'pickup') {
            $fulfillment = new Fulfillment([
                'type' => FulfillmentType::Pickup->value,
                'state' => 'PROPOSED',
                'pickupDetails' => new FulfillmentPickupDetails([
                    'recipient' => $recipient,
                    'scheduleType' => FulfillmentPickupDetailsScheduleType::Asap->value,
                    'note' => $this->config->pickupLabel,
                ]),
            ]);
        } else {
            $fulfillment = new Fulfillment([
                'type' => FulfillmentType::Shipment->value,
                'state' => 'PROPOSED',
                'shipmentDetails' => new FulfillmentShipmentDetails([
                    'recipient' => $recipient,
                    'shippingType' => (string) ($detailed['shipping_label'] ?? 'Shipping'),
                ]),
            ]);
        }

        return new Order([
            'locationId' => $this->config->squareLocationId,
            'referenceId' => $cartId,
            'lineItems' => $lineItems,
            'taxes' => null,
            'discounts' => $discounts ?: null,
            'serviceCharges' => $serviceCharges ?: null,
            'fulfillments' => [$fulfillment],
            'pricingOptions' => new OrderPricingOptions([
                'autoApplyTaxes' => true,
                'autoApplyDiscounts' => true,
            ]),
        ]);
    }

    /**
     * @param array{lines:list<array>,shipping_cents:int,shipping_method:string,shipping_label:string,subtotal_cents:int,total_cents:int,errors:list<string>} $detailed
     * @return array{email:string,phone:string,name:string,shipping_method:string,address:array,coupon?:string,coupon_resolved:?array{id:string,name:string},gift_card_token:string,source_id?:string,verification_token?:string,account_id?:?string}
     */
    private function normalizeForm(array $form, array $detailed): array
    {
        $method = (string) ($form['shipping_method'] ?? $detailed['shipping_method'] ?? 'flat');
        if ($method === 'pickup' && !$this->config->pickupEnabled) {
            $method = 'flat';
        }
        if ($method !== 'pickup') {
            $method = 'flat';
        }

        $address = is_array($form['address'] ?? null) ? $form['address'] : [];
        $couponCode = trim((string) ($form['coupon'] ?? ''));
        $coupon = $this->coupons->requireByCode($couponCode);

        return [
            'email' => strtolower(trim((string) ($form['email'] ?? ''))),
            'phone' => $this->normalizePhoneE164((string) ($form['phone'] ?? '')),
            'name' => trim((string) ($form['name'] ?? '')),
            'shipping_method' => $method,
            'address' => [
                'line1' => trim((string) ($address['line1'] ?? '')),
                'line2' => trim((string) ($address['line2'] ?? '')),
                'city' => trim((string) ($address['city'] ?? '')),
                'state' => strtoupper(trim((string) ($address['state'] ?? ''))),
                'postal' => trim((string) ($address['postal'] ?? '')),
                'country' => strtoupper(trim((string) ($address['country'] ?? 'US'))) ?: 'US',
            ],
            'coupon' => $couponCode,
            'coupon_resolved' => $coupon,
            'gift_card_token' => trim((string) ($form['gift_card_token'] ?? '')),
            'source_id' => isset($form['source_id']) ? trim((string) $form['source_id']) : '',
            'verification_token' => isset($form['verification_token']) ? trim((string) $form['verification_token']) : '',
            'account_id' => $form['account_id'] ?? null,
        ];
    }

    /**
     * @param array{shipping_cents:int,shipping_method:string,shipping_label:string,subtotal_cents:int} $detailed
     * @param array{shipping_method:string} $form
     * @return array{shipping_cents:int,shipping_method:string,shipping_label:string,subtotal_cents:int,total_cents?:int,lines?:list<array>,errors?:list<string>}
     */
    private function applyShippingToDetailed(array $detailed, array $form): array
    {
        if (($form['shipping_method'] ?? '') === 'pickup') {
            $detailed['shipping_method'] = 'pickup';
            $detailed['shipping_cents'] = 0;
            $detailed['shipping_label'] = $this->config->pickupLabel;
        } else {
            $detailed['shipping_method'] = 'flat';
            $subtotal = (int) $detailed['subtotal_cents'];
            if ($this->config->shippingFreeOverCents > 0 && $subtotal >= $this->config->shippingFreeOverCents) {
                $detailed['shipping_cents'] = 0;
                $detailed['shipping_label'] = 'Free shipping';
            } else {
                $detailed['shipping_cents'] = $this->config->shippingFlatCents;
                $detailed['shipping_label'] = 'USPS Ground Advantage';
            }
        }
        $detailed['total_cents'] = (int) $detailed['subtotal_cents'] + (int) $detailed['shipping_cents'];
        return $detailed;
    }

    /**
     * @param array{email:string,shipping_method:string,address:array} $form
     */
    private function validatePayForm(array $form): void
    {
        if ($form['email'] === '' || !filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('A valid email is required.');
        }
        if ($form['shipping_method'] === 'flat') {
            $a = $form['address'];
            foreach (['line1', 'city', 'state', 'postal'] as $req) {
                if (($a[$req] ?? '') === '') {
                    throw new RuntimeException('Shipping address is incomplete.');
                }
            }
            if (strlen($a['state']) !== 2) {
                throw new RuntimeException('Use a 2-letter state code.');
            }
        }
        if ($form['name'] === '') {
            throw new RuntimeException('Name is required.');
        }
    }

    /**
     * @return array{lines:list<array>,shipping_cents:int,shipping_method:string,shipping_label:string,subtotal_cents:int,total_cents:int,errors:list<string>}
     */
    private function requireCart(Cart $cart): array
    {
        $detailed = $cart->detailed();
        if ($detailed['lines'] === []) {
            $msg = $detailed['errors'][0] ?? 'Your cart is empty.';
            throw new RuntimeException($msg);
        }
        // Non-empty cart: prune/stock warnings are informational; payment can proceed.
        return $detailed;
    }

    private function giftCardBalanceCents(string $nonce): int
    {
        $square = SquareClientFactory::make($this->config);
        $resp = $square->giftCards->getFromNonce(new GetGiftCardFromNonceRequest([
            'nonce' => $nonce,
        ]));
        $card = $resp->getGiftCard();
        if ($card === null) {
            throw new RuntimeException('Gift card not found.');
        }
        return (int) ($card->getBalanceMoney()?->getAmount() ?? 0);
    }

    /** Square buyer/fulfillment phones must be E.164 (e.g. +18105550100). */
    private function normalizePhoneE164(string $raw): string
    {
        $raw = trim($raw);
        if ($raw === '') {
            return '';
        }
        $digits = preg_replace('/\D+/', '', $raw) ?? '';
        if (str_starts_with($raw, '+') && strlen($digits) >= 10 && strlen($digits) <= 15) {
            return '+' . $digits;
        }
        if (strlen($digits) === 10) {
            return '+1' . $digits;
        }
        if (strlen($digits) === 11 && str_starts_with($digits, '1')) {
            return '+' . $digits;
        }
        return $raw;
    }

    /**
     * @param array{address:array,name:string} $form
     */
    private function squareAddress(array $form): ?Address
    {
        $a = $form['address'] ?? [];
        if (($a['line1'] ?? '') === '') {
            return null;
        }
        $nameParts = preg_split('/\s+/', trim((string) ($form['name'] ?? '')), 2) ?: [];
        return new Address([
            'addressLine1' => (string) $a['line1'],
            'addressLine2' => ($a['line2'] ?? '') !== '' ? (string) $a['line2'] : null,
            'locality' => (string) ($a['city'] ?? ''),
            'administrativeDistrictLevel1' => (string) ($a['state'] ?? ''),
            'postalCode' => (string) ($a['postal'] ?? ''),
            'country' => (string) ($a['country'] ?? 'US'),
            'firstName' => $nameParts[0] ?? null,
            'lastName' => $nameParts[1] ?? null,
        ]);
    }

    /**
     * @param array{subtotal_cents:int,shipping_cents:int,shipping_method:string,shipping_label:string} $detailed
     * @param array{coupon_resolved:?array,gift_card_token:string,shipping_method:string} $form
     * @return array{subtotal_cents:int,shipping_cents:int,tax_cents:int,discount_cents:int,gift_applied_cents:int,total_cents:int,due_cents:int,shipping_method:string,shipping_label:string,coupon:?array,currency:string}
     */
    private function demoTotals(array $detailed, array $form): array
    {
        $subtotal = (int) $detailed['subtotal_cents'];
        $shipping = $form['shipping_method'] === 'pickup' ? 0 : (int) $detailed['shipping_cents'];
        $discount = 0;
        if (!empty($form['coupon_resolved'])) {
            $discount = (int) round($subtotal * 0.10);
        }
        $taxable = max(0, $subtotal - $discount);
        $tax = (int) round($taxable * 0.06);
        $total = $taxable + $tax + $shipping;
        $gift = 0;
        if (($form['gift_card_token'] ?? '') !== '') {
            $gift = min($total, 2500); // demo gift applies up to $25
        }
        return [
            'subtotal_cents' => $subtotal,
            'shipping_cents' => $shipping,
            'tax_cents' => $tax,
            'discount_cents' => $discount,
            'gift_applied_cents' => $gift,
            'total_cents' => $total,
            'due_cents' => max(0, $total - $gift),
            'shipping_method' => $form['shipping_method'],
            'shipping_label' => $form['shipping_method'] === 'pickup'
                ? $this->config->pickupLabel
                : (string) $detailed['shipping_label'],
            'coupon' => $form['coupon_resolved'] ?? null,
            'currency' => $this->config->currency,
        ];
    }

    /**
     * @param array{lines:list<array>,subtotal_cents:int,shipping_cents:int,shipping_method:string,shipping_label:string,total_cents:int} $detailed
     * @param array{email:string,phone:string,name:string,shipping_method:string,address:array,account_id?:?string} $form
     * @return array{order_number:string,order_id:string,square_order_id:?string,redirect:string}
     */
    private function demoPay(Cart $cart, array $detailed, array $form): array
    {
        $totals = $this->demoTotals($detailed, $form);
        $orderId = Db::newId('demo_ord_');
        $paymentId = Db::newId('demo_pay_');
        $orderNumber = 'DEMO-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

        $cartPayload = $detailed;
        $cartPayload['email'] = $form['email'];
        $cartPayload['shipping_cents'] = $totals['shipping_cents'];
        $cartPayload['shipping_method'] = $totals['shipping_method'];
        $cartPayload['shipping_label'] = $totals['shipping_label'];
        $cartPayload['subtotal_cents'] = $totals['subtotal_cents'];
        $cartPayload['tax_cents'] = $totals['tax_cents'];
        $cartPayload['discount_cents'] = $totals['discount_cents'];
        $cartPayload['total_cents'] = $totals['due_cents'] > 0 ? $totals['total_cents'] : $totals['total_cents'];

        $orders = Orders::make($this->config);
        $local = $orders->createFromDemoCart($cartPayload, $orderId, $paymentId, $orderNumber, $form['email']);

        // Enrich with tax/discount/address
        Db::pdo()->prepare(
            'UPDATE orders SET tax_cents = ?, discount_cents = ?, total_cents = ?, shipping_address_json = ?, phone = ?, shipping_name = ? WHERE id = ?'
        )->execute([
            $totals['tax_cents'],
            $totals['discount_cents'],
            $totals['total_cents'],
            json_encode($form['address']),
            $form['phone'] !== '' ? $form['phone'] : null,
            $form['name'],
            $orderId,
        ]);

        $cart->markConverted($orderId);
        $cart->clear();

        $local = Orders::make($this->config)->getById($orderId) ?? [];
        $this->sendOrderEmails($local);

        return [
            'order_number' => $orderNumber,
            'order_id' => $orderId,
            'square_order_id' => null,
            'redirect' => 'checkout/return.php?order=' . rawurlencode($orderNumber),
        ];
    }

    /** @param array<string,mixed> $order */
    private function sendOrderEmails(array $order): void
    {
        if ($order === []) {
            return;
        }
        try {
            $mailer = Mailer::make($this->config);
            $mailer->orderConfirmation($order);
            $mailer->ownerNewOrder($order);
        } catch (\Throwable) {
            // never fail checkout because mail failed
        }
    }
}
