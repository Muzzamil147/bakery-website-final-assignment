<?php
// Public contact form. Validates the input server-side (never trust a form
// just because JavaScript validated it client-side first), saves valid
// submissions into contact_messages, and shows them to staff in
// admin/messages.php.

session_start(); // needed for the "message sent" flash confirmation below
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = get_db_connection();
$errors = [];
$old = ['name' => '', 'email' => '', 'phone' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['name'] = trim($_POST['name'] ?? '');
    $old['email'] = trim($_POST['email'] ?? '');
    $old['phone'] = trim($_POST['phone'] ?? '');
    $old['message'] = trim($_POST['message'] ?? '');

    if ($old['name'] === '') {
        $errors[] = 'Please enter your name.';
    }
    if ($old['email'] === '' || !filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if ($old['message'] === '') {
        $errors[] = 'Please enter a message.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            'INSERT INTO contact_messages (name, email, phone, message) VALUES (:name, :email, :phone, :message)'
        );
        $stmt->execute([
            'name' => $old['name'],
            'email' => $old['email'],
            'phone' => $old['phone'] !== '' ? $old['phone'] : null,
            'message' => $old['message'],
        ]);

        // Redirect-after-POST (the "PRG pattern"): if we just rendered a
        // "thank you" message directly here, refreshing the page would
        // re-submit the form and insert a duplicate message. Redirecting
        // to a fresh GET request avoids that.
        $_SESSION['contact_sent'] = true;
        header('Location: /contact.php');
        exit;
    }
}

// Read (and clear) the one-time "thanks" flag set by the redirect above.
$sent = !empty($_SESSION['contact_sent']);
unset($_SESSION['contact_sent']);

$pageTitle = 'Contact Us — Golden Crust Bakery';
require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <span class="eyebrow">Contact</span>
        <h1>We'd Love to Hear From You</h1>
    </div>
</section>

<section class="section">
    <div class="container contact-layout">
        <div class="contact-form-wrap">
            <?php if ($sent): ?>
                <div class="alert alert-success">Thanks! Your message has been sent &mdash; we'll get back to you soon.</div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= e($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" action="/contact.php" class="stacked-form">
                <div class="form-row">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="<?= e($old['name']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= e($old['email']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="phone">Phone <span class="optional">(optional)</span></label>
                    <input type="tel" id="phone" name="phone" value="<?= e($old['phone']) ?>">
                </div>
                <div class="form-row">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="5" required><?= e($old['message']) ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send Message</button>
            </form>
        </div>

        <div class="contact-info">
            <h3>Visit Us</h3>
            <p>221 Maple Street<br>Riverside, CA 92501</p>
            <h3>Hours</h3>
            <p>Tuesday &ndash; Sunday: 7:00 AM &ndash; 6:00 PM<br>Monday: Closed</p>
            <h3>Reach Us</h3>
            <p><a href="tel:+19095550142">(909) 555-0142</a><br>
                <a href="mailto:hello@goldencrustbakery.test">hello@goldencrustbakery.test</a></p>
            <div class="map-embed">
                <iframe
                    title="Map"
                    src="https://www.openstreetmap.org/export/embed.html?bbox=-117.4025%2C33.9686%2C-117.3625%2C33.9986&layer=mapnik"
                    loading="lazy"></iframe>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
