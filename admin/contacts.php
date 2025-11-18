<?php
require_once __DIR__ . '/../config/init.php';
$page_title = 'الرسائل';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error = 'خطأ في التحقق من الجلسة';
    } elseif (isset($_POST['mark_read'])) {
        $contact_id = (int)$_POST['contact_id'];
        $stmt = $pdo->prepare("UPDATE contacts SET is_read = 1 WHERE id = :id");
        $stmt->execute([':id' => $contact_id]);
    } elseif (isset($_POST['delete_contact'])) {
        $contact_id = (int)$_POST['contact_id'];
        $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = :id");
        $stmt->execute([':id' => $contact_id]);
    }
}

$stmt = $pdo->query("SELECT * FROM contacts ORDER BY is_read ASC, created_at DESC");
$contacts = $stmt->fetchAll();

require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-header">
    <h1>الرسائل</h1>
</div>

<div class="admin-table">
    <?php if (count($contacts) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>الاسم</th>
                <th>البريد</th>
                <th>الهاتف</th>
                <th>الموضوع</th>
                <th>الرسالة</th>
                <th>التاريخ</th>
                <th>الحالة</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($contacts as $contact): ?>
            <tr style="<?php echo $contact['is_read'] ? '' : 'background: #fff3cd;'; ?>">
                <td><?php echo htmlspecialchars($contact['name']); ?></td>
                <td><?php echo htmlspecialchars($contact['email']); ?></td>
                <td><?php echo htmlspecialchars($contact['phone'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($contact['subject'] ?? '-'); ?></td>
                <td style="max-width: 300px;"><?php echo nl2br(htmlspecialchars($contact['message'])); ?></td>
                <td><?php echo date('Y-m-d H:i', strtotime($contact['created_at'])); ?></td>
                <td>
                    <?php if ($contact['is_read']): ?>
                    <span class="badge bg-success">مقروءة</span>
                    <?php else: ?>
                    <span class="badge bg-warning">جديدة</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="admin-actions">
                        <?php if (!$contact['is_read']): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <input type="hidden" name="contact_id" value="<?php echo $contact['id']; ?>">
                            <button type="submit" name="mark_read" class="btn btn-success">تعليم كمقروءة</button>
                        </form>
                        <?php endif; ?>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('هل تريد حذف هذه الرسالة؟');">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <input type="hidden" name="contact_id" value="<?php echo $contact['id']; ?>">
                            <button type="submit" name="delete_contact" class="btn btn-danger">حذف</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="text-align: center; color: var(--secondary-gray); padding: 2rem;">لا توجد رسائل</p>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
