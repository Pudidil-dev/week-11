<?php
// =====================================================
// MENYIMPAN KIRIMAN CERITA / KARYA SAHABAT
// Dipanggil oleh: form Kirim Cerita (sahabat-noah.php)
// Tabel: kiriman_sahabat
// Kiriman baru otomatis berstatus 'menunggu' (lihat DEFAULT di tabel),
// jadi tidak langsung tampil sebelum diubah jadi 'tampil'.
// =====================================================

include '../koneksi.php';

// 0. File ini hanya boleh dibuka lewat form. Kalau dibuka langsung, kembalikan.
if (!isset($_POST['nama_lengkap'])) {
    header("Location: ../sahabat-noah.php");
    exit;
}

// 1. Ambil isi form
$nama_lengkap = trim($_POST['nama_lengkap']);
$email        = trim($_POST['email']);
$kategori     = $_POST['kategori'];
$pesan        = trim($_POST['pesan']);

// 2. Cek isian wajib
if ($nama_lengkap == '' || $kategori == '' || $pesan == '' || filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
    header("Location: ../sahabat-noah.php?pesan=gagal#form-cerita");
    exit;
}

// 3. Buat teks aman untuk query
$nama_lengkap = mysqli_real_escape_string($koneksi, $nama_lengkap);
$email        = mysqli_real_escape_string($koneksi, $email);
$kategori     = mysqli_real_escape_string($koneksi, $kategori);
$pesan        = mysqli_real_escape_string($koneksi, $pesan);

// 4. Simpan ke database
mysqli_query($koneksi, "INSERT INTO kiriman_sahabat (nama_lengkap, email, kategori, pesan)
                        VALUES ('$nama_lengkap', '$email', '$kategori', '$pesan')");

// 5. Kembali ke halaman Sahabat NOAH dengan pesan berhasil
header("Location: ../sahabat-noah.php?pesan=berhasil#form-cerita");
exit;
