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

// Use Replit PostgreSQL (built-in and reliable)
$pdo = null;
$database_url = getenv('DATABASE_URL');

if (!$database_url) {
    http_response_code(500);
    die(json_encode(['error' => 'DATABASE_URL not configured']));
}

try {
    $pdo = new PDO(
        $database_url,
        null,
        null,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Database Connection failed: ' . $e->getMessage()]));
}
?>
