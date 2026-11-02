<?php
// UPDATE: ubah status kiriman menjadi 'tampil' atau 'menunggu'.
// Dipanggil oleh tombol Tampilkan / Sembunyikan di admin/kiriman/index.php
require __DIR__ . '/../fungsi.php';
wajib_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
cek_token();

$id     = (int) ($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';

// Status hanya boleh salah satu dari dua pilihan ini
if ($status !== 'tampil' && $status !== 'menunggu') {
    set_pesan('Status tidak dikenal.');
    header('Location: index.php');
    exit;
}

$stmt = mysqli_prepare($koneksi, 'UPDATE kiriman_sahabat SET status = ? WHERE id = ?');
mysqli_stmt_bind_param($stmt, 'si', $status, $id);
mysqli_stmt_execute($stmt);

if ($status === 'tampil') {
    set_pesan('Kiriman sekarang tampil di halaman Sahabat NOAH.');
} else {
    set_pesan('Kiriman disembunyikan dari website.');
}
header('Location: index.php');
exit;
