<?php
/**
 * SETUP DATABASE OTOMATIS
 * Jalankan file ini melalui browser (http://localhost/itpln-repairhub/database/setup.php)
 * untuk membuat database dan tabel secara otomatis.
 */

require_once __DIR__ . '/../config/database.php';

try {
    // Koneksi khusus tanpa nama database untuk membuat database terlebih dahulu
    $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h3>Membangun Ulang Database ITPLN-RepairHub...</h3>";

    // 1. Membaca file SQL template
    $sqlFile = __DIR__ . '/itpln_repairhub.sql';
    if (!file_exists($sqlFile)) {
        die("❌ File template itpln_repairhub.sql tidak ditemukan!");
    }
    
    $sqlContent = file_get_contents($sqlFile);

    // 2. Eksekusi semua kueri di dalam file SQL
    $pdo->exec($sqlContent);

    echo "<p style='color:green;'>✅ Setup berhasil! Database 'itpln_repairhub' dan seluruh tabel telah dibuat dan diisi data awal.</p>";
    echo "<p><a href='../index.php'>Kembali ke Halaman Utama</a></p>";

} catch (PDOException $e) {
    echo "<p style='color:red;'>❌ Terjadi kesalahan saat setup database: " . $e->getMessage() . "</p>";
}
