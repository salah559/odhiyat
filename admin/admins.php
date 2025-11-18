<?php
require_once __DIR__ . '/../config/init.php';
$page_title = 'إدارة المسؤولين';

if (!$_SESSION['is_super_admin']) {
    redirect(BASE_URL . '/admin/');
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_admin'])) {
        if (verify_csrf_token($_POST['csrf_token'])) {
            $email = clean_input($_POST['email']);
            $password = $_POST['password'];
            $full_name = clean_input($_POST['full_name']);
            
            if (empty($email) || empty($password) || empty($full_name)) {
                $error = 'الرجاء ملء جميع الحقول';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'الرجاء إدخال بريد إلكتروني صحيح';
            } elseif (strlen($password) < 6) {
                $error = 'كلمة المرور يجب أن تكون 6 أحرف على الأقل';
            } else {
                $stmt = $pdo->prepare("SELECT id FROM admins WHERE email = :email");
                $stmt->execute([':email' => $email]);
                
                if ($stmt->fetch()) {
                    $error = 'هذا البريد الإلكتروني مسجل مسبقاً';
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    
                    $stmt = $pdo->prepare("INSERT INTO admins (email, password, full_name, is_super_admin) VALUES (:email, :password, :full_name, 0)");
                    $stmt->execute([
                        ':email' => $email,
                        ':password' => $hashed_password,
                        ':full_name' => $full_name
                    ]);
                    
                    $success = 'تم إضافة المسؤول بنجاح';
                    $_POST = [];
                }
            }
        }
    } elseif (isset($_POST['delete_admin'])) {
        $admin_id = (int)$_POST['admin_id'];
        
        if ($admin_id == $_SESSION['admin_id']) {
            $error = 'لا يمكنك حذف حسابك الخاص';
        } else {
            $stmt = $pdo->prepare("SELECT is_super_admin FROM admins WHERE id = :id");
            $stmt->execute([':id' => $admin_id]);
            $admin = $stmt->fetch();
            
            if ($admin && $admin['is_super_admin']) {
                $error = 'لا يمكن حذف المسؤول الرئيسي';
            } else {
                $stmt = $pdo->prepare("DELETE FROM admins WHERE id = :id");
                $stmt->execute([':id' => $admin_id]);
                $success = 'تم حذف المسؤول بنجاح';
            }
        }
    }
}

$stmt = $pdo->query("SELECT * FROM admins ORDER BY is_super_admin DESC, created_at DESC");
$admins = $stmt->fetchAll();

require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-header">
    <h1>إدارة المسؤولين</h1>
</div>

<?php if ($success): ?>
<div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<div class="admin-form" style="margin-bottom: 2rem;">
    <h3 style="margin-bottom: 1.5rem;">إضافة مسؤول جديد</h3>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
        
        <div class="form-group">
            <label>الاسم الكامل</label>
            <input type="text" name="full_name" value="<?php echo $_POST['full_name'] ?? ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label>البريد الإلكتروني</label>
            <input type="email" name="email" value="<?php echo $_POST['email'] ?? ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label>كلمة المرور</label>
            <input type="password" name="password" required>
        </div>
        
        <button type="submit" name="add_admin" class="btn" style="width: 100%;">إضافة مسؤول</button>
    </form>
</div>

<div class="admin-table">
    <h3 style="margin-bottom: 1rem;">قائمة المسؤولين</h3>
    <?php if (count($admins) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>الاسم</th>
                <th>البريد الإلكتروني</th>
                <th>الصلاحية</th>
                <th>تاريخ الإضافة</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($admins as $admin): ?>
            <tr>
                <td><?php echo htmlspecialchars($admin['full_name']); ?></td>
                <td><?php echo htmlspecialchars($admin['email']); ?></td>
                <td>
                    <?php if ($admin['is_super_admin']): ?>
                    <span class="badge bg-warning">مسؤول رئيسي</span>
                    <?php else: ?>
                    <span class="badge bg-success">مسؤول</span>
                    <?php endif; ?>
                </td>
                <td><?php echo date('Y-m-d', strtotime($admin['created_at'])); ?></td>
                <td>
                    <?php if (!$admin['is_super_admin'] && $admin['id'] != $_SESSION['admin_id']): ?>
                    <form method="POST" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا المسؤول؟');">
                        <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
                        <button type="submit" name="delete_admin" class="btn btn-danger btn-sm">حذف</button>
                    </form>
                    <?php else: ?>
                    <span style="color: var(--secondary-gray);">-</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="text-align: center; color: var(--secondary-gray); padding: 2rem;">لا يوجد مسؤولين</p>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
