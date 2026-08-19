<?php
require_once __DIR__ . '/config/config.php';
header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['ok' => false, 'error' => 'login_required']);
    exit;
}

$car_id = (int)($_POST['car_id'] ?? 0);
$user_id = current_user_id();

$stmt = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND car_id = ?");
$stmt->execute([$user_id, $car_id]);
$existing = $stmt->fetch();

if ($existing) {
    $pdo->prepare("DELETE FROM wishlist WHERE id = ?")->execute([$existing['id']]);
    echo json_encode(['ok' => true, 'action' => 'removed']);
} else {
    $pdo->prepare("INSERT INTO wishlist (user_id, car_id) VALUES (?,?)")->execute([$user_id, $car_id]);
    echo json_encode(['ok' => true, 'action' => 'added']);
}
