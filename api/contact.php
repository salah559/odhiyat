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
    
    $name = clean_input($data['name'] ?? '');
    $email = clean_input($data['email'] ?? '');
    $phone = clean_input($data['phone'] ?? '');
    $subject = clean_input($data['subject'] ?? '');
    $message = clean_input($data['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($message)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'الرجاء ملء جميع الحقول المطلوبة'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'الرجاء إدخال بريد إلكتروني صحيح'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO contacts (name, email, phone, subject, message) 
        VALUES (:name, :email, :phone, :subject, :message)
    ");
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':phone' => $phone,
        ':subject' => $subject,
        ':message' => $message
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'تم إرسال رسالتك بنجاح! سنتواصل معك قريباً'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'حدث خطأ أثناء إرسال الرسالة'
    ], JSON_UNESCAPED_UNICODE);
}
