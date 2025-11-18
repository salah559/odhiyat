<?php
require_once __DIR__ . '/config/init.php';
$page_title = 'الأضاحي';

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

require_once __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container">
        <h2 class="section-title">تصفح الأضاحي</h2>
        
        <div class="filter-bar">
            <form method="GET" action="">
                <div class="filter-group">
                    <label>النوع:</label>
                    <select name="type">
                        <option value="">الكل</option>
                        <option value="محلي" <?php echo (isset($_GET['type']) && $_GET['type'] == 'محلي') ? 'selected' : ''; ?>>محلي</option>
                        <option value="إسباني" <?php echo (isset($_GET['type']) && $_GET['type'] == 'إسباني') ? 'selected' : ''; ?>>إسباني</option>
                        <option value="روماني" <?php echo (isset($_GET['type']) && $_GET['type'] == 'روماني') ? 'selected' : ''; ?>>روماني</option>
                    </select>
                    
                    <label>الوزن الأدنى (كجم):</label>
                    <input type="number" name="min_weight" step="0.1" value="<?php echo $_GET['min_weight'] ?? ''; ?>" placeholder="مثال: 20">
                    
                    <label>الوزن الأقصى (كجم):</label>
                    <input type="number" name="max_weight" step="0.1" value="<?php echo $_GET['max_weight'] ?? ''; ?>" placeholder="مثال: 50">
                    
                    <label>السعر الأدنى (DH):</label>
                    <input type="number" name="min_price" step="0.01" value="<?php echo $_GET['min_price'] ?? ''; ?>" placeholder="مثال: 1000">
                    
                    <label>السعر الأقصى (DH):</label>
                    <input type="number" name="max_price" step="0.01" value="<?php echo $_GET['max_price'] ?? ''; ?>" placeholder="مثال: 5000">
                    
                    <button type="submit" class="btn">بحث</button>
                    <a href="<?php echo BASE_URL; ?>/products.php" class="btn btn-secondary">إعادة تعيين</a>
                </div>
            </form>
        </div>
        
        <?php if (count($products) > 0): ?>
        <div class="products-grid">
            <?php foreach ($products as $product): 
                $images = json_decode($product['images'], true);
                $first_image = !empty($images) ? UPLOAD_URL . $images[0] : BASE_URL . '/assets/images/placeholder.jpg';
            ?>
            <div class="product-card">
                <img src="<?php echo $first_image; ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" class="product-image">
                <div class="product-body">
                    <span class="product-type"><?php echo htmlspecialchars($product['type']); ?></span>
                    <h3 class="product-title"><?php echo htmlspecialchars($product['title']); ?></h3>
                    <div class="product-details">
                        <p>الوزن: <?php echo $product['weight']; ?> كجم</p>
                    </div>
                    <div class="product-price"><?php echo format_price($product['price']); ?></div>
                    <span class="product-status status-available">متوفرة</span>
                    <br><br>
                    <a href="<?php echo BASE_URL; ?>/product-details.php?id=<?php echo $product['id']; ?>" class="btn">عرض التفاصيل</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p style="text-align: center; color: var(--secondary-gray); font-size: 1.2rem;">لا توجد أضاحي متوفرة بهذه المعايير</p>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
