<?php
require_once __DIR__ . '/../config/init.php';
$page_title = 'إدارة الأضاحي';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_product'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error = 'خطأ في التحقق من الجلسة';
    } else {
        $product_id = (int)$_POST['product_id'];
        
        $stmt = $pdo->prepare("SELECT images FROM products WHERE id = :id");
        $stmt->execute([':id' => $product_id]);
        $product = $stmt->fetch();
    
    if ($product) {
        $images = json_decode($product['images'], true);
        if (!empty($images)) {
            foreach ($images as $image) {
                delete_image($image);
            }
        }
        
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
        $stmt->execute([':id' => $product_id]);
        $success = 'تم حذف الأضحية بنجاح';
        }
    }
}

$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll();

require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-header">
    <h1>إدارة الأضاحي</h1>
    <a href="<?php echo BASE_URL; ?>/admin/product-form.php" class="btn">إضافة أضحية جديدة</a>
</div>

<?php if ($success): ?>
<div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<div class="admin-table">
    <?php if (count($products) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>الصورة</th>
                <th>العنوان</th>
                <th>النوع</th>
                <th>الوزن</th>
                <th>السعر</th>
                <th>الحالة</th>
                <th>التاريخ</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): 
                $images = json_decode($product['images'], true);
                $first_image = !empty($images) ? UPLOAD_URL . $images[0] : '';
            ?>
            <tr>
                <td>
                    <?php if ($first_image): ?>
                    <img src="<?php echo $first_image; ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($product['title']); ?></td>
                <td><?php echo htmlspecialchars($product['type']); ?></td>
                <td><?php echo $product['weight']; ?> كجم</td>
                <td><?php echo format_price($product['price']); ?></td>
                <td><?php echo get_product_status_badge($product['status']); ?></td>
                <td><?php echo date('Y-m-d', strtotime($product['created_at'])); ?></td>
                <td>
                    <div class="admin-actions">
                        <a href="<?php echo BASE_URL; ?>/product-details.php?id=<?php echo $product['id']; ?>" class="btn btn-secondary" target="_blank">عرض</a>
                        <a href="<?php echo BASE_URL; ?>/admin/product-form.php?id=<?php echo $product['id']; ?>" class="btn">تعديل</a>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذه الأضحية؟');">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" name="delete_product" class="btn btn-danger">حذف</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="text-align: center; color: var(--secondary-gray); padding: 2rem;">لا توجد أضاحي</p>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
