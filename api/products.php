<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/../config/init.php';

try {
    $where_clauses = ["status = 'available'"];
    $params = [];
    
    if (isset($_GET['type']) && !empty($_GET['type'])) {
        $where_clauses[] = "type = :type";
        $params[':type'] = $_GET['type'];
    }
    
    if (isset($_GET['min_weight']) && !empty($_GET['min_weight'])) {
        $where_clauses[] = "weight >= :min_weight";
        $params[':min_weight'] = $_GET['min_weight'];
    }
    
    if (isset($_GET['max_weight']) && !empty($_GET['max_weight'])) {
        $where_clauses[] = "weight <= :max_weight";
        $params[':max_weight'] = $_GET['max_weight'];
    }
    
    if (isset($_GET['min_price']) && !empty($_GET['min_price'])) {
        $where_clauses[] = "price >= :min_price";
        $params[':min_price'] = $_GET['min_price'];
    }
    
    if (isset($_GET['max_price']) && !empty($_GET['max_price'])) {
        $where_clauses[] = "price <= :max_price";
        $params[':max_price'] = $_GET['max_price'];
    }
    
    $where_sql = implode(' AND ', $where_clauses);
    $sql = "SELECT * FROM products WHERE $where_sql ORDER BY created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
    
    $response = [
        'success' => true,
        'data' => $products,
        'count' => count($products)
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'حدث خطأ في جلب البيانات'
    ], JSON_UNESCAPED_UNICODE);
}
