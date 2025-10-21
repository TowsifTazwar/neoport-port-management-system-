<?php
// config/db.php

// --- Database Connection ---
// This file provides a centralized way to connect to the Port Management System database.

// --- Configuration ---
// Replace these values with your actual database credentials if they differ from the defaults.
define('DB_HOST', '127.0.0.1'); // Use 127.0.0.1 instead of 'localhost' to avoid potential DNS lookup issues
define('DB_NAME', 'pms');       // The name of your database
define('DB_USER', 'root');      // Your database username (default for XAMPP is 'root')
define('DB_PASS', '');          // Your database password (default for XAMPP is empty)
define('DB_CHAR', 'utf8mb4');   // The character set

// --- PDO Connection Function ---
// This function creates and returns a PDO database connection object.
// It's designed to be called whenever a database interaction is needed.
if (!function_exists('pms_pdo')) {
  function pms_pdo() {
    // Set DSN (Data Source Name)
    $dsn = "mysql:host=" . DB_HOST . ";port=3307;dbname=" . DB_NAME . ";charset=" . DB_CHAR;

    // Set PDO options
    $options = [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch associative arrays
      PDO::ATTR_EMULATE_PREPARES   => false,                  // Use native prepared statements
    ];

    try {
      // Create and return the PDO instance
      return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
      // If connection fails, stop the script and show a generic error message.
      // In a production environment, you would log this error instead of showing it to the user.
      die("Database Connection Error: " . $e->getMessage());

    }
  }
}

// For scripts that might expect a global $pdo variable (legacy compatibility)
try {
  $pdo = pms_pdo();
} catch (RuntimeException $e) {
  $pdo = null; // Ensure $pdo is null if the connection fails
}
