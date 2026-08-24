<?php
require_once __DIR__ . '/../config/config.php';
require_admin();
$page_title = 'Manage Users';

$q = clean($_GET['q'] ?? '');
$where = "role = 'user'";
$params = [];
if ($q !== '') { $where .= " AND (name LIKE ? OR email LIKE ?)"; $params = ["%$q%", "%$q%"]; }

$stmt = $pdo->prepare("SELECT users.*,
    (SELECT COUNT(*) FROM cars WHERE cars.seller_id = users.id) AS listing_count
    FROM users WHERE $where ORDER BY created_at DESC");
$stmt->execute($params);
$users = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<form method="GET" class="inline-search">
    <input type="text" name="q" placeholder="Search by name or email..." value="<?= clean($q) ?>">
    <button type="submit" class="btn btn-sm">&#128269;</button>
</form>

<?php if (!$users): ?>
    <div class="empty-state" data-aos="fade-up">&#128101;<h3>No users found</h3></div>
<?php else: ?>
<div class="table-wrap" data-aos="fade-up">
    <table class="data-table">
        <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Listings</th><th>Status</th><th>Joined</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= clean($u['name']) ?></td>
                <td><?= clean($u['email']) ?></td>
                <td><?= clean($u['phone'] ?: '-') ?></td>
                <td><?= (int)$u['listing_count'] ?></td>
                <td><?= status_badge($u['status']) ?></td>
                <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                <td class="actions-cell">
                    <form method="POST" action="user-action.php" class="inline-form">
                        <?= csrf_field() ?>
                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                        <input type="hidden" name="action" value="<?= $u['status'] === 'active' ? 'block' : 'unblock' ?>">
                        <button type="submit" class="icon-btn <?= $u['status'] === 'active' ? 'text-danger' : 'text-success' ?>" title="<?= $u['status'] === 'active' ? 'Block user' : 'Unblock user' ?>">
                            <?= $u['status'] === 'active' ? '&#128683;' : '&#10003;' ?>
                        </button>
                    </form>
                    <button type="button" class="icon-btn text-danger" title="Delete" onclick="confirmUserDelete(<?= $u['id'] ?>)">&#128465;</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<form id="deleteUserForm" action="user-action.php" method="POST" style="display:none;">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="user_id" id="deleteUserId">
</form>
<script>
function confirmUserDelete(id) {
    if (confirm('Delete this user and all their listings? This cannot be undone.')) {
        document.getElementById('deleteUserId').value = id;
        document.getElementById('deleteUserForm').submit();
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
