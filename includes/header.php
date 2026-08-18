<?php
// Shared header for every public page: the <head>, the sticky nav bar,
// and the opening <main> tag. Each public page does:
//   $pageTitle = '...';
//   require __DIR__ . '/includes/header.php';
//   ...page content...
//   require __DIR__ . '/includes/footer.php';

if (!isset($pageTitle)) {
    $pageTitle = 'Golden Crust Bakery';
}
$currentPage = basename($_SERVER['SCRIPT_NAME']); // used below to highlight the active nav link
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a href="/index.php" class="brand">
            <span class="brand-mark">GC</span>
            Golden Crust Bakery
        </a>
        <button class="nav-toggle" id="navToggle" aria-label="Toggle menu" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>
        <nav class="site-nav" id="siteNav">
            <a href="/index.php" class="<?= $currentPage === 'index.php' ? 'active' : '' ?>">Home</a>
            <a href="/about.php" class="<?= $currentPage === 'about.php' ? 'active' : '' ?>">About</a>
            <a href="/services.php" class="<?= $currentPage === 'services.php' ? 'active' : '' ?>">Products</a>
            <a href="/gallery.php" class="<?= $currentPage === 'gallery.php' ? 'active' : '' ?>">Gallery</a>
            <a href="/contact.php" class="<?= $currentPage === 'contact.php' ? 'active' : '' ?>">Contact</a>
        </nav>
    </div>
</header>
<main>
