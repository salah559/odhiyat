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
        
        <!-- Google Sign-In Button - Enhanced -->
        <div style="margin-bottom: 2rem;">
            <button type="button" onclick="signInWithGoogle()" class="google-signin-btn" style="
                width: 100%; 
                background: linear-gradient(135deg, #4285F4 0%, #34A853 100%);
                color: #fff; 
                border: none;
                padding: 1rem 1.5rem;
                font-size: 1.1rem;
                font-weight: 600;
                border-radius: 12px;
                cursor: pointer;
                display: flex; 
                align-items: center; 
                justify-content: center; 
                gap: 0.75rem;
                box-shadow: 0 4px 15px rgba(66, 133, 244, 0.4);
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            ">
                <svg width="24" height="24" viewBox="0 0 24 24" style="background: white; border-radius: 4px; padding: 4px;">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                <span style="text-shadow: 0 2px 4px rgba(0,0,0,0.1);">تسجيل الدخول عبر Google</span>
                <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%); pointer-events: none;"></div>
            </button>
            <p style="text-align: center; margin-top: 0.75rem; color: var(--secondary-gray); font-size: 0.9rem;">
                ✨ طريقة سريعة وآمنة
            </p>
        </div>
        
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
