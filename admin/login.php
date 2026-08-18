<?php
// Admin login form + authentication check.
// This is the only entry point into the admin panel — every other admin
// page relies on the session values set here (see require_login() in
// includes/functions.php).

session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Already logged in? Skip straight to the dashboard instead of showing the form again.
if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $pdo = get_db_connection();
    // Prepared statement (the :username placeholder) — the submitted
    // username can never be interpreted as SQL, only as plain data.
    $stmt = $pdo->prepare('SELECT * FROM admins WHERE username = :username');
    $stmt->execute(['username' => $username]);
    $admin = $stmt->fetch();

    // password_verify() re-hashes the typed password the same way
    // password_hash() did when the account was created (see database.sql)
    // and compares the two hashes. The real password is never stored or
    // compared directly — only its one-way hash ever touches the database.
    if ($admin && password_verify($password, $admin['password_hash'])) {
        // Regenerating the session ID on login prevents "session fixation" —
        // stops an attacker who somehow knew the pre-login session ID from
        // reusing it to hijack the now-authenticated session.
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        header('Location: dashboard.php');
        exit;
    }

    // Deliberately the same generic message whether the username doesn't
    // exist or the password is wrong — doesn't reveal which one to an attacker.
    $error = 'Incorrect username or password.';
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Golden Crust Bakery</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="admin-login-body">
<div class="admin-login-card">
    <div class="brand">
        <span class="brand-mark">GC</span>
        Golden Crust Bakery
    </div>
    <h1>Admin Login</h1>
    <p class="muted">Sign in to manage the bakery website.</p>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="login.php" class="stacked-form">
        <div class="form-row">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required autofocus>
        </div>
        <div class="form-row">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Log In</button>
    </form>

    <p class="muted small"><a href="/index.php">&larr; Back to website</a></p>
</div>
</body>
</html>
