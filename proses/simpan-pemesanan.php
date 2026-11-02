<?php
// =====================================================
// MENYIMPAN FORM PEMESANAN & KERJA SAMA
// Dipanggil oleh: form Pemesanan (tur-event.php)
// Tabel: pemesanan_event
// =====================================================

include '../koneksi.php';

// 0. File ini hanya boleh dibuka lewat form. Kalau dibuka langsung, kembalikan.
if (!isset($_POST['nama_organisasi'])) {
    header("Location: ../tur-event.php");
    exit;
}

// 1. Ambil semua isi form
$nama_organisasi    = trim($_POST['nama_organisasi']);
$email              = trim($_POST['email']);
$telepon            = trim($_POST['telepon']);
$tanggal_acara      = $_POST['tanggal_acara'];
$tipe_acara         = $_POST['tipe_acara'];
$estimasi_kehadiran = $_POST['estimasi_kehadiran'];
$venue_lokasi       = trim($_POST['venue_lokasi']);
$rentang_anggaran   = trim($_POST['rentang_anggaran']);
$catatan            = trim($_POST['catatan']);

// Pilihan tipe acara harus sama dengan pilihan di tabel
$daftar_tipe = ['Festival Musik', 'Acara Korporat', 'Konser Privat'];

// 2. Cek isian wajib. Kalau ada yang salah, kembali dengan pesan gagal.
//    Estimasi kehadiran boleh kosong, tapi kalau diisi harus angka 1 ke atas.
$estimasi_salah = $estimasi_kehadiran != '' && (int) $estimasi_kehadiran < 1;

if ($nama_organisasi == '' || $telepon == '' || $estimasi_salah || !in_array($tipe_acara, $daftar_tipe) || filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
    header("Location: ../tur-event.php?pesan=pesanan_gagal#pemesanan-event");
    exit;
}

// 3. Buat semua teks aman untuk query
$nama_organisasi  = mysqli_real_escape_string($koneksi, $nama_organisasi);
$email            = mysqli_real_escape_string($koneksi, $email);
$telepon          = mysqli_real_escape_string($koneksi, $telepon);
$tipe_acara       = mysqli_real_escape_string($koneksi, $tipe_acara);
$venue_lokasi     = mysqli_real_escape_string($koneksi, $venue_lokasi);
$rentang_anggaran = mysqli_real_escape_string($koneksi, $rentang_anggaran);
$catatan          = mysqli_real_escape_string($koneksi, $catatan);

// 4. Isian yang boleh kosong: kalau kosong, simpan sebagai NULL (tanpa tanda kutip)
if ($tanggal_acara == '') {
    $tanggal_sql = "NULL";
} else {
    $tanggal_sql = "'" . mysqli_real_escape_string($koneksi, $tanggal_acara) . "'";
}

if ($estimasi_kehadiran == '') {
    $estimasi_sql = "NULL";
} else {
    $estimasi_sql = (int) $estimasi_kehadiran;   // angka tidak perlu tanda kutip
}

// 5. Simpan ke database
$sql = "INSERT INTO pemesanan_event
          (nama_organisasi, email, telepon, tanggal_acara, tipe_acara,
           estimasi_kehadiran, venue_lokasi, rentang_anggaran, catatan)
        VALUES
          ('$nama_organisasi', '$email', '$telepon', $tanggal_sql, '$tipe_acara',
           $estimasi_sql, '$venue_lokasi', '$rentang_anggaran', '$catatan')";

mysqli_query($koneksi, $sql);

// 6. Kembali ke halaman Event dengan pesan berhasil
header("Location: ../tur-event.php?pesan=pesanan_terkirim#pemesanan-event");
exit;
