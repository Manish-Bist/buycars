<?php
require_once __DIR__ . '/../config/config.php';
require_admin();
$page_title = 'Manage Cars';

$status = clean($_GET['status'] ?? '');
$q      = clean($_GET['q'] ?? '');

$where = [];
$params = [];
if ($status !== '') { $where[] = "cars.status = ?"; $params[] = $status; }
if ($q !== '') { $where[] = "(cars.title LIKE ? OR cars.brand LIKE ?)"; $params[] = "%$q%"; $params[] = "%$q%"; }
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $pdo->prepare("SELECT cars.*, users.name AS seller_name, users.email AS seller_email
                        FROM cars JOIN users ON users.id = cars.seller_id
                        $whereSql ORDER BY cars.created_at DESC");
$stmt->execute($params);
$cars = $stmt->fetchAll();

$counts = $pdo->query("SELECT status, COUNT(*) c FROM cars GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);

require_once __DIR__ . '/includes/header.php';
?>

<div class="tabs">
    <a href="cars.php" class="tab-link <?= $status === '' ? 'active' : '' ?>">All (<?= array_sum($counts) ?>)</a>
    <a href="cars.php?status=pending" class="tab-link <?= $status === 'pending' ? 'active' : '' ?>">Pending (<?= $counts['pending'] ?? 0 ?>)</a>
    <a href="cars.php?status=approved" class="tab-link <?= $status === 'approved' ? 'active' : '' ?>">Approved (<?= $counts['approved'] ?? 0 ?>)</a>
    <a href="cars.php?status=rejected" class="tab-link <?= $status === 'rejected' ? 'active' : '' ?>">Rejected (<?= $counts['rejected'] ?? 0 ?>)</a>
    <a href="cars.php?status=sold" class="tab-link <?= $status === 'sold' ? 'active' : '' ?>">Sold (<?= $counts['sold'] ?? 0 ?>)</a>
</div>

<form method="GET" class="inline-search">
    <?php if ($status !== ''): ?><input type="hidden" name="status" value="<?= clean($status) ?>"><?php endif; ?>
    <input type="text" name="q" placeholder="Search by title or brand..." value="<?= clean($q) ?>">
    <button type="submit" class="btn btn-sm">&#128269;</button>
</form>

<?php if (!$cars): ?>
    <div class="empty-state" data-aos="fade-up">&#128663;<h3>No cars found</h3></div>
<?php else: ?>
<div class="table-wrap" data-aos="fade-up">
    <table class="data-table">
        <thead>
            <tr><th>Photo</th><th>Title</th><th>Seller</th><th>Price</th><th>Status</th><th>Views</th><th>Date</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php foreach ($cars as $car): ?>
            <tr>
                <td><img class="table-thumb" src="<?= car_primary_image($pdo, $car['id']) ?>" alt=""></td>
                <td>
                    <a href="<?= BASE_URL ?>car-details.php?id=<?= $car['id'] ?>" target="_blank"><?= clean($car['title']) ?></a>
                    <br><small class="muted"><?= (int)$car['year'] ?> &middot; <?= ucfirst($car['fuel_type']) ?> &middot; <?= ucfirst($car['transmission']) ?></small>
                </td>
                <td><?= clean($car['seller_name']) ?><br><small class="muted"><?= clean($car['seller_email']) ?></small></td>
                <td><?= format_price($car['price']) ?></td>
                <td><?= status_badge($car['status']) ?></td>
                <td><?= (int)$car['views'] ?></td>
                <td><?= date('d M Y', strtotime($car['created_at'])) ?></td>
                <td class="actions-cell">
                    <?php if ($car['status'] === 'pending'): ?>
                        <form method="POST" action="car-action.php" class="inline-form">
                            <?= csrf_field() ?>
                            <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="icon-btn text-success" title="Approve">&#10003;</button>
                        </form>
                        <form method="POST" action="car-action.php" class="inline-form">
                            <?= csrf_field() ?>
                            <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
                            <input type="hidden" name="action" value="reject">
                            <button type="submit" class="icon-btn text-danger" title="Reject">&#10007;</button>
                        </form>
                    <?php elseif ($car['status'] === 'approved'): ?>
                        <form method="POST" action="car-action.php" class="inline-form">
                            <?= csrf_field() ?>
                            <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
                            <input type="hidden" name="action" value="sold">
                            <button type="submit" class="icon-btn text-success" title="Mark as Sold">&#127942;</button>
                        </form>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>car-details.php?id=<?= $car['id'] ?>" target="_blank" class="icon-btn" title="View">&#128065;</a>
                    <button type="button" class="icon-btn text-danger" title="Delete" onclick="confirmAdminDelete(<?= $car['id'] ?>)">&#128465;</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<form id="deleteCarForm" action="car-action.php" method="POST" style="display:none;">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="car_id" id="deleteCarId">
</form>
<script>
function confirmAdminDelete(id) {
    if (confirm('Permanently delete this car listing and its photos?')) {
        document.getElementById('deleteCarId').value = id;
        document.getElementById('deleteCarForm').submit();
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
