<?php
require_once __DIR__ . '/../config/config.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(BASE_URL . 'admin/cars.php');
csrf_verify();

$car_id = (int)($_POST['car_id'] ?? 0);
$action = clean($_POST['action'] ?? '');

$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$car_id]);
$car = $stmt->fetch();

if (!$car) {
    set_flash('error', 'Car listing not found.');
    redirect(BASE_URL . 'admin/cars.php');
}

switch ($action) {
    case 'approve':
        $pdo->prepare("UPDATE cars SET status='approved' WHERE id=?")->execute([$car_id]);
        set_flash('success', 'Listing approved and is now live on the site.');
        break;
    case 'reject':
        $pdo->prepare("UPDATE cars SET status='rejected' WHERE id=?")->execute([$car_id]);
        set_flash('success', 'Listing rejected.');
        break;
    case 'sold':
        $pdo->prepare("UPDATE cars SET status='sold' WHERE id=?")->execute([$car_id]);
        set_flash('success', 'Listing marked as sold.');
        break;
    case 'delete':
        $imgs = $pdo->prepare("SELECT image_path FROM car_images WHERE car_id = ?");
        $imgs->execute([$car_id]);
        foreach ($imgs->fetchAll(PDO::FETCH_COLUMN) as $path) {
            $full = __DIR__ . '/../' . $path;
            if (strpos($path, 'uploads/cars/') === 0 && file_exists($full)) @unlink($full);
        }
        $pdo->prepare("DELETE FROM cars WHERE id = ?")->execute([$car_id]);
        set_flash('success', 'Listing deleted permanently.');
        break;
    default:
        set_flash('error', 'Unknown action.');
}

redirect(BASE_URL . 'admin/cars.php');
