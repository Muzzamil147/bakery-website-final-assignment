<?php
// Full menu page: fetches all available products, then groups them by
// category in PHP so the template below can render one section per
// category (Breads, Cakes, Pastries, ...) instead of one flat list.
// Products marked "unavailable" in the admin panel are excluded here.

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = get_db_connection();
$categories = $pdo->query('SELECT * FROM categories ORDER BY display_order ASC, id ASC')->fetchAll();
$products = $pdo->query(
    'SELECT * FROM products WHERE is_available = 1 ORDER BY category_id ASC, name ASC'
)->fetchAll();

// Group the flat product list into $productsByCategory[category_id] = [products...]
$productsByCategory = [];
foreach ($products as $product) {
    $productsByCategory[$product['category_id']][] = $product;
}

$pageTitle = 'Our Menu — Golden Crust Bakery';
require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <span class="eyebrow">Our Menu</span>
        <h1>Breads, Cakes &amp; Pastries</h1>
        <p>Baked in small batches, every single morning.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if (empty($categories)): ?>
            <p class="empty-note">Our menu is being updated &mdash; please check back soon.</p>
        <?php endif; ?>

        <?php foreach ($categories as $category): ?>
            <?php $items = $productsByCategory[$category['id']] ?? []; ?>
            <?php if (empty($items)) continue; ?>
            <div class="menu-category" id="category-<?= (int) $category['id'] ?>">
                <h2><?= e($category['name']) ?></h2>
                <div class="product-grid">
                    <?php foreach ($items as $product): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <img src="/<?= e($product['image_path']) ?>" alt="<?= e($product['name']) ?>" loading="lazy">
                            </div>
                            <div class="product-body">
                                <h3><?= e($product['name']) ?></h3>
                                <p class="product-description"><?= e($product['description']) ?></p>
                                <p class="product-price">$<?= number_format((float) $product['price'], 2) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
