<?php
require_once __DIR__ . '/config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(BASE_URL . 'index.php');
csrf_verify();

$email = clean($_POST['email'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    set_flash('error', 'Please enter a valid email address.');
    redirect(BASE_URL . 'index.php');
}

try {
    $stmt = $pdo->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?)");
    $stmt->execute([$email]);
    set_flash('success', 'Subscribed successfully! Stay tuned for updates.');
} catch (PDOException $e) {
    set_flash('error', 'This email is already subscribed.');
}

redirect(BASE_URL . 'index.php');
