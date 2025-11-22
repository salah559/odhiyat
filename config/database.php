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

// Use Replit PostgreSQL with individual environment variables
$pdo = null;

$pg_host = getenv('PGHOST');
$pg_port = getenv('PGPORT') ?: 5432;
$pg_user = getenv('PGUSER');
$pg_password = getenv('PGPASSWORD');
$pg_database = getenv('PGDATABASE');

if (!$pg_host || !$pg_user || !$pg_database) {
    http_response_code(500);
    die(json_encode(['error' => 'PostgreSQL environment variables not configured']));
}

try {
    $dsn = "pgsql:host={$pg_host};port={$pg_port};dbname={$pg_database}";
    $pdo = new PDO(
        $dsn,
        $pg_user,
        $pg_password,
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
