<?php
check_admin();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>لوحة التحكم</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <aside class="admin-sidebar">
            <div class="admin-logo">
                <img src="<?php echo BASE_URL; ?>/assets/images/logos/logo.png" alt="Logo">
                <h3>لوحة التحكم</h3>
            </div>
            
            <nav class="admin-menu">
                <a href="<?php echo BASE_URL; ?>/admin/" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
                    📊 الرئيسية
                </a>
                <a href="<?php echo BASE_URL; ?>/admin/products.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'products.php') ? 'active' : ''; ?>">
                    🐑 إدارة الأضاحي
                </a>
                <a href="<?php echo BASE_URL; ?>/admin/orders.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'orders.php') ? 'active' : ''; ?>">
                    📦 إدارة الطلبات
                </a>
                <?php if ($_SESSION['is_super_admin']): ?>
                <a href="<?php echo BASE_URL; ?>/admin/admins.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'admins.php') ? 'active' : ''; ?>">
                    👥 إدارة المسؤولين
                </a>
                <?php endif; ?>
                <a href="<?php echo BASE_URL; ?>/admin/contacts.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'contacts.php') ? 'active' : ''; ?>">
                    📧 الرسائل
                </a>
            </nav>
            
            <div class="admin-user">
                <p><strong><?php echo htmlspecialchars($_SESSION['admin_name']); ?></strong></p>
                <p style="font-size: 0.9rem; color: var(--secondary-gray);"><?php echo htmlspecialchars($_SESSION['admin_email']); ?></p>
                <a href="<?php echo BASE_URL; ?>/admin/logout.php" class="btn btn-danger" style="margin-top: 1rem; width: 100%;">تسجيل الخروج</a>
            </div>
        </aside>
        
        <main class="admin-content">
