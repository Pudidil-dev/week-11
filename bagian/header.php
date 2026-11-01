<?php
// BAGIAN ATAS SEMUA HALAMAN: <head> + navbar.
// Sebelum file ini di-include, halaman harus mengisi 3 variabel:
//   $judul_halaman  -> teks di tab browser
//   $kelas_body     -> class untuk <body>, contoh: page-toko
//   $menu_aktif     -> menu yang diberi warna aktif, contoh: toko
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $judul_halaman; ?></title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="<?php echo $kelas_body; ?>">

  <!-- ===== NAVBAR ===== -->
  <header class="navbar">
    <div class="container navbar-isi">
      <a href="index.php" class="logo">NOAH</a>

      <nav class="menu">
        <a href="cerita-band.php"  <?php if ($menu_aktif == 'cerita')     { echo 'class="aktif"'; } ?>>Cerita Band</a>
        <a href="diskografi.php"   <?php if ($menu_aktif == 'diskografi') { echo 'class="aktif"'; } ?>>Diskografi</a>
        <a href="tur-event.php"    <?php if ($menu_aktif == 'event')      { echo 'class="aktif"'; } ?>>Event</a>
        <a href="berita-media.php" <?php if ($menu_aktif == 'media')      { echo 'class="aktif"'; } ?>>Media</a>
        <a href="sahabat-noah.php" <?php if ($menu_aktif == 'sahabat')    { echo 'class="aktif"'; } ?>>Sahabat NOAH</a>
        <a href="toko.php"         <?php if ($menu_aktif == 'toko')       { echo 'class="aktif"'; } ?>>Toko</a>
      </nav>

      <details class="mobile-menu">
        <summary>Menu</summary>
        <div class="mobile-menu-panel">
          <a href="cerita-band.php">Cerita Band</a>
          <a href="diskografi.php">Diskografi</a>
          <a href="tur-event.php">Event</a>
          <a href="berita-media.php">Media</a>
          <a href="sahabat-noah.php">Sahabat NOAH</a>
          <a href="toko.php">Toko</a>
        </div>
      </details>

      <div class="navbar-kanan">
        <details class="search-box">
          <summary class="search-icon" aria-label="Buka pencarian"><svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></summary>
          <!-- Pencarian dikirim ke halaman Media, lalu dicari di tabel berita -->
          <form class="search-panel" action="berita-media.php" method="get">
            <label for="kotak-cari">Cari berita NOAH</label>
            <div class="search-input-baris">
              <input id="kotak-cari" type="search" name="q" placeholder="Judul berita...">
              <button type="submit" class="btn btn-merah">Cari</button>
            </div>
            <div class="search-saran">
              <a href="diskografi.php">Diskografi</a>
              <a href="tur-event.php">Event</a>
              <a href="berita-media.php">Media</a>
            </div>
          </form>
        </details>
        <span class="lang">ID</span>
      </div>
    </div>
  </header>
