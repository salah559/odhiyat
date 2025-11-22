<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

ob_start();
error_reporting(0);
ini_set('display_errors', 0);

require_once(__DIR__ . '/../config/database.php');

global $pdo;

if (!isset($pdo) || !$pdo) {
    http_response_code(503);
    ob_end_clean();
    echo json_encode(['error' => 'خدمة قاعدة البيانات غير متاحة الآن']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? null;

if ($action === 'signin') {
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    
    if (!$email || !$password) {
        http_response_code(400);
        ob_end_clean();
        echo json_encode(['error' => 'البريد والكلمة مطلوبان']);
        exit;
    }
    
    try {
        if (!isset($pdo)) {
            throw new Exception('Database not available');
        }
        
        $stmt = $pdo->prepare("SELECT id, email, full_name FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => 'بيانات غير صحيحة']);
            exit;
        }
        
        echo json_encode([
            'success' => true,
            'user' => [
                'id' => $user['id'],
                'email' => $user['email'],
                'name' => $user['full_name'],
                'uid' => 'user_' . $user['id']
            ]
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'خطأ في الخادم: ' . $e->getMessage()]);
    }
    exit;
}

if ($action === 'firebase-signin') {
    $token = $_POST['token'] ?? null;
    $email = $_POST['email'] ?? null;
    $name = $_POST['name'] ?? null;
    $uid = $_POST['uid'] ?? null;
    $accountType = $_POST['accountType'] ?? 'buyer';
    
    if (!$email || !$uid) {
        http_response_code(400);
        ob_end_clean();
        echo json_encode(['error' => 'بيانات ناقصة']);
        exit;
    }
    
    try {
        if (!isset($pdo)) {
            throw new Exception('Database not available');
        }
        
        $firebase_uid = 'firebase_' . $uid;
        
        // Check if users table exists
        $tableCheck = $pdo->query("SELECT EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'users' LIMIT 1) AS table_exists")->fetch();
        if (!$tableCheck || !$tableCheck['table_exists']) {
            throw new Exception('Database tables not initialized. Please visit /setup.html first');
        }
        
        $stmt = $pdo->prepare("SELECT id, email, full_name FROM users WHERE firebase_uid = ?");
        $stmt->execute([$firebase_uid]);
        $user = $stmt->fetch();
        
        if (!$user) {
            $stmt = $pdo->prepare("INSERT INTO users (firebase_uid, email, full_name, account_type) VALUES (?, ?, ?, ?)");
            $stmt->execute([$firebase_uid, $email, $name, $accountType]);
            $userId = $pdo->lastInsertId();
        } else {
            $userId = $user['id'];
        }
        
        ob_end_clean();
        echo json_encode([
            'success' => true,
            'user' => [
                'id' => $userId,
                'email' => $email,
                'name' => $name,
                'uid' => $firebase_uid
            ]
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        ob_end_clean();
        echo json_encode(['error' => 'خطأ: ' . $e->getMessage()]);
    }
    exit;
}

if ($action === 'signup') {
    $email = $_POST['email'] ?? null;
    $fullName = $_POST['fullName'] ?? null;
    $accountType = $_POST['accountType'] ?? 'buyer';
    
    if (!$email || !$fullName) {
        http_response_code(400);
        echo json_encode(['error' => 'البيانات ناقصة']);
        exit;
    }
    
    try {
        if (!isset($pdo)) {
            throw new Exception('Database not available');
        }
        
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode(['error' => 'البريد مستخدم بالفعل']);
            exit;
        }
        
        $firebase_uid = 'user_' . bin2hex(random_bytes(8));
        $stmt = $pdo->prepare("INSERT INTO users (firebase_uid, email, full_name, account_type) VALUES (?, ?, ?, ?)");
        $stmt->execute([$firebase_uid, $email, $fullName, $accountType]);
        
        echo json_encode([
            'success' => true,
            'user' => [
                'uid' => $firebase_uid,
                'email' => $email,
                'name' => $fullName
            ]
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'خطأ: ' . $e->getMessage()]);
    }
    exit;
}

echo json_encode(['error' => 'إجراء غير صحيح']);
?>
