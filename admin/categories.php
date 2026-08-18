<?php
// Full CRUD for categories — same save/delete pattern as products.php
// (see that file for detailed comments on the shared approach).
// Note: categories.php has no image upload, since categories are just
// a name + sort order.
//
// Deleting a category also deletes every product in it — the products
// table's category_id column has "ON DELETE CASCADE" (see database.sql),
// so MySQL itself handles that cleanup, not this PHP code.

$pageTitle = 'Categories';
require __DIR__ . '/includes/admin_header.php';

$flash = null;
$flashType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $order = (int) ($_POST['display_order'] ?? 0);

        if ($name === '') {
            $flash = 'Category name is required.';
            $flashType = 'error';
        } elseif ($id > 0) {
            $stmt = $pdo->prepare('UPDATE categories SET name = :name, display_order = :order WHERE id = :id');
            $stmt->execute(['name' => $name, 'order' => $order, 'id' => $id]);
            $flash = 'Category updated.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO categories (name, display_order) VALUES (:name, :order)');
            $stmt->execute(['name' => $name, 'order' => $order]);
            $flash = 'Category added.';
        }
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = $pdo->prepare('DELETE FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $flash = 'Category deleted (its products were removed too).';
    }
}

$editing = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = :id');
    $stmt->execute(['id' => (int) $_GET['edit']]);
    $editing = $stmt->fetch() ?: null;
}

$categories = $pdo->query(
    'SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) AS product_count
     FROM categories c ORDER BY display_order ASC, id ASC'
)->fetchAll();
?>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flashType === 'error' ? 'error' : 'success' ?>"><?= e($flash) ?></div>
<?php endif; ?>

<div class="admin-panel">
    <div class="admin-panel-header">
        <h2><?= $editing ? 'Edit Category' : 'Add Category' ?></h2>
    </div>
    <form method="post" action="categories.php" class="inline-form">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= $editing ? (int) $editing['id'] : 0 ?>">
        <input type="text" name="name" placeholder="Category name" value="<?= e($editing['name'] ?? '') ?>" required>
        <input type="number" name="display_order" placeholder="Order" value="<?= e((string) ($editing['display_order'] ?? 0)) ?>" style="width:90px">
        <button type="submit" class="btn btn-primary"><?= $editing ? 'Save Changes' : 'Add' ?></button>
        <?php if ($editing): ?>
            <a href="categories.php" class="btn btn-sm">Cancel</a>
        <?php endif; ?>
    </form>
</div>

<div class="admin-panel">
    <div class="admin-panel-header">
        <h2>All Categories</h2>
    </div>
    <table class="admin-table">
        <thead>
        <tr>
            <th>Name</th>
            <th>Order</th>
            <th>Products</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($categories as $category): ?>
            <tr>
                <td><?= e($category['name']) ?></td>
                <td><?= (int) $category['display_order'] ?></td>
                <td><?= (int) $category['product_count'] ?></td>
                <td class="actions">
                    <a href="categories.php?edit=<?= (int) $category['id'] ?>" class="btn btn-sm">Edit</a>
                    <form method="post" action="categories.php" class="inline-delete" onsubmit="return confirm('Delete this category? Its products will be deleted too.');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= (int) $category['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
