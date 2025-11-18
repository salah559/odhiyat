<?php

function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function is_logged_in() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function check_admin() {
    if (!is_logged_in()) {
        redirect(BASE_URL . '/admin/login.php');
    }
}

function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function upload_images($files) {
    $uploaded_files = [];
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
    $max_size = 5 * 1024 * 1024;
    
    for ($i = 0; $i < count($files['name']); $i++) {
        if ($files['error'][$i] === UPLOAD_ERR_OK) {
            $file_type = $files['type'][$i];
            $file_size = $files['size'][$i];
            
            if (!in_array($file_type, $allowed_types)) {
                continue;
            }
            
            if ($file_size > $max_size) {
                continue;
            }
            
            $extension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
            $new_filename = uniqid() . '_' . time() . '.' . $extension;
            $destination = UPLOAD_PATH . $new_filename;
            
            if (move_uploaded_file($files['tmp_name'][$i], $destination)) {
                $uploaded_files[] = $new_filename;
            }
        }
    }
    
    return $uploaded_files;
}

function delete_image($filename) {
    $file_path = UPLOAD_PATH . $filename;
    if (file_exists($file_path)) {
        unlink($file_path);
    }
}

function format_price($price) {
    return number_format($price, 2) . ' DH';
}

function get_product_status_badge($status) {
    $badges = [
        'available' => '<span class="badge bg-success">متوفرة</span>',
        'sold' => '<span class="badge bg-danger">مباعة</span>',
        'reserved' => '<span class="badge bg-warning">محجوزة</span>'
    ];
    return $badges[$status] ?? '';
}

function get_order_status_badge($status) {
    $badges = [
        'pending' => '<span class="badge bg-warning">قيد الانتظار</span>',
        'confirmed' => '<span class="badge bg-success">مؤكد</span>',
        'cancelled' => '<span class="badge bg-danger">ملغي</span>'
    ];
    return $badges[$status] ?? '';
}

function get_stats() {
    global $pdo;
    
    $stats = [];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM products");
    $stats['total_products'] = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM products WHERE status = 'available'");
    $stats['available_products'] = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM products WHERE status = 'sold'");
    $stats['sold_products'] = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM orders");
    $stats['total_orders'] = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'");
    $stats['pending_orders'] = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE status = 'confirmed'");
    $stats['confirmed_orders'] = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT SUM(p.price) as total FROM orders o JOIN products p ON o.product_id = p.id WHERE o.status = 'confirmed'");
    $result = $stmt->fetch();
    $stats['total_revenue'] = $result['total'] ?? 0;
    
    return $stats;
}
?>