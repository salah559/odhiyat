<?php
require_once __DIR__ . '/../config/init.php';
$page_title = 'إدارة الطلبات';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error = 'خطأ في التحقق من الجلسة';
    } elseif (isset($_POST['confirm_order'])) {
        $order_id = (int)$_POST['order_id'];
        
        try {
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare("SELECT product_id FROM orders WHERE id = :id");
            $stmt->execute([':id' => $order_id]);
            $order = $stmt->fetch();
            
            if ($order) {
                $stmt = $pdo->prepare("UPDATE orders SET status = 'confirmed' WHERE id = :id");
                $stmt->execute([':id' => $order_id]);
                
                $stmt = $pdo->prepare("UPDATE products SET status = 'sold' WHERE id = :id");
                $stmt->execute([':id' => $order['product_id']]);
                
                $pdo->commit();
                $success = 'تم تأكيد الطلب وتحديث حالة الأضحية إلى مباعة';
            }
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'حدث خطأ أثناء تأكيد الطلب';
        }
    } elseif (isset($_POST['cancel_order'])) {
        $order_id = (int)$_POST['order_id'];
        
        try {
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare("SELECT product_id FROM orders WHERE id = :id");
            $stmt->execute([':id' => $order_id]);
            $order = $stmt->fetch();
            
            if ($order) {
                $stmt = $pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE id = :id");
                $stmt->execute([':id' => $order_id]);
                
                $stmt = $pdo->prepare("UPDATE products SET status = 'available' WHERE id = :id");
                $stmt->execute([':id' => $order['product_id']]);
                
                $pdo->commit();
                $success = 'تم إلغاء الطلب وإعادة الأضحية للمتاحة';
            }
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'حدث خطأ أثناء إلغاء الطلب';
        }
    }
}

$filter = $_GET['status'] ?? 'all';
$where = $filter == 'all' ? '' : "WHERE o.status = :status";

$sql = "SELECT o.*, p.title, p.price, p.type FROM orders o 
        LEFT JOIN products p ON o.product_id = p.id 
        $where 
        ORDER BY o.created_at DESC";

$stmt = $pdo->prepare($sql);
if ($filter != 'all') {
    $stmt->execute([':status' => $filter]);
} else {
    $stmt->execute();
}
$orders = $stmt->fetchAll();

require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-header">
    <h1>إدارة الطلبات</h1>
</div>

<?php if ($success): ?>
<div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<div class="filter-bar" style="margin-bottom: 1.5rem;">
    <div class="filter-group">
        <a href="?status=all" class="btn <?php echo $filter == 'all' ? '' : 'btn-secondary'; ?>">الكل</a>
        <a href="?status=pending" class="btn <?php echo $filter == 'pending' ? '' : 'btn-secondary'; ?>">قيد الانتظار</a>
        <a href="?status=confirmed" class="btn <?php echo $filter == 'confirmed' ? '' : 'btn-secondary'; ?>">مؤكدة</a>
        <a href="?status=cancelled" class="btn <?php echo $filter == 'cancelled' ? '' : 'btn-secondary'; ?>">ملغاة</a>
    </div>
</div>

<div class="admin-table">
    <?php if (count($orders) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>رقم الطلب</th>
                <th>الأضحية</th>
                <th>العميل</th>
                <th>الهاتف</th>
                <th>البريد</th>
                <th>العنوان</th>
                <th>السعر</th>
                <th>الحالة</th>
                <th>التاريخ</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td>#<?php echo $order['id']; ?></td>
                <td><?php echo htmlspecialchars($order['title'] ?? 'محذوفة'); ?></td>
                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                <td><?php echo htmlspecialchars($order['customer_phone']); ?></td>
                <td><?php echo htmlspecialchars($order['customer_email'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($order['customer_address'] ?? '-'); ?></td>
                <td><?php echo format_price($order['price'] ?? 0); ?></td>
                <td><?php echo get_order_status_badge($order['status']); ?></td>
                <td><?php echo date('Y-m-d H:i', strtotime($order['created_at'])); ?></td>
                <td>
                    <div class="admin-actions">
                        <?php if ($order['status'] == 'pending'): ?>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('هل تريد تأكيد هذا الطلب؟');">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <button type="submit" name="confirm_order" class="btn btn-success">تأكيد</button>
                        </form>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('هل تريد إلغاء هذا الطلب؟');">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <button type="submit" name="cancel_order" class="btn btn-danger">إلغاء</button>
                        </form>
                        <?php else: ?>
                        <span style="color: var(--secondary-gray);">-</span>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="text-align: center; color: var(--secondary-gray); padding: 2rem;">لا توجد طلبات</p>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
