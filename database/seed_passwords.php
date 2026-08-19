<?php
/**
 * database/seed_passwords.php
 * 
 * Run this file ONCE in your browser after importing car_marketplace.sql.
 * It will update the 3 demo accounts with proper bcrypt password hashes.
 *
 * URL:  http://localhost/car_marketplace/database/seed_passwords.php
 * Then: DELETE this file (or restrict access to it).
 */

require_once __DIR__ . '/../config/db.php';

$accounts = [
    ['email' => 'admin@buycars.com',   'password' => 'Admin@123'],
    ['email' => 'sagar@example.com',   'password' => 'User@123'],
    ['email' => 'nisha@example.com',   'password' => 'User@123'],
];

echo '<pre style="font-family:monospace;font-size:14px;padding:30px;">';
echo "BuyCars — Seeding demo account passwords\n";
echo str_repeat('-', 50) . "\n";

$updated = 0;
foreach ($accounts as $acc) {
    $hash = password_hash($acc['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->execute([$hash, $acc['email']]);
    if ($stmt->rowCount() > 0) {
        echo "✓ Updated: {$acc['email']} → password: {$acc['password']}\n";
        $updated++;
    } else {
        echo "✗ Not found: {$acc['email']} (already updated or not imported yet)\n";
    }
}

echo str_repeat('-', 50) . "\n";
echo "Done! $updated account(s) updated.\n\n";
echo "⚠  IMPORTANT: Delete this file after running it.\n";
echo '</pre>';
