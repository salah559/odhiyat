<?php
ob_start();
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

require_once(__DIR__ . '/../config/database.php');

global $pdo;

if (!$pdo) {
    http_response_code(500);
    ob_end_clean();
    die(json_encode(['error' => 'Database connection failed']));
}

$tables = [
    "CREATE TABLE IF NOT EXISTS users (
        id SERIAL PRIMARY KEY,
        firebase_uid VARCHAR(255) UNIQUE NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        full_name VARCHAR(255) NOT NULL,
        phone VARCHAR(20),
        account_type VARCHAR(50) DEFAULT 'buyer',
        profile_image VARCHAR(500),
        bio TEXT,
        address TEXT,
        city VARCHAR(100),
        region VARCHAR(100),
        is_active BOOLEAN DEFAULT TRUE,
        rating DECIMAL(3, 2) DEFAULT 0,
        total_reviews INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    "CREATE INDEX IF NOT EXISTS idx_email ON users(email)",
    "CREATE INDEX IF NOT EXISTS idx_account_type ON users(account_type)",
    "CREATE INDEX IF NOT EXISTS idx_created_at ON users(created_at)",

    "CREATE TABLE IF NOT EXISTS products (
        id SERIAL PRIMARY KEY,
        seller_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
        name VARCHAR(255) NOT NULL,
        category VARCHAR(50) NOT NULL,
        breed VARCHAR(100),
        age INT,
        weight DECIMAL(10, 2),
        color VARCHAR(100),
        price DECIMAL(12, 2) NOT NULL,
        description TEXT,
        image_url VARCHAR(500),
        health_status VARCHAR(100),
        vaccination_status BOOLEAN DEFAULT FALSE,
        vaccination_date DATE,
        available BOOLEAN DEFAULT TRUE,
        views INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    "CREATE INDEX IF NOT EXISTS idx_seller_id ON products(seller_id)",
    "CREATE INDEX IF NOT EXISTS idx_category ON products(category)",
    "CREATE INDEX IF NOT EXISTS idx_available ON products(available)",
    "CREATE INDEX IF NOT EXISTS idx_price ON products(price)",

    "CREATE TABLE IF NOT EXISTS orders (
        id SERIAL PRIMARY KEY,
        buyer_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
        product_id INT NOT NULL REFERENCES products(id) ON DELETE CASCADE,
        seller_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
        quantity INT DEFAULT 1,
        total_price DECIMAL(12, 2) NOT NULL,
        order_status VARCHAR(50) DEFAULT 'pending',
        delivery_address TEXT,
        delivery_date DATE,
        payment_method VARCHAR(100),
        payment_status VARCHAR(50) DEFAULT 'pending',
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    "CREATE INDEX IF NOT EXISTS idx_buyer_id ON orders(buyer_id)",
    "CREATE INDEX IF NOT EXISTS idx_seller_id_orders ON orders(seller_id)",
    "CREATE INDEX IF NOT EXISTS idx_product_id ON orders(product_id)",
    "CREATE INDEX IF NOT EXISTS idx_order_status ON orders(order_status)",

    "CREATE TABLE IF NOT EXISTS contacts (
        id SERIAL PRIMARY KEY,
        sender_id INT REFERENCES users(id) ON DELETE SET NULL,
        receiver_id INT REFERENCES users(id) ON DELETE SET NULL,
        subject VARCHAR(255),
        message TEXT NOT NULL,
        is_read BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    "CREATE INDEX IF NOT EXISTS idx_receiver_id ON contacts(receiver_id)",
    "CREATE INDEX IF NOT EXISTS idx_sender_id ON contacts(sender_id)",

    "CREATE TABLE IF NOT EXISTS reviews (
        id SERIAL PRIMARY KEY,
        order_id INT NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
        reviewer_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
        reviewed_user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
        product_id INT REFERENCES products(id) ON DELETE SET NULL,
        rating INT CHECK (rating >= 1 AND rating <= 5),
        comment TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    "CREATE INDEX IF NOT EXISTS idx_reviewed_user_id ON reviews(reviewed_user_id)",
    "CREATE INDEX IF NOT EXISTS idx_product_id_reviews ON reviews(product_id)",

    "CREATE TABLE IF NOT EXISTS favorites (
        id SERIAL PRIMARY KEY,
        user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
        product_id INT NOT NULL REFERENCES products(id) ON DELETE CASCADE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE(user_id, product_id)
    )",

    "CREATE INDEX IF NOT EXISTS idx_user_id ON favorites(user_id)",

    "CREATE TABLE IF NOT EXISTS notifications (
        id SERIAL PRIMARY KEY,
        user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
        type VARCHAR(100),
        title VARCHAR(255),
        message TEXT,
        is_read BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    "CREATE INDEX IF NOT EXISTS idx_user_id_notif ON notifications(user_id)",
    "CREATE INDEX IF NOT EXISTS idx_is_read_notif ON notifications(is_read)",

    "CREATE TABLE IF NOT EXISTS admins (
        id SERIAL PRIMARY KEY,
        user_id INT NOT NULL UNIQUE REFERENCES users(id) ON DELETE CASCADE,
        role VARCHAR(50) DEFAULT 'admin',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )"
];

$created = 0;
$failed = 0;
$errors = [];

foreach ($tables as $sql) {
    try {
        $pdo->exec($sql);
        $created++;
    } catch (Exception $e) {
        $failed++;
        $errors[] = $e->getMessage();
    }
}

ob_end_clean();
echo json_encode([
    'success' => true,
    'created' => $created,
    'failed' => $failed,
    'message' => 'Database setup complete!',
    'errors' => $errors
]);
?>
