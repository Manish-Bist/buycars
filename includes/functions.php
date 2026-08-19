<?php
/** Sanitize user input */
function clean($str) {
    return htmlspecialchars(trim($str ?? ''), ENT_QUOTES, 'UTF-8');
}

/** Redirect helper */
function redirect($url) {
    header("Location: $url");
    exit;
}

/** Flash messages (stored in session, shown once) */
function set_flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}
function get_flash() {
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/** Auth helpers */
function is_logged_in() {
    return !empty($_SESSION['user_id']);
}
function is_admin() {
    return is_logged_in() && ($_SESSION['role'] ?? '') === 'admin';
}
function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}
function require_login() {
    if (!is_logged_in()) {
        set_flash('error', 'Please login to continue.');
        redirect(BASE_URL . 'login.php');
    }
}
function require_admin() {
    if (!is_admin()) {
        redirect(BASE_URL . 'admin/login.php');
    }
}

/** CSRF token */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}
function csrf_verify() {
    $token = $_POST['csrf_token'] ?? '';
    if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        die('Invalid request (CSRF check failed). Please go back and try again.');
    }
}

/** Format price with commas */
function format_price($price) {
    return '$' . number_format((float)$price, 0);
}

/** Time ago helper */
function time_ago($datetime) {
    $diff = time() - strtotime($datetime);
    if ($diff < 60) return 'just now';
    if ($diff < 3600) return floor($diff / 60) . ' min ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hr ago';
    if ($diff < 2592000) return floor($diff / 86400) . ' day(s) ago';
    return date('d M Y', strtotime($datetime));
}

/** Get primary image for a car, fallback to placeholder */
function car_primary_image($pdo, $car_id) {
    $stmt = $pdo->prepare("SELECT image_path FROM car_images WHERE car_id = ? ORDER BY is_primary DESC, id ASC LIMIT 1");
    $stmt->execute([$car_id]);
    $img = $stmt->fetchColumn();
    return $img ? BASE_URL . $img : BASE_URL . 'assets/image/car-1.png';
}

/** Handle single image upload, returns relative path or false */
function handle_image_upload($file) {
    if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) return false;

    $allowed = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!array_key_exists($ext, $allowed)) return false;
    if ($file['size'] > 5 * 1024 * 1024) return false; // 5MB max

    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }

    $filename = 'car_' . uniqid() . '_' . mt_rand(1000, 9999) . '.' . $ext;
    $destination = UPLOAD_DIR . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return UPLOAD_URL . $filename;
    }
    return false;
}

/** Small status badge helper (used in admin + user dashboards) */
function status_badge($status) {
    $map = [
        'pending'  => 'badge-pending',
        'approved' => 'badge-approved',
        'rejected' => 'badge-rejected',
        'sold'     => 'badge-sold',
        'active'   => 'badge-approved',
        'blocked'  => 'badge-rejected',
        'new'      => 'badge-pending',
        'read'     => 'badge-approved',
        'replied'  => 'badge-sold',
    ];
    $class = $map[$status] ?? 'badge-pending';
    return '<span class="status-badge ' . $class . '">' . ucfirst($status) . '</span>';
}
