<?php
// Ends the admin session: clears all session data, destroys the session
// on the server, then sends the browser back to the login page.
session_start();
$_SESSION = [];
session_destroy();
header('Location: login.php');
exit;
