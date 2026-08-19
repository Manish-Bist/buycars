<?php
require_once __DIR__ . '/config/config.php';
require_login();
$page_title = 'Inquiries';

$uid = current_user_id();
$tab = $_GET['tab'] ?? 'received';

if ($tab === 'sent') {
    $stmt = $pdo->prepare("SELECT inquiries.*, cars.title AS car_title, cars.id AS car_id, users.name AS seller_name
                            FROM inquiries JOIN cars ON cars.id = inquiries.car_id JOIN users ON users.id = inquiries.seller_id
                            WHERE inquiries.buyer_id = ? ORDER BY inquiries.created_at DESC");
} else {
    $stmt = $pdo->prepare("SELECT inquiries.*, cars.title AS car_title, cars.id AS car_id, users.name AS buyer_name
                            FROM inquiries JOIN cars ON cars.id = inquiries.car_id JOIN users ON users.id = inquiries.buyer_id
                            WHERE inquiries.seller_id = ? ORDER BY inquiries.created_at DESC");
    // mark as read
    $pdo->prepare("UPDATE inquiries SET status='read' WHERE seller_id = ? AND status='new'")->execute([$uid]);
}
$stmt->execute([$uid]);
$rows = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>
<section class="page-banner" data-aos="fade-in">
    <h1>My <span>Inquiries</span></h1>
</section>

<section class="dashboard-section" data-aos="fade-up">
    <div class="tabs">
        <a href="inquiries.php?tab=received" class="tab-link <?= $tab !== 'sent' ? 'active' : '' ?>">Received (as Seller)</a>
        <a href="inquiries.php?tab=sent" class="tab-link <?= $tab === 'sent' ? 'active' : '' ?>">Sent (as Buyer)</a>
    </div>

    <?php if (!$rows): ?>
        <div class="empty-state" data-aos="fade-up">
            <i class='bx bx-message-dots'></i>
            <h3>No inquiries here yet</h3>
            <p><?= $tab === 'sent' ? 'Messages you send to sellers will show up here.' : 'When buyers contact you about your cars, it will show here.' ?></p>
        </div>
    <?php else: ?>
        <div class="inquiry-list">
            <?php foreach ($rows as $r): ?>
                <div class="inquiry-card" data-aos="fade-up">
                    <div class="inquiry-header">
                        <div>
                            <strong><?= $tab === 'sent' ? clean($r['seller_name']) : clean($r['buyer_name']) ?></strong>
                            <span class="muted"> about </span>
                            <a href="car-details.php?id=<?= $r['car_id'] ?>"><?= clean($r['car_title']) ?></a>
                        </div>
                        <?= status_badge($r['status']) ?>
                    </div>
                    <p class="inquiry-msg"><?= nl2br(clean($r['message'])) ?></p>
                    <div class="inquiry-footer">
                        <span><i class='bx bx-envelope'></i> <?= clean($r['email']) ?></span>
                        <?php if ($r['phone']): ?><span><i class='bx bx-phone'></i> <?= clean($r['phone']) ?></span><?php endif; ?>
                        <span class="muted"><?= time_ago($r['created_at']) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
