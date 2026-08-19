<?php
$current = basename($_SERVER['PHP_SELF']);
$pendingCount = $pdo->query("SELECT COUNT(*) FROM cars WHERE status='pending'")->fetchColumn();
$newMsgCount  = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read=0")->fetchColumn();
?>
<aside class="admin-sidebar" id="adminSidebar">
    <div class="admin-logo">
        <a href="<?= BASE_URL ?>admin/index.php"><span>Buy</span>Cars <small>admin</small></a>
    </div>
    <nav class="admin-nav">
        <a href="<?= BASE_URL ?>admin/index.php" class="<?= $current === 'index.php' ? 'active' : '' ?>"><i class='bx bxs-dashboard'></i> Dashboard</a>
        <a href="<?= BASE_URL ?>admin/cars.php" class="<?= $current === 'cars.php' ? 'active' : '' ?>">
            <i class='bx bx-car'></i> Manage Cars
            <?php if ($pendingCount > 0): ?><span class="nav-badge"><?= $pendingCount ?></span><?php endif; ?>
        </a>
        <a href="<?= BASE_URL ?>admin/users.php" class="<?= $current === 'users.php' ? 'active' : '' ?>"><i class='bx bx-group'></i> Manage Users</a>
        <a href="<?= BASE_URL ?>admin/inquiries.php" class="<?= $current === 'inquiries.php' ? 'active' : '' ?>"><i class='bx bx-message-dots'></i> Inquiries</a>
        <a href="<?= BASE_URL ?>admin/messages.php" class="<?= $current === 'messages.php' ? 'active' : '' ?>">
            <i class='bx bx-envelope'></i> Contact Messages
            <?php if ($newMsgCount > 0): ?><span class="nav-badge"><?= $newMsgCount ?></span><?php endif; ?>
        </a>
        <a href="<?= BASE_URL ?>admin/reviews.php" class="<?= $current === 'reviews.php' ? 'active' : '' ?>"><i class='bx bx-star'></i> Testimonials</a>
        <div class="admin-nav-divider"></div>
        <a href="<?= BASE_URL ?>index.php"><i class='bx bx-globe'></i> Visit Website</a>
        <a href="<?= BASE_URL ?>admin/logout.php" class="text-danger"><i class='bx bx-log-out'></i> Logout</a>
    </nav>
</aside>
