<?php
require_once __DIR__ . '/../config/init.php';
$page_title = 'لوحة التحكم الرئيسية';

$stats = get_stats();

require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-header">
    <h1>مرحباً، <?php echo htmlspecialchars($_SESSION['admin_name']); ?></h1>
    <p style="color: var(--secondary-gray); margin: 0.5rem 0 0;">إليك نظرة سريعة على إحصائيات الموقع</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <h3>إجمالي الأضاحي</h3>
        <div class="stat-value"><?php echo $stats['total_products']; ?></div>
    </div>
    
    <div class="stat-card success">
        <h3>الأضاحي المتاحة</h3>
        <div class="stat-value"><?php echo $stats['available_products']; ?></div>
    </div>
    
    <div class="stat-card danger">
        <h3>الأضاحي المباعة</h3>
        <div class="stat-value"><?php echo $stats['sold_products']; ?></div>
    </div>
    
    <div class="stat-card">
        <h3>إجمالي الطلبات</h3>
        <div class="stat-value"><?php echo $stats['total_orders']; ?></div>
    </div>
    
    <div class="stat-card warning">
        <h3>طلبات قيد الانتظار</h3>
        <div class="stat-value"><?php echo $stats['pending_orders']; ?></div>
    </div>
    
    <div class="stat-card success">
        <h3>طلبات مؤكدة</h3>
        <div class="stat-value"><?php echo $stats['confirmed_orders']; ?></div>
    </div>
    
    <div class="stat-card" style="grid-column: span 2;">
        <h3>إجمالي المبيعات</h3>
        <div class="stat-value" style="color: var(--primary-gold);"><?php echo format_price($stats['total_revenue']); ?></div>
    </div>
</div>

<div class="admin-table">
    <h2 style="margin-bottom: 1rem;">آخر الطلبات</h2>
    <?php
    $stmt = $pdo->query("SELECT o.*, p.title, p.price FROM orders o JOIN products p ON o.product_id = p.id ORDER BY o.created_at DESC LIMIT 5");
    $recent_orders = $stmt->fetchAll();
    ?>
    
    <?php if (count($recent_orders) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>رقم الطلب</th>
                <th>العميل</th>
                <th>الأضحية</th>
                <th>السعر</th>
                <th>الحالة</th>
                <th>التاريخ</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recent_orders as $order): ?>
            <tr>
                <td>#<?php echo $order['id']; ?></td>
                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                <td><?php echo htmlspecialchars($order['title']); ?></td>
                <td><?php echo format_price($order['price']); ?></td>
                <td><?php echo get_order_status_badge($order['status']); ?></td>
                <td><?php echo date('Y-m-d H:i', strtotime($order['created_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="text-align: center; color: var(--secondary-gray);">لا توجد طلبات</p>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
