<?php

declare(strict_types=1);

namespace Webstar\Shop;

use PHPMailer\PHPMailer\PHPMailer;

final class Mailer
{
    public function __construct(private readonly Config $config)
    {
    }

    public static function make(Config $config): self
    {
        return new self($config);
    }

    public function send(string $to, string $subject, string $htmlBody, ?string $textBody = null): bool
    {
        if ($to === '') {
            return false;
        }

        // Demo / local: write to data/mail.log when SMTP is not configured
        if (empty($this->config->smtp['host'] ?? null)) {
            $logDir = dirname($this->config->sqlitePath);
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0775, true);
            }
            $entry = sprintf(
                "[%s] TO:%s SUBJECT:%s\n%s\n\n",
                date('c'),
                $to,
                $subject,
                $textBody ?? strip_tags($htmlBody)
            );
            file_put_contents($logDir . '/mail.log', $entry, FILE_APPEND);
            return true;
        }

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = (string) $this->config->smtp['host'];
            $mail->Port = (int) ($this->config->smtp['port'] ?? 587);
            $mail->SMTPAuth = true;
            $mail->Username = (string) ($this->config->smtp['user'] ?? '');
            $mail->Password = (string) ($this->config->smtp['pass'] ?? '');
            $mail->SMTPSecure = (string) ($this->config->smtp['secure'] ?? PHPMailer::ENCRYPTION_STARTTLS);
            $mail->setFrom($this->config->mailFrom, $this->config->mailFromName);
            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->isHTML(true);
            $mail->Body = $htmlBody;
            $mail->AltBody = $textBody ?? strip_tags($htmlBody);
            $mail->send();
            return true;
        } catch (\Throwable $e) {
            $logDir = dirname($this->config->sqlitePath);
            file_put_contents(
                $logDir . '/mail.log',
                '[' . date('c') . '] ERROR ' . $e->getMessage() . "\n",
                FILE_APPEND
            );
            return false;
        }
    }

    public function magicLink(string $email, string $url): void
    {
        $html = '<p>Sign in to ' . Helpers::e($this->config->siteName) . ':</p>'
            . '<p><a href="' . Helpers::e($url) . '">Click to sign in</a></p>'
            . '<p>This link expires in 15 minutes. If you did not request it, ignore this email.</p>';
        $this->send($email, 'Your sign-in link', $html);
    }

    public function abandonedCart(string $email, string $restoreUrl, int $hours): void
    {
        $html = '<p>You left items in your cart at ' . Helpers::e($this->config->siteName) . '.</p>'
            . '<p><a href="' . Helpers::e($restoreUrl) . '">Return to your cart</a></p>'
            . '<p><small>Reminder after ' . (int) $hours . ' hours. '
            . '<a href="' . Helpers::e($restoreUrl . '&unsubscribe=1') . '">Unsubscribe</a></small></p>';
        $this->send($email, 'You left something in your cart', $html);
    }

    public function backInStock(string $email, string $productName, string $productUrl): void
    {
        $html = '<p>' . Helpers::e($productName) . ' is back in stock.</p>'
            . '<p><a href="' . Helpers::e($productUrl) . '">View product</a></p>';
        $this->send($email, $productName . ' is back in stock', $html);
    }

    /**
     * Customer order confirmation (Square Payments API does not auto-email receipts).
     *
     * @param array{
     *   order_number:string,email:string,total_cents:int,subtotal_cents?:int,
     *   shipping_cents?:int,tax_cents?:int,discount_cents?:int,
     *   shipping_method?:string,shipping_name?:?string,phone?:?string,
     *   shipping_address_json?:?string,
     *   items?:list<array{name:string,variation_name?:?string,quantity:int|string,line_total_cents:int|string}>
     * } $order
     */
    public function orderConfirmation(array $order): void
    {
        $to = strtolower(trim((string) ($order['email'] ?? '')));
        if ($to === '') {
            return;
        }
        $number = (string) ($order['order_number'] ?? '');
        $html = $this->orderEmailHtml($order, false);
        $this->send($to, 'Order ' . $number . ' — ' . $this->config->siteName, $html);
    }

    /**
     * Notify the store owner of a new paid order.
     *
     * @param array<string,mixed> $order
     */
    public function ownerNewOrder(array $order): void
    {
        $to = trim($this->config->ownerEmail !== '' ? $this->config->ownerEmail : $this->config->siteEmail);
        if ($to === '') {
            return;
        }
        $number = (string) ($order['order_number'] ?? '');
        $html = $this->orderEmailHtml($order, true);
        $this->send($to, 'New order ' . $number . ' — ' . $this->config->siteName, $html);
    }

    /**
     * @param array<string,mixed> $order
     */
    private function orderEmailHtml(array $order, bool $forOwner): string
    {
        $number = Helpers::e((string) ($order['order_number'] ?? ''));
        $name = Helpers::e((string) ($order['shipping_name'] ?? ''));
        $email = Helpers::e((string) ($order['email'] ?? ''));
        $method = ((string) ($order['shipping_method'] ?? 'flat')) === 'pickup' ? 'Pickup' : 'Shipping';
        $lines = '';
        foreach ($order['items'] ?? [] as $item) {
            $label = Helpers::e((string) ($item['name'] ?? 'Item'));
            $vname = trim((string) ($item['variation_name'] ?? ''));
            if ($vname !== '' && !in_array($vname, ['Default', 'Regular'], true)) {
                $label .= ' <span style="color:#666">(' . Helpers::e($vname) . ')</span>';
            }
            $lines .= '<tr><td style="padding:.35rem 0">' . $label
                . ' × ' . (int) ($item['quantity'] ?? 0) . '</td>'
                . '<td style="padding:.35rem 0;text-align:right">'
                . Helpers::e(Helpers::money((int) ($item['line_total_cents'] ?? 0)))
                . '</td></tr>';
        }

        $addr = '';
        $rawAddr = $order['shipping_address_json'] ?? null;
        if (is_string($rawAddr) && $rawAddr !== '') {
            $a = json_decode($rawAddr, true);
            if (is_array($a) && ($a['line1'] ?? '') !== '') {
                $parts = array_filter([
                    $a['line1'] ?? '',
                    $a['line2'] ?? '',
                    trim(($a['city'] ?? '') . ', ' . ($a['state'] ?? '') . ' ' . ($a['postal'] ?? '')),
                ]);
                $addr = '<p style="margin:.5rem 0">' . Helpers::e(implode("\n", $parts)) . '</p>';
            }
        }

        $intro = $forOwner
            ? '<p>New paid order on ' . Helpers::e($this->config->siteName) . '.</p>'
            : '<p>Thanks for your order at ' . Helpers::e($this->config->siteName) . '.</p>';

        return $intro
            . '<p><strong>Order ' . $number . '</strong></p>'
            . ($forOwner ? '<p>Customer: ' . $name . ' &lt;' . $email . '&gt;</p>' : '')
            . '<p>Fulfillment: ' . Helpers::e($method) . '</p>'
            . $addr
            . '<table style="width:100%;border-collapse:collapse;margin:1rem 0">' . $lines . '</table>'
            . '<p>Subtotal: ' . Helpers::e(Helpers::money((int) ($order['subtotal_cents'] ?? 0))) . '<br>'
            . 'Shipping: ' . Helpers::e(Helpers::money((int) ($order['shipping_cents'] ?? 0))) . '<br>'
            . 'Tax: ' . Helpers::e(Helpers::money((int) ($order['tax_cents'] ?? 0))) . '<br>'
            . ((int) ($order['discount_cents'] ?? 0) > 0
                ? 'Discount: −' . Helpers::e(Helpers::money((int) $order['discount_cents'])) . '<br>'
                : '')
            . '<strong>Total: ' . Helpers::e(Helpers::money((int) ($order['total_cents'] ?? 0))) . '</strong></p>'
            . ($forOwner
                ? '<p>Fulfill this order in the Square Dashboard.</p>'
                : '<p>We will follow up with shipping or pickup details. Questions? Reply to this email or contact '
                    . Helpers::e($this->config->siteEmail) . '.</p>');
    }
}
