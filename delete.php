<?php
require_once 'includes/functions.php';

// Delete hanya boleh lewat POST (bukan link biasa) supaya aman
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}
if (!verifyCsrf($_POST['csrf'] ?? null)) {
    setFlash('danger', 'Token tidak valid.');
    redirect('index.php');
}

$id  = (int) ($_POST['id'] ?? 0);
$pdo = db();

// TRANSACTION: hapus produk + catat log harus berhasil bersamaan, atau batal semuanya
try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare('SELECT nama_produk FROM produk WHERE id = :id FOR UPDATE');
    $stmt->execute([':id' => $id]);
    $produk = $stmt->fetch();

    if (!$produk) {
        $pdo->rollBack();
        setFlash('danger', 'Produk tidak ditemukan.');
        redirect('index.php');
    }

    $stmt = $pdo->prepare('DELETE FROM produk WHERE id = :id');
    $stmt->execute([':id' => $id]);

    $stmt = $pdo->prepare('INSERT INTO log_aktivitas (aksi, detail) VALUES (:aksi, :detail)');
    $stmt->execute([
        ':aksi'   => 'DELETE',
        ':detail' => 'Menghapus produk "' . $produk['nama_produk'] . '" (ID ' . $id . ')',
    ]);

    $pdo->commit();
    setFlash('success', 'Produk "' . $produk['nama_produk'] . '" berhasil dihapus.');
} catch (Throwable $ex) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log($ex->getMessage());
    setFlash('danger', 'Gagal menghapus produk.');
}

redirect('index.php');
