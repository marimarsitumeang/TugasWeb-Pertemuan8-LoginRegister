<?php
require_once 'includes/functions.php';

$pdo     = db();
$q       = trim($_GET['q'] ?? '');
$perPage = 5;
$page    = max(1, (int) ($_GET['page'] ?? 1));

// Bagian WHERE dibangun dari string tetap; nilai dari user TETAP lewat parameter (:q1, :q2, :q3)
$where  = '';
$params = [];
if ($q !== '') {
    $where  = 'WHERE p.nama_produk LIKE :q1 OR k.nama_kategori LIKE :q2 OR s.nama_supplier LIKE :q3';
    $like   = '%' . $q . '%';
    $params = [':q1' => $like, ':q2' => $like, ':q3' => $like];
}

// 1) Hitung total data untuk pagination
$stmt = $pdo->prepare(
    "SELECT COUNT(*)
     FROM produk p
     JOIN kategori k ON p.kategori_id = k.id
     JOIN supplier s ON p.supplier_id = s.id
     $where"
);
$stmt->execute($params);
$total      = (int) $stmt->fetchColumn();
$totalPages = max(1, (int) ceil($total / $perPage));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $perPage;

// 2) Ambil data halaman ini (JOIN tabel produk, kategori, supplier)
$stmt = $pdo->prepare(
    "SELECT p.id, p.nama_produk, p.harga, p.stok, k.nama_kategori, s.nama_supplier
     FROM produk p
     JOIN kategori k ON p.kategori_id = k.id
     JOIN supplier s ON p.supplier_id = s.id
     $where
     ORDER BY p.id DESC
     LIMIT :limit OFFSET :offset"
);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();

$pageTitle = 'Daftar Produk';
require 'includes/header.php';
?>
<div class="page-head">
    <h1>Daftar Produk</h1>
    <a href="create.php" class="btn btn-primary">+ Tambah Produk</a>
</div>

<form method="GET" class="search">
    <input type="text" name="q" placeholder="Cari produk, kategori, atau supplier..." value="<?= e($q) ?>">
    <button type="submit" class="btn btn-primary">Cari</button>
    <?php if ($q !== ''): ?><a href="index.php" class="btn btn-ghost">Reset</a><?php endif; ?>
</form>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>No</th><th>Nama Produk</th><th>Kategori</th><th>Supplier</th>
                <th>Harga</th><th>Stok</th><th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!$products): ?>
            <tr><td colspan="7" class="empty">Data tidak ditemukan.</td></tr>
        <?php endif; ?>
        <?php foreach ($products as $i => $p): ?>
            <tr>
                <td><?= $offset + $i + 1 ?></td>
                <td><?= e($p['nama_produk']) ?></td>
                <td><span class="badge"><?= e($p['nama_kategori']) ?></span></td>
                <td><?= e($p['nama_supplier']) ?></td>
                <td><?= e(rupiah($p['harga'])) ?></td>
                <td class="<?= (int) $p['stok'] <= 10 ? 'low' : '' ?>"><?= (int) $p['stok'] ?></td>
                <td class="aksi">
                    <a class="btn btn-sm btn-warning" href="edit.php?id=<?= (int) $p['id'] ?>">Edit</a>
                    <form method="POST" action="delete.php"
                          onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                        <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
                        <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="pagination">
    <span>Total <?= $total ?> produk &middot; Halaman <?= $page ?> dari <?= $totalPages ?></span>
    <div>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a class="page <?= $i === $page ? 'active' : '' ?>"
               href="?<?= e(http_build_query(['q' => $q, 'page' => $i])) ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
</div>
<?php require 'includes/footer.php'; ?>
