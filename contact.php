<?php
require_once __DIR__ . '/config/init.php';
$page_title = 'تواصل معنا';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (verify_csrf_token($_POST['csrf_token'])) {
        $name = clean_input($_POST['name']);
        $email = clean_input($_POST['email']);
        $phone = clean_input($_POST['phone'] ?? '');
        $subject = clean_input($_POST['subject'] ?? '');
        $message = clean_input($_POST['message']);
        
        if (empty($name) || empty($email) || empty($message)) {
            $error = 'الرجاء ملء جميع الحقول المطلوبة';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'الرجاء إدخال بريد إلكتروني صحيح';
        } else {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO contacts (name, email, phone, subject, message) 
                    VALUES (:name, :email, :phone, :subject, :message)
                ");
                $stmt->execute([
                    ':name' => $name,
                    ':email' => $email,
                    ':phone' => $phone,
                    ':subject' => $subject,
                    ':message' => $message
                ]);
                
                $success = 'تم إرسال رسالتك بنجاح! سنتواصل معك قريباً';
                $_POST = [];
            } catch (Exception $e) {
                $error = 'حدث خطأ أثناء إرسال الرسالة';
            }
        }
    } else {
        $error = 'خطأ في التحقق من الجلسة';
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container">
        <h2 class="section-title">تواصل معنا</h2>
        
        <div class="contact-form">
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
                    <input type="text" name="name" value="<?php echo $_POST['name'] ?? ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>البريد الإلكتروني *</label>
                    <input type="email" name="email" value="<?php echo $_POST['email'] ?? ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>رقم الهاتف</label>
                    <input type="tel" name="phone" value="<?php echo $_POST['phone'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>الموضوع</label>
                    <input type="text" name="subject" value="<?php echo $_POST['subject'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>الرسالة *</label>
                    <textarea name="message" rows="6" required><?php echo $_POST['message'] ?? ''; ?></textarea>
                </div>
                
                <button type="submit" class="btn" style="width: 100%;">إرسال الرسالة</button>
            </form>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
