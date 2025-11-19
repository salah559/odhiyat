<?php
require_once __DIR__ . '/config/init.php';

try {
    $products = [
        [
            'title' => 'خروف محلي ممتاز',
            'type' => 'محلي',
            'weight' => 45.5,
            'price' => 3500.00,
            'images' => '[]',
            'status' => 'available',
            'notes' => 'خروف بحالة ممتازة'
        ],
        [
            'title' => 'كبش إسباني',
            'type' => 'إسباني',
            'weight' => 60.0,
            'price' => 4500.00,
            'images' => '[]',
            'status' => 'available',
            'notes' => 'كبش إسباني أصيل'
        ],
        [
            'title' => 'خروف روماني',
            'type' => 'روماني',
            'weight' => 50.0,
            'price' => 4000.00,
            'images' => '[]',
            'status' => 'available',
            'notes' => 'خروف روماني عالي الجودة'
        ],
        [
            'title' => 'كبش محلي كبير',
            'type' => 'محلي',
            'weight' => 65.0,
            'price' => 5000.00,
            'images' => '[]',
            'status' => 'available',
            'notes' => 'كبش محلي وزن كبير'
        ],
        [
            'title' => 'خروف إسباني متوسط',
            'type' => 'إسباني',
            'weight' => 48.0,
            'price' => 3800.00,
            'images' => '[]',
            'status' => 'available',
            'notes' => 'خروف إسباني بوزن متوسط'
        ]
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO products (title, type, weight, price, images, status, notes) 
        VALUES (:title, :type, :weight, :price, :images, :status, :notes)
    ");
    
    foreach ($products as $product) {
        $stmt->execute($product);
    }
    
    echo "تم إضافة " . count($products) . " منتجات بنجاح!\n";
    
} catch (Exception $e) {
    echo "خطأ: " . $e->getMessage() . "\n";
}
?>
