<?php
require __DIR__ . '/fungsi.php';
wajib_login();

// Hitung jumlah baris tiap tabel untuk ringkasan di dashboard.
// Nama tabel ditulis langsung (bukan dari input pengguna), jadi aman tanpa prepared statement.
$tabel = [
    'berita'          => 'Berita',
    'event'           => 'Event',
    'kiriman_sahabat' => 'Kiriman Sahabat',
    'pemesanan_event' => 'Pemesanan Event',
    'pelanggan_email' => 'Pelanggan Email',
];

$jumlah = [];
foreach ($tabel as $nama => $label) {
    $hasil = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM $nama");
    $jumlah[$label] = mysqli_fetch_assoc($hasil)['total'];
}

$judul_halaman = 'Dashboard';
require __DIR__ . '/_atas.php';
?>

<h1>Dashboard</h1>
<p>Selamat datang, <?= e($_SESSION['admin_username']) ?>.</p>

<div class="kartu-grid">
  <?php foreach ($jumlah as $label => $total) : ?>
    <div class="kartu">
      <span class="kartu-angka"><?= $total ?></span>
      <span><?= e($label) ?></span>
    </div>
  <?php endforeach; ?>
</div>

<p>
  <a class="tombol" href="berita/index.php">Kelola Berita</a>
  <a class="tombol" href="event/index.php">Kelola Event</a>
</p>

<?php require __DIR__ . '/_bawah.php'; ?>
