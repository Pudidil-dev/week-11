<?php
// UPDATE: ambil berita lama berdasarkan id, tampilkan di form, lalu simpan perubahan.
require __DIR__ . '/../fungsi.php';
require __DIR__ . '/_data.php';
wajib_login();

// 1. Ambil id dari alamat, contoh: edit.php?id=3
$id = (int) ($_GET['id'] ?? 0);

$stmt = mysqli_prepare($koneksi, 'SELECT * FROM berita WHERE id = ?');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$lama = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$lama) {
    set_pesan('Berita tidak ditemukan.');
    header('Location: index.php');
    exit;
}

// 2. Isi form dengan data lama.
$data = $lama;
$error = [];

// 3. Kalau form dikirim, periksa lalu simpan.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cek_token();
    [$data, $error] = baca_input_berita();
    $data['gambar'] = $lama['gambar'];   // supaya info "gambar sekarang" tetap tampil kalau ada error

    if (!$error) {
        try {
            // Tidak pilih file baru = pakai gambar lama.
            $gambar_baru = upload_gambar('gambar', 'berita');
            $gambar = $gambar_baru !== '' ? $gambar_baru : $lama['gambar'];

            if ($data['jadi_utama']) {
                lepas_berita_utama($koneksi);
            }

            $stmt = mysqli_prepare(
                $koneksi,
                'UPDATE berita
                 SET judul = ?, kategori = ?, ringkasan = ?, gambar = ?, tanggal_terbit = ?, jadi_utama = ?
                 WHERE id = ?'
            );
            mysqli_stmt_bind_param(
                $stmt, 'sssssii',
                $data['judul'], $data['kategori'], $data['ringkasan'],
                $gambar, $data['tanggal_terbit'], $data['jadi_utama'], $id
            );
            mysqli_stmt_execute($stmt);

            set_pesan('Berita berhasil diperbarui.');
            header('Location: index.php');
            exit;
        } catch (Exception $e) {
            $error[] = $e->getMessage();
        }
    }
}

$judul_halaman = 'Edit Berita';
$tombol = 'Simpan Perubahan';
require __DIR__ . '/../_atas.php';
?>

<h1>Edit Berita</h1>
<?php require __DIR__ . '/_form.php'; ?>

<?php require __DIR__ . '/../_bawah.php'; ?>
