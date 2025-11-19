<?php
require_once __DIR__ . '/../config/init.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!isset($data['idToken']) || !isset($data['email'])) {
    echo json_encode(['success' => false, 'message' => 'بيانات غير كاملة']);
    exit;
}

$idToken = $data['idToken'];
$email = clean_input($data['email']);
$name = isset($data['name']) ? clean_input($data['name']) : '';
$photoURL = isset($data['photoURL']) ? clean_input($data['photoURL']) : '';

try {
    $projectId = getenv('FIREBASE_PROJECT_ID');
    
    if (empty($projectId)) {
        throw new Exception('إعدادات Firebase غير مكتملة');
    }
    
    $verifyUrl = "https://identitytoolkit.googleapis.com/v1/accounts:lookup?key=" . getenv('FIREBASE_API_KEY');
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $verifyUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['idToken' => $idToken]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        throw new Exception('فشل التحقق من الحساب');
    }
    
    $userData = json_decode($response, true);
    
    if (!isset($userData['users'][0])) {
        throw new Exception('مستخدم غير صالح');
    }
    
    $firebaseUser = $userData['users'][0];
    $verifiedEmail = $firebaseUser['email'];
    
    if ($verifiedEmail !== $email) {
        throw new Exception('البريد الإلكتروني غير متطابق');
    }
    
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $admin = $stmt->fetch();
    
    if (!$admin) {
        $stmt = $pdo->prepare("INSERT INTO admins (email, password, full_name, is_super_admin) VALUES (:email, :password, :name, 0)");
        $randomPassword = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
        $stmt->execute([
            ':email' => $email,
            ':password' => $randomPassword,
            ':name' => $name ?: $email
        ]);
        
        $adminId = $pdo->lastInsertId();
        $isSuperAdmin = 0;
    } else {
        $adminId = $admin['id'];
        $isSuperAdmin = $admin['is_super_admin'];
        
        if (!empty($name) && $admin['full_name'] !== $name) {
            $stmt = $pdo->prepare("UPDATE admins SET full_name = :name WHERE id = :id");
            $stmt->execute([':name' => $name, ':id' => $adminId]);
        }
    }
    
    $_SESSION['admin_id'] = $adminId;
    $_SESSION['admin_email'] = $email;
    $_SESSION['admin_name'] = $name ?: $email;
    $_SESSION['is_super_admin'] = $isSuperAdmin;
    $_SESSION['firebase_auth'] = true;
    
    echo json_encode([
        'success' => true,
        'message' => 'تم تسجيل الدخول بنجاح',
        'redirect' => BASE_URL . '/admin/'
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
