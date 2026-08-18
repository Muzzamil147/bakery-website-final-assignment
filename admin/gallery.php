<?php
// Full CRUD for gallery photos — same save/delete/image-upload pattern as
// products.php (see that file for detailed comments). The image is
// required when adding a new photo (there's nothing else to show), but
// optional when editing (an existing photo can just get a new caption).

$pageTitle = 'Gallery';
require __DIR__ . '/includes/admin_header.php';

$flash = null;
$flashType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id = (int) ($_POST['id'] ?? 0);
        $caption = trim($_POST['caption'] ?? '');

        [$uploadedPath, $uploadError] = handle_image_upload('image', 'gallery');

        if ($uploadError) {
            $flash = $uploadError;
            $flashType = 'error';
        } elseif ($id > 0) {
            if ($uploadedPath) {
                $existing = $pdo->prepare('SELECT image_path FROM gallery WHERE id = :id');
                $existing->execute(['id' => $id]);
                delete_uploaded_image($existing->fetchColumn() ?: null);
                $stmt = $pdo->prepare('UPDATE gallery SET caption=:caption, image_path=:image WHERE id=:id');
                $stmt->execute(['caption' => $caption, 'image' => $uploadedPath, 'id' => $id]);
            } else {
                $stmt = $pdo->prepare('UPDATE gallery SET caption=:caption WHERE id=:id');
                $stmt->execute(['caption' => $caption, 'id' => $id]);
            }
            $flash = 'Photo updated.';
        } elseif (!$uploadedPath) {
            $flash = 'Please choose an image to upload.';
            $flashType = 'error';
        } else {
            $stmt = $pdo->prepare('INSERT INTO gallery (image_path, caption) VALUES (:image, :caption)');
            $stmt->execute(['image' => $uploadedPath, 'caption' => $caption]);
            $flash = 'Photo added.';
        }
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $existing = $pdo->prepare('SELECT image_path FROM gallery WHERE id = :id');
        $existing->execute(['id' => $id]);
        delete_uploaded_image($existing->fetchColumn() ?: null);
        $stmt = $pdo->prepare('DELETE FROM gallery WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $flash = 'Photo deleted.';
    }
}

$editing = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM gallery WHERE id = :id');
    $stmt->execute(['id' => (int) $_GET['edit']]);
    $editing = $stmt->fetch() ?: null;
}

$images = $pdo->query('SELECT * FROM gallery ORDER BY created_at DESC')->fetchAll();
?>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flashType === 'error' ? 'error' : 'success' ?>"><?= e($flash) ?></div>
<?php endif; ?>

<div class="admin-panel">
    <div class="admin-panel-header">
        <h2><?= $editing ? 'Edit Photo' : 'Add Photo' ?></h2>
    </div>
    <form method="post" action="gallery.php" enctype="multipart/form-data" class="stacked-form form-grid">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= $editing ? (int) $editing['id'] : 0 ?>">

        <div class="form-row">
            <label>Image <?= $editing ? '<span class="optional">(leave empty to keep current)</span>' : '' ?></label>
            <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp" <?= $editing ? '' : 'required' ?>>
        </div>
        <div class="form-row">
            <label>Caption <span class="optional">(optional)</span></label>
            <input type="text" name="caption" value="<?= e($editing['caption'] ?? '') ?>">
        </div>
        <?php if ($editing): ?>
            <div class="form-row">
                <label>Current Photo</label>
                <img src="/<?= e($editing['image_path']) ?>" alt="" class="thumb-preview">
            </div>
        <?php endif; ?>

        <div class="form-actions form-row-full">
            <button type="submit" class="btn btn-primary"><?= $editing ? 'Save Changes' : 'Add Photo' ?></button>
            <?php if ($editing): ?>
                <a href="gallery.php" class="btn">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="admin-panel">
    <div class="admin-panel-header">
        <h2>All Photos (<?= count($images) ?>)</h2>
    </div>
    <?php if (empty($images)): ?>
        <p class="empty-note">No photos yet.</p>
    <?php else: ?>
        <div class="admin-photo-grid">
            <?php foreach ($images as $image): ?>
                <div class="admin-photo-card">
                    <img src="/<?= e($image['image_path']) ?>" alt="">
                    <p><?= e($image['caption'] ?: '(no caption)') ?></p>
                    <div class="actions">
                        <a href="gallery.php?edit=<?= (int) $image['id'] ?>" class="btn btn-sm">Edit</a>
                        <form method="post" action="gallery.php" class="inline-delete" onsubmit="return confirm('Delete this photo?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int) $image['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
