<?php
// Database connection settings.
// Change these if you created the MySQL user with a different name/password
// (see the "Setup" section in README.md).

function get_db_connection(): PDO
{
    // "static" makes this variable persist between calls, so every page
    // reuses the same connection instead of opening a new one each time.
    static $pdo = null;

    if ($pdo === null) {
        $host = 'localhost';
        $dbName = 'golden_crust_bakery';
        $username = 'bakery_app';
        $password = 'BakeryApp!2026';

        $dsn = "mysql:host={$host};dbname={$dbName};charset=utf8mb4";
        $options = [
            // Throw exceptions on DB errors instead of failing silently.
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // Return rows as associative arrays, e.g. $row['name'].
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // Use real prepared statements (sent to MySQL as ? placeholders)
            // instead of PHP faking them client-side — this is what actually
            // protects every query in this project from SQL injection.
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $pdo = new PDO($dsn, $username, $password, $options);
    }

    return $pdo;
}
