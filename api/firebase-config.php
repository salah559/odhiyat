<?php
ob_start();
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, no-store, must-revalidate');

// Load environment variables from .env file
$env_file = dirname(dirname(__FILE__)) . '/.env';

$config = [];

if (file_exists($env_file)) {
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse key=value
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Only return Firebase config keys
            if (strpos($key, 'FIREBASE_') === 0) {
                $config[$key] = $value;
            }
        }
    }
}

// Map .env keys to Firebase config object format
$firebaseConfig = [
    'apiKey' => $config['FIREBASE_API_KEY'] ?? '',
    'authDomain' => $config['FIREBASE_AUTH_DOMAIN'] ?? '',
    'projectId' => $config['FIREBASE_PROJECT_ID'] ?? '',
    'storageBucket' => $config['FIREBASE_STORAGE_BUCKET'] ?? '',
    'messagingSenderId' => $config['FIREBASE_MESSAGING_SENDER_ID'] ?? '',
    'appId' => $config['FIREBASE_APP_ID'] ?? ''
];

// Return as JSON
$output = json_encode($firebaseConfig);
ob_end_clean();
echo $output;
?>
