<?php
// =====================================================
// HALAMAN SAHABAT NOAH
// Data dari database: kiriman_sahabat, komunitas_regional, event (komunitas)
// Form: kirim cerita/karya -> proses/simpan-kiriman.php
// =====================================================

include 'koneksi.php';

// Cerita penggemar dan cover lagu yang sudah disetujui (status = tampil)
$hasil_cerita = mysqli_query($koneksi, "SELECT * FROM kiriman_sahabat WHERE (kategori = 'Cerita Penggemar' OR kategori = 'Cover Lagu') AND status = 'tampil' ORDER BY waktu_kirim DESC");

// Karya penggemar yang sudah disetujui dan punya foto
$hasil_karya = mysqli_query($koneksi, "SELECT * FROM kiriman_sahabat WHERE kategori = 'Karya Penggemar' AND status = 'tampil' AND foto IS NOT NULL ORDER BY waktu_kirim DESC");

// Komunitas regional, urut dari anggota terbanyak
$hasil_komunitas = mysqli_query($koneksi, "SELECT * FROM komunitas_regional ORDER BY jumlah_anggota DESC");

// Statistik hero dihitung dari tabel komunitas_regional:
// SUM = jumlahkan semua anggota, COUNT = hitung jumlah wilayah
$hasil_statistik = mysqli_query($koneksi, "SELECT SUM(jumlah_anggota) AS total_anggota, COUNT(*) AS total_wilayah FROM komunitas_regional");
$statistik       = mysqli_fetch_assoc($hasil_statistik);

// Event komunitas
$hasil_event = mysqli_query($koneksi, "SELECT * FROM event WHERE jenis = 'komunitas' ORDER BY tanggal ASC");

$bulan_lengkap = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

$pesan = '';
if (isset($_GET['pesan'])) {
    $pesan = $_GET['pesan'];
}

