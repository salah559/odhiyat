<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/../config/init.php';

echo json_encode([
    'success' => true,
    'token' => generate_csrf_token()
], JSON_UNESCAPED_UNICODE);
