<?php
/**
 * Form yang dipakai bersama oleh create.php dan edit.php.
 * Variabel yang dibutuhkan: $data, $kategori, $supplier, $submitLabel
 */
?>
<form method="POST" novalidate>
    <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
    <?php if (!empty($data['id'])): ?>
        <input type="hidden" name="id" value="<?= (int) $data['id'] ?>">
    <?php endif; ?>

    <label for="nama_produk">Nama Produk</label>
    <input type="text" id="nama_produk" name="nama_produk" maxlength="150"
           value="<?= e($data['nama_produk']) ?>" required>

    <label for="kategori_id">Kategori</label>
    <select id="kategori_id" name="kategori_id" required>
        <option value="">-- Pilih Kategori --</option>
        <?php foreach ($kategori as $k): ?>
            <option value="<?= (int) $k['id'] ?>"
                <?= (int) $data['kategori_id'] === (int) $k['id'] ? 'selected' : '' ?>>
                <?= e($k['nama_kategori']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="supplier_id">Supplier</label>
    <select id="supplier_id" name="supplier_id" required>
        <option value="">-- Pilih Supplier --</option>
        <?php foreach ($supplier as $s): ?>
            <option value="<?= (int) $s['id'] ?>"
                <?= (int) $data['supplier_id'] === (int) $s['id'] ? 'selected' : '' ?>>
                <?= e($s['nama_supplier']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <div class="row">
        <div>
            <label for="harga">Harga (Rp)</label>
            <input type="number" id="harga" name="harga" min="0" step="any"
                   value="<?= e($data['harga']) ?>" required>
        </div>
        <div>
            <label for="stok">Stok</label>
            <input type="number" id="stok" name="stok" min="0" step="1"
                   value="<?= e($data['stok']) ?>" required>
        </div>
    </div>

    <div class="actions">
        <button type="submit" class="btn btn-primary"><?= e($submitLabel) ?></button>
        <a href="index.php" class="btn btn-ghost">Batal</a>
    </div>
</form>
