<?php
/**
 * Run this file ONCE in your browser after importing car_marketplace.sql
 * to set correct password hashes for the demo accounts.
 *
 * URL: http://localhost/car_marketplace/database/seed_passwords.php
 *
 * Delete this file after running it.
 */

require_once __DIR__ . '/../config/db.php';

$accounts = [
    ['email' => 'admin@buycars.com', 'password' => 'Admin@123'],
    ['email' => 'sagar@example.com', 'password' => 'User@123'],
    ['email' => 'nisha@example.com', 'password' => 'User@123'],
];

echo '<pre style="font-family:monospace;font-size:14px;padding:30px;background:#f9f9f9;">';
echo "BuyCars — Seeding passwords\n";
echo str_repeat('-', 40) . "\n";

foreach ($accounts as $acc) {
    $hash = password_hash($acc['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->execute([$hash, $acc['email']]);
    echo ($stmt->rowCount() > 0 ? '✓' : '✗') . " {$acc['email']}\n";
}

echo str_repeat('-', 40) . "\n";
echo "Done. Delete this file now.\n";
echo '</pre>';
