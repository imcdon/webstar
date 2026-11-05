<?php

declare(strict_types=1);

namespace Webstar\Shop;

use PDO;

final class Accounts
{
    private const SESSION_KEY = 'webstar_shop_account_id';

    public function __construct(
        private readonly Config $config,
        private readonly PDO $db,
        private readonly Mailer $mailer,
    ) {
        if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
            session_start();
        }
    }

    public static function make(Config $config): self
    {
        return new self($config, Db::pdo(), Mailer::make($config));
    }

    public function current(): ?array
    {
        $id = $_SESSION[self::SESSION_KEY] ?? null;
        if (!$id) {
            return null;
        }
        return $this->getById((string) $id);
    }

    public function getById(string $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM accounts WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM accounts WHERE email = ? COLLATE NOCASE');
        $stmt->execute([strtolower(trim($email))]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findOrCreate(string $email, ?string $name = null): array
    {
        $email = strtolower(trim($email));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email.');
        }
        $existing = $this->getByEmail($email);
        if ($existing) {
            if ($name && empty($existing['name'])) {
                $this->db->prepare('UPDATE accounts SET name = ?, updated_at = datetime(\'now\') WHERE id = ?')
                    ->execute([$name, $existing['id']]);
                $existing['name'] = $name;
            }
            return $existing;
        }
        $id = Db::newId('acct_');
        $this->db->prepare(
            'INSERT INTO accounts (id, email, name) VALUES (?,?,?)'
        )->execute([$id, $email, $name]);
        return $this->getById($id) ?? ['id' => $id, 'email' => $email, 'name' => $name];
    }

    public function requestMagicLink(string $email, string $verifyBaseUrl): void
    {
        $account = $this->findOrCreate($email);
        $token = bin2hex(random_bytes(32));
        $hash = hash('sha256', $token);
        $id = Db::newId('tok_');
        $this->db->prepare(
            'INSERT INTO login_tokens (id, account_id, token_hash, expires_at)
             VALUES (?,?,?,datetime(\'now\', \'+15 minutes\'))'
        )->execute([$id, $account['id'], $hash]);

        $url = $verifyBaseUrl . (str_contains($verifyBaseUrl, '?') ? '&' : '?') . 'token=' . urlencode($token);
        $this->mailer->magicLink($account['email'], $url);
    }

    public function verifyMagicLink(string $token): ?array
    {
        $hash = hash('sha256', $token);
        $stmt = $this->db->prepare(
            'SELECT * FROM login_tokens WHERE token_hash = ? AND used_at IS NULL AND expires_at > datetime(\'now\') LIMIT 1'
        );
        $stmt->execute([$hash]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }
        $this->db->prepare('UPDATE login_tokens SET used_at = datetime(\'now\') WHERE id = ?')->execute([$row['id']]);
        $this->db->prepare('UPDATE accounts SET last_login_at = datetime(\'now\') WHERE id = ?')->execute([$row['account_id']]);
        $_SESSION[self::SESSION_KEY] = $row['account_id'];
        return $this->getById($row['account_id']);
    }

    public function logout(): void
    {
        unset($_SESSION[self::SESSION_KEY]);
    }

    public function updateProfile(string $accountId, ?string $name, ?string $phone, ?array $address): void
    {
        $this->db->prepare(
            'UPDATE accounts SET name = ?, phone = ?, shipping_address_json = ?, updated_at = datetime(\'now\') WHERE id = ?'
        )->execute([
            $name,
            $phone,
            $address ? json_encode($address) : null,
            $accountId,
        ]);
    }

    public function prefillForCheckout(?array $account): ?array
    {
        if (!$account) {
            return null;
        }
        $prefill = ['email' => $account['email']];
        if (!empty($account['phone'])) {
            $prefill['phone'] = $account['phone'];
        }
        if (!empty($account['shipping_address_json'])) {
            $addr = json_decode((string) $account['shipping_address_json'], true);
            if (is_array($addr)) {
                $prefill['address'] = $addr;
            }
        }
        return $prefill;
    }
}
