<?php
// Koneksi ke database. File ini di-include oleh semua halaman yang butuh data.
// Sesuaikan 4 nilai di bawah dengan pengaturan MySQL di komputer Anda.
// Default XAMPP: user "root", password kosong.

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_nama = 'noah_db';

// Supaya error MySQL dilempar sebagai exception (bisa ditangkap dengan try/catch).
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $koneksi = mysqli_connect($db_host, $db_user, $db_pass, $db_nama);
    mysqli_set_charset($koneksi, 'utf8mb4');
} catch (mysqli_sql_exception $e) {
    // Jangan tampilkan detail error ke pengunjung, cukup pesan umum.
    error_log('Koneksi database gagal: ' . $e->getMessage());
    die('Database belum bisa dihubungi. Pastikan MySQL sudah jalan dan database noah_db sudah di-import.');
}
