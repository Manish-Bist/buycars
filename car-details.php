<?php
require_once __DIR__ . '/config/config.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT cars.*, users.name AS seller_name, users.phone AS seller_phone, users.email AS seller_email, users.created_at AS seller_since
                        FROM cars JOIN users ON users.id = cars.seller_id WHERE cars.id = ?");
$stmt->execute([$id]);
$car = $stmt->fetch();

if (!$car || ($car['status'] !== 'approved' && (!is_logged_in() || ($car['seller_id'] != current_user_id() && !is_admin())))) {
    set_flash('error', 'This car listing is not available.');
    redirect(BASE_URL . 'cars.php');
}

// increment view count (only for approved & not the owner)
if ($car['status'] === 'approved' && $car['seller_id'] != current_user_id()) {
    $pdo->prepare("UPDATE cars SET views = views + 1 WHERE id = ?")->execute([$id]);
}

$page_title = $car['title'];

$imgStmt = $pdo->prepare("SELECT * FROM car_images WHERE car_id = ? ORDER BY is_primary DESC, id ASC");
$imgStmt->execute([$id]);
$images = $imgStmt->fetchAll();
if (!$images) $images = [['image_path' => 'assets/image/car-1.png']];

$isWishlisted = false;
if (is_logged_in()) {
    $w = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND car_id = ?");
    $w->execute([current_user_id(), $id]);
    $isWishlisted = (bool)$w->fetch();
}

// similar cars
$sim = $pdo->prepare("SELECT * FROM cars WHERE status='approved' AND brand = ? AND id != ? LIMIT 3");
$sim->execute([$car['brand'], $id]);
$similar = $sim->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<section class="car-details" data-aos="fade-up">
    <div class="cd-gallery">
        <div class="cd-main-img">
            <img id="mainImg" src="<?= BASE_URL . clean($images[0]['image_path']) ?>" alt="<?= clean($car['title']) ?>">
            <?php if ($car['status'] !== 'approved'): ?>
                <span class="car-tag status-tag"><?= ucfirst($car['status']) ?></span>
            <?php endif; ?>
        </div>
        <?php if (count($images) > 1): ?>
        <div class="cd-thumbs">
            <?php foreach ($images as $img): ?>
                <img src="<?= BASE_URL . clean($img['image_path']) ?>" onclick="document.getElementById('mainImg').src=this.src" alt="">
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="cd-info">
        <h1><?= clean($car['title']) ?></h1>
        <div class="cd-price"><?= format_price($car['price']) ?></div>

        <div class="cd-specs">
            <div>&#128197;<span>Year</span><strong><?= (int)$car['year'] ?></strong></div>
            <div>&#9881;<span>Mileage</span><strong><?= number_format($car['mileage']) ?> km</strong></div>
            <div>&#9881;<span>Transmission</span><strong><?= ucfirst($car['transmission']) ?></strong></div>
            <div>&#9981;<span>Fuel</span><strong><?= ucfirst($car['fuel_type']) ?></strong></div>
            <div>&#127912;<span>Color</span><strong><?= clean($car['color'] ?: 'N/A') ?></strong></div>
            <div>&#128205;<span>Location</span><strong><?= clean($car['location'] ?: 'N/A') ?></strong></div>
        </div>

        <h3>Description</h3>
        <p class="cd-desc"><?= nl2br(clean($car['description'])) ?></p>

        <div class="cd-actions">
            <?php if (is_logged_in()): ?>
                <button class="btn wish-btn-lg <?= $isWishlisted ? 'active' : '' ?>" data-car="<?= $car['id'] ?>">
                    <?= $isWishlisted ? '&#9829; Saved' : '&#9825; Save to Wishlist' ?>
                </button>
            <?php endif; ?>
            <?php if (current_user_id() != $car['seller_id']): ?>
                <button class="btn btn-outline" onclick="document.getElementById('inquiryModal').classList.add('open')"> Contact Seller</button>
            <?php endif; ?>
        </div>

        <div class="seller-card">
            &#128100;#128100;
            <div>
                <h4><?= clean($car['seller_name']) ?></h4>
                <p>Member since <?= date('M Y', strtotime($car['seller_since'])) ?></p>
            </div>
        </div>
    </div>
</section>

<!-- Inquiry Modal -->
<div class="modal-overlay" id="inquiryModal">
    <div class="modal-box">
        <button class="modal-close" onclick="document.getElementById('inquiryModal').classList.remove('open')">&times;</button>
        <h3>Contact Seller</h3>
        <p class="modal-sub">Send a message about "<?= clean($car['title']) ?>"</p>
        <?php if (is_logged_in()): ?>
        <form action="send-inquiry.php" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
            <input type="text" class="box" value="<?= clean($_SESSION['user_name']) ?>" disabled>
            <input type="text" name="phone" class="box" placeholder="your phone (optional)">
            <textarea name="message" class="box" rows="4" placeholder="I'm interested in this car..." required>Hi, is the <?= clean($car['title']) ?> still available?</textarea>
            <button type="submit" class="btn full-width">Send Message</button>
        </form>
        <?php else: ?>
            <p>Please <a href="login.php">login</a> or <a href="register.php">create an account</a> to message the seller.</p>
        <?php endif; ?>
    </div>
</div>

<?php if ($similar): ?>
<section class="similar-cars" data-aos="fade-up">
    <h1 class="heading">similar <span>cars</span></h1>
    <div class="cars-grid">
        <?php foreach ($similar as $car2): ?>
            <div class="car-card">
                <div class="car-card-img">
                    <img src="<?= car_primary_image($pdo, $car2['id']) ?>" alt="">
                </div>
                <div class="car-card-body">
                    <h3><?= clean($car2['title']) ?></h3>
                    <div class="car-price"><?= format_price($car2['price']) ?></div>
                    <a href="car-details.php?id=<?= $car2['id'] ?>" class="btn full-width">View Details</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
