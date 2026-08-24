<?php
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? clean($page_title) . ' — ' . SITE_NAME : SITE_NAME ?></title>

    <!-- Core styles (local, always work) -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/app.css">

    <!-- CDN styles loaded with media trick so they don't block render -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" media="print" onload="this.media='all'">

    <!-- Fallback icons if CDN fails -->
    <style>
    .bx,.fas,.far,.fab,.fa{font-family:inherit;}
    </style>

    <!-- Pass PHP BASE_URL to JavaScript -->
    <script>window.BASE_URL = '<?= BASE_URL ?>';</script>
</head>
<body>

<!-- Page loader - hides on DOMContentLoaded (NOT window.load) -->
<div class="page-loader" id="pageLoader"><div class="loader-ring"></div></div>

<header class="header">
    <div id="menu-btn" class="fas fa-bars"></div>
    <a href="<?= BASE_URL ?>index.php" class="logo"><span>Buy</span>Cars</a>
    <nav class="navbar">
        <a href="<?= BASE_URL ?>index.php">home</a>
        <a href="<?= BASE_URL ?>cars.php">vehicles</a>
        <a href="<?= BASE_URL ?>index.php#services">gallery</a>
        <a href="<?= BASE_URL ?>index.php#reviews">reviews</a>
        <a href="<?= BASE_URL ?>index.php#contact">contact</a>
        <?php if (is_logged_in()): ?>
            <a href="<?= BASE_URL ?>sell-car.php" class="nav-highlight">+ sell your car</a>
        <?php endif; ?>
    </nav>
    <div id="login-btn">
        <?php if (is_logged_in()): ?>
            <div class="user-menu">
                <button class="btn user-menu-btn"><?= clean(explode(' ', $_SESSION['user_name'])[0]) ?> &#9660;</button>
                <div class="user-dropdown">
                    <a href="<?= BASE_URL ?>dashboard.php">Dashboard</a>
                    <a href="<?= BASE_URL ?>my-listings.php">My Listings</a>
                    <a href="<?= BASE_URL ?>wishlist.php">Wishlist</a>
                    <a href="<?= BASE_URL ?>inquiries.php">Inquiries</a>
                    <a href="<?= BASE_URL ?>profile.php">Profile</a>
                    <?php if (is_admin()): ?>
                        <a href="<?= BASE_URL ?>admin/index.php">Admin Panel</a>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>logout.php" class="text-danger">Logout</a>
                </div>
            </div>
        <?php else: ?>
            <a href="<?= BASE_URL ?>login.php" class="btn">login</a>
            <a href="<?= BASE_URL ?>register.php" class="btn btn-outline">register</a>
        <?php endif; ?>
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
