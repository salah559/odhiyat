<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/../config/init.php';

try {
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'معرف المنتج مطلوب'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute([':id' => $_GET['id']]);
    $product = $stmt->fetch();
    
    if (!$product) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'المنتج غير موجود'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $product
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'حدث خطأ في جلب البيانات'
    ], JSON_UNESCAPED_UNICODE);
}
