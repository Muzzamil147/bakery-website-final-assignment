<?php
// Full CRUD for products: this one file handles Create, Update, and Delete
// (via the $action branches below) plus Read (the $products query near the
// bottom). Categories/Team/Gallery all follow this same shape.

$pageTitle = 'Products';
require __DIR__ . '/includes/admin_header.php'; // starts the session, checks login, gives us $pdo

$flash = null;
$flashType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf(); // reject the request outright if the CSRF token doesn't match
    $action = $_POST['action'] ?? '';

    if ($action === 'save') { // handles BOTH "add new" and "edit existing" — see the $id check below
        $id = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = (float) ($_POST['price'] ?? 0);
        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $isAvailable = isset($_POST['is_available']) ? 1 : 0;

        // Validate the image (if one was submitted) before touching the database at all.
        [$uploadedPath, $uploadError] = handle_image_upload('image', 'products');

        if ($name === '' || $categoryId <= 0) {
            $flash = 'Name and category are required.';
            $flashType = 'error';
        } elseif ($uploadError) {
            $flash = $uploadError;
            $flashType = 'error';
        } else {
            if ($id > 0) {
                // id > 0 means this is an EDIT of an existing product.
                if ($uploadedPath) {
                    // A new image was uploaded — delete the old file from disk
                    // first so we don't leave orphaned images behind, then
                    // update every column including the new image path.
                    $existing = $pdo->prepare('SELECT image_path FROM products WHERE id = :id');
                    $existing->execute(['id' => $id]);
                    delete_uploaded_image($existing->fetchColumn() ?: null);
                    $stmt = $pdo->prepare(
                        'UPDATE products SET category_id=:cat, name=:name, description=:desc, price=:price,
                         image_path=:image, is_available=:avail WHERE id=:id'
                    );
                    $stmt->execute(['cat' => $categoryId, 'name' => $name, 'desc' => $description, 'price' => $price,
                        'image' => $uploadedPath, 'avail' => $isAvailable, 'id' => $id]);
                } else {
                    // No new image chosen — update everything except image_path,
                    // so the existing photo is left alone.
                    $stmt = $pdo->prepare(
                        'UPDATE products SET category_id=:cat, name=:name, description=:desc, price=:price,
                         is_available=:avail WHERE id=:id'
                    );
                    $stmt->execute(['cat' => $categoryId, 'name' => $name, 'desc' => $description, 'price' => $price,
                        'avail' => $isAvailable, 'id' => $id]);
                }
                $flash = 'Product updated.';
            } else {
                // id is 0 — this is a brand new product (Create).
                $stmt = $pdo->prepare(
                    'INSERT INTO products (category_id, name, description, price, image_path, is_available)
                     VALUES (:cat, :name, :desc, :price, :image, :avail)'
                );
                $stmt->execute(['cat' => $categoryId, 'name' => $name, 'desc' => $description, 'price' => $price,
                    'image' => $uploadedPath, 'avail' => $isAvailable]);
                $flash = 'Product added.';
            }
        }
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        // Delete the uploaded image file first, then the database row —
        // otherwise a failed delete could leave an orphaned image on disk.
        $existing = $pdo->prepare('SELECT image_path FROM products WHERE id = :id');
        $existing->execute(['id' => $id]);
        delete_uploaded_image($existing->fetchColumn() ?: null);
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $flash = 'Product deleted.';
    }
}

// If the URL has ?edit=<id>, load that product so the form below can be
// pre-filled with its current values instead of showing a blank "Add" form.
$editing = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
    $stmt->execute(['id' => (int) $_GET['edit']]);
    $editing = $stmt->fetch() ?: null;
}

// READ: everything needed to render the page — the category dropdown
// options, and the full product list (joined with categories so we can
// show the category name instead of just its id).
$categories = $pdo->query('SELECT * FROM categories ORDER BY display_order ASC, id ASC')->fetchAll();
$products = $pdo->query(
    'SELECT p.*, c.name AS category_name FROM products p
     JOIN categories c ON c.id = p.category_id
     ORDER BY p.id DESC'
)->fetchAll();
?>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flashType === 'error' ? 'error' : 'success' ?>"><?= e($flash) ?></div>
<?php endif; ?>

<?php if (empty($categories)): ?>
    <div class="alert alert-error">You need at least one category before adding products. <a href="categories.php">Add one here</a>.</div>
<?php endif; ?>

<div class="admin-panel">
    <div class="admin-panel-header">
        <h2><?= $editing ? 'Edit Product' : 'Add Product' ?></h2>
    </div>
    <form method="post" action="products.php" enctype="multipart/form-data" class="stacked-form form-grid">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= $editing ? (int) $editing['id'] : 0 ?>">

        <div class="form-row">
            <label>Name</label>
            <input type="text" name="name" value="<?= e($editing['name'] ?? '') ?>" required>
        </div>
        <div class="form-row">
            <label>Category</label>
            <select name="category_id" required>
                <option value="">Select a category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int) $category['id'] ?>" <?= (int) ($editing['category_id'] ?? 0) === (int) $category['id'] ? 'selected' : '' ?>>
                        <?= e($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-row form-row-full">
            <label>Description</label>
            <textarea name="description" rows="3"><?= e($editing['description'] ?? '') ?></textarea>
        </div>
        <div class="form-row">
            <label>Price ($)</label>
            <input type="number" name="price" step="0.01" min="0" value="<?= e((string) ($editing['price'] ?? '')) ?>" required>
        </div>
        <div class="form-row">
            <label>Image <?= $editing ? '<span class="optional">(leave empty to keep current)</span>' : '' ?></label>
            <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
        </div>
        <?php if ($editing && $editing['image_path']): ?>
            <div class="form-row">
                <label>Current Image</label>
                <img src="/<?= e($editing['image_path']) ?>" alt="" class="thumb-preview">
            </div>
        <?php endif; ?>
        <div class="form-row form-row-full form-row-checkbox">
            <label>
                <input type="checkbox" name="is_available" <?= ($editing['is_available'] ?? 1) ? 'checked' : '' ?>>
                Available on menu
            </label>
        </div>

        <div class="form-actions form-row-full">
            <button type="submit" class="btn btn-primary"><?= $editing ? 'Save Changes' : 'Add Product' ?></button>
            <?php if ($editing): ?>
                <a href="products.php" class="btn">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="admin-panel">
    <div class="admin-panel-header">
        <h2>All Products (<?= count($products) ?>)</h2>
    </div>
    <table class="admin-table">
        <thead>
        <tr>
            <th></th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><img src="/<?= e($product['image_path']) ?>" alt="" class="table-thumb"></td>
                <td><?= e($product['name']) ?></td>
                <td><?= e($product['category_name']) ?></td>
                <td>$<?= number_format((float) $product['price'], 2) ?></td>
                <td>
                    <span class="badge <?= $product['is_available'] ? 'badge-active' : 'badge-inactive' ?>">
                        <?= $product['is_available'] ? 'Available' : 'Hidden' ?>
                    </span>
                </td>
                <td class="actions">
                    <a href="products.php?edit=<?= (int) $product['id'] ?>" class="btn btn-sm">Edit</a>
                    <form method="post" action="products.php" class="inline-delete" onsubmit="return confirm('Delete this product?');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
