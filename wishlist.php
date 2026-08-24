<?php
require_once __DIR__ . '/config/config.php';
require_login();
$page_title = 'My Wishlist';

$stmt = $pdo->prepare("SELECT cars.* FROM wishlist JOIN cars ON cars.id = wishlist.car_id WHERE wishlist.user_id = ? ORDER BY wishlist.created_at DESC");
$stmt->execute([current_user_id()]);
$cars = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>
<section class="page-banner" data-aos="fade-in">
    <h1>My <span>Wishlist</span></h1>
    <p><?= count($cars) ?> saved car<?= count($cars) == 1 ? '' : 's' ?></p>
</section>

<section class="cars-browse single-col">
    <div class="cars-grid-wrap">
        <?php if (!$cars): ?>
            <div class="empty-state" data-aos="fade-up">
                &#9825;
                <h3>Your wishlist is empty</h3>
                <p>Browse cars and tap the heart icon to save your favourites.</p>
                <a href="cars.php" class="btn">Browse Cars</a>
            </div>
        <?php else: ?>
            <div class="cars-grid">
                <?php foreach ($cars as $car): ?>
                    <div class="car-card" data-aos="fade-up">
                        <div class="car-card-img">
                            <img src="<?= car_primary_image($pdo, $car['id']) ?>" alt="<?= clean($car['title']) ?>">
                            <span class="car-tag"><?= status_badge($car['status']) ?></span>
                            <button class="wish-btn active" data-car="<?= $car['id'] ?>" title="Remove from wishlist">&#9829;#9829;</button>
                        </div>
                        <div class="car-card-body">
                            <h3><?= clean($car['title']) ?></h3>
                            <div class="car-price"><?= format_price($car['price']) ?></div>
                            <a href="car-details.php?id=<?= $car['id'] ?>" class="btn full-width">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
