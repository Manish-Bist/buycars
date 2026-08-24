<?php
require_once __DIR__ . '/config/config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'method_not_allowed']);
    exit;
}

if (!is_logged_in()) {
    echo json_encode(['ok' => false, 'error' => 'login_required']);
    exit;
}

$car_id  = (int)($_POST['car_id'] ?? 0);
$user_id = current_user_id();

if (!$car_id) {
    echo json_encode(['ok' => false, 'error' => 'invalid_car']);
    exit;
}

// Verify car exists
$check = $pdo->prepare("SELECT id FROM cars WHERE id = ? AND status = 'approved'");
$check->execute([$car_id]);
if (!$check->fetch()) {
    echo json_encode(['ok' => false, 'error' => 'not_found']);
    exit;
}

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
