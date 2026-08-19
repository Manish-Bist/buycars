<?php
/**
 * Database connection (PDO)
 * Update these 4 values to match your MySQL / XAMPP / WAMP setup.
 */
define('DB_HOST', 'localhost');
define('DB_NAME', 'car_marketplace');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('<div style="font-family:sans-serif;max-width:600px;margin:80px auto;padding:30px;background:#fff3f3;border:1px solid #ffb3b3;border-radius:12px;">
        <h2 style="color:#c0392b;">Database connection failed</h2>
        <p>Please make sure MySQL is running and that you imported <code>database/car_marketplace.sql</code>, and check the credentials in <code>config/db.php</code>.</p>
        <p style="color:#888;font-size:13px;">' . htmlspecialchars($e->getMessage()) . '</p>
        </div>');
}
