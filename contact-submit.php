<?php
require_once __DIR__ . '/config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(BASE_URL . 'index.php');
csrf_verify();

$name    = clean($_POST['name'] ?? '');
$email   = clean($_POST['email'] ?? '');
$subject = clean($_POST['subject'] ?? '');
$message = clean($_POST['message'] ?? '');

if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$message) {
    set_flash('error', 'Please fill in your name, a valid email and a message.');
    redirect(BASE_URL . 'index.php#contact');
}

$stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?,?,?,?)");
$stmt->execute([$name, $email, $subject, $message]);

set_flash('success', 'Thanks for reaching out! We will get back to you soon.');
redirect(BASE_URL . 'index.php#contact');
