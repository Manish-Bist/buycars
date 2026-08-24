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
        <a href="<?= BASE_URL ?>admin/index.php" class="<?= $current === 'index.php' ? 'active' : '' ?>">
            &#9632; Dashboard
        </a>
        <a href="<?= BASE_URL ?>admin/cars.php" class="<?= $current === 'cars.php' ? 'active' : '' ?>">
            &#9632; Manage Cars
            <?php if ($pendingCount > 0): ?><span class="nav-badge"><?= $pendingCount ?></span><?php endif; ?>
        </a>
        <a href="<?= BASE_URL ?>admin/users.php" class="<?= $current === 'users.php' ? 'active' : '' ?>">
            &#9632; Manage Users
        </a>
        <a href="<?= BASE_URL ?>admin/inquiries.php" class="<?= $current === 'inquiries.php' ? 'active' : '' ?>">
            &#9632; Inquiries
        </a>
        <a href="<?= BASE_URL ?>admin/messages.php" class="<?= $current === 'messages.php' ? 'active' : '' ?>">
            &#9632; Contact Messages
            <?php if ($newMsgCount > 0): ?><span class="nav-badge"><?= $newMsgCount ?></span><?php endif; ?>
        </a>
        <a href="<?= BASE_URL ?>admin/reviews.php" class="<?= $current === 'reviews.php' ? 'active' : '' ?>">
            &#9632; Testimonials
        </a>
        <div class="admin-nav-divider"></div>
        <a href="<?= BASE_URL ?>index.php" target="_blank">&#8599; Visit Website</a>
        <a href="<?= BASE_URL ?>admin/logout.php" class="text-danger">&#8594; Logout</a>
    </nav>
</aside>
