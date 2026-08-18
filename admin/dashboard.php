<?php
// Landing page after login: quick counts across every table, plus the
// 5 most recent contact form submissions.

$pageTitle = 'Dashboard';
require __DIR__ . '/includes/admin_header.php';

$stats = [
    'Products' => $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn(),
    'Categories' => $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn(),
    'Team Members' => $pdo->query('SELECT COUNT(*) FROM team_members')->fetchColumn(),
    'Gallery Photos' => $pdo->query('SELECT COUNT(*) FROM gallery')->fetchColumn(),
];
$unreadCount = (int) $pdo->query('SELECT COUNT(*) FROM contact_messages WHERE is_read = 0')->fetchColumn();
$recentMessages = $pdo->query('SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5')->fetchAll();
?>

<div class="stat-grid">
    <?php foreach ($stats as $label => $value): ?>
        <div class="stat-card">
            <span class="stat-value"><?= (int) $value ?></span>
            <span class="stat-label"><?= e($label) ?></span>
        </div>
    <?php endforeach; ?>
    <div class="stat-card <?= $unreadCount > 0 ? 'stat-card-highlight' : '' ?>">
        <span class="stat-value"><?= $unreadCount ?></span>
        <span class="stat-label">Unread Messages</span>
    </div>
</div>

<div class="admin-panel">
    <div class="admin-panel-header">
        <h2>Recent Contact Messages</h2>
        <a href="messages.php" class="btn btn-sm">View All</a>
    </div>
    <?php if (empty($recentMessages)): ?>
        <p class="empty-note">No messages yet.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Message</th>
                <th>Received</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($recentMessages as $message): ?>
                <tr>
                    <td><?= e($message['name']) ?></td>
                    <td><?= e($message['email']) ?></td>
                    <td class="truncate"><?= e($message['message']) ?></td>
                    <td><?= e(date('M j, Y', strtotime($message['created_at']))) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
