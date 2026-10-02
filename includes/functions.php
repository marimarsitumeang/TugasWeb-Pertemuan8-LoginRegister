<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

/** Shortcut untuk mendapatkan koneksi PDO dari Singleton */
function db(): PDO
{
    return Database::getInstance()->getConnection();
}

/** Escape output HTML (wajib dipakai di semua output) */
function e($text): string
{
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

function rupiah($angka): string
{
    return 'Rp ' . number_format((float) $angka, 0, ',', '.');
}

/** Redirect lalu hentikan script */
function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/* ---------- Flash message (Post/Redirect/Get) ---------- */
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

/* ---------- CSRF ---------- */
function csrfToken(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function verifyCsrf(?string $token): bool
{
    return isset($_SESSION['csrf']) && is_string($token)
        && hash_equals($_SESSION['csrf'], $token);
}

/* ---------- Data pendukung ---------- */
function getKategori(): array
{
    $stmt = db()->prepare('SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori');
    $stmt->execute();
    return $stmt->fetchAll();
}

function getSupplier(): array
{
    $stmt = db()->prepare('SELECT id, nama_supplier FROM supplier ORDER BY nama_supplier');
    $stmt->execute();
    return $stmt->fetchAll();
}

/** Cek apakah sebuah id ada di tabel (nama tabel di-whitelist, bukan dari user) */
function recordExists(string $table, int $id): bool
{
    if (!in_array($table, ['kategori', 'supplier', 'produk'], true)) {
        return false;
    }
    $stmt = db()->prepare("SELECT 1 FROM {$table} WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return (bool) $stmt->fetchColumn();
}

/** Validasi input form produk (dipakai create & edit). Return [errors, dataBersih] */
function validateProduk(array $in): array
{
    $errors = [];
    $clean = [
        'nama_produk' => trim($in['nama_produk'] ?? ''),
        'kategori_id' => (int) ($in['kategori_id'] ?? 0),
        'supplier_id' => (int) ($in['supplier_id'] ?? 0),
        'harga'       => trim((string) ($in['harga'] ?? '')),
        'stok'        => trim((string) ($in['stok'] ?? '')),
    ];

    if ($clean['nama_produk'] === '') {
        $errors[] = 'Nama produk wajib diisi.';
    } elseif (mb_strlen($clean['nama_produk']) > 150) {
        $errors[] = 'Nama produk maksimal 150 karakter.';
    }

    if (!recordExists('kategori', $clean['kategori_id'])) {
        $errors[] = 'Kategori wajib dipilih.';
    }
    if (!recordExists('supplier', $clean['supplier_id'])) {
        $errors[] = 'Supplier wajib dipilih.';
    }

    if ($clean['harga'] === '' || !is_numeric($clean['harga']) || (float) $clean['harga'] < 0) {
        $errors[] = 'Harga harus berupa angka dan tidak boleh negatif.';
    }
    if ($clean['stok'] === '' || filter_var($clean['stok'], FILTER_VALIDATE_INT) === false || (int) $clean['stok'] < 0) {
        $errors[] = 'Stok harus berupa bilangan bulat dan tidak boleh negatif.';
    }

    return [$errors, $clean];
}
