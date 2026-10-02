<?php
require_once 'includes/functions.php';

$stmt = db()->prepare(
    'SELECT p.id, p.nama_produk, k.nama_kategori, s.nama_supplier, p.harga, p.stok
     FROM produk p
     JOIN kategori k ON p.kategori_id = k.id
     JOIN supplier s ON p.supplier_id = s.id
     ORDER BY p.id'
);
$stmt->execute();

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="laporan_inventaris_' . date('Ymd_His') . '.csv"');

$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF");   // BOM agar Excel membaca UTF-8 dengan benar
fputcsv($out, ['ID', 'Nama Produk', 'Kategori', 'Supplier', 'Harga', 'Stok']);
while ($row = $stmt->fetch()) {
    fputcsv($out, $row);
}
fclose($out);
exit;
