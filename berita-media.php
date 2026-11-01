<?php
// =====================================================
// HALAMAN MEDIA
// Data dari database: berita, galeri
// Alamat yang bisa dipakai:
//   berita-media.php?hal=2           -> halaman berita ke-2
//   berita-media.php?q=konser        -> cari berita
//   berita-media.php?kategori=Konser -> filter galeri foto
// =====================================================

include 'koneksi.php';

$bulan_lengkap = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

// ---------- 1. ARTIKEL UTAMA ----------
$hasil_utama = mysqli_query($koneksi, "SELECT * FROM berita WHERE jadi_utama = 1 LIMIT 1");
$utama = mysqli_fetch_assoc($hasil_utama);   // berisi null kalau tidak ada berita utama

// ---------- 2. DAFTAR BERITA + PENCARIAN + HALAMAN ----------
$per_halaman = 3;   // jumlah berita di satu halaman

// Kata pencarian dari form di navbar
$cari = '';
if (isset($_GET['q'])) {
    $cari = trim($_GET['q']);
}

// Nomor halaman sekarang. (int) memastikan isinya angka.
$hal = 1;
if (isset($_GET['hal'])) {
    $hal = (int) $_GET['hal'];
}
if ($hal < 1) {
    $hal = 1;
}

// Syarat WHERE: kalau sedang mencari, cari di judul dan ringkasan.
// Kalau tidak mencari, tampilkan semua berita kecuali Artikel Utama.
if ($cari != '') {
    $cari_aman = mysqli_real_escape_string($koneksi, $cari);
    $syarat = "judul LIKE '%$cari_aman%' OR ringkasan LIKE '%$cari_aman%'";
} else {
    $syarat = "jadi_utama = 0";
}

// Hitung jumlah semua berita yang cocok, untuk menentukan jumlah halaman
$hasil_jumlah  = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM berita WHERE $syarat");
$baris_jumlah  = mysqli_fetch_assoc($hasil_jumlah);
$total_berita  = $baris_jumlah['total'];
$total_halaman = ceil($total_berita / $per_halaman);   // ceil = bulatkan ke atas

// OFFSET = berapa baris dilewati. Halaman 1 lewati 0, halaman 2 lewati 3, dst.
$lewati = ($hal - 1) * $per_halaman;
$hasil_berita = mysqli_query($koneksi, "SELECT * FROM berita WHERE $syarat ORDER BY tanggal_terbit DESC LIMIT $per_halaman OFFSET $lewati");

// ---------- 3. DI BALIK LAYAR ----------
$hasil_layar = mysqli_query($koneksi, "SELECT * FROM galeri WHERE bagian = 'di_balik_layar' ORDER BY id ASC");

// ---------- 4. GALERI FOTO + FILTER ----------
$kategori = '';
if (isset($_GET['kategori'])) {
    $kategori = $_GET['kategori'];
}

if ($kategori != '') {
    $kategori_aman = mysqli_real_escape_string($koneksi, $kategori);
    $hasil_foto = mysqli_query($koneksi, "SELECT * FROM galeri WHERE bagian = 'galeri_foto' AND kategori = '$kategori_aman' ORDER BY id ASC");
} else {
    $hasil_foto = mysqli_query($koneksi, "SELECT * FROM galeri WHERE bagian = 'galeri_foto' ORDER BY id ASC");
}

// Daftar tombol filter galeri
$daftar_kategori = ['Peterpan Era', 'NOAH Era', 'Konser', 'Studio'];

