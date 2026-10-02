<?php
/**
 * Koneksi PDO dengan Singleton pattern.
 * Hanya ada SATU objek Database selama aplikasi berjalan.
 */
class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    // Sesuaikan jika password MySQL kamu berbeda (default XAMPP: root tanpa password)
    private const HOST    = 'localhost';
    private const DB_NAME = 'inventaris_db';
    private const DB_USER = 'root';
    private const DB_PASS = '';

    // Constructor private: objek tidak bisa dibuat dengan "new Database()" dari luar
    private function __construct()
    {
        $dsn = 'mysql:host=' . self::HOST . ';dbname=' . self::DB_NAME . ';charset=utf8mb4';

        try {
            $this->pdo = new PDO($dsn, self::DB_USER, self::DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // error jadi exception
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,                   // prepared statement asli
            ]);
        } catch (PDOException $e) {
            error_log('DB connection failed: ' . $e->getMessage());
            die('Koneksi database gagal. Pastikan MySQL di XAMPP sudah Start dan database sudah di-import.');
        }
    }

    // Cegah duplikasi objek
    private function __clone() {}

    public function __wakeup()
    {
        throw new Exception('Singleton tidak boleh di-unserialize.');
    }

    // Satu-satunya pintu untuk mendapatkan objek Database
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}
