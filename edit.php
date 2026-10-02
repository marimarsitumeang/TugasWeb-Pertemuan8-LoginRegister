<?php
require_once 'includes/functions.php';

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

// Ambil data lama untuk mengisi form (pre-filled)
$stmt = db()->prepare('SELECT * FROM produk WHERE id = :id');
$stmt->execute([':id' => $id]);
$data = $stmt->fetch();

if (!$data) {
    setFlash('danger', 'Produk tidak ditemukan.');
    redirect('index.php');
}

$errors   = [];
$kategori = getKategori();
$supplier = getSupplier();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf'] ?? null)) {
        die('Token tidak valid.');
    }

    [$errors, $clean] = validateProduk($_POST);
    $data = array_merge($data, $clean);   // tampilkan input terbaru jika ada error

    if (!$errors) {
        try {
            $stmt = db()->prepare(
                'UPDATE produk
                 SET nama_produk = :nama, kategori_id = :kategori, supplier_id = :supplier,
                     harga = :harga, stok = :stok
                 WHERE id = :id'
            );
            $stmt->execute([
                ':nama'     => $clean['nama_produk'],
                ':kategori' => $clean['kategori_id'],
                ':supplier' => $clean['supplier_id'],
                ':harga'    => $clean['harga'],
                ':stok'     => (int) $clean['stok'],
                ':id'       => $id,
            ]);
            setFlash('success', 'Produk "' . $clean['nama_produk'] . '" berhasil diperbarui.');
        } catch (PDOException $ex) {
            error_log($ex->getMessage());
            setFlash('danger', 'Gagal memperbarui produk.');
        }
        redirect('index.php');
    }
}

$pageTitle   = 'Edit Produk';
$submitLabel = 'Update Produk';
require 'includes/header.php';
?>
<div class="card">
    <h1>Edit Produk</h1>
    <?php if ($errors): ?>
        <div class="alert danger"><ul>
            <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
        </ul></div>
    <?php endif; ?>
    <?php require 'includes/form_produk.php'; ?>
</div>
<?php require 'includes/footer.php'; ?>
