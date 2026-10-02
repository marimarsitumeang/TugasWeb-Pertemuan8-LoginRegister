-- =====================================================
-- Tugas Rutin 8 - CRUD Inventaris
-- Cara pakai: phpMyAdmin -> tab "Import" -> pilih file ini -> Go
-- =====================================================
CREATE DATABASE IF NOT EXISTS inventaris_db
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE inventaris_db;

-- Hapus tabel lama (urutan: tabel anak dulu, baru tabel induk)
DROP TABLE IF EXISTS log_aktivitas;
DROP TABLE IF EXISTS produk;
DROP TABLE IF EXISTS kategori;
DROP TABLE IF EXISTS supplier;

-- TABEL 1: kategori
CREATE TABLE kategori (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- TABEL 2: supplier
CREATE TABLE supplier (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_supplier VARCHAR(150) NOT NULL,
    telepon       VARCHAR(20),
    alamat        VARCHAR(255)
) ENGINE=InnoDB;

-- TABEL 3: produk (punya 2 Foreign Key)
CREATE TABLE produk (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_produk VARCHAR(150) NOT NULL,
    kategori_id INT UNSIGNED NOT NULL,
    supplier_id INT UNSIGNED NOT NULL,
    harga       DECIMAL(12,2) NOT NULL DEFAULT 0,
    stok        INT UNSIGNED NOT NULL DEFAULT 0,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_produk_kategori FOREIGN KEY (kategori_id)
        REFERENCES kategori(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_produk_supplier FOREIGN KEY (supplier_id)
        REFERENCES supplier(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- SEED DATA (minimal 5 per tabel)
INSERT INTO kategori (nama_kategori) VALUES
('Elektronik'), ('Alat Tulis'), ('Furnitur'), ('Makanan & Minuman'), ('Peralatan Kebersihan');

INSERT INTO supplier (nama_supplier, telepon, alamat) VALUES
('PT Maju Jaya Elektronik', '061-4512345', 'Jl. Gatot Subroto No. 10, Medan'),
('CV Sinar Pena',           '061-7788990', 'Jl. Sisingamangaraja No. 25, Medan'),
('UD Kayu Mulia',           '0812-6000-111', 'Jl. Setia Budi No. 7, Medan'),
('PT Sumber Pangan',        '061-8234567', 'Jl. Yos Sudarso No. 88, Medan'),
('CV Bersih Sejahtera',     '0813-7000-222', 'Jl. Ringroad No. 3, Medan');

INSERT INTO produk (nama_produk, kategori_id, supplier_id, harga, stok) VALUES
('Laptop ASUS Vivobook 14',   1, 1, 7500000, 12),
('Mouse Wireless Logitech',   1, 1,  185000, 40),
('Pulpen Gel Hitam (1 box)',  2, 2,   45000, 100),
('Buku Tulis A5 (1 pak)',     2, 2,   35000, 75),
('Meja Kerja Kayu Jati',      3, 3, 1850000, 6),
('Kursi Kantor Ergonomis',    3, 3, 1250000, 9),
('Kopi Arabika 250gr',        4, 4,   65000, 55),
('Teh Celup (1 box)',         4, 4,   22000, 80),
('Sabun Cuci Tangan 5L',      5, 5,   98000, 20),
('Cairan Pembersih Lantai 1L',5, 5,   28000, 35);

-- TABEL BONUS: log aktivitas (dipakai oleh transaction pada delete)
CREATE TABLE log_aktivitas (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    aksi       VARCHAR(50)  NOT NULL,
    detail     VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
