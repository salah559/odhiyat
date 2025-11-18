<?php
require_once __DIR__ . '/config/init.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect(BASE_URL . '/products.php');
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute([':id' => $_GET['id']]);
$product = $stmt->fetch();

if (!$product) {
    redirect(BASE_URL . '/products.php');
}

$page_title = $product['title'];

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_order'])) {
    if (verify_csrf_token($_POST['csrf_token'])) {
        if ($product['status'] != 'available') {
            $error = 'عذراً، هذه الأضحية غير متاحة للطلب';
        } else {
            $customer_name = clean_input($_POST['customer_name']);
            $customer_phone = clean_input($_POST['customer_phone']);
            $customer_email = clean_input($_POST['customer_email']);
            $customer_address = clean_input($_POST['customer_address']);
            $notes = clean_input($_POST['notes'] ?? '');
            
            if (empty($customer_name) || empty($customer_phone)) {
                $error = 'الرجاء ملء جميع الحقول المطلوبة';
            } else {
                try {
                    $pdo->beginTransaction();
                    
                    $stmt = $pdo->prepare("SELECT id, status FROM products WHERE id = :id FOR UPDATE");
                    $stmt->execute([':id' => $product['id']]);
                    $locked_product = $stmt->fetch();
                    
                    if (!$locked_product || $locked_product['status'] != 'available') {
                        $pdo->rollBack();
                        $error = 'عذراً، هذه الأضحية لم تعد متاحة. تم حجزها من قبل عميل آخر';
                    } else {
                        $stmt = $pdo->prepare("
                            INSERT INTO orders (product_id, customer_name, customer_phone, customer_email, customer_address, notes, status) 
                            VALUES (:product_id, :customer_name, :customer_phone, :customer_email, :customer_address, :notes, 'pending')
                        ");
                        $stmt->execute([
                            ':product_id' => $product['id'],
                            ':customer_name' => $customer_name,
                            ':customer_phone' => $customer_phone,
                            ':customer_email' => $customer_email,
                            ':customer_address' => $customer_address,
                            ':notes' => $notes
                        ]);
                        
                        $stmt = $pdo->prepare("UPDATE products SET status = 'reserved' WHERE id = :id");
                        $stmt->execute([':id' => $product['id']]);
                        
                        $pdo->commit();
                        
                        $success = 'تم إرسال طلبك بنجاح! سيتم التواصل معك قريباً';
                        $product['status'] = 'reserved';
                    }
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $error = 'حدث خطأ أثناء إرسال الطلب';
                }
            }
        }
    } else {
        $error = 'خطأ في التحقق من الجلسة';
    }
}

$images = json_decode($product['images'], true) ?? [];

require_once __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container">
        <div style="max-width: 900px; margin: 0 auto;">
            <h2 class="section-title"><?php echo htmlspecialchars($product['title']); ?></h2>
            
            <span class="product-type" style="font-size: 1.1rem; padding: 0.5rem 1.5rem;"><?php echo htmlspecialchars($product['type']); ?></span>
            <?php echo get_product_status_badge($product['status']); ?>
            
            <?php if (!empty($images)): ?>
            <div class="product-gallery">
                <?php foreach ($images as $image): ?>
                <img src="<?php echo UPLOAD_URL . $image; ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <div style="background: white; padding: 2rem; border-radius: 10px; margin: 2rem 0;">
                <h3 style="color: var(--dark-gray); margin-bottom: 1rem;">تفاصيل الأضحية</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr style="border-bottom: 1px solid var(--light-gray);">
                        <td style="padding: 1rem; font-weight: bold;">الوزن:</td>
                        <td style="padding: 1rem;"><?php echo $product['weight']; ?> كجم</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--light-gray);">
                        <td style="padding: 1rem; font-weight: bold;">النوع:</td>
                        <td style="padding: 1rem;"><?php echo htmlspecialchars($product['type']); ?></td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--light-gray);">
                        <td style="padding: 1rem; font-weight: bold;">السعر:</td>
                        <td style="padding: 1rem; color: var(--primary-gold); font-size: 1.5rem; font-weight: bold;"><?php echo format_price($product['price']); ?></td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--light-gray);">
                        <td style="padding: 1rem; font-weight: bold;">الحالة:</td>
                        <td style="padding: 1rem;"><?php echo get_product_status_badge($product['status']); ?></td>
                    </tr>
                    <?php if (!empty($product['notes'])): ?>
                    <tr>
                        <td style="padding: 1rem; font-weight: bold;">ملاحظات:</td>
                        <td style="padding: 1rem;"><?php echo nl2br(htmlspecialchars($product['notes'])); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
            
            <?php if ($product['status'] == 'available'): ?>
            <div class="contact-form">
                <h3 style="text-align: center; margin-bottom: 1.5rem;">اطلب هذه الأضحية</h3>
                
                <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    
                    <div class="form-group">
                        <label>الاسم الكامل *</label>
                        <input type="text" name="customer_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label>رقم الهاتف *</label>
                        <input type="tel" name="customer_phone" required>
                    </div>
                    
                    <div class="form-group">
                        <label>البريد الإلكتروني</label>
                        <input type="email" name="customer_email">
                    </div>
                    
                    <div class="form-group">
                        <label>العنوان</label>
                        <textarea name="customer_address" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>ملاحظات إضافية</label>
                        <textarea name="notes" rows="3"></textarea>
                    </div>
                    
                    <button type="submit" name="submit_order" class="btn" style="width: 100%;">إرسال الطلب</button>
                </form>
            </div>
            <?php else: ?>
            <div class="alert alert-warning">
                <strong>عذراً!</strong> هذه الأضحية غير متاحة للطلب حالياً.
            </div>
            <?php endif; ?>
            
            <div style="text-align: center; margin-top: 2rem;">
                <a href="<?php echo BASE_URL; ?>/products.php" class="btn btn-secondary">العودة لقائمة الأضاحي</a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
