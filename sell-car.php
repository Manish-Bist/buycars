<?php
require_once __DIR__ . '/config/config.php';
require_login();
$page_title = 'Sell Your Car';

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
    if ($year < 1950 || $year > (int)date('Y') + 1) $errors[] = 'Please enter a valid year.';
    if ($price <= 0) $errors[] = 'Please enter a valid price.';
    if (empty($_FILES['images']['tmp_name'][0])) $errors[] = 'Please upload at least one photo of the car.';

    if (!$errors) {
        $stmt = $pdo->prepare("INSERT INTO cars (seller_id, title, brand, model, year, price, mileage, fuel_type, transmission, condition_type, color, location, description, status)
                                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?, 'pending')");
        $stmt->execute([current_user_id(), $title, $brand, $model, $year, $price, $mileage, $fuel, $trans, $cond, $color, $location, $desc]);
        $car_id = $pdo->lastInsertId();

        $uploaded = 0;
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
                $pdo->prepare("INSERT INTO car_images (car_id, image_path, is_primary) VALUES (?,?,?)")
                    ->execute([$car_id, $path, $uploaded === 0 ? 1 : 0]);
                $uploaded++;
            }
        }

        set_flash('success', 'Your car has been submitted! It will appear on the site once approved by our admin team.');
        redirect(BASE_URL . 'my-listings.php');
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<section class="page-banner" data-aos="fade-in">
    <h1>Sell Your <span>Car</span></h1>
    <p>Fill in the details below - your listing will go live after a quick admin review.</p>
</section>

<section class="form-section" data-aos="fade-up">
    <div class="form-card">
        <?php if ($errors): ?>
            <div class="alert alert-error"><ul><?php foreach ($errors as $e) echo '<li>' . clean($e) . '</li>'; ?></ul></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="car-form">
            <?= csrf_field() ?>
            <div class="form-grid">
                <div class="form-field">
                    <label>Brand *</label>
                    <input type="text" name="brand" placeholder="e.g. Toyota" required value="<?= clean($_POST['brand'] ?? '') ?>">
                </div>
                <div class="form-field">
                    <label>Model *</label>
                    <input type="text" name="model" placeholder="e.g. Corolla" required value="<?= clean($_POST['model'] ?? '') ?>">
                </div>
                <div class="form-field">
                    <label>Year *</label>
                    <input type="number" name="year" min="1950" max="<?= date('Y') + 1 ?>" required value="<?= clean($_POST['year'] ?? date('Y')) ?>">
                </div>
                <div class="form-field">
                    <label>Price (USD) *</label>
                    <input type="number" name="price" min="1" step="1" required value="<?= clean($_POST['price'] ?? '') ?>">
                </div>
                <div class="form-field">
                    <label>Mileage (km)</label>
                    <input type="number" name="mileage" min="0" value="<?= clean($_POST['mileage'] ?? '0') ?>">
                </div>
                <div class="form-field">
                    <label>Color</label>
                    <input type="text" name="color" placeholder="e.g. White" value="<?= clean($_POST['color'] ?? '') ?>">
                </div>
                <div class="form-field">
                    <label>Fuel Type</label>
                    <select name="fuel_type">
                        <?php foreach (['petrol','diesel','electric','hybrid','cng'] as $f): ?>
                            <option value="<?= $f ?>"><?= ucfirst($f) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-field">
                    <label>Transmission</label>
                    <select name="transmission">
                        <option value="automatic">Automatic</option>
                        <option value="manual">Manual</option>
                    </select>
                </div>
                <div class="form-field">
                    <label>Condition</label>
                    <select name="condition_type">
                        <option value="used">Used</option>
                        <option value="new">New</option>
                    </select>
                </div>
                <div class="form-field">
                    <label>Location</label>
                    <input type="text" name="location" placeholder="e.g. Kathmandu" value="<?= clean($_POST['location'] ?? '') ?>">
                </div>
            </div>

            <div class="form-field full">
                <label>Description</label>
                <textarea name="description" rows="5" placeholder="Tell buyers about the car's condition, history, features..."><?= clean($_POST['description'] ?? '') ?></textarea>
            </div>

            <div class="form-field full">
                <label>Photos * (you can select multiple)</label>
                <div class="upload-box" id="uploadBox">
                    <span style="font-size:4rem;">&#8686;#8686;</span>
                    <p>Click or drag photos here</p>
                    <input type="file" name="images[]" id="imagesInput" accept="image/*" multiple required>
                </div>
                <div class="preview-grid" id="previewGrid"></div>
            </div>

            <button type="submit" class="btn full-width"> Submit Listing</button>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
