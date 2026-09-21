<?php
/*
 * setup.php - One-time CLI setup: create tables and seed sample data.
 * Run: C:\xampp\php\php.exe database/setup.php
 */
$configFile = __DIR__ . '/../includes/db.config.php';
if (!file_exists($configFile)) {
    fwrite(STDERR, "Missing includes/db.config.php — copy db.config.example.php first.\n");
    exit(1);
}

$config = require $configFile;

$dsn = sprintf('mysql:host=%s;charset=%s', $config['host'], $config['charset']);
$pdo = new PDO($dsn, $config['user'], $config['pass'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

foreach (['schema.sql', 'seed.sql'] as $file) {
    $sql = file_get_contents(__DIR__ . '/' . $file);
    // Drop full-line SQL comments so semicolons inside comments do not break splitting.
    $sql = preg_replace('/^\s*--.*$/m', '', $sql);
    foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
        if ($statement !== '') {
            $pdo->exec($statement);
        }
    }
    echo "Ran {$file}\n";
}

echo "Database ready.\n";
