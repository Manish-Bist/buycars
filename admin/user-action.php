<?php
require_once __DIR__ . '/../config/config.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(BASE_URL . 'admin/users.php');
csrf_verify();

$user_id = (int)($_POST['user_id'] ?? 0);
$action  = clean($_POST['action'] ?? '');

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'user'");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    set_flash('error', 'User not found.');
    redirect(BASE_URL . 'admin/users.php');
}

switch ($action) {
    case 'block':
        $pdo->prepare("UPDATE users SET status='blocked' WHERE id=?")->execute([$user_id]);
        set_flash('success', $user['name'] . ' has been blocked.');
        break;
    case 'unblock':
        $pdo->prepare("UPDATE users SET status='active' WHERE id=?")->execute([$user_id]);
        set_flash('success', $user['name'] . ' has been unblocked.');
        break;
    case 'delete':
        // clean up uploaded images for all of this user's cars first
        $carImgs = $pdo->prepare("SELECT image_path FROM car_images WHERE car_id IN (SELECT id FROM cars WHERE seller_id = ?)");
        $carImgs->execute([$user_id]);
        foreach ($carImgs->fetchAll(PDO::FETCH_COLUMN) as $path) {
            $full = __DIR__ . '/../' . $path;
            if (strpos($path, 'uploads/cars/') === 0 && file_exists($full)) @unlink($full);
        }
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$user_id]);
        set_flash('success', $user['name'] . ' and all their listings have been deleted.');
        break;
    default:
        set_flash('error', 'Unknown action.');
}

redirect(BASE_URL . 'admin/users.php');
