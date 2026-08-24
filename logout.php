<?php
require_once __DIR__ . '/config/config.php';
session_unset();
session_destroy();
session_start();
session_regenerate_id(true);
set_flash('success', 'You have been logged out successfully.');
redirect(BASE_URL . 'login.php');
