<?php
// =====================================================
// MENYIMPAN EMAIL BERLANGGANAN
// Dipanggil oleh: form Berlangganan (index.php)
//                 form Beritahu Saya (tur-event.php)
// Tabel: pelanggan_email
// =====================================================

include '../koneksi.php';

// 0. File ini hanya boleh dibuka lewat form. Kalau dibuka langsung, kembalikan.
if (!isset($_POST['email'])) {
    header("Location: ../index.php");
    exit;
}

// 1. Ambil isi form. trim() membuang spasi di awal dan akhir.
$email     = trim($_POST['email']);
$asal_form = $_POST['asal_form'];

// 2. Tentukan halaman tujuan setelah selesai
if ($asal_form == 'event') {
    $kembali = '../tur-event.php';
    $bagian  = '#banner-hiatus';
} else {
    $asal_form = 'beranda';
    $kembali = '../index.php';
    $bagian  = '#komunitas';
}

// 3. Cek format email. Kalau salah, kembali dengan pesan gagal.
if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
    header("Location: $kembali?pesan=gagal$bagian");
    exit;
}

// 4. Buat teks aman untuk query
$email_aman = mysqli_real_escape_string($koneksi, $email);

// 5. Cek apakah email sudah pernah terdaftar
$hasil_cek = mysqli_query($koneksi, "SELECT id FROM pelanggan_email WHERE email = '$email_aman'");
if (mysqli_num_rows($hasil_cek) > 0) {
    header("Location: $kembali?pesan=sudah_ada$bagian");
    exit;
}

// 6. Simpan ke database
mysqli_query($koneksi, "INSERT INTO pelanggan_email (email, asal_form) VALUES ('$email_aman', '$asal_form')");

// 7. Kembali ke halaman asal dengan pesan berhasil
header("Location: $kembali?pesan=berhasil$bagian");
exit;
