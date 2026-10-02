# CRUD Inventaris - Tugas Rutin 8

Aplikasi CRUD inventaris sederhana menggunakan **PHP Native + PDO + MySQL**.

**Nama:** (isi nama lengkap)
**NIM:**  (isi NIM)
**Kelas:** (isi kelas)

## Fitur
- Database `inventaris_db` dengan 3 tabel (kategori, supplier, produk) + Foreign Key
- Koneksi PDO dengan Singleton pattern
- Create, Read (JOIN), Update (form pre-filled), Delete (konfirmasi)
- Prepared statements pada semua query
- Output di-escape dengan `htmlspecialchars()`
- Flash message (Post/Redirect/Get) dan proteksi CSRF
- Bonus: transaction + log aktivitas pada delete, pencarian, pagination, export CSV

## Cara Menjalankan
1. Install XAMPP, start **Apache** dan **MySQL**.
2. Letakkan folder proyek di `C:\xampp\htdocs\inventaris\`.
3. Buka `http://localhost/phpmyadmin`, tab **Import**, pilih `database.sql`, klik **Go**.
4. Buka `http://localhost/inventaris/` di browser.

## Struktur
```
config/database.php     -> Singleton PDO
includes/               -> functions, header, footer, form bersama
index.php               -> list produk (JOIN, search, pagination)
create.php / edit.php   -> tambah & ubah produk
delete.php              -> hapus (transaction + log)
log.php / export.php    -> log aktivitas & export CSV
database.sql            -> struktur tabel + seed data
```
