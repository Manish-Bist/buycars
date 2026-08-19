<?php
require_once __DIR__ . '/config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(BASE_URL . 'cars.php');

$car_id  = (int)($_POST['car_id'] ?? 0);

if (!is_logged_in()) {
    set_flash('error', 'Please login or register first so the seller can reply to you.');
    redirect(BASE_URL . 'login.php');
}

csrf_verify();

$phone   = clean($_POST['phone'] ?? '');
$message = clean($_POST['message'] ?? '');

$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$car_id]);
$car = $stmt->fetch();

if (!$car || !$message) {
    set_flash('error', 'Could not send your message. Please try again.');
    redirect(BASE_URL . 'car-details.php?id=' . $car_id);
}

if ($car['seller_id'] == current_user_id()) {
    set_flash('error', 'You cannot send an inquiry on your own listing.');
    redirect(BASE_URL . 'car-details.php?id=' . $car_id);
}

$name  = $_SESSION['user_name'];
$email_stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
$email_stmt->execute([current_user_id()]);
$email = $email_stmt->fetchColumn();
$buyer_id = current_user_id();

$stmt = $pdo->prepare("INSERT INTO inquiries (car_id, buyer_id, seller_id, name, email, phone, message) VALUES (?,?,?,?,?,?,?)");
$stmt->execute([$car_id, $buyer_id, $car['seller_id'], $name, $email, $phone, $message]);

set_flash('success', 'Your message has been sent to the seller!');
redirect(BASE_URL . 'car-details.php?id=' . $car_id);
