<?php
require_once __DIR__ . '/../config/config.php';
session_unset();
session_destroy();
session_start();
redirect(BASE_URL . 'admin/login.php');
