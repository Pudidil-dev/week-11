<?php
// READ: tampilkan semua event, dikelompokkan per jenis.
require __DIR__ . '/../fungsi.php';
require __DIR__ . '/_data.php';
wajib_login();

// Urutkan per jenis dulu, lalu tanggal terbaru. Tanggal kosong (NULL) taruh di bawah.
$hasil = mysqli_query($koneksi, 'SELECT * FROM event ORDER BY jenis, tanggal IS NULL, tanggal DESC');

$judul_halaman = 'Event';
require __DIR__ . '/../_atas.php';
?>

<h1>Event</h1>
<p><a class="tombol" href="tambah.php">+ Tambah Event</a></p>

<div class="tabel-wrap">
  <table>
    <tr>
      <th>Jenis</th>
      <th>Judul</th>
      <th>Tanggal</th>
      <th>Kota / Venue</th>
      <th>Label</th>
      <th>Aksi</th>
    </tr>
    <?php while ($ev = mysqli_fetch_assoc($hasil)) : ?>
      <tr>
        <td><?= e($ev['jenis']) ?></td>
        <td><?= e($ev['judul']) ?></td>
        <td><?= e($ev['tanggal'] ?? '-') ?></td>
        <td><?= e(trim(($ev['kota'] ?? '') . ' / ' . ($ev['venue'] ?? ''), ' /')) ?></td>
        <td><?= e($ev['label']) ?></td>
        <td class="aksi">
          <a href="edit.php?id=<?= $ev['id'] ?>">Edit</a> |
          <a href="hapus.php?id=<?= $ev['id'] ?>">Hapus</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
</div>

<?php require __DIR__ . '/../_bawah.php'; ?>
