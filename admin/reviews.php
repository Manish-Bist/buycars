<?php
require_once __DIR__ . '/../config/config.php';
require_admin();
$page_title = 'Testimonials';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name    = clean($_POST['name'] ?? '');
        $rating  = (int)($_POST['rating'] ?? 5);
        $message = clean($_POST['message'] ?? '');
        if ($name && $message) {
            $pdo->prepare("INSERT INTO reviews (name, rating, message, image) VALUES (?,?,?,'assets/image/pic-1.png')")
                ->execute([$name, $rating, $message]);
            set_flash('success', 'Testimonial added.');
        }
    }
    if ($action === 'delete') {
        $pdo->prepare("DELETE FROM reviews WHERE id = ?")->execute([(int)($_POST['id'] ?? 0)]);
        set_flash('success', 'Testimonial removed.');
    }
    redirect(BASE_URL . 'admin/reviews.php');
}

$reviews = $pdo->query("SELECT * FROM reviews ORDER BY created_at DESC")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="form-card" data-aos="fade-up" style="margin-bottom:2rem;">
    <h3><i class='bx bx-plus-circle'></i> Add Testimonial</h3>
    <form method="POST" class="car-form">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="add">
        <div class="form-grid">
            <div class="form-field"><label>Name</label><input type="text" name="name" required></div>
            <div class="form-field"><label>Rating (1-5)</label><input type="number" name="rating" min="1" max="5" value="5"></div>
        </div>
        <div class="form-field full"><label>Message</label><textarea name="message" rows="3" required></textarea></div>
        <button type="submit" class="btn">Add Testimonial</button>
    </form>
</div>

<div class="table-wrap" data-aos="fade-up">
    <table class="data-table">
        <thead><tr><th>Name</th><th>Rating</th><th>Message</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($reviews as $r): ?>
            <tr>
                <td><?= clean($r['name']) ?></td>
                <td><?= str_repeat('★', (int)$r['rating']) ?></td>
                <td class="truncate-cell"><?= clean($r['message']) ?></td>
                <td>
                    <form method="POST" class="inline-form">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $r['id'] ?>">
                        <button type="submit" class="icon-btn text-danger" onclick="return confirm('Remove this testimonial?')"><i class='bx bx-trash'></i></button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
