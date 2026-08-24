<?php
require_once __DIR__ . '/config/config.php';
$page_title = 'Login';

if (is_logged_in()) redirect(BASE_URL . 'dashboard.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $email    = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        $error = 'Incorrect email or password.';
    } elseif ($user['status'] === 'blocked') {
        $error = 'Your account has been blocked. Please contact support.';
    } else {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role']      = $user['role'];

        set_flash('success', 'Welcome back, ' . $user['name'] . '!');
        redirect($user['role'] === 'admin' ? BASE_URL . 'admin/index.php' : BASE_URL . 'dashboard.php');
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<section class="auth-section" data-aos="fade-up">
    <div class="auth-card">
        <div class="auth-side">
            <img src="assets/image/homecar1.png" alt="">
            <h2>Welcome <span>Back</span></h2>
            <p>Login to manage your listings, wishlist and inquiries.</p>
        </div>
        <div class="auth-form-wrap">
            <h3>user login</h3>

            <?php if ($error): ?>
                <div class="alert alert-error"><?= clean($error) ?></div>
            <?php endif; ?>

            <form method="POST" class="auth-form" novalidate>
                <?= csrf_field() ?>
                <div class="input-group">
                    <span class="field-icon">&#9993;#9993;</span>
                    <input type="email" name="email" placeholder="email address" value="<?= clean($_POST['email'] ?? '') ?>" required>
                </div>
                <div class="input-group">
                    <span class="field-icon">&#128274;#128274;</span>
                    <input type="password" name="password" placeholder="password" required>
                </div>
                <button type="submit" class="btn full-width">login</button>
            </form>
            <p class="auth-switch">Don't have an account? <a href="register.php">Create one</a></p>
            <p class="auth-switch"><a href="admin/login.php">Admin login &rarr;</a></p>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
