<?php
require_once __DIR__ . '/config/config.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(BASE_URL . 'my-listings.php');
csrf_verify();

$car_id = (int)($_POST['car_id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ? AND seller_id = ?");
$stmt->execute([$car_id, current_user_id()]);
$car = $stmt->fetch();

if ($car) {
    // remove uploaded image files from disk
    $imgs = $pdo->prepare("SELECT image_path FROM car_images WHERE car_id = ?");
    $imgs->execute([$car_id]);
    foreach ($imgs->fetchAll(PDO::FETCH_COLUMN) as $path) {
        $full = __DIR__ . '/' . $path;
        if (strpos($path, 'uploads/cars/') === 0 && file_exists($full)) {
            @unlink($full);
        }
    }
    $pdo->prepare("DELETE FROM cars WHERE id = ?")->execute([$car_id]);
    set_flash('success', 'Listing deleted.');
} else {
    set_flash('error', 'Listing not found.');
}

redirect(BASE_URL . 'my-listings.php');
