<?php
// =====================================================
// HALAMAN CERITA BAND
// Data dari database: anggota (Kenali Anggota)
// Bagian lain (sejarah, penghargaan) jarang berubah, jadi cukup ditulis langsung.
// =====================================================

include 'koneksi.php';

$hasil_anggota = mysqli_query($koneksi, "SELECT * FROM anggota ORDER BY id ASC");

$judul_halaman = 'Cerita Band - NOAH';
$kelas_body    = 'page-cerita';
$menu_aktif    = 'cerita';
include 'bagian/header.php';
?>

  <!-- ===== PAGE HEADER (judul halaman + breadcrumb) ===== -->
  <header class="page-header">
    <div class="container">
      <p class="breadcrumb"><a href="index.php">Beranda</a> &gt; <span>Cerita Band</span></p>
      <h1 class="page-judul">Cerita Band</h1>
      <p class="page-sub">Dari Peterpan ke NOAH: Perjalanan Band Rock Terlaris Indonesia yang mendefinisikan ulang musik pop-rock tanah air.</p>
    </div>
  </header>

  <!-- ===== NARASI PEMBUKA ===== -->
  <section class="section">
    <div class="container grid-2">
      <div>
        <p class="paragraf">
          Berawal dari panggung kecil sekolah di Bandung, perjalanan NOAH adalah narasi
          tentang ketekunan, evolusi, dan dedikasi tanpa henti pada karya seni. Sebelum
          dikenal sebagai raksasa industri musik Indonesia, benih kreativitas ini muncul
          dari grup bernama 'Topi', sebuah band sekolah yang menjadi pondasi.
        </p>
        <p class="paragraf">
          Dengan penjualan lebih dari 9 juta keping album, transisi dari Peterpan menjadi
          NOAH bukan sekadar pergantian nama, melainkan sebuah re-inkarnasi artistik. Band
          ini telah melewati berbagai pasang surut industri, namun tetap menjaga
          otentisitas musik yang hangat dan lirik yang menyentuh jiwa jutaan penggemar.
        </p>
      </div>
      <div class="band-foto card-border-merah"><img src="images/noah-stage.jpg" alt="NOAH di Panggung"></div>
    </div>
  </section>

  <!-- ===== TONGGAK SEJARAH (garis waktu sederhana) ===== -->
  <section class="section bg-abu">
    <div class="container">
      <h2 class="section-title tengah mb">Tonggak Sejarah</h2>

      <div class="timeline">
        <div class="timeline-item">
          <span class="tahun">1997</span>
          <h3 class="berita-judul">Terbentuknya Topi</h3>
          <p class="paragraf-kecil">Cikal bakal NOAH yang dibentuk di Bandung, mengawali perjalanan dari kafe ke kafe.</p>
        </div>
        <div class="timeline-item">
          <span class="tahun">2000</span>
          <h3 class="berita-judul">Lahirnya Peterpan</h3>
          <p class="paragraf-kecil">Resmi menggunakan nama Peterpan dan mulai merekam demo yang mengubah industri musik.</p>
        </div>
        <div class="timeline-item">
          <span class="tahun">2004</span>
          <h3 class="berita-judul">Bintang di Surga</h3>
          <p class="paragraf-kecil">Album fenomenal yang terjual lebih dari 3.2 juta keping, salah satu terlaris sepanjang masa.</p>
        </div>
        <div class="timeline-item">
          <span class="tahun">2012</span>
          <h3 class="berita-judul">Reinkarnasi: NOAH</h3>
          <p class="paragraf-kecil">Kembali dengan nama NOAH dan merilis 'Seperti Seharusnya', membuktikan kekuatan mereka.</p>
        </div>
        <div class="timeline-item">
          <span class="tahun">2024</span>
          <h3 class="berita-judul">Hiatus &amp; Refleksi</h3>
          <p class="paragraf-kecil">Mengambil waktu sejenak untuk persiapan karya yang lebih monumental di masa depan.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== KENALI ANGGOTA (dari tabel anggota) ===== -->
  <section class="section">
    <div class="container">
      <h2 class="section-title mb">Kenali Anggota</h2>
      <p class="label-merah">Anggota Aktif</p>

      <div class="grid-3">
        <?php while ($anggota = mysqli_fetch_assoc($hasil_anggota)) { ?>
          <div class="card card-border-merah tengah">
            <?php if ($anggota['foto'] != '') { ?>
              <div class="avatar"><img src="<?php echo htmlspecialchars($anggota['foto']); ?>" alt="<?php echo htmlspecialchars($anggota['nama']); ?>"></div>
            <?php } ?>
            <h4 class="kota"><?php echo htmlspecialchars($anggota['nama']); ?></h4>
            <p class="lagu-artis"><?php echo htmlspecialchars($anggota['posisi']); ?></p>
            <p class="paragraf-kecil"><?php echo htmlspecialchars($anggota['deskripsi']); ?></p>
          </div>
        <?php } ?>
      </div>
    </div>
  </section>

  <!-- ===== PENGHARGAAN (section gelap) ===== -->
  <section class="section bg-gelap">
    <div class="container tengah">
      <h2 class="judul-putih mb">Penghargaan &amp; Prestasi</h2>
      <div class="stats">
        <div class="stat"><span class="stat-angka">3x</span><p class="teks-abu">AMI Awards</p></div>
        <div class="stat"><span class="stat-angka">2x</span><p class="teks-abu">MTV Asia</p></div>
        <div class="stat"><span class="stat-angka">2007</span><p class="teks-abu">Asia Song Fest</p></div>
        <div class="stat"><span class="stat-angka">2</span><p class="teks-abu">MURI Records</p></div>
        <div class="stat"><span class="stat-angka">9M+</span><p class="teks-abu">Album Terjual</p></div>
        <div class="stat"><span class="stat-angka">KCA</span><p class="teks-abu">Nickelodeon</p></div>
      </div>
    </div>
  </section>

  <!-- ===== PENGARUH & WARISAN ===== -->
  <section class="section">
    <div class="container">
      <h2 class="section-title mb">Pengaruh &amp; Warisan</h2>
      <div class="grid-2">
        <div>
          <p class="label-merah">Akar &amp; Inspirasi</p>
          <div class="tag-list">
            <span class="tag">Nirvana</span>
            <span class="tag">Pearl Jam</span>
            <span class="tag">Coldplay</span>
            <span class="tag">U2</span>
            <span class="tag">Radiohead</span>
          </div>
        </div>
        <div>
          <p class="label-merah">Sang Evolusi</p>
          <p class="paragraf">
            Musik NOAH telah berevolusi dari rock alternatif mentah di akhir 90-an menjadi
            suara yang lebih dipoles dan kaya akan nuansa elektronik. Pengaruh dari band
            arena rock global diterjemahkan ke dalam rasa lokal yang unik.
          </p>
        </div>
      </div>
    </div>
  </section>

<?php include 'bagian/footer.php'; ?>
