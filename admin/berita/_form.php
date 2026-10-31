<?php
// Form berita. Dipakai oleh tambah.php (form kosong) dan edit.php (form berisi data lama).
// Sebelum include, siapkan: $data (array isi form), $error (array pesan), $tombol (teks tombol).
?>
<?php foreach ($error as $pesan_error) : ?>
  <p class="pesan pesan-error"><?= e($pesan_error) ?></p>
<?php endforeach; ?>

<!-- enctype wajib ada kalau form mengirim file -->
<form class="form-admin" method="post" enctype="multipart/form-data">
  <?= input_token() ?>

  <label for="judul">Judul</label>
  <input id="judul" type="text" name="judul" maxlength="200" required value="<?= e($data['judul']) ?>">

  <label for="kategori">Kategori</label>
  <select id="kategori" name="kategori" required>
    <option value="">Pilih kategori</option>
    <?php foreach (KATEGORI_BERITA as $kategori) : ?>
      <option value="<?= e($kategori) ?>" <?= $data['kategori'] === $kategori ? 'selected' : '' ?>>
        <?= e($kategori) ?>
      </option>
    <?php endforeach; ?>
  </select>

  <label for="ringkasan">Ringkasan</label>
  <textarea id="ringkasan" name="ringkasan" rows="4" required><?= e($data['ringkasan']) ?></textarea>

  <label for="tanggal_terbit">Tanggal terbit</label>
  <input id="tanggal_terbit" type="date" name="tanggal_terbit" required value="<?= e($data['tanggal_terbit']) ?>">

  <label for="gambar">Gambar</label>
  <?php if (!empty($data['gambar'])) : ?>
    <p class="bantuan">Gambar sekarang: <?= e($data['gambar']) ?>. Pilih file baru hanya kalau ingin mengganti.</p>
  <?php endif; ?>
  <input id="gambar" type="file" name="gambar" accept="image/jpeg,image/png,image/webp">
  <p class="bantuan">Opsional. JPG, PNG, atau WEBP, maksimal 2 MB.</p>

  <label class="centang">
    <input type="checkbox" name="jadi_utama" value="1" <?= $data['jadi_utama'] ? 'checked' : '' ?>>
    Jadikan Artikel Utama di halaman Media (berita utama lama otomatis dilepas)
  </label>

  <div class="tombol-baris">
    <button type="submit" class="tombol"><?= e($tombol) ?></button>
    <a class="tombol tombol-abu" href="index.php">Batal</a>
  </div>
</form>
