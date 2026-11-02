<?php
// =====================================================
// HALAMAN EVENT
// Data dari database: event (konser & arsip), setlist
// Form: "Beritahu Saya"  -> proses/simpan-email.php
//       Pemesanan        -> proses/simpan-pemesanan.php
// =====================================================

include 'koneksi.php';

$hasil_konser  = mysqli_query($koneksi, "SELECT * FROM event WHERE jenis = 'konser' ORDER BY tanggal ASC");
$hasil_arsip   = mysqli_query($koneksi, "SELECT * FROM event WHERE jenis = 'arsip' ORDER BY id ASC");
$hasil_setlist = mysqli_query($koneksi, "SELECT * FROM setlist ORDER BY id ASC");

$bulan_singkat = ['', 'JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'];
$bulan_lengkap = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

// Pesan dari file proses (kalau ada)
$pesan = '';
if (isset($_GET['pesan'])) {
    $pesan = $_GET['pesan'];
}

$judul_halaman = 'Event - NOAH';
$kelas_body    = 'page-event';
$menu_aktif    = 'event';
include 'bagian/header.php';
?>

  <!-- ===== BANNER HIATUS + FORM EMAIL ===== -->
  <section id="banner-hiatus" class="banner-merah">
    <div class="container tengah">
      <h2 class="judul-putih"><svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg> NOAH SEDANG HIATUS SEJAK 2024</h2>
      <p>Daftar untuk jadi yang pertama tahu tentang panggung comeback.</p>

      <?php if ($pesan == 'berhasil') { ?>
        <p class="pesan-form">Terima kasih, email kamu sudah terdaftar.</p>
      <?php } ?>
      <?php if ($pesan == 'sudah_ada') { ?>
        <p class="pesan-form gagal">Email ini sudah terdaftar sebelumnya.</p>
      <?php } ?>
      <?php if ($pesan == 'gagal') { ?>
        <p class="pesan-form gagal">Format email salah. Coba lagi.</p>
      <?php } ?>

      <form class="banner-form" action="proses/simpan-email.php" method="post">
        <input type="email" name="email" placeholder="Alamat Email Anda" required>
        <input type="hidden" name="asal_form" value="event">
        <button type="submit" class="btn btn-outline-putih">Beritahu Saya</button>
      </form>
    </div>
  </section>

  <!-- ===== KONSER MENDATANG (dari tabel event, jenis = konser) ===== -->
  <section id="konser-mendatang" class="container section">
    <h1 class="page-judul tengah mb">Konser Mendatang</h1>
    <p class="page-sub tengah mb">Jadwal ini adalah contoh event komunitas untuk website fan NOAH, bukan pengumuman konser resmi.</p>

    <div class="grid-auto">
      <?php while ($konser = mysqli_fetch_assoc($hasil_konser)) { ?>
        <?php
          $waktu = strtotime($konser['tanggal']);
          $hari  = date('d', $waktu);
          $bulan = $bulan_singkat[date('n', $waktu)] . ' ' . date('Y', $waktu);
        ?>
        <div class="card card-border-merah">
          <div class="card-atas">
            <div>
              <p class="tanggal-besar"><?php echo $hari; ?></p>
              <p class="bulan"><?php echo $bulan; ?></p>
            </div>
            <span class="label-tersedia"><?php echo htmlspecialchars($konser['label'] ?? ''); ?></span>
          </div>
          <div class="card-tengah">
            <h3 class="kota"><?php echo htmlspecialchars($konser['kota'] ?? ''); ?></h3>
            <p class="venue"><?php echo htmlspecialchars($konser['venue'] ?? ''); ?></p>
            <p class="paragraf-kecil"><?php echo htmlspecialchars($konser['deskripsi'] ?? ''); ?></p>
          </div>
          <a class="btn btn-merah lebar" href="#pemesanan-event">Daftar Minat</a>
        </div>
      <?php } ?>
    </div>
  </section>

  <!-- ===== ARSIP EVENT (dari tabel event, jenis = arsip) ===== -->
  <section class="section bg-abu">
    <div class="container">
      <h2 class="section-title judul-garis mb">Arsip Event</h2>
      <div class="grid-auto">
        <?php while ($arsip = mysqli_fetch_assoc($hasil_arsip)) { ?>

          <?php if ($arsip['label'] != '') { ?>
            <!-- Arsip yang punya label: card kuning + label -->
            <div class="card card-border-kuning">
              <span class="label-kuning"><?php echo htmlspecialchars($arsip['label']); ?></span>
          <?php } else { ?>
            <!-- Arsip tanpa label: card merah + tanggal -->
            <div class="card card-border-merah">
              <?php if ($arsip['tanggal'] != '') { ?>
                <?php $waktu = strtotime($arsip['tanggal']); ?>
                <p class="lagu-artis"><?php echo date('d', $waktu) . ' ' . $bulan_singkat[date('n', $waktu)] . ' ' . date('Y', $waktu); ?></p>
              <?php } ?>
          <?php } ?>

              <h3 class="berita-judul"><?php echo htmlspecialchars($arsip['judul']); ?></h3>
              <!-- Kolom kota berisi beberapa kota dipisah koma -->
              <p class="paragraf-kecil"><?php echo htmlspecialchars($arsip['kota'] ?? ''); ?></p>
              <?php if ($arsip['deskripsi'] != '') { ?>
                <p class="paragraf-kecil"><?php echo htmlspecialchars($arsip['deskripsi']); ?></p>
              <?php } ?>
              <div class="tag-list">
                <a href="#arsip-setlist" class="pill">Setlist</a>
                <a href="berita-media.php?kategori=Konser#galeri-foto" class="pill">Galeri</a>
              </div>
            </div>

        <?php } ?>
      </div>
    </div>
  </section>

  <!-- ===== ARSIP SETLIST (dari tabel setlist) ===== -->
  <section id="arsip-setlist" class="container section">
    <h2 class="section-title mb">Arsip Setlist</h2>

    <?php $urutan = 1; ?>
    <?php while ($setlist = mysqli_fetch_assoc($hasil_setlist)) { ?>

      <?php
        // Daftar lagu disimpan satu lagu per baris. explode memecahnya jadi daftar.
        $lagu_utama  = [];
        $lagu_encore = [];
        if ($setlist['lagu_utama'] != '') {
            $lagu_utama = explode("\n", $setlist['lagu_utama']);
        }
        if ($setlist['lagu_encore'] != '') {
            $lagu_encore = explode("\n", $setlist['lagu_encore']);
        }
        // Nomor encore melanjutkan nomor lagu utama
        $nomor_encore = count($lagu_utama) + 1;
      ?>

      <!-- Setlist pertama dibuka otomatis (atribut open) -->
      <details class="setlist" <?php if ($urutan == 1) { echo 'open'; } ?>>
        <summary class="setlist-head">
          <span><?php echo htmlspecialchars($setlist['judul']); ?></span>
          <span class="setlist-chevron"><svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg></span>
        </summary>

        <?php if (count($lagu_utama) == 0) { ?>
          <p class="setlist-body data-kosong">Daftar lagu untuk pertunjukan ini belum tersedia.</p>
        <?php } else { ?>
          <div class="setlist-body grid-2">
            <div>
              <p class="label-merah">Set Utama</p>
              <ol class="tracklist">
                <?php foreach ($lagu_utama as $lagu) { ?>
                  <li><?php echo htmlspecialchars($lagu); ?></li>
                <?php } ?>
              </ol>
            </div>
            <div>
              <?php if (count($lagu_encore) > 0) { ?>
                <p class="label-merah">Encore</p>
                <ul class="tracklist">
                  <?php foreach ($lagu_encore as $lagu) { ?>
                    <li><?php echo $nomor_encore . '. ' . htmlspecialchars($lagu); ?></li>
                    <?php $nomor_encore = $nomor_encore + 1; ?>
                  <?php } ?>
                </ul>
              <?php } ?>
              <div class="detail-show">
                <p class="lagu-artis">Detail Pertunjukan</p>
                <p><strong><?php echo htmlspecialchars($setlist['venue'] ?? ''); ?></strong></p>
                <?php if ($setlist['tanggal'] != '') { ?>
                  <?php $waktu = strtotime($setlist['tanggal']); ?>
                  <p class="paragraf-kecil"><?php echo date('d', $waktu) . ' ' . $bulan_lengkap[date('n', $waktu)] . ' ' . date('Y', $waktu); ?></p>
                <?php } ?>
                <?php if ($setlist['tamu'] != '') { ?>
                  <p class="paragraf-kecil">Tamu: <?php echo htmlspecialchars($setlist['tamu']); ?></p>
                <?php } ?>
              </div>
            </div>
          </div>
        <?php } ?>
      </details>

      <?php $urutan = $urutan + 1; ?>
    <?php } ?>
  </section>

  <!-- ===== PEMESANAN & KERJA SAMA ===== -->
  <section id="pemesanan-event" class="section bg-abu">
    <div class="container grid-2">
      <div>
        <h2 class="section-title mb">Pemesanan &amp; Kerja Sama</h2>

        <?php if ($pesan == 'pesanan_terkirim') { ?>
          <p class="pesan-form">Pertanyaan kamu sudah terkirim. Tim manajemen akan menghubungi lewat email.</p>
        <?php } ?>
        <?php if ($pesan == 'pesanan_gagal') { ?>
          <p class="pesan-form gagal">Nama organisasi, email, telepon, dan tipe acara wajib diisi. Estimasi kehadiran (kalau diisi) minimal 1.</p>
        <?php } ?>

        <!-- Setiap input punya atribut name. Nama ini yang dibaca di proses/simpan-pemesanan.php -->
        <form class="form" action="proses/simpan-pemesanan.php" method="post">
          <div class="form-baris">
            <input type="text" name="nama_organisasi" placeholder="Nama Organisasi" required>
            <input type="email" name="email" placeholder="Email" required>
          </div>
          <div class="form-baris">
            <input type="tel" name="telepon" placeholder="Telepon" required>
            <input type="date" name="tanggal_acara">
          </div>
          <select name="tipe_acara" required>
            <option value="">Tipe Acara</option>
            <option value="Festival Musik">Festival Musik</option>
            <option value="Acara Korporat">Acara Korporat</option>
            <option value="Konser Privat">Konser Privat</option>
          </select>
          <div class="form-baris">
            <input type="number" name="estimasi_kehadiran" placeholder="Estimasi Kehadiran" min="1">
            <input type="text" name="venue_lokasi" placeholder="Venue &amp; Lokasi">
          </div>
          <input type="text" name="rentang_anggaran" placeholder="Rentang Anggaran (Opsional)">
          <textarea name="catatan" rows="4" placeholder="Catatan Tambahan"></textarea>
          <button type="submit" class="btn btn-merah lebar">Kirim Pertanyaan</button>
        </form>
      </div>

      <!-- Info kontak (tetap statis) -->
      <div>
        <div class="card card-border-merah mb">
          <h3 class="berita-judul">Informasi Kontak Manajemen</h3>
          <p class="lagu-artis">Email Pertanyaan</p>
          <p><strong>management@noah-band.com</strong></p>
          <p class="lagu-artis">Telepon / WhatsApp</p>
          <p><strong>+62 811-2222-3333</strong></p>
        </div>
        <div class="card mb">
          <h3 class="berita-judul">Lokasi Venue Rujukan</h3>
          <div class="map-wrap">
            <iframe
              src="https://www.google.com/maps?q=JIExpo%20Convention%20Centre%20and%20Theatre%20Jakarta&output=embed"
              loading="lazy" referrerpolicy="no-referrer-when-downgrade"
              allowfullscreen></iframe>
          </div>
        </div>
        <div class="card tengah">
          <p class="kosong-ikon"><svg class="icon icon-besar" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/></svg></p>
          <h4 class="berita-judul">Technical Rider</h4>
          <p class="paragraf-kecil mb">Kebutuhan Teknis: unduh dokumen audio, tata cahaya, dan tata panggung untuk penyelenggaraan konser NOAH.</p>
          <a class="btn btn-gelap" href="#pemesanan-event">Minta Technical Rider</a>
        </div>
      </div>
    </div>
  </section>

<?php include 'bagian/footer.php'; ?>
