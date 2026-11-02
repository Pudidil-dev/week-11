<?php
// =====================================================
// HALAMAN DISKOGRAFI
// Data dari database: album (grid album), single (tabel single)
// Filter lewat alamat, contoh: diskografi.php?era=noah&kategori=Studio
// =====================================================

include 'koneksi.php';

// 1. Baca pilihan filter dari alamat. Kalau tidak ada, isinya kosong.
$era      = '';
$kategori = '';
if (isset($_GET['era'])) {
    $era = $_GET['era'];
}
if (isset($_GET['kategori'])) {
    $kategori = $_GET['kategori'];
}

// 2. Susun syarat WHERE sedikit demi sedikit. "1" artinya selalu benar.
$syarat = "1";

if ($era == 'peterpan') {
    $syarat = $syarat . " AND tahun_rilis <= 2008";
}
if ($era == 'noah') {
    $syarat = $syarat . " AND tahun_rilis >= 2012";
}
// Kategori hanya diterima kalau sesuai pilihan, jadi aman dimasukkan ke query
if ($kategori == 'Studio' || $kategori == 'Kompilasi' || $kategori == 'Lagu Tema') {
    $syarat = $syarat . " AND kategori = '$kategori'";
}

// 3. Ambil album sesuai filter, urut dari yang terbaru
$hasil_album = mysqli_query($koneksi, "SELECT * FROM album WHERE $syarat ORDER BY tahun_rilis DESC");

// 4. Ambil semua single untuk tabel di bawah
$hasil_single = mysqli_query($koneksi, "SELECT * FROM `single` ORDER BY tahun_rilis DESC");

$judul_halaman = 'Diskografi - NOAH';
$kelas_body    = 'page-diskografi';
$menu_aktif    = 'diskografi';
include 'bagian/header.php';
?>

  <!-- ===== JUDUL + FILTER ===== -->
  <section class="container section">
    <h1 class="page-judul">Diskografi</h1>
    <p class="page-sub mb">Koleksi Lengkap Musik NOAH dan Peterpan</p>

    <!-- Tombol filter berupa link. Saat diklik, halaman dibuka ulang dengan ?era= atau ?kategori= -->
    <div class="filter-bar">
      <div class="filter-grup">
        <span class="filter-label">Era:</span>
        <a href="diskografi.php"              class="filter-btn <?php if ($era == '' && $kategori == '') { echo 'aktif'; } ?>">Semua</a>
        <a href="diskografi.php?era=peterpan" class="filter-btn <?php if ($era == 'peterpan') { echo 'aktif'; } ?>">Peterpan 2000-2008</a>
        <a href="diskografi.php?era=noah"     class="filter-btn <?php if ($era == 'noah') { echo 'aktif'; } ?>">NOAH 2012-Kini</a>
      </div>
      <div class="filter-grup">
        <span class="filter-label">Kategori:</span>
        <a href="diskografi.php?kategori=Studio"    class="filter-btn <?php if ($kategori == 'Studio') { echo 'aktif'; } ?>">Studio</a>
        <a href="diskografi.php?kategori=Lagu%20Tema" class="filter-btn <?php if ($kategori == 'Lagu Tema') { echo 'aktif'; } ?>">Lagu Tema</a>
        <a href="diskografi.php?kategori=Kompilasi" class="filter-btn <?php if ($kategori == 'Kompilasi') { echo 'aktif'; } ?>">Kompilasi</a>
        <a href="#single"                       class="filter-btn">Single</a>
      </div>
    </div>
  </section>

  <!-- ===== GRID ALBUM (dari tabel album) ===== -->
  <section class="container section-kecil">
    <?php if (mysqli_num_rows($hasil_album) == 0) { ?>
      <p class="data-kosong">Belum ada album untuk filter ini.</p>
    <?php } ?>

    <div class="grid-3">
      <?php while ($album = mysqli_fetch_assoc($hasil_album)) { ?>
        <?php
          // lagu_unggulan berisi beberapa lagu dipisah Enter. explode memecahnya per baris,
          // lalu baris yang kosong dibuang (trim juga membuang spasi dan tanda Enter sisa).
          $daftar_lagu = [];
          foreach (explode("\n", $album['lagu_unggulan'] ?? '') as $baris) {
              if (trim($baris) != '') {
                  $daftar_lagu[] = trim($baris);
              }
          }
        ?>
        <div id="album-<?php echo $album['id']; ?>" class="card card-border-merah">
          <div class="album-cover"><img src="<?php echo htmlspecialchars($album['foto_album']); ?>" alt="<?php echo htmlspecialchars($album['judul']); ?>"></div>
          <div class="album-head">
            <div>
              <h3 class="berita-judul"><?php echo htmlspecialchars($album['judul']); ?></h3>
              <p class="lagu-artis"><?php echo $album['tahun_rilis']; ?> &bull; <?php echo $album['jumlah_lagu']; ?> Lagu</p>
            </div>
            <span class="pill"><?php echo htmlspecialchars($album['kategori']); ?></span>
          </div>
          <ul class="tracklist">
            <?php
              $nomor = 1;
              foreach ($daftar_lagu as $lagu) {
                  echo '<li>' . $nomor . '. ' . htmlspecialchars($lagu) . '</li>';
                  $nomor = $nomor + 1;
              }
            ?>
          </ul>
          <a class="btn btn-merah lebar" href="#album-<?php echo $album['id']; ?>">Lihat Album</a>
        </div>
      <?php } ?>
    </div>
  </section>

  <!-- ===== TABEL SINGLE (dari tabel single) ===== -->
  <section id="single" class="container section">
    <h2 class="section-title mb">Single</h2>
    <table class="tabel">
      <thead>
        <tr>
          <th>Judul</th>
          <th>Tahun</th>
          <th>Album</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($single = mysqli_fetch_assoc($hasil_single)) { ?>
          <tr>
            <td><?php echo htmlspecialchars($single['judul']); ?></td>
            <td><?php echo $single['tahun_rilis']; ?></td>
            <td><?php echo htmlspecialchars($single['album_asal'] ?? '-'); ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </section>

<?php include 'bagian/footer.php'; ?>
