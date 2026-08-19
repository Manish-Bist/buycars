<?php
require_once __DIR__ . '/config/config.php';
require_login();
$page_title = 'Edit Listing';

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ? AND seller_id = ?");
$stmt->execute([$id, current_user_id()]);
$car = $stmt->fetch();

if (!$car) {
    set_flash('error', 'Listing not found.');
    redirect(BASE_URL . 'my-listings.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $brand    = clean($_POST['brand'] ?? '');
    $model    = clean($_POST['model'] ?? '');
    $year     = (int)($_POST['year'] ?? 0);
    $price    = (float)($_POST['price'] ?? 0);
    $mileage  = (int)($_POST['mileage'] ?? 0);
    $fuel     = clean($_POST['fuel_type'] ?? 'petrol');
    $trans    = clean($_POST['transmission'] ?? 'automatic');
    $cond     = clean($_POST['condition_type'] ?? 'used');
    $color    = clean($_POST['color'] ?? '');
    $location = clean($_POST['location'] ?? '');
    $desc     = clean($_POST['description'] ?? '');
    $title    = $year . ' ' . $brand . ' ' . $model;

    if (!$brand || !$model) $errors[] = 'Brand and model are required.';
    if ($price <= 0) $errors[] = 'Please enter a valid price.';

    if (!$errors) {
        // editing resets status back to pending so admin re-reviews changes (unless it was already sold)
        $newStatus = $car['status'] === 'sold' ? 'sold' : 'pending';

        $stmt = $pdo->prepare("UPDATE cars SET title=?, brand=?, model=?, year=?, price=?, mileage=?, fuel_type=?, transmission=?, condition_type=?, color=?, location=?, description=?, status=? WHERE id=?");
        $stmt->execute([$title, $brand, $model, $year, $price, $mileage, $fuel, $trans, $cond, $color, $location, $desc, $newStatus, $id]);

        // handle any newly added photos
        if (!empty($_FILES['images']['tmp_name'][0])) {
            foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
                if (!$tmp) continue;
                $file = [
                    'tmp_name' => $_FILES['images']['tmp_name'][$i],
                    'name'     => $_FILES['images']['name'][$i],
                    'size'     => $_FILES['images']['size'][$i],
                    'error'    => $_FILES['images']['error'][$i],
                ];
                $path = handle_image_upload($file);
                if ($path) {
                    $pdo->prepare("INSERT INTO car_images (car_id, image_path, is_primary) VALUES (?,?,0)")->execute([$id, $path]);
                }
            }
        }

        set_flash('success', 'Listing updated successfully' . ($newStatus === 'pending' ? ' and sent for re-approval.' : '.'));
        redirect(BASE_URL . 'my-listings.php');
    }
}

$imgStmt = $pdo->prepare("SELECT * FROM car_images WHERE car_id = ? ORDER BY is_primary DESC, id ASC");
$imgStmt->execute([$id]);
$images = $imgStmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>
<section class="page-banner" data-aos="fade-in">
    <h1>Edit <span>Listing</span></h1>
    <p>Update your car's details below.</p>
</section>

<section class="form-section" data-aos="fade-up">
    <div class="form-card">
        <?php if ($errors): ?>
            <div class="alert alert-error"><ul><?php foreach ($errors as $e) echo '<li>' . clean($e) . '</li>'; ?></ul></div>
        <?php endif; ?>

        <?php if ($images): ?>
        <div class="preview-grid">
            <?php foreach ($images as $img): ?>
                <div class="preview-item"><img src="<?= BASE_URL . clean($img['image_path']) ?>" alt=""></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="car-form">
            <?= csrf_field() ?>
            <div class="form-grid">
                <div class="form-field">
                    <label>Brand *</label>
                    <input type="text" name="brand" required value="<?= clean($car['brand']) ?>">
                </div>
                <div class="form-field">
                    <label>Model *</label>
                    <input type="text" name="model" required value="<?= clean($car['model']) ?>">
                </div>
                <div class="form-field">
                    <label>Year *</label>
                    <input type="number" name="year" min="1950" max="<?= date('Y') + 1 ?>" required value="<?= (int)$car['year'] ?>">
                </div>
                <div class="form-field">
                    <label>Price (USD) *</label>
                    <input type="number" name="price" min="1" step="1" required value="<?= (float)$car['price'] ?>">
                </div>
                <div class="form-field">
                    <label>Mileage (km)</label>
                    <input type="number" name="mileage" min="0" value="<?= (int)$car['mileage'] ?>">
                </div>
                <div class="form-field">
                    <label>Color</label>
                    <input type="text" name="color" value="<?= clean($car['color']) ?>">
                </div>
                <div class="form-field">
                    <label>Fuel Type</label>
                    <select name="fuel_type">
                        <?php foreach (['petrol','diesel','electric','hybrid','cng'] as $f): ?>
                            <option value="<?= $f ?>" <?= $car['fuel_type'] === $f ? 'selected' : '' ?>><?= ucfirst($f) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-field">
                    <label>Transmission</label>
                    <select name="transmission">
                        <option value="automatic" <?= $car['transmission'] === 'automatic' ? 'selected' : '' ?>>Automatic</option>
                        <option value="manual" <?= $car['transmission'] === 'manual' ? 'selected' : '' ?>>Manual</option>
                    </select>
                </div>
                <div class="form-field">
                    <label>Condition</label>
                    <select name="condition_type">
                        <option value="used" <?= $car['condition_type'] === 'used' ? 'selected' : '' ?>>Used</option>
                        <option value="new" <?= $car['condition_type'] === 'new' ? 'selected' : '' ?>>New</option>
                    </select>
                </div>
                <div class="form-field">
                    <label>Location</label>
                    <input type="text" name="location" value="<?= clean($car['location']) ?>">
                </div>
            </div>

            <div class="form-field full">
                <label>Description</label>
                <textarea name="description" rows="5"><?= clean($car['description']) ?></textarea>
            </div>

            <div class="form-field full">
                <label>Add more photos (optional)</label>
                <div class="upload-box" id="uploadBox">
                    <i class='bx bx-cloud-upload'></i>
                    <p>Click or drag photos here</p>
                    <input type="file" name="images[]" id="imagesInput" accept="image/*" multiple>
                </div>
                <div class="preview-grid" id="previewGrid"></div>
            </div>

            <button type="submit" class="btn full-width"><i class='bx bx-save'></i> Update Listing</button>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
