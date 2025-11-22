<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once(__DIR__ . '/../config/database.php');

global $pdo;

$token = $_POST['token'] ?? null;

if (!$token) {
    http_response_code(400);
    echo json_encode(['error' => 'Token مفقود']);
    exit;
}

try {
    // التحقق من التوكن مع Google
    $url = 'https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=' . urlencode($token);
    $response = @file_get_contents($url);
    
    if (!$response) {
        throw new Exception('فشل التحقق من التوكن');
    }
    
    $data = json_decode($response, true);
    
    if (!isset($data['email'])) {
        throw new Exception('بيانات التوكن غير صحيحة');
    }
    
    $email = $data['email'];
    $name = $data['name'] ?? 'مستخدم جديد';
    $accountType = $_POST['accountType'] ?? 'buyer';
    
    if (!isset($pdo)) {
        throw new Exception('قاعدة البيانات غير متاحة');
    }
    
    // البحث عن المستخدم
    $stmt = $pdo->prepare("SELECT id, email, full_name FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user) {
        // إنشاء مستخدم جديد
        $firebase_uid = 'google_' . bin2hex(random_bytes(8));
        $stmt = $pdo->prepare("INSERT INTO users (firebase_uid, email, full_name, account_type) VALUES (?, ?, ?, ?)");
        $stmt->execute([$firebase_uid, $email, $name, $accountType]);
        $userId = $pdo->lastInsertId();
    } else {
        $userId = $user['id'];
    }
    
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $userId,
            'email' => $email,
            'name' => $name,
            'uid' => 'google_' . $userId
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['error' => 'خطأ: ' . $e->getMessage()]);
}
?>
