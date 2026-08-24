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
        $error = 'Incorrect email or password.';
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
    <title>Admin Login — <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/app.css">
    <script>window.BASE_URL = '<?= BASE_URL ?>';</script>
    <style>
    body { margin:0; background: linear-gradient(135deg,#0a0f2c 0%,#0F5298 100%); min-height:100vh; display:flex; align-items:center; justify-content:center; font-family:sans-serif; }
    .login-card { background:#fff; border-radius:1.2rem; padding:4rem; width:100%; max-width:42rem; text-align:center; box-shadow:0 2rem 6rem rgba(0,0,0,.35); }
    .login-icon { font-size:5rem; color:#0F5298; margin-bottom:1rem; }
    .login-card h2 { font-size:2.8rem; color:#1a1a2e; margin-bottom:.4rem; }
    .login-card p.sub { color:#888; font-size:1.4rem; margin-bottom:2rem; }
    .form-wrap { display:flex; flex-direction:column; gap:1.4rem; }
    .field { border:1.5px solid #ddd; border-radius:.6rem; padding:1.2rem 1.6rem; display:flex; align-items:center; gap:1rem; transition:border-color .2s; }
    .field:focus-within { border-color:#0F5298; }
    .field input { border:none; outline:none; font-size:1.5rem; color:#333; width:100%; }
    .login-btn { background:#0F5298; color:#fff; border:none; border-radius:.6rem; padding:1.3rem; font-size:1.6rem; cursor:pointer; width:100%; transition:background .2s; }
    .login-btn:hover { background:#0a3d7a; }
    .back-link { display:block; margin-top:1.5rem; font-size:1.4rem; color:#888; }
    .back-link a { color:#0F5298; }
    .alert-error { background:#fdecea; color:#c0392b; border-left:4px solid #e74c3c; padding:1rem 1.4rem; border-radius:.5rem; font-size:1.4rem; margin-bottom:1.5rem; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-icon">&#128737;</div>
        <h2>Admin Panel</h2>
        <p class="sub">Sign in to manage BuyCars</p>

        <?php if ($error): ?>
            <div class="alert-error"><?= clean($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="form-wrap">
            <?= csrf_field() ?>
            <div class="field">
                <span>&#9993;</span>
                <input type="email" name="email" placeholder="admin email" required>
            </div>
            <div class="field">
                <span>&#128274;</span>
                <input type="password" name="password" placeholder="password" required>
            </div>
            <button type="submit" class="login-btn">Login</button>
        </form>
        <p class="back-link"><a href="<?= BASE_URL ?>index.php">&larr; Back to website</a></p>
    </div>
</body>
</html>
