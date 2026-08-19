<?php
require_once __DIR__ . '/config/config.php';
require_login();
$page_title = 'Dashboard';

$uid = current_user_id();

$myListings   = $pdo->prepare("SELECT COUNT(*) FROM cars WHERE seller_id = ?");
$myListings->execute([$uid]);
$myListingsCount = $myListings->fetchColumn();

$pending = $pdo->prepare("SELECT COUNT(*) FROM cars WHERE seller_id = ? AND status='pending'");
$pending->execute([$uid]); $pendingCount = $pending->fetchColumn();

$approved = $pdo->prepare("SELECT COUNT(*) FROM cars WHERE seller_id = ? AND status='approved'");
$approved->execute([$uid]); $approvedCount = $approved->fetchColumn();

$sold = $pdo->prepare("SELECT COUNT(*) FROM cars WHERE seller_id = ? AND status='sold'");
$sold->execute([$uid]); $soldCount = $sold->fetchColumn();

$wish = $pdo->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ?");
$wish->execute([$uid]); $wishCount = $wish->fetchColumn();

$inqReceived = $pdo->prepare("SELECT COUNT(*) FROM inquiries WHERE seller_id = ?");
$inqReceived->execute([$uid]); $inqReceivedCount = $inqReceived->fetchColumn();

$recentCars = $pdo->prepare("SELECT * FROM cars WHERE seller_id = ? ORDER BY created_at DESC LIMIT 5");
$recentCars->execute([$uid]);
$recentCars = $recentCars->fetchAll();

$recentInquiries = $pdo->prepare("SELECT inquiries.*, cars.title AS car_title FROM inquiries JOIN cars ON cars.id = inquiries.car_id WHERE inquiries.seller_id = ? ORDER BY inquiries.created_at DESC LIMIT 5");
$recentInquiries->execute([$uid]);
$recentInquiries = $recentInquiries->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>
<section class="page-banner" data-aos="fade-in">
    <h1>Welcome, <span><?= clean(explode(' ', $_SESSION['user_name'])[0]) ?></span></h1>
    <p>Here's what's happening with your account.</p>
</section>

<section class="dashboard-section" data-aos="fade-up">
    <div class="stats-grid">
        <div class="stat-card"><i class='bx bx-car stat-icon'></i><div><h3><?= $myListingsCount ?></h3><p>Total Listings</p></div></div>
        <div class="stat-card"><i class='bx bx-time-five stat-icon warn'></i><div><h3><?= $pendingCount ?></h3><p>Pending Review</p></div></div>
        <div class="stat-card"><i class='bx bx-check-circle stat-icon success'></i><div><h3><?= $approvedCount ?></h3><p>Live Listings</p></div></div>
        <div class="stat-card"><i class='bx bx-trophy stat-icon'></i><div><h3><?= $soldCount ?></h3><p>Cars Sold</p></div></div>
        <div class="stat-card"><i class='bx bx-heart stat-icon danger'></i><div><h3><?= $wishCount ?></h3><p>Wishlist Items</p></div></div>
        <div class="stat-card"><i class='bx bx-message-dots stat-icon'></i><div><h3><?= $inqReceivedCount ?></h3><p>Inquiries Received</p></div></div>
    </div>

    <div class="dash-columns">
        <div class="dash-col">
            <div class="section-toolbar">
                <h3>Recent Listings</h3>
                <a href="my-listings.php">View all &rarr;</a>
            </div>
            <?php if (!$recentCars): ?>
                <p class="muted">No listings yet. <a href="sell-car.php">Sell your first car</a>.</p>
            <?php else: ?>
                <ul class="mini-list">
                    <?php foreach ($recentCars as $c): ?>
                        <li>
                            <img src="<?= car_primary_image($pdo, $c['id']) ?>" alt="">
                            <div class="mini-list-info">
                                <strong><?= clean($c['title']) ?></strong>
                                <span><?= format_price($c['price']) ?></span>
                            </div>
                            <?= status_badge($c['status']) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="dash-col">
            <div class="section-toolbar">
                <h3>Recent Inquiries</h3>
                <a href="inquiries.php">View all &rarr;</a>
            </div>
            <?php if (!$recentInquiries): ?>
                <p class="muted">No inquiries received yet.</p>
            <?php else: ?>
                <ul class="mini-list">
                    <?php foreach ($recentInquiries as $iq): ?>
                        <li>
                            <i class='bx bxs-user-circle mini-avatar'></i>
                            <div class="mini-list-info">
                                <strong><?= clean($iq['name']) ?></strong>
                                <span>about <?= clean($iq['car_title']) ?></span>
                            </div>
                            <small class="muted"><?= time_ago($iq['created_at']) ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
