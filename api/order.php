<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'طريقة الطلب غير مسموحة'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!verify_csrf_token($data['csrf_token'] ?? '')) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'خطأ في التحقق من الجلسة'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $product_id = $data['product_id'] ?? '';
    $customer_name = clean_input($data['customer_name'] ?? '');
    $customer_phone = clean_input($data['customer_phone'] ?? '');
    $customer_email = clean_input($data['customer_email'] ?? '');
    $customer_address = clean_input($data['customer_address'] ?? '');
    $notes = clean_input($data['notes'] ?? '');
    
    if (empty($product_id) || empty($customer_name) || empty($customer_phone)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'الرجاء ملء جميع الحقول المطلوبة'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare("SELECT id, status FROM products WHERE id = :id FOR UPDATE");
    $stmt->execute([':id' => $product_id]);
    $product = $stmt->fetch();
    
    if (!$product || $product['status'] != 'available') {
        $pdo->rollBack();
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'عذراً، هذه الأضحية لم تعد متاحة. تم حجزها من قبل عميل آخر'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO orders (product_id, customer_name, customer_phone, customer_email, customer_address, notes, status) 
        VALUES (:product_id, :customer_name, :customer_phone, :customer_email, :customer_address, :notes, 'pending')
    ");
    $stmt->execute([
        ':product_id' => $product_id,
        ':customer_name' => $customer_name,
        ':customer_phone' => $customer_phone,
        ':customer_email' => $customer_email,
        ':customer_address' => $customer_address,
        ':notes' => $notes
    ]);
    
    $stmt = $pdo->prepare("UPDATE products SET status = 'reserved' WHERE id = :id");
    $stmt->execute([':id' => $product_id]);
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'تم إرسال طلبك بنجاح! سيتم التواصل معك قريباً'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'حدث خطأ أثناء إرسال الطلب'
    ], JSON_UNESCAPED_UNICODE);
}
