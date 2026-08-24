<?php
require_once __DIR__ . '/../config/config.php';
session_unset();
session_destroy();
session_start();
session_regenerate_id(true);
redirect(BASE_URL . 'admin/login.php');
