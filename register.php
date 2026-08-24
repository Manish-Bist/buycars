<?php
require_once __DIR__ . '/config/config.php';
$page_title = 'Register';

if (is_logged_in()) redirect(BASE_URL . 'dashboard.php');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $name     = clean($_POST['name'] ?? '');
    $email    = clean($_POST['email'] ?? '');
    $phone    = clean($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (!$name) $errors[] = 'Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';

    if (!$errors) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'An account with this email already exists.';
        }
    }

    if (!$errors) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, role, status) VALUES (?,?,?,?, 'user','active')");
        $stmt->execute([$name, $email, $hash, $phone]);

        $_SESSION['user_id']   = $pdo->lastInsertId();
        $_SESSION['user_name'] = $name;
        $_SESSION['role']      = 'user';

        set_flash('success', 'Welcome to BuyCars, ' . $name . '! Your account has been created.');
        redirect(BASE_URL . 'dashboard.php');
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<section class="auth-section" data-aos="fade-up">
    <div class="auth-card">
        <div class="auth-side">
            <img src="assets/image/homecar1.png" alt="">
            <h2>Join <span>BuyCars</span></h2>
            <p>Create an account to list your car for sale, save favourites, and message sellers directly.</p>
        </div>
        <div class="auth-form-wrap">
            <h3>create account</h3>

            <?php if ($errors): ?>
                <div class="alert alert-error">
                    <ul><?php foreach ($errors as $e) echo '<li>' . clean($e) . '</li>'; ?></ul>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form" novalidate>
                <?= csrf_field() ?>
                <div class="input-group">
                    <span class="field-icon">&#128100;#128100;</span>
                    <input type="text" name="name" placeholder="full name" value="<?= clean($_POST['name'] ?? '') ?>" required>
                </div>
                <div class="input-group">
                    <span class="field-icon">&#9993;#9993;</span>
                    <input type="email" name="email" placeholder="email address" value="<?= clean($_POST['email'] ?? '') ?>" required>
                </div>
                <div class="input-group">
                    <span class="field-icon">&#128222;#128222;</span>
                    <input type="text" name="phone" placeholder="phone number (optional)" value="<?= clean($_POST['phone'] ?? '') ?>">
                </div>
                <div class="input-group">
                    <span class="field-icon">&#128274;#128274;</span>
                    <input type="password" name="password" placeholder="password" required>
                </div>
                <div class="input-group">
                    <span class="field-icon">&#128274;#128274;</span>
                    <input type="password" name="confirm_password" placeholder="confirm password" required>
                </div>
                <button type="submit" class="btn full-width">create account</button>
            </form>
            <p class="auth-switch">Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
