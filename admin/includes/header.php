<?php
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? clean($page_title) . ' — Admin' : 'Admin Panel' ?> | <?= SITE_NAME ?></title>

    <!-- Local styles -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/app.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>admin/assets/admin.css">

    <!-- CDN styles non-blocking -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" media="print" onload="this.media='all'">

    <script>window.BASE_URL = '<?= BASE_URL ?>';</script>
</head>
<body class="admin-body">

<div class="admin-wrap">
    <?php require __DIR__ . '/sidebar.php'; ?>

    <main class="admin-main">
        <header class="admin-topbar">
            <button id="sidebarToggle" class="icon-btn">&#9776;</button>
            <h2><?= clean($page_title ?? 'Dashboard') ?></h2>
            <div class="admin-topbar-right">
                <a href="<?= BASE_URL ?>index.php" target="_blank" class="btn btn-outline btn-sm">View Site</a>
                <div class="admin-user"><?= clean($_SESSION['user_name'] ?? 'Admin') ?></div>
            </div>
        </header>

        <?php if ($flash): ?>
        <div class="toast-container">
            <div class="toast toast-<?= $flash['type'] ?>" id="flashToast">
                <?= $flash['type'] === 'success' ? '&#10003;' : '&#9888;' ?>
                <span><?= clean($flash['message']) ?></span>
                <button type="button" class="toast-close" onclick="this.parentElement.remove()">&times;</button>
            </div>
        </div>
        <?php endif; ?>

        <div class="admin-content">
