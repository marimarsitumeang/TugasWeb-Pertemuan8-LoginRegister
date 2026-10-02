<?php
require_once 'includes/functions.php';

$errors   = [];
$data     = ['nama_produk' => '', 'kategori_id' => 0, 'supplier_id' => 0, 'harga' => '', 'stok' => ''];
$kategori = getKategori();   // isi dropdown kategori
$supplier = getSupplier();   // isi dropdown supplier

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf'] ?? null)) {
        die('Token tidak valid.');
    }

    [$errors, $data] = validateProduk($_POST);

    if (!$errors) {
        try {
            $stmt = db()->prepare(
                'INSERT INTO produk (nama_produk, kategori_id, supplier_id, harga, stok)
                 VALUES (:nama, :kategori, :supplier, :harga, :stok)'
            );
            $stmt->execute([
                ':nama'     => $data['nama_produk'],
                ':kategori' => $data['kategori_id'],
                ':supplier' => $data['supplier_id'],
                ':harga'    => $data['harga'],
                ':stok'     => (int) $data['stok'],
            ]);
            setFlash('success', 'Produk "' . $data['nama_produk'] . '" berhasil ditambahkan.');
        } catch (PDOException $ex) {
            error_log($ex->getMessage());
            setFlash('danger', 'Gagal menambahkan produk.');
        }
        redirect('index.php');   // Post/Redirect/Get
    }
}

$pageTitle   = 'Tambah Produk';
$submitLabel = 'Simpan Produk';
require 'includes/header.php';
?>
<div class="card">
    <h1>Tambah Produk</h1>
    <?php if ($errors): ?>
        <div class="alert danger"><ul>
            <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
        </ul></div>
    <?php endif; ?>
    <?php require 'includes/form_produk.php'; ?>
</div>
<?php require 'includes/footer.php'; ?>
