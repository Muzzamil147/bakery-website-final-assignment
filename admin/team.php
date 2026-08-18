<?php
// Full CRUD for team members — same save/delete/image-upload pattern as
// products.php (see that file for detailed comments).

$pageTitle = 'Team';
require __DIR__ . '/includes/admin_header.php';

$flash = null;
$flashType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $role = trim($_POST['role'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $order = (int) ($_POST['display_order'] ?? 0);

        [$uploadedPath, $uploadError] = handle_image_upload('photo', 'team');

        if ($name === '' || $role === '') {
            $flash = 'Name and role are required.';
            $flashType = 'error';
        } elseif ($uploadError) {
            $flash = $uploadError;
            $flashType = 'error';
        } else {
            if ($id > 0) {
                if ($uploadedPath) {
                    $existing = $pdo->prepare('SELECT photo_path FROM team_members WHERE id = :id');
                    $existing->execute(['id' => $id]);
                    delete_uploaded_image($existing->fetchColumn() ?: null);
                    $stmt = $pdo->prepare(
                        'UPDATE team_members SET name=:name, role=:role, bio=:bio, photo_path=:photo, display_order=:order WHERE id=:id'
                    );
                    $stmt->execute(['name' => $name, 'role' => $role, 'bio' => $bio, 'photo' => $uploadedPath, 'order' => $order, 'id' => $id]);
                } else {
                    $stmt = $pdo->prepare(
                        'UPDATE team_members SET name=:name, role=:role, bio=:bio, display_order=:order WHERE id=:id'
                    );
                    $stmt->execute(['name' => $name, 'role' => $role, 'bio' => $bio, 'order' => $order, 'id' => $id]);
                }
                $flash = 'Team member updated.';
            } else {
                $stmt = $pdo->prepare(
                    'INSERT INTO team_members (name, role, bio, photo_path, display_order) VALUES (:name, :role, :bio, :photo, :order)'
                );
                $stmt->execute(['name' => $name, 'role' => $role, 'bio' => $bio, 'photo' => $uploadedPath, 'order' => $order]);
                $flash = 'Team member added.';
            }
        }
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $existing = $pdo->prepare('SELECT photo_path FROM team_members WHERE id = :id');
        $existing->execute(['id' => $id]);
        delete_uploaded_image($existing->fetchColumn() ?: null);
        $stmt = $pdo->prepare('DELETE FROM team_members WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $flash = 'Team member deleted.';
    }
}

$editing = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM team_members WHERE id = :id');
    $stmt->execute(['id' => (int) $_GET['edit']]);
    $editing = $stmt->fetch() ?: null;
}

$team = $pdo->query('SELECT * FROM team_members ORDER BY display_order ASC, id ASC')->fetchAll();
?>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flashType === 'error' ? 'error' : 'success' ?>"><?= e($flash) ?></div>
<?php endif; ?>

<div class="admin-panel">
    <div class="admin-panel-header">
        <h2><?= $editing ? 'Edit Team Member' : 'Add Team Member' ?></h2>
    </div>
    <form method="post" action="team.php" enctype="multipart/form-data" class="stacked-form form-grid">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= $editing ? (int) $editing['id'] : 0 ?>">

        <div class="form-row">
            <label>Name</label>
            <input type="text" name="name" value="<?= e($editing['name'] ?? '') ?>" required>
        </div>
        <div class="form-row">
            <label>Role</label>
            <input type="text" name="role" value="<?= e($editing['role'] ?? '') ?>" required>
        </div>
        <div class="form-row form-row-full">
            <label>Bio</label>
            <textarea name="bio" rows="3"><?= e($editing['bio'] ?? '') ?></textarea>
        </div>
        <div class="form-row">
            <label>Photo <?= $editing ? '<span class="optional">(leave empty to keep current)</span>' : '' ?></label>
            <input type="file" name="photo" accept="image/jpeg,image/png,image/gif,image/webp">
        </div>
        <div class="form-row">
            <label>Display Order</label>
            <input type="number" name="display_order" value="<?= e((string) ($editing['display_order'] ?? 0)) ?>">
        </div>
        <?php if ($editing && $editing['photo_path']): ?>
            <div class="form-row">
                <label>Current Photo</label>
                <img src="/<?= e($editing['photo_path']) ?>" alt="" class="thumb-preview">
            </div>
        <?php endif; ?>

        <div class="form-actions form-row-full">
            <button type="submit" class="btn btn-primary"><?= $editing ? 'Save Changes' : 'Add Team Member' ?></button>
            <?php if ($editing): ?>
                <a href="team.php" class="btn">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="admin-panel">
    <div class="admin-panel-header">
        <h2>All Team Members (<?= count($team) ?>)</h2>
    </div>
    <table class="admin-table">
        <thead>
        <tr>
            <th></th>
            <th>Name</th>
            <th>Role</th>
            <th>Order</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($team as $member): ?>
            <tr>
                <td><img src="/<?= e($member['photo_path']) ?>" alt="" class="table-thumb table-thumb-round"></td>
                <td><?= e($member['name']) ?></td>
                <td><?= e($member['role']) ?></td>
                <td><?= (int) $member['display_order'] ?></td>
                <td class="actions">
                    <a href="team.php?edit=<?= (int) $member['id'] ?>" class="btn btn-sm">Edit</a>
                    <form method="post" action="team.php" class="inline-delete" onsubmit="return confirm('Delete this team member?');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= (int) $member['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
