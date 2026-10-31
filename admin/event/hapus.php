<?php
// DELETE: tampilkan konfirmasi dulu, hapus setelah tombol ditekan.
require __DIR__ . '/../fungsi.php';
wajib_login();

$id = (int) ($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cek_token();

    $stmt = mysqli_prepare($koneksi, 'DELETE FROM event WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);

    set_pesan('Event berhasil dihapus.');
    header('Location: index.php');
    exit;
}

$stmt = mysqli_prepare($koneksi, 'SELECT judul FROM event WHERE id = ?');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$event = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$event) {
    set_pesan('Event tidak ditemukan.');
    header('Location: index.php');
    exit;
}

$judul_halaman = 'Hapus Event';
require __DIR__ . '/../_atas.php';
?>

<h1>Hapus Event</h1>
<form class="form-admin" method="post">
  <?= input_token() ?>
  <p>Yakin ingin menghapus event <strong><?= e($event['judul']) ?></strong>? Data yang dihapus tidak bisa dikembalikan.</p>
  <div class="tombol-baris">
    <button type="submit" class="tombol">Ya, Hapus</button>
    <a class="tombol tombol-abu" href="index.php">Batal</a>
  </div>
</form>

<?php require __DIR__ . '/../_bawah.php'; ?>
