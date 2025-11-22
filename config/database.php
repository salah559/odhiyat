<?php
// Load environment variables from .env file
$env_file = dirname(dirname(__FILE__)) . '/.env';
if (file_exists($env_file)) {
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') !== 0 && strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($value));
        }
    }
}

// Try connection priorities: MySQL (from .env) > PostgreSQL (Replit) > SQLite
$pdo = null;

// 1. Try MySQL from .env first
$database_url = getenv('DATABASE_URL');
if ($database_url && strpos($database_url, 'mysql') === 0) {
    $url = parse_url($database_url);
    $db_host = $url['host'] ?? 'localhost';
    $db_port = $url['port'] ?? 3306;
    $db_user = $url['user'] ?? '';
    $db_pass = $url['pass'] ?? '';
    $db_name = ltrim($url['path'] ?? '', '/');
    
    try {
        $pdo = new PDO(
            "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4",
            $db_user,
            $db_pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
    } catch(PDOException $e) {
        $pdo = null;
    }
}

// 2. Try Replit PostgreSQL
if (!$pdo && $database_url && strpos($database_url, 'postgresql') === 0) {
    try {
        $pdo = new PDO($database_url);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $pdo = null;
    }
}

// 3. Fallback to SQLite
if (!$pdo) {
    define('DB_PATH', __DIR__ . '/../database/odhiyaty.db');
    
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
        
        $pdo->exec('PRAGMA foreign_keys = ON;');
    } catch(PDOException $e) {
        $pdo = null;
    }
}
?>
