<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('SITE_NAME', 'BuyCars');
define('SITE_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/')); // fallback, overridden by BASE_URL below when needed
define('BASE_URL', '/'); // if hosted in a subfolder e.g. /car_marketplace/ , change this to that path
define('UPLOAD_DIR', __DIR__ . '/../uploads/cars/');
define('UPLOAD_URL', 'uploads/cars/');

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../includes/functions.php';
