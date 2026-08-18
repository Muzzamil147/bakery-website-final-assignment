<?php
// Lets staff review submissions from the public contact form
// (contact.php inserts the rows this page reads). Read + Delete +
// a "mark read" status update — no image upload here, so it's simpler
// than the other admin pages.

$pageTitle = 'Messages';
require __DIR__ . '/includes/admin_header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();
    $action = $_POST['action'] ?? '';
    $id = (int) ($_POST['id'] ?? 0);

    if ($action === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM contact_messages WHERE id = :id');
        $stmt->execute(['id' => $id]);
    } elseif ($action === 'mark_read') {
        $stmt = $pdo->prepare('UPDATE contact_messages SET is_read = 1 WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}

$messages = $pdo->query('SELECT * FROM contact_messages ORDER BY created_at DESC')->fetchAll();
?>

<div class="admin-panel">
    <div class="admin-panel-header">
        <h2>Contact Messages (<?= count($messages) ?>)</h2>
    </div>
    <?php if (empty($messages)): ?>
        <p class="empty-note">No messages yet.</p>
    <?php else: ?>
        <div class="message-list">
            <?php foreach ($messages as $message): ?>
                <div class="message-card <?= $message['is_read'] ? '' : 'message-unread' ?>">
                    <div class="message-card-header">
                        <div>
                            <strong><?= e($message['name']) ?></strong>
                            <span class="muted"><?= e($message['email']) ?><?= $message['phone'] ? ' · ' . e($message['phone']) : '' ?></span>
                        </div>
                        <span class="muted small"><?= e(date('M j, Y g:ia', strtotime($message['created_at']))) ?></span>
                    </div>
                    <p class="message-body"><?= nl2br(e($message['message'])) ?></p>
                    <div class="actions">
                        <?php if (!$message['is_read']): ?>
                            <form method="post" action="messages.php" class="inline-delete">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="mark_read">
                                <input type="hidden" name="id" value="<?= (int) $message['id'] ?>">
                                <button type="submit" class="btn btn-sm">Mark Read</button>
                            </form>
                        <?php endif; ?>
                        <form method="post" action="messages.php" class="inline-delete" onsubmit="return confirm('Delete this message?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int) $message['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
