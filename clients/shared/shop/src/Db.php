<?php

declare(strict_types=1);

namespace Webstar\Shop;

use PDO;
use RuntimeException;

final class Db
{
    private static ?PDO $pdo = null;

    public static function connect(string $sqlitePath): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $dir = dirname($sqlitePath);
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException('Cannot create SQLite directory: ' . $dir);
        }

        $pdo = new PDO('sqlite:' . $sqlitePath, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $pdo->exec('PRAGMA foreign_keys = ON');
        $pdo->exec('PRAGMA journal_mode = WAL');

        self::$pdo = $pdo;
        self::migrate($pdo);

        return $pdo;
    }

    public static function pdo(): PDO
    {
        if (!self::$pdo instanceof PDO) {
            throw new RuntimeException('Database not connected. Call Db::connect() first.');
        }

        return self::$pdo;
    }

    private static function migrate(PDO $pdo): void
    {
        $migrationsDir = dirname(__DIR__) . '/db/migrations';
        $files = glob($migrationsDir . '/*.sql') ?: [];
        sort($files);

        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS schema_migrations (
                id TEXT PRIMARY KEY,
                applied_at TEXT NOT NULL DEFAULT (datetime(\'now\'))
            )'
        );

        $check = $pdo->prepare('SELECT 1 FROM schema_migrations WHERE id = ?');
        $mark = $pdo->prepare('INSERT INTO schema_migrations (id) VALUES (?)');

        foreach ($files as $file) {
            $id = basename($file);
            $check->execute([$id]);
            if ($check->fetchColumn()) {
                continue;
            }
            $sql = file_get_contents($file);
            if ($sql === false) {
                throw new RuntimeException('Cannot read migration: ' . $file);
            }
            $pdo->exec($sql);
            $mark->execute([$id]);
        }
    }

    public static function newId(string $prefix = ''): string
    {
        return $prefix . bin2hex(random_bytes(12));
    }
}
