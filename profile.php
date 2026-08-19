<?php
require_once __DIR__ . '/config/config.php';
require_login();
$page_title = 'My Profile';

$uid = current_user_id();
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$uid]);
$user = $stmt->fetch();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? '';

    if ($action === 'update_info') {
        $name  = clean($_POST['name'] ?? '');
        $phone = clean($_POST['phone'] ?? '');
        if (!$name) $errors[] = 'Name is required.';

        if (!$errors) {
            $pdo->prepare("UPDATE users SET name = ?, phone = ? WHERE id = ?")->execute([$name, $phone, $uid]);
            $_SESSION['user_name'] = $name;
            set_flash('success', 'Profile updated successfully.');
            redirect(BASE_URL . 'profile.php');
        }
    }

    if ($action === 'change_password') {
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (!password_verify($current, $user['password'])) $errors[] = 'Current password is incorrect.';
        if (strlen($new) < 6) $errors[] = 'New password must be at least 6 characters.';
        if ($new !== $confirm) $errors[] = 'New passwords do not match.';

        if (!$errors) {
            $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([password_hash($new, PASSWORD_DEFAULT), $uid]);
            set_flash('success', 'Password changed successfully.');
            redirect(BASE_URL . 'profile.php');
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<section class="page-banner" data-aos="fade-in">
    <h1>My <span>Profile</span></h1>
</section>

<section class="form-section" data-aos="fade-up">
    <?php if ($errors): ?>
        <div class="alert alert-error" style="max-width:700px;margin:0 auto 2rem;"><ul><?php foreach ($errors as $e) echo '<li>' . clean($e) . '</li>'; ?></ul></div>
    <?php endif; ?>

    <div class="profile-grid">
        <div class="form-card">
            <h3><i class='bx bx-user'></i> Basic Information</h3>
            <form method="POST" class="car-form">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="update_info">
                <div class="form-field full">
                    <label>Full Name</label>
                    <input type="text" name="name" value="<?= clean($user['name']) ?>" required>
                </div>
                <div class="form-field full">
                    <label>Email</label>
                    <input type="email" value="<?= clean($user['email']) ?>" disabled>
                </div>
                <div class="form-field full">
                    <label>Phone</label>
                    <input type="text" name="phone" value="<?= clean($user['phone']) ?>">
                </div>
                <button type="submit" class="btn full-width">Save Changes</button>
            </form>
        </div>

        <div class="form-card">
            <h3><i class='bx bx-lock-alt'></i> Change Password</h3>
            <form method="POST" class="car-form">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="change_password">
                <div class="form-field full">
                    <label>Current Password</label>
                    <input type="password" name="current_password" required>
                </div>
                <div class="form-field full">
                    <label>New Password</label>
                    <input type="password" name="new_password" required>
                </div>
                <div class="form-field full">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn full-width">Update Password</button>
            </form>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
