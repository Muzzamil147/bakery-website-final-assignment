<?php
// Shared layout for every admin page: starts the session, blocks anyone
// who isn't logged in (require_login()), opens the database connection,
// and renders the sidebar + top bar. Each admin/*.php page does:
//   require __DIR__ . '/includes/admin_header.php';
//   ...its own content...
//   require __DIR__ . '/includes/admin_footer.php';

session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_login(); // redirects to login.php and exits if not authenticated

$pdo = get_db_connection();
$currentPage = basename($_SERVER['SCRIPT_NAME']); // used below to highlight the active sidebar link

if (!isset($pageTitle)) {
    $pageTitle = 'Admin';
}

$navItems = [
    'dashboard.php' => 'Dashboard',
    'products.php' => 'Products',
    'categories.php' => 'Categories',
    'team.php' => 'Team',
    'gallery.php' => 'Gallery',
    'messages.php' => 'Messages',
];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> — Admin</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="admin-body">
<div class="admin-shell">
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="admin-brand">
            <span class="brand-mark">GC</span>
            Golden Crust
        </div>
        <nav class="admin-nav">
            <?php foreach ($navItems as $href => $label): ?>
                <a href="<?= e($href) ?>" class="<?= $currentPage === $href ? 'active' : '' ?>"><?= e($label) ?></a>
            <?php endforeach; ?>
        </nav>
        <div class="admin-sidebar-footer">
            <a href="/index.php" target="_blank">View Site &#8599;</a>
            <a href="logout.php">Log Out</a>
        </div>
    </aside>
    <div class="admin-content">
        <header class="admin-topbar">
            <button class="nav-toggle admin-nav-toggle" id="adminNavToggle" aria-label="Toggle menu">
                <span></span><span></span><span></span>
            </button>
            <h1><?= e($pageTitle) ?></h1>
            <span class="admin-user">Signed in as <?= e($_SESSION['admin_username']) ?></span>
        </header>
        <div class="admin-main">
