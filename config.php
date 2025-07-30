<?php
// #################################################################
// # CONFIGURATION FILE FOR کافه دنیس WEBSITE                      #
// #################################################################

// --- DATABASE SETTINGS ---
// Replace with your actual cPanel database credentials.
define('DB_HOST', 'localhost');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');
define('DB_NAME', 'your_db_name');

// --- SITE SETTINGS ---
// The full base URL of your site with a trailing slash.
// Example: https://yourdomain.com/
// You might need to change this from the default for local testing.
define('BASE_URL', '/');

// --- ADMIN CREDENTIALS (for reference) ---
// The actual login uses a securely hashed password from the database.
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'mrdoctor11228!'); // The password to use on the login form.

// Start the session for login management.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- DATABASE CONNECTION ---
// Create a new MySQLi object for database operations.
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check for connection errors and stop if any occur.
if ($conn->connect_error) {
    // On a live site, you might want to log this error instead of displaying it.
    die("Database Connection Failed: " . $conn->connect_error);
}

// Set the character set to utf8mb4 for full Persian/Unicode support.
$conn->set_charset("utf8mb4");

?>
