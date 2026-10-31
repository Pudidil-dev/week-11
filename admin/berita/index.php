<?php
// READ: tampilkan semua berita dalam tabel.
require __DIR__ . '/../fungsi.php';
wajib_login();

$hasil = mysqli_query($koneksi, 'SELECT * FROM berita ORDER BY tanggal_terbit DESC');

$judul_halaman = 'Berita';
require __DIR__ . '/../_atas.php';
?>

<h1>Berita</h1>
<p><a class="tombol" href="tambah.php">+ Tambah Berita</a></p>

<div class="tabel-wrap">
  <table>
    <tr>
      <th>Gambar</th>
      <th>Judul</th>
      <th>Kategori</th>
      <th>Tanggal</th>
      <th>Utama</th>
      <th>Aksi</th>
    </tr>
    <?php while ($b = mysqli_fetch_assoc($hasil)) : ?>
      <tr>
        <td>
          <?php if ($b['gambar']) : ?>
            <img src="../../<?= e($b['gambar']) ?>" alt="">
          <?php else : ?>
            -
          <?php endif; ?>
        </td>
        <td><?= e($b['judul']) ?></td>
        <td><?= e($b['kategori']) ?></td>
        <td><?= e($b['tanggal_terbit']) ?></td>
        <td><?= $b['jadi_utama'] ? 'Ya' : '' ?></td>
        <td class="aksi">
          <a href="edit.php?id=<?= $b['id'] ?>">Edit</a> |
          <a href="hapus.php?id=<?= $b['id'] ?>">Hapus</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
</div>

<?php require __DIR__ . '/../_bawah.php'; ?>
