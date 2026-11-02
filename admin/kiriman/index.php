<?php
// READ: daftar kiriman dari form Kirim Cerita atau Karyamu (sahabat-noah.php).
// Admin bisa menampilkan / menyembunyikan kiriman di website, atau menghapusnya.
require __DIR__ . '/../fungsi.php';
wajib_login();

// Kiriman yang masih menunggu ditaruh paling atas, lalu urut dari yang terbaru
$hasil = mysqli_query($koneksi, "SELECT * FROM kiriman_sahabat ORDER BY status = 'tampil', waktu_kirim DESC");

$judul_halaman = 'Kiriman Sahabat';
require __DIR__ . '/../_atas.php';
?>

<h1>Kiriman Sahabat</h1>
<p>Kiriman baru berstatus <strong>menunggu</strong> dan belum tampil di website. Klik <strong>Tampilkan</strong> supaya muncul di halaman Sahabat NOAH.</p>

<div class="tabel-wrap">
  <table>
    <tr>
      <th>Waktu</th>
      <th>Nama / Email / Kota</th>
      <th>Kategori</th>
      <th>Pesan</th>
      <th>Foto</th>
      <th>Status</th>
      <th>Aksi</th>
    </tr>
    <?php while ($k = mysqli_fetch_assoc($hasil)) { ?>
      <tr>
        <td><?= e($k['waktu_kirim']) ?></td>
        <td>
          <?= e($k['nama_lengkap']) ?><br>
          <?= e($k['email']) ?><br>
          <?= e($k['kota'] ?? '-') ?>
        </td>
        <td><?= e($k['kategori']) ?></td>
        <td><?= e($k['pesan']) ?></td>
        <td>
          <?php if ($k['foto'] != '') { ?>
            <img src="../../<?= e($k['foto']) ?>" alt="">
          <?php } else { ?>
            -
          <?php } ?>
        </td>
        <td><?= e($k['status']) ?></td>
        <td class="aksi">
          <!-- Tombol ubah status dikirim lewat form POST (bukan link) supaya aman -->
          <form method="post" action="ubah-status.php">
            <?= input_token() ?>
            <input type="hidden" name="id" value="<?= $k['id'] ?>">
            <?php if ($k['status'] == 'menunggu') { ?>
              <input type="hidden" name="status" value="tampil">
              <button type="submit" class="tombol">Tampilkan</button>
            <?php } else { ?>
              <input type="hidden" name="status" value="menunggu">
              <button type="submit" class="tombol tombol-abu">Sembunyikan</button>
            <?php } ?>
          </form>
          <a href="hapus.php?id=<?= $k['id'] ?>">Hapus</a>
        </td>
      </tr>
    <?php } ?>
  </table>
</div>

<?php require __DIR__ . '/../_bawah.php'; ?>
