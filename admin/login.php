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
    
    <!-- Firebase SDKs -->
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-auth-compat.js"></script>
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
        
        <div id="error-message" class="alert alert-error" style="display: none;"></div>
        
        <!-- Google Sign-In Button -->
        <button type="button" onclick="signInWithGoogle()" class="btn" style="width: 100%; background: #fff; color: #333; border: 1px solid #ddd; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
            <svg width="18" height="18" viewBox="0 0 18 18">
                <path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.615z"/>
                <path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.956-2.184l-2.908-2.258c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332C2.438 15.983 5.482 18 9 18z"/>
                <path fill="#FBBC05" d="M3.964 10.707c-.18-.54-.282-1.117-.282-1.707s.102-1.167.282-1.707V4.961H.957C.347 6.175 0 7.55 0 9s.348 2.825.957 4.039l3.007-2.332z"/>
                <path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0 5.482 0 2.438 2.017.957 4.961L3.964 7.293C4.672 5.163 6.656 3.58 9 3.58z"/>
            </svg>
            تسجيل الدخول عبر Google
        </button>
        
        <div style="text-align: center; margin: 1.5rem 0; color: var(--secondary-gray); position: relative;">
            <span style="background: rgba(255,255,255,0.9); padding: 0 1rem; position: relative; z-index: 1;">أو</span>
            <div style="position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: #ddd; z-index: 0;"></div>
        </div>
        
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
    
    <script src="<?php echo BASE_URL; ?>/assets/js/firebase-auth.js"></script>
</body>
</html>
