<?php
// READ: daftar permintaan dari form Pemesanan & Kerja Sama (tur-event.php).
require __DIR__ . '/../fungsi.php';
wajib_login();

$hasil = mysqli_query($koneksi, 'SELECT * FROM pemesanan_event ORDER BY waktu_kirim DESC');

$judul_halaman = 'Pemesanan Event';
require __DIR__ . '/../_atas.php';
?>

<h1>Pemesanan Event</h1>
<p>Data dari form Pemesanan &amp; Kerja Sama di halaman Event. Hubungi pemesan lewat email atau telepon.</p>

<?php if (mysqli_num_rows($hasil) == 0) { ?>
  <p>Belum ada pemesanan.</p>
<?php } ?>

<div class="tabel-wrap">
  <table>
    <tr>
      <th>Waktu Kirim</th>
      <th>Organisasi / Kontak</th>
      <th>Tipe Acara</th>
      <th>Tanggal Acara</th>
      <th>Estimasi</th>
      <th>Venue</th>
      <th>Anggaran</th>
      <th>Catatan</th>
      <th>Aksi</th>
    </tr>
    <?php while ($p = mysqli_fetch_assoc($hasil)) { ?>
      <tr>
        <td><?= e($p['waktu_kirim']) ?></td>
        <td>
          <strong><?= e($p['nama_organisasi']) ?></strong><br>
          <?= e($p['email']) ?><br>
          <?= e($p['telepon']) ?>
        </td>
        <td><?= e($p['tipe_acara']) ?></td>
        <td><?= e($p['tanggal_acara'] ?? '-') ?></td>
        <td><?= e($p['estimasi_kehadiran'] ?? '-') ?></td>
        <td><?= e($p['venue_lokasi'] ?? '-') ?></td>
        <td><?= e($p['rentang_anggaran'] ?? '-') ?></td>
        <td><?= e($p['catatan'] ?? '-') ?></td>
        <td class="aksi"><a href="hapus.php?id=<?= $p['id'] ?>">Hapus</a></td>
      </tr>
    <?php } ?>
  </table>
</div>

<?php require __DIR__ . '/../_bawah.php'; ?>
