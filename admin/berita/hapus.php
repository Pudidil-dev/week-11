<?php
// DELETE: tampilkan halaman konfirmasi dulu, baru hapus setelah tombol ditekan.
// Penghapusan lewat form POST (bukan link biasa) supaya tidak terhapus tanpa sengaja.
require __DIR__ . '/../fungsi.php';
wajib_login();

$id = (int) ($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cek_token();

    $stmt = mysqli_prepare($koneksi, 'DELETE FROM berita WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);

    // File gambar sengaja TIDAK ikut dihapus, karena satu gambar bisa dipakai beberapa berita.
    set_pesan('Berita berhasil dihapus.');
    header('Location: index.php');
    exit;
}

// Ambil judul untuk ditampilkan di konfirmasi.
$stmt = mysqli_prepare($koneksi, 'SELECT judul FROM berita WHERE id = ?');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$berita = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$berita) {
    set_pesan('Berita tidak ditemukan.');
    header('Location: index.php');
    exit;
}

$judul_halaman = 'Hapus Berita';
require __DIR__ . '/../_atas.php';
?>

<h1>Hapus Berita</h1>
<form class="form-admin" method="post">
  <?= input_token() ?>
  <p>Yakin ingin menghapus berita <strong><?= e($berita['judul']) ?></strong>? Data yang dihapus tidak bisa dikembalikan.</p>
  <div class="tombol-baris">
    <button type="submit" class="tombol">Ya, Hapus</button>
    <a class="tombol tombol-abu" href="index.php">Batal</a>
  </div>
</form>

<?php require __DIR__ . '/../_bawah.php'; ?>
