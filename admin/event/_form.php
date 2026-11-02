<?php
// Form event. Dipakai oleh tambah.php dan edit.php.
// Sebelum include, siapkan: $data, $error, $tombol.
?>
<?php foreach ($error as $pesan_error) : ?>
  <p class="pesan pesan-error"><?= e($pesan_error) ?></p>
<?php endforeach; ?>

<form class="form-admin" method="post">
  <?= input_token() ?>

  <label for="jenis">Tampil di section</label>
  <select id="jenis" name="jenis" required>
    <option value="">Pilih section</option>
    <?php foreach (JENIS_EVENT as $nilai => $teks) : ?>
      <option value="<?= e($nilai) ?>" <?= $data['jenis'] === $nilai ? 'selected' : '' ?>><?= e($teks) ?></option>
    <?php endforeach; ?>
  </select>

  <label for="judul">Judul</label>
  <input id="judul" type="text" name="judul" maxlength="150" required value="<?= e($data['judul']) ?>">
  <p class="bantuan">Untuk Konser Mendatang, isi dengan nama kota (contoh: Jakarta). Card konser menampilkan kolom Kota.</p>

  <label for="tanggal">Tanggal</label>
  <input id="tanggal" type="date" name="tanggal" value="<?= e($data['tanggal'] ?? '') ?>">
  <p class="bantuan">Wajib untuk Konser Mendatang dan Event Komunitas. Boleh kosong untuk arsip yang hanya diketahui tahunnya.</p>

  <label for="kota">Kota</label>
  <input id="kota" type="text" name="kota" maxlength="255" value="<?= e($data['kota'] ?? '') ?>">
  <p class="bantuan">Untuk Arsip Event, isi daftar kota yang dikunjungi, dipisah koma.</p>

  <label for="venue">Venue</label>
  <input id="venue" type="text" name="venue" maxlength="150" value="<?= e($data['venue'] ?? '') ?>">
  <p class="bantuan">Hanya tampil untuk Konser Mendatang. Kosongkan untuk Arsip dan Event Komunitas.</p>

  <label for="deskripsi">Deskripsi</label>
  <textarea id="deskripsi" name="deskripsi" rows="3"><?= e($data['deskripsi'] ?? '') ?></textarea>
  <p class="bantuan">Boleh kosong untuk Arsip Event.</p>

  <label for="label">Label (badge kecil di card)</label>
  <input id="label" type="text" name="label" maxlength="50" value="<?= e($data['label'] ?? '') ?>">
  <p class="bantuan">Contoh: Tersedia, TUR DUNIA, Gathnas 2024. Boleh kosong.</p>

  <div class="tombol-baris">
    <button type="submit" class="tombol"><?= e($tombol) ?></button>
    <a class="tombol tombol-abu" href="index.php">Batal</a>
  </div>
</form>
