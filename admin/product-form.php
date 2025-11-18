<?php
require_once __DIR__ . '/../config/init.php';

$is_edit = isset($_GET['id']) && !empty($_GET['id']);
$product = null;

if ($is_edit) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute([':id' => $_GET['id']]);
    $product = $stmt->fetch();
    
    if (!$product) {
        redirect(BASE_URL . '/admin/products.php');
    }
    
    $page_title = 'تعديل الأضحية';
} else {
    $page_title = 'إضافة أضحية جديدة';
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (verify_csrf_token($_POST['csrf_token'])) {
        $title = clean_input($_POST['title']);
        $type = clean_input($_POST['type']);
        $weight = (float)$_POST['weight'];
        $price = (float)$_POST['price'];
        $status = clean_input($_POST['status']);
        $notes = clean_input($_POST['notes'] ?? '');
        
        if (empty($title) || empty($type) || $weight <= 0 || $price <= 0) {
            $error = 'الرجاء ملء جميع الحقول المطلوبة';
        } else {
            $images_json = $product['images'] ?? '[]';
            
            if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                $uploaded_images = upload_images($_FILES['images']);
                if (!empty($uploaded_images)) {
                    $existing_images = json_decode($images_json, true) ?? [];
                    $all_images = array_merge($existing_images, $uploaded_images);
                    $images_json = json_encode($all_images);
                }
            }
            
            if (isset($_POST['remove_images']) && is_array($_POST['remove_images'])) {
                $existing_images = json_decode($images_json, true) ?? [];
                foreach ($_POST['remove_images'] as $remove_img) {
                    delete_image($remove_img);
                    $existing_images = array_diff($existing_images, [$remove_img]);
                }
                $images_json = json_encode(array_values($existing_images));
            }
            
            try {
                if ($is_edit) {
                    $stmt = $pdo->prepare("
                        UPDATE products 
                        SET title = :title, type = :type, weight = :weight, price = :price, 
                            status = :status, notes = :notes, images = :images, updated_at = CURRENT_TIMESTAMP
                        WHERE id = :id
                    ");
                    $stmt->execute([
                        ':title' => $title,
                        ':type' => $type,
                        ':weight' => $weight,
                        ':price' => $price,
                        ':status' => $status,
                        ':notes' => $notes,
                        ':images' => $images_json,
                        ':id' => $product['id']
                    ]);
                    $success = 'تم تحديث الأضحية بنجاح';
                } else {
                    $stmt = $pdo->prepare("
                        INSERT INTO products (title, type, weight, price, status, notes, images) 
                        VALUES (:title, :type, :weight, :price, :status, :notes, :images)
                    ");
                    $stmt->execute([
                        ':title' => $title,
                        ':type' => $type,
                        ':weight' => $weight,
                        ':price' => $price,
                        ':status' => $status,
                        ':notes' => $notes,
                        ':images' => $images_json
                    ]);
                    $success = 'تم إضافة الأضحية بنجاح';
                    redirect(BASE_URL . '/admin/products.php');
                }
                
                $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
                $stmt->execute([':id' => $product['id']]);
                $product = $stmt->fetch();
            } catch (Exception $e) {
                $error = 'حدث خطأ أثناء حفظ البيانات';
            }
        }
    } else {
        $error = 'خطأ في التحقق من الجلسة';
    }
}

$existing_images = $product ? json_decode($product['images'], true) ?? [] : [];

require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-header">
    <h1><?php echo $is_edit ? 'تعديل الأضحية' : 'إضافة أضحية جديدة'; ?></h1>
    <a href="<?php echo BASE_URL; ?>/admin/products.php" class="btn btn-secondary">العودة</a>
</div>

<?php if ($success): ?>
<div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<div class="admin-form">
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
        
        <div class="form-group">
            <label>عنوان الأضحية *</label>
            <input type="text" name="title" value="<?php echo $product['title'] ?? ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label>النوع *</label>
            <select name="type" required>
                <option value="">اختر النوع</option>
                <option value="محلي" <?php echo ($product['type'] ?? '') == 'محلي' ? 'selected' : ''; ?>>محلي</option>
                <option value="إسباني" <?php echo ($product['type'] ?? '') == 'إسباني' ? 'selected' : ''; ?>>إسباني</option>
                <option value="روماني" <?php echo ($product['type'] ?? '') == 'روماني' ? 'selected' : ''; ?>>روماني</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>الوزن (كجم) *</label>
            <input type="number" name="weight" step="0.1" min="0" value="<?php echo $product['weight'] ?? ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label>السعر (DH) *</label>
            <input type="number" name="price" step="0.01" min="0" value="<?php echo $product['price'] ?? ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label>الحالة *</label>
            <select name="status" required>
                <option value="available" <?php echo ($product['status'] ?? 'available') == 'available' ? 'selected' : ''; ?>>متوفرة</option>
                <option value="reserved" <?php echo ($product['status'] ?? '') == 'reserved' ? 'selected' : ''; ?>>محجوزة</option>
                <option value="sold" <?php echo ($product['status'] ?? '') == 'sold' ? 'selected' : ''; ?>>مباعة</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>ملاحظات</label>
            <textarea name="notes" rows="4"><?php echo $product['notes'] ?? ''; ?></textarea>
        </div>
        
        <?php if (!empty($existing_images)): ?>
        <div class="form-group">
            <label>الصور الحالية</label>
            <div class="image-preview">
                <?php foreach ($existing_images as $image): ?>
                <div class="image-preview-item">
                    <img src="<?php echo UPLOAD_URL . $image; ?>" alt="">
                    <label style="position: absolute; top: 5px; left: 5px; background: white; padding: 0.3rem; border-radius: 3px; cursor: pointer;">
                        <input type="checkbox" name="remove_images[]" value="<?php echo $image; ?>"> حذف
                    </label>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="form-group">
            <label>إضافة صور جديدة</label>
            <input type="file" name="images[]" multiple accept="image/*">
            <small style="color: var(--secondary-gray);">يمكنك رفع عدة صور (حجم أقصى 5 ميجابايت لكل صورة)</small>
        </div>
        
        <button type="submit" class="btn" style="width: 100%;"><?php echo $is_edit ? 'حفظ التعديلات' : 'إضافة الأضحية'; ?></button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