$judul_halaman = 'Media - NOAH';
$kelas_body    = 'page-media';
$menu_aktif    = 'media';
include 'bagian/header.php';
?>

  <!-- ===== ARTIKEL UTAMA (berita dengan jadi_utama = 1) ===== -->
  <?php if ($utama != null) { ?>
    <?php
      $waktu = strtotime($utama['tanggal_terbit']);
      $tanggal_utama = date('d', $waktu) . ' ' . $bulan_lengkap[date('n', $waktu)] . ' ' . date('Y', $waktu);
    ?>
    <!-- Gambar latar diambil dari kolom gambar; kalau kosong, pakai gambar bawaan di style.css -->
    <section id="artikel-utama" class="berita-hero"
      <?php if ($utama['gambar'] != '') { ?>
        style="background-image: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.85)), url('<?php echo htmlspecialchars($utama['gambar']); ?>');"
      <?php } ?>>
      <div class="container">
        <span class="pill-merah"><?php echo htmlspecialchars($utama['kategori']); ?></span>
        <h1 class="hero-judul"><?php echo htmlspecialchars($utama['judul']); ?></h1>
        <p class="hero-subjudul-kiri"><?php echo htmlspecialchars($utama['ringkasan']); ?></p>
        <div class="hero-meta">
          <span class="teks-abu"><?php echo $tanggal_utama; ?></span>
          <a class="btn btn-merah" href="#berita-terbaru">Baca Selengkapnya</a>
        </div>
      </div>
    </section>
  <?php } ?>

  <!-- ===== BERITA TERBARU / HASIL PENCARIAN ===== -->
  <section id="berita-terbaru" class="container section">
    <?php if ($cari != '') { ?>
      <h2 class="section-title judul-garis mb">Hasil pencarian "<?php echo htmlspecialchars($cari); ?>"</h2>
      <p class="mb"><?php echo $total_berita; ?> berita ditemukan. <a href="berita-media.php" class="link-merah">Hapus pencarian</a></p>
    <?php } else { ?>
      <h2 class="section-title judul-garis mb">Berita Terbaru</h2>
    <?php } ?>

    <?php if ($total_berita == 0) { ?>
      <p class="data-kosong">Tidak ada berita yang cocok.</p>
    <?php } ?>

    <div class="grid-auto">
      <?php while ($berita = mysqli_fetch_assoc($hasil_berita)) { ?>
        <?php
          $waktu   = strtotime($berita['tanggal_terbit']);
          $tanggal = date('d', $waktu) . ' ' . $bulan_lengkap[date('n', $waktu)] . ' ' . date('Y', $waktu);
        ?>
        <article id="berita-<?php echo $berita['id']; ?>" class="card card-border-merah">
          <?php if ($berita['gambar'] != '') { ?>
            <div class="berita-gambar"><img src="<?php echo htmlspecialchars($berita['gambar']); ?>" alt="<?php echo htmlspecialchars($berita['judul']); ?>"></div>
          <?php } ?>
          <span class="pill"><?php echo htmlspecialchars($berita['kategori']); ?></span>
          <h3 class="berita-judul"><?php echo htmlspecialchars($berita['judul']); ?></h3>
          <p class="berita-tanggal"><?php echo $tanggal; ?></p>
          <p class="paragraf-kecil mb"><?php echo htmlspecialchars($berita['ringkasan']); ?></p>
          <a href="#berita-<?php echo $berita['id']; ?>" class="link-merah">Baca Selengkapnya</a>
        </article>
      <?php } ?>
    </div>

    <!-- Pagination: hanya tampil kalau halamannya lebih dari 1 -->
    <?php if ($total_halaman > 1) { ?>
      <div class="pagination">
        <?php for ($i = 1; $i <= $total_halaman; $i++) { ?>
          <a href="berita-media.php?hal=<?php echo $i; ?>&q=<?php echo urlencode($cari); ?>#berita-terbaru"
             class="page-btn <?php if ($i == $hal) { echo 'aktif'; } ?>"><?php echo $i; ?></a>
        <?php } ?>

        <?php if ($hal < $total_halaman) { ?>
          <a href="berita-media.php?hal=<?php echo $hal + 1; ?>&q=<?php echo urlencode($cari); ?>#berita-terbaru" class="page-btn">Berikutnya</a>
        <?php } ?>
      </div>
    <?php } ?>
  </section>

  <!-- ===== DI BALIK LAYAR (dari tabel galeri) ===== -->
  <section class="section bg-abu">
    <div class="container">
      <h2 class="section-title judul-garis mb">Di Balik Layar</h2>
      <div class="grid-auto">
        <?php while ($item = mysqli_fetch_assoc($hasil_layar)) { ?>
          <div class="card">
            <div class="berita-gambar"><img src="<?php echo htmlspecialchars($item['gambar']); ?>" alt="<?php echo htmlspecialchars($item['judul'] ?? ''); ?>"></div>

            <!-- Video diberi label merah, Foto diberi label biasa -->
            <?php if ($item['jenis'] == 'Video') { ?>
              <span class="pill-merah">Video</span>
            <?php } else { ?>
              <span class="pill">Foto</span>
            <?php } ?>

            <h4 class="berita-judul"><?php echo htmlspecialchars($item['judul'] ?? ''); ?></h4>
            <p class="paragraf-kecil"><?php echo htmlspecialchars($item['keterangan'] ?? ''); ?></p>
          </div>
        <?php } ?>
      </div>
    </div>
  </section>

  <!-- ===== GALERI FOTO (dari tabel galeri) ===== -->
  <section id="galeri-foto" class="container section">
    <h2 class="section-title judul-garis mb">Galeri Foto</h2>

    <div class="filter-grup mb">
      <a href="berita-media.php#galeri-foto" class="filter-btn <?php if ($kategori == '') { echo 'aktif'; } ?>">Semua</a>
      <?php foreach ($daftar_kategori as $nama) { ?>
        <a href="berita-media.php?kategori=<?php echo urlencode($nama); ?>#galeri-foto"
           class="filter-btn <?php if ($kategori == $nama) { echo 'aktif'; } ?>"><?php echo $nama; ?></a>
      <?php } ?>
    </div>

    <?php if (mysqli_num_rows($hasil_foto) == 0) { ?>
      <p class="data-kosong">Belum ada foto untuk kategori ini.</p>
    <?php } ?>

    <div class="galeri-foto">
      <?php $nomor_foto = 1; ?>
      <?php while ($foto = mysqli_fetch_assoc($hasil_foto)) { ?>
        <div class="galeri-item"><img src="<?php echo htmlspecialchars($foto['gambar']); ?>" alt="Foto <?php echo $nomor_foto; ?>"></div>
        <?php $nomor_foto = $nomor_foto + 1; ?>
      <?php } ?>
    </div>
  </section>

  <!-- ===== MATERI PERS (tetap statis) ===== -->
  <section id="materi-pers" class="container section">
    <h2 class="section-title judul-garis mb">Materi Pers &amp; Sumber Media</h2>
    <div class="card">
      <div class="presskit-baris">
        <div>
          <h4 class="berita-judul">Biografi Resmi</h4>
          <p class="lagu-artis">PDF &bull; 2.4 MB</p>
        </div>
        <a class="btn btn-merah" href="#materi-pers">Lihat Info</a>
      </div>
      <div class="presskit-baris">
        <div>
          <h4 class="berita-judul">Paket Foto Resolusi Tinggi</h4>
          <p class="lagu-artis">ZIP &bull; 145.0 MB</p>
        </div>
        <a class="btn btn-merah" href="#materi-pers">Lihat Info</a>
      </div>
      <div class="presskit-baris">
        <div>
          <h4 class="berita-judul">Paket Logo</h4>
          <p class="lagu-artis">SVG/PNG &bull; 12.0 MB</p>
        </div>
        <a class="btn btn-merah" href="#materi-pers">Lihat Info</a>
      </div>
    </div>
  </section>

<?php include 'bagian/footer.php'; ?>
