<?php
require_once __DIR__ . '/config/init.php';
$page_title = 'الرئيسية';

$stmt = $pdo->query("SELECT * FROM products WHERE status = 'available' ORDER BY created_at DESC LIMIT 6");
$featured_products = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="container">
        <h1>مرحباً بكم في أضحيتي</h1>
        <p>منصتكم الموثوقة لاختيار أفضل الأضاحي والأغنام</p>
        <a href="<?php echo BASE_URL; ?>/products.php" class="btn">تصفح الأضاحي</a>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="section-title">الأضاحي المتاحة</h2>
        
        <?php if (count($featured_products) > 0): ?>
        <div class="products-grid">
            <?php foreach ($featured_products as $product): 
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
        <p style="text-align: center; color: var(--secondary-gray); font-size: 1.2rem;">لا توجد أضاحي متاحة حالياً</p>
        <?php endif; ?>
        
        <div style="text-align: center; margin-top: 3rem;">
            <a href="<?php echo BASE_URL; ?>/products.php" class="btn btn-secondary">عرض جميع الأضاحي</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
