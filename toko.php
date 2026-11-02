<?php
// =====================================================
// HALAMAN TOKO
// Data dari database: produk
// Filter lewat alamat, contoh:
//   toko.php?kategori=Kaos&harga_max=300000&urut=termurah
//   toko.php?baru=1  -> hanya produk baru
// =====================================================

include 'koneksi.php';

// ---------- 1. BACA PILIHAN FILTER ----------
$kategori  = '';
$harga_max = 2000000;     // nilai awal slider: harga paling tinggi
$urut      = 'terbaru';
$hanya_baru = false;

if (isset($_GET['kategori'])) {
    $kategori = $_GET['kategori'];
}
if (isset($_GET['harga_max'])) {
    $harga_max = (int) $_GET['harga_max'];   // (int) memastikan isinya angka
}
if (isset($_GET['urut'])) {
    $urut = $_GET['urut'];
}
if (isset($_GET['baru']) && $_GET['baru'] == '1') {
    $hanya_baru = true;
}

// ---------- 2. SUSUN QUERY PRODUK ----------
$syarat = "harga <= $harga_max";

if ($kategori != '') {
    $kategori_aman = mysqli_real_escape_string($koneksi, $kategori);
    $syarat = $syarat . " AND kategori = '$kategori_aman'";
}
if ($hanya_baru) {
    $syarat = $syarat . " AND produk_baru = 1";
}

// Urutan hanya boleh salah satu dari pilihan di bawah
if ($urut == 'termurah') {
    $urutan = "harga ASC";
} elseif ($urut == 'termahal') {
    $urutan = "harga DESC";
} else {
    $urutan = "id DESC";    // terbaru = id paling besar
}

$hasil_produk = mysqli_query($koneksi, "SELECT * FROM produk WHERE $syarat ORDER BY $urutan");

// ---------- 3. SIDEBAR: JUMLAH PRODUK PER KATEGORI ----------
$hasil_kategori = mysqli_query($koneksi, "SELECT kategori, COUNT(*) AS jumlah FROM produk GROUP BY kategori ORDER BY kategori");

$hasil_total  = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM produk");
$baris_total  = mysqli_fetch_assoc($hasil_total);
$total_produk = $baris_total['total'];

$hasil_jumlah_baru = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM produk WHERE produk_baru = 1");
$baris_jumlah_baru = mysqli_fetch_assoc($hasil_jumlah_baru);
$jumlah_baru       = $baris_jumlah_baru['total'];

// ---------- 4. EDISI TERBATAS ----------
$hasil_terbatas = mysqli_query($koneksi, "SELECT * FROM produk WHERE edisi_terbatas = 1 ORDER BY id DESC");

