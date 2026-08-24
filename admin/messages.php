<?php
require_once __DIR__ . '/../config/config.php';
require_admin();
$page_title = 'Contact Messages';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $id = (int)($_POST['message_id'] ?? 0);
    if (($_POST['action'] ?? '') === 'delete') {
        $pdo->prepare("DELETE FROM contact_messages WHERE id = ?")->execute([$id]);
        set_flash('success', 'Message deleted.');
    }
    redirect(BASE_URL . 'admin/messages.php');
}

$pdo->exec("UPDATE contact_messages SET is_read = 1 WHERE is_read = 0");
$rows = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<?php if (!$rows): ?>
    <div class="empty-state" data-aos="fade-up">&#9993;<h3>No messages yet</h3></div>
<?php else: ?>
<div class="inquiry-list" data-aos="fade-up">
    <?php foreach ($rows as $m): ?>
        <div class="inquiry-card">
            <div class="inquiry-header">
                <div><strong><?= clean($m['name']) ?></strong> <span class="muted">&lt;<?= clean($m['email']) ?>&gt;</span></div>
                <form method="POST" class="inline-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="message_id" value="<?= $m['id'] ?>">
                    <input type="hidden" name="action" value="delete">
                    <button type="submit" class="icon-btn text-danger" title="Delete" onclick="return confirm('Delete this message?')">&#128465;</button>
                </form>
            </div>
            <?php if ($m['subject']): ?><p><strong>Subject:</strong> <?= clean($m['subject']) ?></p><?php endif; ?>
            <p class="inquiry-msg"><?= nl2br(clean($m['message'])) ?></p>
            <div class="inquiry-footer"><span class="muted"><?= time_ago($m['created_at']) ?></span></div>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
