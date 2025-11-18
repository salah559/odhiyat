<?php
session_start();

define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST']);
define('SITE_NAME', 'Odhiyaty');
define('UPLOAD_PATH', __DIR__ . '/../uploads/products/');
define('UPLOAD_URL', BASE_URL . '/uploads/products/');

date_default_timezone_set('Africa/Casablanca');

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../includes/functions.php';
?>