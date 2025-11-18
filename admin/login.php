<?php
require_once __DIR__ . '/../config/init.php';

if (is_logged_in()) {
    redirect(BASE_URL . '/admin/');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        $error = 'خطأ في التحقق من الجلسة';
    } else {
        $email = clean_input($_POST['email']);
        $password = $_POST['password'];
        
        if (empty($email) || empty($password)) {
            $error = 'الرجاء ملء جميع الحقول';
        } else {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['admin_name'] = $admin['full_name'];
            $_SESSION['is_super_admin'] = $admin['is_super_admin'];
            
            redirect(BASE_URL . '/admin/');
        } else {
            $error = 'البريد الإلكتروني أو كلمة المرور غير صحيحة';
        }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - لوحة التحكم</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body style="background: linear-gradient(135deg, var(--primary-gold) 0%, var(--dark-gray) 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center;">
    <div class="contact-form" style="max-width: 450px; width: 90%;">
        <div style="text-align: center; margin-bottom: 2rem;">
            <img src="<?php echo BASE_URL; ?>/assets/images/logos/logo.png" alt="Logo" style="height: 80px; margin-bottom: 1rem;">
            <h2 style="color: var(--dark-gray);">لوحة التحكم</h2>
            <p style="color: var(--secondary-gray);">تسجيل الدخول</p>
        </div>
        
        <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            
            <div class="form-group">
                <label>البريد الإلكتروني</label>
                <input type="email" name="email" required autofocus>
            </div>
            
            <div class="form-group">
                <label>كلمة المرور</label>
                <input type="password" name="password" required>
            </div>
            
            <button type="submit" class="btn" style="width: 100%;">تسجيل الدخول</button>
        </form>
        
        <div style="text-align: center; margin-top: 1.5rem;">
            <a href="<?php echo BASE_URL; ?>/" style="color: var(--secondary-gray); text-decoration: none;">← العودة للموقع</a>
        </div>
    </div>
</body>
</html>
