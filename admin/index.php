<?php
require_once __DIR__ . '/../config/config.php';
require_admin();
$page_title = 'Dashboard';

$totalCars    = $pdo->query("SELECT COUNT(*) FROM cars")->fetchColumn();
$pendingCars  = $pdo->query("SELECT COUNT(*) FROM cars WHERE status='pending'")->fetchColumn();
$approvedCars = $pdo->query("SELECT COUNT(*) FROM cars WHERE status='approved'")->fetchColumn();
$soldCars     = $pdo->query("SELECT COUNT(*) FROM cars WHERE status='sold'")->fetchColumn();
$totalUsers   = $pdo->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn();
$totalInquiries = $pdo->query("SELECT COUNT(*) FROM inquiries")->fetchColumn();
$totalMessages  = $pdo->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
$totalValue   = $pdo->query("SELECT COALESCE(SUM(price),0) FROM cars WHERE status='approved'")->fetchColumn();

$recentCars = $pdo->query("SELECT cars.*, users.name AS seller_name FROM cars JOIN users ON users.id = cars.seller_id ORDER BY cars.created_at DESC LIMIT 6")->fetchAll();
$brandStats = $pdo->query("SELECT brand, COUNT(*) AS c FROM cars GROUP BY brand ORDER BY c DESC LIMIT 6")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="stats-grid admin-stats">
    <div class="stat-card"><span class="stat-icon-sym">&#128663;</span><div><h3><?= $totalCars ?></h3><p>Total Cars</p></div></div>
    <div class="stat-card"><span class="stat-icon-sym warn">&#9201;</span><div><h3><?= $pendingCars ?></h3><p>Pending Approval</p></div></div>
    <div class="stat-card"><span class="stat-icon-sym success">&#10003;</span><div><h3><?= $approvedCars ?></h3><p>Live Listings</p></div></div>
    <div class="stat-card"><span class="stat-icon-sym">&#127942;</span><div><h3><?= $soldCars ?></h3><p>Cars Sold</p></div></div>
    <div class="stat-card"><span class="stat-icon-sym">&#128101;</span><div><h3><?= $totalUsers ?></h3><p>Registered Users</p></div></div>
    <div class="stat-card"><span class="stat-icon-sym">&#128172;</span><div><h3><?= $totalInquiries ?></h3><p>Total Inquiries</p></div></div>
    <div class="stat-card"><span class="stat-icon-sym">&#9993;</span><div><h3><?= $totalMessages ?></h3><p>Contact Messages</p></div></div>
    <div class="stat-card"><span class="stat-icon-sym success">&#36;</span><div><h3><?= format_price($totalValue) ?></h3><p>Live Inventory Value</p></div></div>
</div>

<?php if ($pendingCars > 0): ?>
<div class="alert alert-warn" data-aos="fade-up">
    &#9888; You have <strong><?= $pendingCars ?></strong> car listing(s) waiting for approval.
    <a href="cars.php?status=pending">Review now &rarr;</a>
</div>
<?php endif; ?>

<div class="dash-columns" data-aos="fade-up">
    <div class="dash-col wide">
        <div class="section-toolbar"><h3>Recent Listings</h3><a href="cars.php">Manage all &rarr;</a></div>
        <div class="table-wrap">
            <table class="data-table">
                <thead><tr><th>Photo</th><th>Title</th><th>Seller</th><th>Price</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                <?php foreach ($recentCars as $c): ?>
                    <tr>
                        <td><img class="table-thumb" src="<?= car_primary_image($pdo, $c['id']) ?>" alt=""></td>
                        <td><a href="cars.php?id=<?= $c['id'] ?>"><?= clean($c['title']) ?></a></td>
                        <td><?= clean($c['seller_name']) ?></td>
                        <td><?= format_price($c['price']) ?></td>
                        <td><?= status_badge($c['status']) ?></td>
                        <td><?= time_ago($c['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="dash-col">
        <div class="section-toolbar"><h3>Top Brands</h3></div>
        <ul class="bar-list">
            <?php $max = max(array_column($brandStats, 'c') ?: [1]); ?>
            <?php foreach ($brandStats as $b): ?>
                <li>
                    <span><?= clean($b['brand']) ?></span>
                    <div class="bar-track"><div class="bar-fill" style="width:<?= ($b['c'] / $max) * 100 ?>%"></div></div>
                    <strong><?= $b['c'] ?></strong>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
