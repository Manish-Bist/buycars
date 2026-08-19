<?php
require_once __DIR__ . '/../config/config.php';

if (is_admin()) redirect(BASE_URL . 'admin/index.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $email    = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        $error = 'Incorrect admin email or password.';
    } else {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role']      = 'admin';
        redirect(BASE_URL . 'admin/index.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/app.css">
</head>
<body class="admin-login-body">
    <div class="admin-login-card" data-aos="zoom-in">
        <div class="admin-login-icon"><i class='bx bxs-shield-quarter'></i></div>
        <h2>Admin Panel</h2>
        <p class="muted">Sign in to manage BuyCars</p>

        <?php if ($error): ?><div class="alert alert-error"><?= clean($error) ?></div><?php endif; ?>

        <form method="POST" class="auth-form">
            <?= csrf_field() ?>
            <div class="input-group">
                <i class='bx bx-envelope'></i>
                <input type="email" name="email" placeholder="admin email" required>
            </div>
            <div class="input-group">
                <i class='bx bx-lock-alt'></i>
                <input type="password" name="password" placeholder="password" required>
            </div>
            <button type="submit" class="btn full-width">Login</button>
        </form>
        <p class="auth-switch"><a href="<?= BASE_URL ?>index.php">&larr; Back to website</a></p>
        <p class="muted" style="font-size:1.2rem;margin-top:1rem;">Default: admin@buycars.com / Admin@123</p>
    </div>
</body>
</html>
