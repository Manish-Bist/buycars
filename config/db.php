<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'car_marketplace');
define('DB_USER', 'root');
define('DB_PASS', '');   // XAMPP default has no password — leave empty

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
    // Stop the page loader immediately then show the error
    echo '<!DOCTYPE html><html><head>
    <meta charset="UTF-8">
    <title>Database Error</title>
    <style>
      body{font-family:Arial,sans-serif;background:#0a0a1a;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;}
      .box{background:#fff;border-radius:12px;padding:40px;max-width:580px;width:90%;}
      h2{color:#c0392b;margin:0 0 16px;}
      code{background:#f5f5f5;padding:2px 6px;border-radius:4px;font-size:13px;}
      ol{margin:16px 0;padding-left:20px;line-height:2;}
      .err{background:#fdecea;border-left:4px solid #e74c3c;padding:12px 16px;border-radius:4px;font-size:13px;color:#c0392b;margin-top:16px;}
    </style>
    </head><body>
    <div class="box">
      <h2>&#9888; Database Connection Failed</h2>
      <p>The site cannot connect to MySQL. Follow these steps:</p>
      <ol>
        <li>Open <strong>XAMPP Control Panel</strong></li>
        <li>Make sure <strong>MySQL</strong> is running (green)</li>
        <li>Open <a href="http://localhost/phpmyadmin" target="_blank">phpMyAdmin</a></li>
        <li>Create a database named <code>car_marketplace</code></li>
        <li>Import <code>database/car_marketplace.sql</code></li>
        <li>Run <code>database/seed_passwords.php</code> once</li>
      </ol>
      <div class="err"><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</div>
    </div>
    </body></html>';
    exit;
}
