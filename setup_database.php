<?php
require_once __DIR__ . '/config/database.php';

try {
    // Create admins table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admins (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            full_name TEXT NOT NULL,
            is_super_admin INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");
    
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_email ON admins(email);");

    // Create products table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            type TEXT NOT NULL,
            weight REAL NOT NULL,
            price REAL NOT NULL,
            status TEXT DEFAULT 'available',
            notes TEXT,
            images TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");
    
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_type ON products(type);");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_status ON products(status);");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_price ON products(price);");

    // Create orders table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            product_id INTEGER NOT NULL,
            customer_name TEXT NOT NULL,
            customer_phone TEXT NOT NULL,
            customer_email TEXT,
            customer_address TEXT,
            notes TEXT,
            status TEXT DEFAULT 'pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        );
    ");
    
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_status_orders ON orders(status);");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_product_id ON orders(product_id);");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_created_at ON orders(created_at);");

    // Create contacts table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS contacts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL,
            phone TEXT,
            subject TEXT,
            message TEXT NOT NULL,
            is_read INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");
    
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_is_read ON contacts(is_read);");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_created_at_contacts ON contacts(created_at);");

    // Check if super admin exists
    $check_admin = $pdo->query("SELECT COUNT(*) FROM admins WHERE email = 'bouazzasalah120120@gmail.com'")->fetchColumn();
    
    if ($check_admin == 0) {
        $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admins (email, password, full_name, is_super_admin) VALUES (?, ?, ?, ?)");
        $stmt->execute(['bouazzasalah120120@gmail.com', $hashed_password, 'Salah Bouazza', 1]);
    }

    echo "Database setup completed successfully!\n";
    echo "Super Admin created: bouazzasalah120120@gmail.com\n";
    echo "Password: admin123\n";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
