<?php
// =====================================================
// MENYIMPAN KIRIMAN CERITA / KARYA SAHABAT
// Dipanggil oleh: form Kirim Cerita atau Karyamu (sahabat-noah.php)
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
$kota         = trim($_POST['kota']);
$kategori     = $_POST['kategori'];
$pesan        = trim($_POST['pesan']);

// Pilihan kategori harus sama dengan pilihan di tabel
$daftar_kategori = ['Cerita Penggemar', 'Karya Penggemar', 'Cover Lagu'];

// 2. Cek isian wajib
if ($nama_lengkap == '' || $pesan == '' || !in_array($kategori, $daftar_kategori) || filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
    header("Location: ../sahabat-noah.php?pesan=gagal#form-cerita");
    exit;
}

// 3. Foto (wajib untuk Karya Penggemar, boleh kosong untuk yang lain)
$ada_foto = isset($_FILES['foto']) && $_FILES['foto']['error'] != UPLOAD_ERR_NO_FILE;

if ($kategori == 'Karya Penggemar' && !$ada_foto) {
    header("Location: ../sahabat-noah.php?pesan=foto_wajib#form-cerita");
    exit;
}

$foto_sql = "NULL";   // kalau tidak ada foto, simpan NULL (tanpa tanda kutip)

if ($ada_foto) {
    $file = $_FILES['foto'];

    // Jenis file dicek dari isinya, bukan dari nama file
    $ekstensi_boleh = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $tipe_asli = '';
    if ($file['error'] == UPLOAD_ERR_OK) {
        $tipe_asli = mime_content_type($file['tmp_name']);
    }

    if ($file['error'] != UPLOAD_ERR_OK || $file['size'] > 2 * 1024 * 1024 || !isset($ekstensi_boleh[$tipe_asli])) {
        header("Location: ../sahabat-noah.php?pesan=foto_gagal#form-cerita");
        exit;
    }

    // Nama file dibuat baru supaya tidak bentrok dengan file lain
    $nama_file = 'kiriman-' . time() . '-' . bin2hex(random_bytes(2)) . '.' . $ekstensi_boleh[$tipe_asli];
    if (!move_uploaded_file($file['tmp_name'], '../images/' . $nama_file)) {
        header("Location: ../sahabat-noah.php?pesan=foto_gagal#form-cerita");
        exit;
    }
    $foto_sql = "'images/" . $nama_file . "'";
}

// 4. Kota boleh kosong: kalau kosong simpan NULL
if ($kota == '') {
    $kota_sql = "NULL";
} else {
    $kota_sql = "'" . mysqli_real_escape_string($koneksi, $kota) . "'";
}

// 5. Buat teks aman untuk query
$nama_lengkap = mysqli_real_escape_string($koneksi, $nama_lengkap);
$email        = mysqli_real_escape_string($koneksi, $email);
$kategori     = mysqli_real_escape_string($koneksi, $kategori);
$pesan        = mysqli_real_escape_string($koneksi, $pesan);

// 6. Simpan ke database
mysqli_query($koneksi, "INSERT INTO kiriman_sahabat (nama_lengkap, email, kota, kategori, pesan, foto)
                        VALUES ('$nama_lengkap', '$email', $kota_sql, '$kategori', '$pesan', $foto_sql)");

// 7. Kembali ke halaman Sahabat NOAH dengan pesan berhasil
header("Location: ../sahabat-noah.php?pesan=berhasil#form-cerita");
exit;
