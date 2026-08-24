<?php
require_once __DIR__ . '/../config/config.php';
require_admin();
$page_title = 'All Inquiries';

$rows = $pdo->query("SELECT inquiries.*, cars.title AS car_title, cars.id AS car_id, seller.name AS seller_name
                      FROM inquiries
                      JOIN cars ON cars.id = inquiries.car_id
                      JOIN users seller ON seller.id = inquiries.seller_id
                      ORDER BY inquiries.created_at DESC")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<?php if (!$rows): ?>
    <div class="empty-state" data-aos="fade-up">&#128172;<h3>No inquiries yet</h3></div>
<?php else: ?>
<div class="table-wrap" data-aos="fade-up">
    <table class="data-table">
        <thead><tr><th>Buyer</th><th>Car</th><th>Seller</th><th>Message</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><?= clean($r['name']) ?><br><small class="muted"><?= clean($r['email']) ?></small></td>
                <td><a href="<?= BASE_URL ?>car-details.php?id=<?= $r['car_id'] ?>" target="_blank"><?= clean($r['car_title']) ?></a></td>
                <td><?= clean($r['seller_name']) ?></td>
                <td class="truncate-cell"><?= clean($r['message']) ?></td>
                <td><?= status_badge($r['status']) ?></td>
                <td><?= time_ago($r['created_at']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
