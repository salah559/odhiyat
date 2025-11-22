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

// Use MySQL only from .env
$pdo = null;
$database_url = getenv('DATABASE_URL');

if (!$database_url || strpos($database_url, 'mysql') !== 0) {
    http_response_code(500);
    die(json_encode(['error' => 'DATABASE_URL not configured or invalid format']));
}

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
    http_response_code(500);
    die(json_encode(['error' => 'MySQL Connection failed: ' . $e->getMessage()]));
}
?>
