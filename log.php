<?php
require_once 'includes/functions.php';

$stmt = db()->prepare('SELECT aksi, detail, created_at FROM log_aktivitas ORDER BY id DESC LIMIT 50');
$stmt->execute();
$logs = $stmt->fetchAll();

$pageTitle = 'Log Aktivitas';
require 'includes/header.php';
?>
<div class="page-head"><h1>Log Aktivitas</h1></div>
<div class="table-wrap">
    <table>
        <thead><tr><th>Waktu</th><th>Aksi</th><th>Detail</th></tr></thead>
        <tbody>
        <?php if (!$logs): ?>
            <tr><td colspan="3" class="empty">Belum ada aktivitas.</td></tr>
        <?php endif; ?>
        <?php foreach ($logs as $l): ?>
            <tr>
                <td><?= e($l['created_at']) ?></td>
                <td><span class="badge"><?= e($l['aksi']) ?></span></td>
                <td><?= e($l['detail']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require 'includes/footer.php'; ?>
