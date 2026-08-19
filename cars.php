<?php
require_once __DIR__ . '/config/config.php';
$page_title = 'Browse Vehicles';

// ---- filters ----
$q         = clean($_GET['q'] ?? '');
$brand     = clean($_GET['brand'] ?? '');
$fuel      = clean($_GET['fuel'] ?? '');
$transmission = clean($_GET['transmission'] ?? '');
$max_price = clean($_GET['max_price'] ?? '');
$sort      = clean($_GET['sort'] ?? 'newest');

$where  = ["status = 'approved'"];
$params = [];

if ($q !== '') {
    $where[] = "(title LIKE ? OR brand LIKE ? OR model LIKE ?)";
    $like = "%$q%";
    array_push($params, $like, $like, $like);
}
if ($brand !== '') { $where[] = "brand = ?"; $params[] = $brand; }
if ($fuel !== '') { $where[] = "fuel_type = ?"; $params[] = $fuel; }
if ($transmission !== '') { $where[] = "transmission = ?"; $params[] = $transmission; }
if ($max_price !== '' && is_numeric($max_price)) { $where[] = "price <= ?"; $params[] = $max_price; }

$order = "created_at DESC";
if ($sort === 'price_low')  $order = "price ASC";
if ($sort === 'price_high') $order = "price DESC";
if ($sort === 'year_new')   $order = "year DESC";
if ($sort === 'popular')    $order = "views DESC";

$whereSql = implode(' AND ', $where);

// pagination
$perPage = 9;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM cars WHERE $whereSql");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$totalPages = max(1, ceil($total / $perPage));

$sql = "SELECT * FROM cars WHERE $whereSql ORDER BY $order LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$cars = $stmt->fetchAll();

$brands = $pdo->query("SELECT DISTINCT brand FROM cars WHERE status='approved' ORDER BY brand")->fetchAll(PDO::FETCH_COLUMN);

require_once __DIR__ . '/includes/header.php';
?>
<section class="page-banner" data-aos="fade-in">
    <h1>Browse <span>Vehicles</span></h1>
    <p><?= $total ?> car<?= $total == 1 ? '' : 's' ?> available right now</p>
</section>

<section class="cars-browse">
    <aside class="filters-panel" data-aos="fade-right">
        <form method="GET" id="filterForm">
            <h3><i class='bx bx-filter-alt'></i> Filters</h3>

            <label>Search</label>
            <input type="text" name="q" value="<?= clean($q) ?>" placeholder="brand or model...">

            <label>Brand</label>
            <select name="brand">
                <option value="">All brands</option>
                <?php foreach ($brands as $b): ?>
                    <option value="<?= clean($b) ?>" <?= $brand === $b ? 'selected' : '' ?>><?= clean($b) ?></option>
                <?php endforeach; ?>
            </select>

            <label>Fuel Type</label>
            <select name="fuel">
                <option value="">Any</option>
                <?php foreach (['petrol','diesel','electric','hybrid','cng'] as $f): ?>
                    <option value="<?= $f ?>" <?= $fuel === $f ? 'selected' : '' ?>><?= ucfirst($f) ?></option>
                <?php endforeach; ?>
            </select>

            <label>Transmission</label>
            <select name="transmission">
                <option value="">Any</option>
                <option value="automatic" <?= $transmission === 'automatic' ? 'selected' : '' ?>>Automatic</option>
                <option value="manual" <?= $transmission === 'manual' ? 'selected' : '' ?>>Manual</option>
            </select>

            <label>Max Price</label>
            <input type="number" name="max_price" value="<?= clean($max_price) ?>" placeholder="e.g. 50000">

            <label>Sort By</label>
            <select name="sort">
                <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest first</option>
                <option value="price_low" <?= $sort === 'price_low' ? 'selected' : '' ?>>Price: low to high</option>
                <option value="price_high" <?= $sort === 'price_high' ? 'selected' : '' ?>>Price: high to low</option>
                <option value="year_new" <?= $sort === 'year_new' ? 'selected' : '' ?>>Year: newest</option>
                <option value="popular" <?= $sort === 'popular' ? 'selected' : '' ?>>Most viewed</option>
            </select>

            <button type="submit" class="btn full-width"><i class='bx bx-search'></i> Apply Filters</button>
            <a href="cars.php" class="btn btn-outline full-width">Reset</a>
        </form>
    </aside>

    <div class="cars-grid-wrap">
        <?php if (!$cars): ?>
            <div class="empty-state" data-aos="fade-up">
                <i class='bx bx-car'></i>
                <h3>No cars match your filters</h3>
                <p>Try adjusting or resetting your search.</p>
            </div>
        <?php else: ?>
            <div class="cars-grid">
                <?php foreach ($cars as $i => $car): ?>
                    <div class="car-card" data-aos="fade-up" data-aos-delay="<?= ($i % 3) * 80 ?>">
                        <div class="car-card-img">
                            <img src="<?= car_primary_image($pdo, $car['id']) ?>" alt="<?= clean($car['title']) ?>">
                            <span class="car-tag"><?= ucfirst($car['condition_type']) ?></span>
                            <?php if (is_logged_in()): ?>
                            <button class="wish-btn" data-car="<?= $car['id'] ?>" title="Save to wishlist"><i class='bx bx-heart'></i></button>
                            <?php endif; ?>
                        </div>
                        <div class="car-card-body">
                            <h3><?= clean($car['title']) ?></h3>
                            <div class="car-price"><?= format_price($car['price']) ?></div>
                            <ul class="car-meta">
                                <li><i class='bx bx-calendar'></i> <?= (int)$car['year'] ?></li>
                                <li><i class='bx bx-cog'></i> <?= ucfirst($car['transmission']) ?></li>
                                <li><i class='bx bx-gas-pump'></i> <?= ucfirst($car['fuel_type']) ?></li>
                                <li><i class='bx bx-map'></i> <?= clean($car['location'] ?: 'N/A') ?></li>
                            </ul>
                            <a href="car-details.php?id=<?= $car['id'] ?>" class="btn full-width">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php for ($p = 1; $p <= $totalPages; $p++):
                    $qs = $_GET; $qs['page'] = $p; ?>
                    <a href="cars.php?<?= http_build_query($qs) ?>" class="<?= $p == $page ? 'active' : '' ?>"><?= $p ?></a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
