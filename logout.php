<?php
require_once __DIR__ . '/config/config.php';
session_unset();
session_destroy();
session_start();
set_flash('success', 'You have been logged out.');
redirect(BASE_URL . 'login.php');
