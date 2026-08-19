<?php
require_once __DIR__ . '/config/config.php';
require_login();
$page_title = 'My Listings';

$stmt = $pdo->prepare("SELECT * FROM cars WHERE seller_id = ? ORDER BY created_at DESC");
$stmt->execute([current_user_id()]);
$cars = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>
<section class="page-banner" data-aos="fade-in">
    <h1>My <span>Listings</span></h1>
    <p><?= count($cars) ?> car<?= count($cars) == 1 ? '' : 's' ?> listed by you</p>
</section>

<section class="dashboard-section" data-aos="fade-up">
    <div class="section-toolbar">
        <a href="sell-car.php" class="btn"><i class='bx bx-plus-circle'></i> Sell a New Car</a>
    </div>

    <?php if (!$cars): ?>
        <div class="empty-state" data-aos="fade-up">
            <i class='bx bx-car'></i>
            <h3>You haven't listed any cars yet</h3>
            <p>Start selling by listing your first car.</p>
            <a href="sell-car.php" class="btn">List a Car</a>
        </div>
    <?php else: ?>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Photo</th><th>Title</th><th>Price</th><th>Status</th><th>Views</th><th>Listed On</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cars as $car): ?>
                <tr>
                    <td><img class="table-thumb" src="<?= car_primary_image($pdo, $car['id']) ?>" alt=""></td>
                    <td><?= clean($car['title']) ?></td>
                    <td><?= format_price($car['price']) ?></td>
                    <td><?= status_badge($car['status']) ?></td>
                    <td><?= (int)$car['views'] ?></td>
                    <td><?= date('d M Y', strtotime($car['created_at'])) ?></td>
                    <td class="actions-cell">
                        <a href="car-details.php?id=<?= $car['id'] ?>" title="View"><i class='bx bx-show'></i></a>
                        <a href="edit-car.php?id=<?= $car['id'] ?>" title="Edit"><i class='bx bx-edit'></i></a>
                        <a href="#" class="text-danger" title="Delete" onclick="confirmDelete(<?= $car['id'] ?>); return false;"><i class='bx bx-trash'></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</section>

<form id="deleteForm" action="delete-car.php" method="POST" style="display:none;">
    <?= csrf_field() ?>
    <input type="hidden" name="car_id" id="deleteCarId">
</form>
<script>
function confirmDelete(id) {
    if (confirm('Delete this listing? This cannot be undone.')) {
        document.getElementById('deleteCarId').value = id;
        document.getElementById('deleteForm').submit();
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
