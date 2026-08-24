<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('SITE_NAME', 'BuyCars');

/* Auto-detect BASE_URL — works for root or subfolder installs */
if (!defined('BASE_URL')) {
    $configPath = str_replace('\\', '/', dirname(__DIR__));
    $docRoot    = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']));
    $subPath    = str_replace($docRoot, '', $configPath);
    $subPath    = rtrim(str_replace('\\', '/', $subPath), '/') . '/';
    define('BASE_URL', $subPath ?: '/');
}

define('UPLOAD_DIR', __DIR__ . '/../uploads/cars/');
define('UPLOAD_URL', 'uploads/cars/');

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../includes/functions.php';
