<?php
// =====================================================
// HALAMAN BERANDA
// Data dari database: event (konser), berita
// Form: berlangganan email -> proses/simpan-email.php
// =====================================================

// 1. Sambungkan ke database
include 'koneksi.php';

// 2. Ambil 3 konser, urut dari tanggal paling dekat
$hasil_konser = mysqli_query($koneksi, "SELECT * FROM event WHERE jenis = 'konser' ORDER BY tanggal ASC LIMIT 3");

// 3. Ambil 3 berita paling baru
$hasil_berita = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY tanggal_terbit DESC LIMIT 3");

// Nama bulan singkat untuk card konser (index 1 = Januari)
$bulan_singkat = ['', 'JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'];

// Nama bulan lengkap untuk tanggal berita
$bulan_lengkap = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

// 4. Isi variabel untuk header, lalu tampilkan header
$judul_halaman = 'NOAH - Situs Resmi';
$kelas_body    = 'page-index';
$menu_aktif    = '';
include 'bagian/header.php';
?>

  <!-- ===== HERO SECTION ===== -->
  <section class="hero">
    <div class="hero-isi">
      <h1 class="hero-judul">NOAH - Suara Satu Generasi</h1>
      <p class="hero-subjudul">Band Rock Terlaris Indonesia | 9 Juta+ Album Terjual</p>
      <div class="hero-tombol">
        <a class="btn btn-merah" href="#player">Dengarkan Sekarang</a>
        <a class="btn btn-outline-putih" href="cerita-band.php">Selengkapnya</a>
      </div>
    </div>
  </section>

  <!-- ===== MUSIK RESMI (tetap statis) ===== -->
  <section id="player" class="player-wrap">
    <div class="musik-native-card card-border-merah">
      <div class="musik-native-cover">
        <img src="images/album-cover.jpg" alt="Album Seperti Seharusnya">
      </div>
      <div class="musik-native-info">
        <p class="label-merah">Album Pilihan</p>
        <h2 class="section-title">Seperti Seharusnya</h2>
        <p class="lagu-artis">NOAH</p>

        <ol class="musik-native-list">
          <li><span>Raja Negeriku</span><span>04:30</span></li>
          <li><span>Jika Engkau</span><span>04:26</span></li>
          <li><span>Separuh Aku</span><span>04:26</span></li>
        </ol>

        <div class="musik-native-aksi">
          <a href="https://open.spotify.com/album/4EneY8crCrt61L7siIXtkS?si=UTO1NEtATPqdLRThsBjRjQ" target="_blank" rel="noopener" class="btn btn-merah">Spotify</a>
          <a href="https://music.youtube.com/channel/UCbtHlc5rJCvSb_k0VyUowLA" target="_blank" rel="noopener" class="btn btn-outline-merah">YouTube Music</a>
          <a href="https://music.apple.com/id/artist/noah/766158889?l=id" target="_blank" rel="noopener" class="link-merah">Apple Music</a>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== KONSER MENDATANG (dari tabel event) ===== -->
  <section class="container section">
    <div class="section-header">
      <h2 class="section-title">Konser Mendatang</h2>
      <a href="tur-event.php#konser-mendatang" class="link-merah">Lihat Semua Jadwal</a>
    </div>

    <div class="grid-3">
      <?php while ($konser = mysqli_fetch_assoc($hasil_konser)) { ?>
        <?php
          // Pecah tanggal 2026-08-24 menjadi hari (24) dan bulan (8)
          $hari  = date('d', strtotime($konser['tanggal']));
          $bulan = date('n', strtotime($konser['tanggal']));
        ?>
        <div class="card card-border-merah">
          <div class="card-atas">
            <div>
              <p class="tanggal-besar"><?php echo $hari; ?></p>
              <p class="bulan"><?php echo $bulan_singkat[$bulan]; ?></p>
            </div>
            <span class="label-tersedia"><?php echo htmlspecialchars($konser['label']); ?></span>
          </div>
          <div class="card-tengah">
            <h3 class="kota"><?php echo htmlspecialchars($konser['kota']); ?></h3>
            <p class="venue"><?php echo htmlspecialchars($konser['venue']); ?></p>
          </div>
          <a class="btn btn-merah lebar" href="tur-event.php#konser-mendatang">Info Acara</a>
        </div>
      <?php } ?>
    </div>
  </section>

  <!-- ===== PROFIL BAND (tetap statis) ===== -->
  <section class="section bg-abu">
    <div class="container grid-2">
      <div class="band-foto"><img src="images/noah_band.jpg" alt="Foto Band NOAH"></div>
      <div class="band-teks">
        <h2 class="section-title">Evolusi Musik Indonesia</h2>
        <p class="paragraf">
          Berawal dari Peterpan hingga bertransformasi menjadi NOAH, perjalanan Ariel,
          Lukman, dan David adalah sejarah hidup musik Indonesia. Dengan dedikasi untuk
          terus berinovasi dalam aransemen dan lirik yang puitis, NOAH berhasil
          mempertahankan posisinya sebagai ikon pop-rock selama lebih dari dua dekade.
          Mereka bukan sekadar band, melainkan representasi semangat satu generasi yang
          terus tumbuh dan bermimpi lewat untaian nada-nada legendaris.
        </p>
        <a class="btn btn-merah" href="cerita-band.php">Baca Cerita Lengkap</a>
      </div>
    </div>
  </section>

  <!-- ===== BERITA TERBARU (dari tabel berita) ===== -->
  <section class="container section">
    <h2 class="section-title mb">Berita Terbaru</h2>

    <div class="grid-3">
      <?php while ($berita = mysqli_fetch_assoc($hasil_berita)) { ?>
        <?php
          // Ubah 2024-09-12 menjadi "12 September 2024"
          $waktu   = strtotime($berita['tanggal_terbit']);
          $tanggal = date('d', $waktu) . ' ' . $bulan_lengkap[date('n', $waktu)] . ' ' . date('Y', $waktu);
        ?>
        <div class="berita">
          <?php if ($berita['gambar'] != '') { ?>
            <div class="berita-gambar"><img src="<?php echo htmlspecialchars($berita['gambar']); ?>" alt="<?php echo htmlspecialchars($berita['judul']); ?>"></div>
          <?php } ?>
          <span class="pill"><?php echo htmlspecialchars($berita['kategori']); ?></span>
          <p class="berita-tanggal"><?php echo $tanggal; ?></p>
          <h3 class="berita-judul"><?php echo htmlspecialchars($berita['judul']); ?></h3>
          <p class="paragraf-kecil"><?php echo htmlspecialchars($berita['ringkasan']); ?></p>
        </div>
      <?php } ?>
    </div>

    <div class="tengah">
      <a href="berita-media.php" class="link-merah">Baca Semua Berita</a>
    </div>
  </section>

  <!-- ===== KOMUNITAS / BULETIN ===== -->
  <section id="komunitas" class="section bg-gelap">
    <div class="container grid-2">
      <div>
        <h2 class="judul-putih">Bergabung dengan Komunitas</h2>
        <p class="teks-abu">
          Dapatkan update eksklusif mengenai tur, rilis lagu terbaru, dan konten
          eksklusif lainnya langsung di inbox Anda.
        </p>

        <div class="sosial">
          <a class="sosial-box" href="https://open.spotify.com/album/4EneY8crCrt61L7siIXtkS?si=UTO1NEtATPqdLRThsBjRjQ" target="_blank" rel="noopener" aria-label="Buka Spotify NOAH"><svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg></a>
          <a class="sosial-box" href="https://music.youtube.com/channel/UCbtHlc5rJCvSb_k0VyUowLA" target="_blank" rel="noopener" aria-label="Buka YouTube Music NOAH"><svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg></a>
          <a class="sosial-box" href="sahabat-noah.php" aria-label="Buka halaman Sahabat NOAH"><svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></a>
          <a class="sosial-box" href="berita-media.php#galeri-foto" aria-label="Buka galeri foto NOAH"><svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg></a>
        </div>

        <!-- Pesan setelah form dikirim (dikirim balik oleh proses/simpan-email.php) -->
        <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'berhasil') { ?>
          <p class="pesan-form">Terima kasih, email kamu sudah terdaftar.</p>
        <?php } ?>
        <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'sudah_ada') { ?>
          <p class="pesan-form gagal">Email ini sudah terdaftar sebelumnya.</p>
        <?php } ?>
        <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'gagal') { ?>
          <p class="pesan-form gagal">Format email salah. Coba lagi.</p>
        <?php } ?>

        <!-- Form buletin: dikirim ke proses/simpan-email.php -->
        <form class="newsletter" action="proses/simpan-email.php" method="post">
          <input type="email" name="email" placeholder="Alamat Email Anda" required>
          <input type="hidden" name="asal_form" value="beranda">
          <button type="submit" class="btn btn-merah">Berlangganan</button>
        </form>
      </div>

      <div class="galeri">
        <div class="galeri-item"><img src="images/feed-1.jpg" alt="Instagram 1"></div>
        <div class="galeri-item"><img src="images/feed-2.jpg" alt="Instagram 2"></div>
        <div class="galeri-item"><img src="images/feed-3.jpg" alt="Instagram 3"></div>
        <div class="galeri-item"><img src="images/feed-4.jpg" alt="Instagram 4"></div>
      </div>
    </div>
  </section>

  <!-- ===== INFORMASI SITUS ===== -->
  <section id="info-situs" class="container section">
    <h2 class="section-title mb">Informasi Situs</h2>
    <div class="grid-2">
      <div id="kebijakan-privasi" class="card card-border-merah">
        <h3 class="berita-judul">Kebijakan Privasi</h3>
        <p class="paragraf-kecil">Email dan isian form disimpan di database hanya untuk keperluan website fan ini dan tidak dibagikan ke pihak lain.</p>
      </div>
      <div id="ketentuan-layanan" class="card card-border-merah">
        <h3 class="berita-judul">Ketentuan Layanan</h3>
        <p class="paragraf-kecil">Konten dibuat sebagai website fan/latihan HTML, CSS, PHP, dan MySQL. Link musik resmi diarahkan ke platform NOAH yang tersedia publik.</p>
      </div>
    </div>
  </section>

<?php include 'bagian/footer.php'; ?>
