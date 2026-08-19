<?php
// Expects $page_title to be set by the including page
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? clean($page_title) . ' - ' . SITE_NAME : SITE_NAME ?></title>

    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">

    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/app.css">
</head>
<body>

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
            <a href="<?= BASE_URL ?>sell-car.php" class="nav-highlight"><i class='bx bx-plus-circle'></i> sell your car</a>
        <?php endif; ?>
    </nav>

    <div id="login-btn">
        <?php if (is_logged_in()): ?>
            <div class="user-menu">
                <button class="btn user-menu-btn"><i class='bx bx-user-circle'></i> <?= clean(explode(' ', $_SESSION['user_name'])[0]) ?> <i class='bx bx-chevron-down'></i></button>
                <div class="user-dropdown">
                    <a href="<?= BASE_URL ?>dashboard.php"><i class='bx bx-grid-alt'></i> Dashboard</a>
                    <a href="<?= BASE_URL ?>my-listings.php"><i class='bx bx-car'></i> My Listings</a>
                    <a href="<?= BASE_URL ?>wishlist.php"><i class='bx bx-heart'></i> Wishlist</a>
                    <a href="<?= BASE_URL ?>inquiries.php"><i class='bx bx-message-dots'></i> Inquiries</a>
                    <a href="<?= BASE_URL ?>profile.php"><i class='bx bx-user'></i> Profile</a>
                    <?php if (is_admin()): ?>
                        <a href="<?= BASE_URL ?>admin/index.php"><i class='bx bx-shield-quarter'></i> Admin Panel</a>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>logout.php" class="text-danger"><i class='bx bx-log-out'></i> Logout</a>
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
        <i class='bx <?= $flash['type'] === 'success' ? 'bx-check-circle' : 'bx-error-circle' ?>'></i>
        <span><?= clean($flash['message']) ?></span>
        <button type="button" class="toast-close" onclick="this.parentElement.remove()">&times;</button>
    </div>
</div>
<?php endif; ?>
