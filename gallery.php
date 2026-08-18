<?php
// Public photo gallery — newest photos first, all managed from admin/gallery.php.

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = get_db_connection();
$images = $pdo->query('SELECT * FROM gallery ORDER BY created_at DESC')->fetchAll();

$pageTitle = 'Gallery — Golden Crust Bakery';
require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <span class="eyebrow">Gallery</span>
        <h1>A Look Inside the Bakery</h1>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if (empty($images)): ?>
            <p class="empty-note">No photos yet &mdash; check back soon.</p>
        <?php else: ?>
            <div class="gallery-grid">
                <?php foreach ($images as $image): ?>
                    <figure class="gallery-item">
                        <img src="/<?= e($image['image_path']) ?>" alt="<?= e($image['caption'] ?: 'Gallery photo') ?>" loading="lazy">
                        <?php if ($image['caption']): ?>
                            <figcaption><?= e($image['caption']) ?></figcaption>
                        <?php endif; ?>
                    </figure>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