$judul_halaman = 'Sahabat NOAH - Komunitas Resmi';
$kelas_body    = 'page-sahabat';
$menu_aktif    = 'sahabat';
include 'bagian/header.php';
?>

  <!-- ===== HERO + STATISTIK (anggota & wilayah dari tabel komunitas_regional) ===== -->
  <section class="hero-sahabat">
    <div class="container tengah">
      <h1 class="hero-judul">Gabung Sahabat NOAH</h1>
      <p class="hero-subjudul">Komunitas resmi penggemar NOAH. Terhubung dengan sesama Sahabat di seluruh Indonesia dan dapatkan akses eksklusif konten band kesayanganmu.</p>
      <a class="btn btn-outline-putih mb" href="#form-cerita">Daftar Sekarang</a>

      <!-- Statistik komunitas -->
      <div class="stats stats-garis">
        <!-- number_format mengubah 28220 menjadi 28.220. (int) supaya hasil kosong terbaca 0. -->
        <div class="stat"><span class="stat-angka"><?php echo number_format((int) $statistik['total_anggota'], 0, ',', '.'); ?></span><p>Anggota</p></div>
        <div class="stat"><span class="stat-angka"><?php echo $statistik['total_wilayah']; ?></span><p>Wilayah</p></div>
        <!-- Angka event per tahun tidak tersimpan di database, jadi tetap ditulis manual -->
        <div class="stat"><span class="stat-angka">100+</span><p>Event/Tahun</p></div>
      </div>
    </div>
  </section>

  <!-- ===== CERITA SAHABAT (dari tabel kiriman_sahabat) ===== -->
  <section class="container section">
    <h2 class="section-title tengah mb">Cerita Sahabat</h2>

    <?php if (mysqli_num_rows($hasil_cerita) == 0) { ?>
      <p class="data-kosong tengah">Belum ada cerita. Jadilah yang pertama berbagi!</p>
    <?php } ?>

    <div class="grid-auto">
      <?php while ($cerita = mysqli_fetch_assoc($hasil_cerita)) { ?>
        <div class="card card-border-merah">
          <div class="cerita-head">
            <?php if ($cerita['foto'] != '') { ?>
              <div class="avatar-kecil"><img src="<?php echo htmlspecialchars($cerita['foto']); ?>" alt="<?php echo htmlspecialchars($cerita['nama_lengkap']); ?>"></div>
            <?php } ?>
            <div>
              <h4 class="kota"><?php echo htmlspecialchars($cerita['nama_lengkap']); ?></h4>
              <p class="lagu-artis"><?php echo htmlspecialchars($cerita['kota'] ?? ''); ?></p>
            </div>
          </div>
          <!-- Kiriman Cover Lagu diberi label supaya beda dari cerita biasa -->
          <?php if ($cerita['kategori'] == 'Cover Lagu') { ?>
            <span class="pill">Cover Lagu</span>
          <?php } ?>
          <p class="kutipan">"<?php echo htmlspecialchars($cerita['pesan']); ?>"</p>
        </div>
      <?php } ?>
    </div>

    <div class="tengah" style="margin-top:40px;">
      <a class="btn btn-merah" href="#form-cerita">Bagikan Ceritamu</a>
    </div>
  </section>

  <!-- ===== KARYA SAHABAT (dari tabel kiriman_sahabat) ===== -->
  <section class="section bg-abu">
    <div class="container">
      <h2 class="section-title tengah mb">Karya Sahabat</h2>

      <?php if (mysqli_num_rows($hasil_karya) == 0) { ?>
        <p class="data-kosong tengah">Belum ada karya yang ditampilkan.</p>
      <?php } ?>

      <div class="galeri-foto">
        <?php while ($karya = mysqli_fetch_assoc($hasil_karya)) { ?>
          <!-- title = teks yang muncul saat kursor diarahkan ke gambar: nama pengirim + keterangan karya -->
          <div class="galeri-item"><img src="<?php echo htmlspecialchars($karya['foto']); ?>"
               alt="<?php echo htmlspecialchars($karya['pesan']); ?>"
               title="<?php echo htmlspecialchars($karya['nama_lengkap'] . ': ' . $karya['pesan']); ?>"></div>
        <?php } ?>
      </div>

      <div class="tengah" style="margin-top:40px;">
        <a class="btn btn-outline-merah" href="#form-cerita">Kirim Karyamu</a>
      </div>
    </div>
  </section>

  <!-- ===== KOMUNITAS REGIONAL (dari tabel komunitas_regional) ===== -->
  <section class="container section">
    <h2 class="section-title tengah mb">Komunitas Regional</h2>
    <div class="grid-auto">
      <?php while ($komunitas = mysqli_fetch_assoc($hasil_komunitas)) { ?>
        <div class="card card-border-merah">
          <h3 class="berita-judul"><?php echo htmlspecialchars($komunitas['kota']); ?></h3>
          <!-- number_format mengubah 8420 menjadi 8.420 -->
          <p class="paragraf-kecil">Anggota: <strong><?php echo number_format($komunitas['jumlah_anggota'], 0, ',', '.'); ?></strong></p>
          <p class="paragraf-kecil mb">Ketua: <strong><?php echo htmlspecialchars($komunitas['ketua']); ?></strong></p>
          <a href="#form-cerita" class="link-merah">Gabung Komunitas</a>
        </div>
      <?php } ?>
    </div>
  </section>

  <!-- ===== EVENT KOMUNITAS (dari tabel event, jenis = komunitas) ===== -->
  <section class="section bg-abu">
    <div class="container">
      <h2 class="section-title tengah mb">Event Komunitas</h2>
      <div class="grid-auto">
        <?php $urutan = 1; ?>
        <?php while ($event = mysqli_fetch_assoc($hasil_event)) { ?>
          <?php
            // Kalau tanggal kosong, kotak tanggal menampilkan "Segera" (bukan tanggal palsu 1970)
            if ($event['tanggal'] != '') {
                $waktu        = strtotime($event['tanggal']);
                $teks_hari    = date('d', $waktu);
                $teks_bulan   = $bulan_lengkap[date('n', $waktu)];
            } else {
                $teks_hari    = '--';
                $teks_bulan   = 'Segera';
            }
            // Warna kotak tanggal berselang-seling: ganjil merah, genap hitam
            if ($urutan % 2 == 1) {
                $warna = 'bg-merah';
            } else {
                $warna = 'bg-hitam';
            }
          ?>
          <div class="event-card">
            <div class="event-tanggal <?php echo $warna; ?>">
              <span class="event-angka"><?php echo $teks_hari; ?></span>
              <span><?php echo $teks_bulan; ?></span>
            </div>
            <div class="event-isi">
              <?php if ($event['label'] != '') { ?>
                <span class="label-merah"><?php echo htmlspecialchars($event['label']); ?></span>
              <?php } ?>
              <h3 class="berita-judul"><?php echo htmlspecialchars($event['judul']); ?></h3>
              <?php if ($event['kota'] != '') { ?>
                <p class="lagu-artis"><?php echo htmlspecialchars($event['kota']); ?></p>
              <?php } ?>
              <p class="paragraf-kecil mb"><?php echo htmlspecialchars($event['deskripsi'] ?? ''); ?></p>
              <a class="btn btn-merah" href="#form-cerita">Daftar</a>
            </div>
          </div>
          <?php $urutan = $urutan + 1; ?>
        <?php } ?>
      </div>
    </div>
  </section>

  <!-- ===== FORM KIRIM CERITA ===== -->
  <section id="form-cerita" class="container section">
    <h2 class="section-title tengah mb">Kirim Cerita atau Karyamu</h2>
    <div class="card form-besar">

      <?php if ($pesan == 'berhasil') { ?>
        <p class="pesan-form">Terima kasih! Kiriman kamu akan tampil setelah diperiksa admin.</p>
      <?php } ?>
      <?php if ($pesan == 'gagal') { ?>
        <p class="pesan-form gagal">Semua isian wajib diisi dan email harus benar.</p>
      <?php } ?>
      <?php if ($pesan == 'foto_wajib') { ?>
        <p class="pesan-form gagal">Kategori Karya Penggemar wajib menyertakan foto.</p>
      <?php } ?>
      <?php if ($pesan == 'foto_gagal') { ?>
        <p class="pesan-form gagal">Foto harus JPG, PNG, atau WEBP dengan ukuran maksimal 2 MB.</p>
      <?php } ?>

      <!-- enctype multipart/form-data wajib supaya form bisa mengirim file foto -->
      <form class="form" action="proses/simpan-kiriman.php" method="post" enctype="multipart/form-data">
        <div class="form-baris">
          <div>
            <label for="nama_lengkap">Nama Lengkap</label>
            <input id="nama_lengkap" type="text" name="nama_lengkap" placeholder="Masukkan nama" required>
          </div>
          <div>
            <label for="email">Alamat Email</label>
            <input id="email" type="email" name="email" placeholder="email@domain.com" required>
          </div>
        </div>
        <label for="kota">Kota (opsional)</label>
        <input id="kota" type="text" name="kota" placeholder="Contoh: Bandung" maxlength="100">
        <label for="kategori">Kategori</label>
        <select id="kategori" name="kategori" required>
          <option value="">Pilih Kategori</option>
          <option value="Cerita Penggemar">Cerita Penggemar</option>
          <option value="Karya Penggemar">Karya Penggemar</option>
          <option value="Cover Lagu">Cover Lagu</option>
        </select>
        <label for="pesan">Pesan / Deskripsi</label>
        <textarea id="pesan" name="pesan" rows="4" placeholder="Ceritakan sedikit tentang karyamu..." required></textarea>
        <label for="foto">Foto (wajib untuk Karya Penggemar, opsional untuk foto profil cerita)</label>
        <input id="foto" type="file" name="foto" accept="image/jpeg,image/png,image/webp">
        <button type="submit" class="btn btn-merah lebar">Kirim</button>
      </form>
    </div>
  </section>

<?php include 'bagian/footer.php'; ?>
