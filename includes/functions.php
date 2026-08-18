<?php
// Shared helper functions used by both the public site and the admin panel:
// output escaping, login checks, CSRF protection, and image upload handling.

// Escapes a value before printing it into HTML, so user-submitted text
// (e.g. a name typed into the contact form) can never be interpreted as
// HTML/JavaScript by the browser. Every piece of dynamic text on this site
// is wrapped in e(...) before being echoed — that's what stops XSS attacks.
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

// True if an admin is currently logged in (i.e. login.php set this session value).
function is_logged_in(): bool
{
    return isset($_SESSION['admin_id']);
}

// Called at the top of every protected admin page. Kicks visitors who
// aren't logged in back to the login screen before any admin content loads.
function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

// ---------------------------------------------------------------
// CSRF protection
//
// CSRF = Cross-Site Request Forgery: without this, a malicious website
// could trick your browser into secretly submitting a "delete product"
// form to this site while you're logged in, since browsers attach cookies
// automatically. The fix: every form includes a random token that only
// this site's own pages know about; forms from anywhere else can't guess it.
// ---------------------------------------------------------------

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Prints a hidden input carrying the current CSRF token — include this
// inside every <form method="post"> on the site.
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

// Called at the top of every POST handler. Stops the request cold (403)
// if the submitted token doesn't match the one issued to this session.
// hash_equals() is used instead of === to avoid leaking timing information
// that could help an attacker guess the token character by character.
function require_valid_csrf(): void
{
    $submitted = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $submitted)) {
        http_response_code(403);
        die('Invalid or expired form submission. Please go back and try again.');
    }
}

// ---------------------------------------------------------------
// Image upload handling (used by admin/products.php, team.php, gallery.php)
// ---------------------------------------------------------------

const ALLOWED_IMAGE_TYPES = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif',
    'image/webp' => 'webp',
];
const MAX_IMAGE_BYTES = 5 * 1024 * 1024;

/**
 * Validates and stores an uploaded image, returning [webRelativePath, errorMessage].
 * Exactly one of the two is ever non-null. If no file was submitted at all,
 * returns [null, null] so the caller knows to just keep the existing image.
 */
function handle_image_upload(string $fieldName, string $subfolder): array
{
    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return [null, null];
    }

    $file = $_FILES[$fieldName];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [null, 'Upload failed. Please try again.'];
    }

    if ($file['size'] > MAX_IMAGE_BYTES) {
        return [null, 'Image is too large (max 5MB).'];
    }

    // Don't trust the filename or the browser-reported content type — both
    // are just labels the uploader chose. Instead, open the file and ask
    // the OS what it actually is. This is what rejects a malicious script
    // renamed to look like "photo.png".
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!isset(ALLOWED_IMAGE_TYPES[$mimeType])) {
        return [null, 'Only JPG, PNG, GIF, or WEBP images are allowed.'];
    }

    // Save under a random filename rather than the original one, so two
    // people uploading "cake.jpg" can't overwrite each other, and nobody
    // can guess a filename to access an image that isn't linked anywhere.
    $extension = ALLOWED_IMAGE_TYPES[$mimeType];
    $filename = bin2hex(random_bytes(16)) . '.' . $extension;
    $destinationDir = __DIR__ . '/../assets/uploads/' . $subfolder;
    $destinationPath = $destinationDir . '/' . $filename;

    if (!is_dir($destinationDir)) {
        mkdir($destinationDir, 0755, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $destinationPath)) {
        return [null, 'Could not save the uploaded image.'];
    }

    return ['assets/uploads/' . $subfolder . '/' . $filename, null];
}

// Removes an old uploaded image from disk (called when a record is deleted,
// or when it's updated with a new image replacing the old one). The
// realpath()/str_starts_with() check makes sure we only ever delete files
// that actually live inside assets/uploads — never anything else on disk.
function delete_uploaded_image(?string $relativePath): void
{
    if (!$relativePath) {
        return;
    }
    $fullPath = __DIR__ . '/../' . $relativePath;
    if (str_starts_with(realpath($fullPath) ?: '', realpath(__DIR__ . '/../assets/uploads') ?: '---') && is_file($fullPath)) {
        unlink($fullPath);
    }
}
