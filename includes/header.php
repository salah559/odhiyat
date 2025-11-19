<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="<?php echo BASE_URL; ?>/" class="navbar-brand">
                <img src="<?php echo BASE_URL; ?>/assets/images/logos/logo.png" alt="Odhiyaty Logo">
                <span>أضحيتي</span>
            </a>
            <ul class="navbar-menu">
                <li><a href="<?php echo BASE_URL; ?>/" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">الرئيسية</a></li>
                <li><a href="<?php echo BASE_URL; ?>/products.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'products.php') ? 'active' : ''; ?>">الأضاحي</a></li>
                <li><a href="<?php echo BASE_URL; ?>/contact.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'active' : ''; ?>">تواصل معنا</a></li>
                <li><a href="<?php echo BASE_URL; ?>/admin/login.php" class="login-btn" style="background: linear-gradient(135deg, var(--primary-gold) 0%, var(--gold-light) 100%); padding: 0.7rem 1.5rem; border-radius: 30px; color: white; font-weight: 600;">تسجيل الدخول</a></li>
                <?php if (is_logged_in()): ?>
                <li><a href="<?php echo BASE_URL; ?>/admin/">لوحة التحكم</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