$judul_halaman = 'Toko - NOAH';
$kelas_body    = 'page-toko';
$menu_aktif    = 'toko';
include 'bagian/header.php';
?>

  <!-- ===== BANNER PROMO (tetap statis) ===== -->
  <section class="toko-banner">
    <div class="container grid-2">
      <div>
        <h1 class="hero-judul">Koleksi Terbaru NOAH 2024</h1>
        <p class="hero-subjudul-kiri">Produk resmi eksklusif - Edisi terbatas tersedia!</p>
        <a class="btn btn-putih" href="#produk">Belanja Sekarang</a>
      </div>
      <div class="banner-gambar"><img src="images/shop-banner.jpg" alt="Produk Resmi NOAH"></div>
    </div>
  </section>

  <!-- ===== INFO PENGIRIMAN ===== -->
  <section class="info-bar">
    <div class="container info-isi">
      <div><svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg> Gratis Ongkir di atas Rp 500.000</div>
      <div><svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Garansi Produk Resmi</div>
      <div><svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg> 30 Hari Pengembalian</div>
    </div>
  </section>

  <!-- ===== FILTER + PRODUK ===== -->
  <section class="container section">
    <div class="toko-layout">

      <!-- Sidebar filter -->
      <aside class="sidebar">
        <h3 class="berita-judul">Kategori</h3>
        <ul class="kategori-list">
          <li class="<?php if ($kategori == '' && !$hanya_baru) { echo 'aktif'; } ?>">
            <a href="toko.php#produk">Semua <span><?php echo $total_produk; ?></span></a>
          </li>
          <?php while ($kat = mysqli_fetch_assoc($hasil_kategori)) { ?>
            <li class="<?php if ($kategori == $kat['kategori']) { echo 'aktif'; } ?>">
              <a href="toko.php?kategori=<?php echo urlencode($kat['kategori']); ?>#produk">
                <?php echo htmlspecialchars($kat['kategori']); ?> <span><?php echo $kat['jumlah']; ?></span>
              </a>
            </li>
          <?php } ?>
          <li class="<?php if ($hanya_baru) { echo 'aktif'; } ?>">
            <a href="toko.php?baru=1#produk">Produk Baru <span><?php echo $jumlah_baru; ?></span></a>
          </li>
        </ul>

        <!-- Harga dan urutan dikirim lewat form GET. Tombol Terapkan diperlukan karena tidak ada JavaScript. -->
        <form class="form-filter" action="toko.php#produk" method="get">
          <input type="hidden" name="kategori" value="<?php echo htmlspecialchars($kategori); ?>">
          <?php if ($hanya_baru) { ?>
            <input type="hidden" name="baru" value="1">
          <?php } ?>

          <h3 class="berita-judul">Harga Maksimal</h3>
          <input type="range" name="harga_max" min="0" max="2000000" step="50000" value="<?php echo $harga_max; ?>" class="range">
          <div class="range-label">
            <span>Rp 0</span>
            <span>Rp <?php echo number_format($harga_max, 0, ',', '.'); ?></span>
          </div>

          <h3 class="berita-judul">Urutkan</h3>
          <select name="urut">
            <option value="terbaru"  <?php if ($urut == 'terbaru')  { echo 'selected'; } ?>>Terbaru</option>
            <option value="termurah" <?php if ($urut == 'termurah') { echo 'selected'; } ?>>Harga Terendah</option>
            <option value="termahal" <?php if ($urut == 'termahal') { echo 'selected'; } ?>>Harga Tertinggi</option>
          </select>

          <button type="submit" class="btn btn-merah">Terapkan</button>
        </form>
      </aside>

      <!-- Grid produk (dari tabel produk) -->
      <div id="produk" class="produk-grid">
        <?php if (mysqli_num_rows($hasil_produk) == 0) { ?>
          <p class="data-kosong">Tidak ada produk untuk filter ini.</p>
        <?php } ?>

        <?php while ($produk = mysqli_fetch_assoc($hasil_produk)) { ?>
          <div class="card card-border-merah produk">
            <div class="produk-gambar">
              <!-- Badge dari kolom produk_baru dan edisi_terbatas (1 = ya) -->
              <?php if ($produk['produk_baru'] == 1) { ?>
                <span class="badge-baru">Baru</span>
              <?php } ?>
              <?php if ($produk['edisi_terbatas'] == 1) { ?>
                <span class="badge-limited">Terbatas</span>
              <?php } ?>
              <img src="<?php echo htmlspecialchars($produk['gambar']); ?>" alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>">
            </div>
            <h4 class="berita-judul"><?php echo htmlspecialchars($produk['nama_produk']); ?></h4>
            <p class="paragraf-kecil"><?php echo htmlspecialchars($produk['deskripsi']); ?></p>
            <!-- number_format mengubah 250000 menjadi 250.000 -->
            <p class="harga">Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?></p>
            <a class="btn btn-merah lebar" href="#produk">Tambah ke Keranjang</a>
          </div>
        <?php } ?>
      </div>

    </div>
  </section>

  <!-- ===== EDISI TERBATAS (produk dengan edisi_terbatas = 1) ===== -->
  <section class="section bg-abu">
    <div class="container">
      <p class="label-merah">Pilihan Kolektor</p>
      <h2 class="section-title mb">Edisi Terbatas</h2>
      <div class="grid-2">
        <?php while ($terbatas = mysqli_fetch_assoc($hasil_terbatas)) { ?>
          <div class="card card-border-merah le-card">
            <div class="produk-gambar"><img src="<?php echo htmlspecialchars($terbatas['gambar']); ?>" alt="<?php echo htmlspecialchars($terbatas['nama_produk']); ?>"></div>
            <div class="le-isi">
              <div class="le-head">
                <span class="label-emas">TERBATAS</span>
                <?php if ($terbatas['stok'] != '') { ?>
                  <?php if ($terbatas['stok'] > 0) { ?>
                    <span class="link-merah">Sisa <?php echo $terbatas['stok']; ?> item</span>
                  <?php } else { ?>
                    <span class="link-merah">Habis</span>
                  <?php } ?>
                <?php } ?>
              </div>
              <h3 class="berita-judul"><?php echo htmlspecialchars($terbatas['nama_produk']); ?></h3>
              <p class="paragraf-kecil mb"><?php echo htmlspecialchars($terbatas['deskripsi']); ?></p>
              <p class="harga-besar">Rp <?php echo number_format($terbatas['harga'], 0, ',', '.'); ?></p>
              <a class="btn btn-merah lebar" href="#produk">Beli Sekarang</a>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>
  </section>

<?php include 'bagian/footer.php'; ?>
