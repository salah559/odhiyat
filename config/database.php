<?php
// SQLite configuration for Replit environment
define('DB_PATH', __DIR__ . '/../database/odhiyaty.db');

// Create database directory if it doesn't exist
if (!is_dir(__DIR__ . '/../database')) {
    mkdir(__DIR__ . '/../database', 0755, true);
}

try {
    $pdo = new PDO(
        "sqlite:" . DB_PATH,
        null,
        null,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    // Enable foreign keys for SQLite
    $pdo->exec('PRAGMA foreign_keys = ON;');
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
