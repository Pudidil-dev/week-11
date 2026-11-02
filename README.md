# Website NOAH (PHP + MySQL)

Website fan band NOAH. Halaman awalnya HTML statis, sekarang memakai PHP native dan MySQL supaya isinya bisa diubah dari database. Kode sengaja ditulis gaya pemula: tanpa framework, tanpa class, tanpa JavaScript.

## Kebutuhan

- [Laragon](https://laragon.org) (disarankan) atau XAMPP
- PHP 8.x dengan ekstensi `mysqli` dan `fileinfo` (sudah aktif bawaan Laragon/XAMPP)
- MySQL 8.x atau MariaDB 10.x

## Setup di Laragon

1. Salin folder proyek ke `C:\laragon\www\noah`.
2. Buka Laragon, klik **Start All**. Tunggu sampai Apache dan MySQL menyala.
3. Buka terminal di folder `C:\laragon\www\noah`, lalu import database.

   **Terminal Laragon / Command Prompt:**

   ```bash
   mysql -u root -e "DROP DATABASE IF EXISTS noah_db"
   mysql -u root --default-character-set=utf8mb4 -e "source database/noah_db.sql"
   ```

   **PowerShell** (path program wajib diawali `&`):

   ```powershell
   & "C:/laragon/bin/mysql/mysql-8.4.3-winx64/bin/mysql.exe" -u root -e "DROP DATABASE IF EXISTS noah_db"
   & "C:/laragon/bin/mysql/mysql-8.4.3-winx64/bin/mysql.exe" -u root --default-character-set=utf8mb4 -e "source database/noah_db.sql"
   ```

   Sesuaikan `mysql-8.4.3-winx64` dengan versi MySQL di folder `C:\laragon\bin\mysql\`.

   > `DROP DATABASE` menghapus isi `noah_db` yang lama, termasuk data yang masuk lewat form. Lewati baris pertama kalau database belum pernah dibuat.

4. Buka website:
   - `http://noah.test` (Laragon membuat alamat ini otomatis; klik **Reload** di Laragon kalau belum muncul)
   - atau `http://localhost/noah`
5. Buka admin panel di `/admin/`. Akun awal tertulis di komentar bagian bawah `database/noah_db.sql`. **Ganti password setelah login pertama.**

### Cek hasil import

```bash
mysql -u root noah_db -e "SHOW TABLES"
```

Harus muncul 13 tabel.

## Setup di XAMPP

1. Salin folder proyek ke `C:\xampp\htdocs\noah`.
2. Nyalakan **Apache** dan **MySQL** di XAMPP Control Panel.
3. Buka `http://localhost/phpmyadmin` > tab **Import** > pilih `database/noah_db.sql` > **Go**.
4. Buka `http://localhost/noah`.

Jangan menyalakan MySQL Laragon dan MySQL XAMPP bersamaan, karena keduanya memakai port 3306.

## Koneksi database

Pengaturan ada di `koneksi.php`. Nilai bawaan cocok untuk Laragon dan XAMPP:

| Variabel | Nilai |
|---|---|
| `$db_host` | `localhost` |
| `$db_user` | `root` |
| `$db_pass` | kosong |
| `$db_nama` | `noah_db` |

Ubah `$db_pass` kalau MySQL Anda memakai password.

## Struktur folder

```
koneksi.php          sambungan ke database (dipakai semua halaman)
bagian/              header.php (navbar) dan footer.php
index.php            Beranda
cerita-band.php      Cerita Band
diskografi.php       Diskografi
tur-event.php        Event
berita-media.php     Media
sahabat-noah.php     Sahabat NOAH
toko.php             Toko
proses/              penyimpan form: simpan-email, simpan-pemesanan, simpan-kiriman
admin/               admin panel (login, berita, event, kiriman, pemesanan, pelanggan)
database/            noah_db.sql
images/              gambar, termasuk foto hasil upload
style.css            tampilan website
```

## Database: 13 tabel, tanpa relasi

Setiap tabel berdiri sendiri (tanpa foreign key, tanpa JOIN). Satu tabel = satu section atau satu form.

| Tabel | Dipakai di | Cara mengisi |
|---|---|---|
| `event` | Beranda & Event (Konser Mendatang), Event (Arsip Event), Sahabat NOAH (Event Komunitas) | Admin > Event |
| `setlist` | Event > Arsip Setlist | phpMyAdmin |
| `album` | Diskografi > Grid Album | phpMyAdmin |
| `single` | Diskografi > Tabel Single | phpMyAdmin |
| `berita` | Beranda > Berita Terbaru, Media > Artikel Utama, Berita, Pencarian | Admin > Berita |
| `galeri` | Media > Di Balik Layar, Galeri Foto | phpMyAdmin |
| `produk` | Toko > Grid Produk, Edisi Terbatas | phpMyAdmin |
| `kiriman_sahabat` | Sahabat NOAH > Cerita Sahabat, Karya Sahabat | Form Kirim Cerita, disetujui di Admin > Kiriman |
| `pemesanan_event` | Admin > Pemesanan | Form Pemesanan (halaman Event) |
| `pelanggan_email` | Admin > Pelanggan | Form Berlangganan (Beranda) dan Beritahu Saya (Event) |
| `anggota` | Cerita Band > Kenali Anggota | phpMyAdmin |
| `komunitas_regional` | Sahabat NOAH > Komunitas Regional dan angka Anggota/Wilayah di hero | phpMyAdmin |
| `admin` | Login admin panel | phpMyAdmin (password berupa hash) |

### Kolom yang tergantung jenis data

Dua tabel menyimpan beberapa section sekaligus. Kolom yang tidak dipakai di suatu jenis dibiarkan kosong (NULL).

**`event` (kolom `jenis` menentukan section):**

| Kolom | konser | arsip | komunitas |
|---|---|---|---|
| `judul` | hanya di admin (isi nama kota) | ya | ya |
| `tanggal` | wajib, hanya yang belum lewat yang tampil | boleh kosong | wajib |
| `kota` | ya | ya (beberapa kota, dipisah koma) | ya |
| `venue` | ya | – | – |
| `deskripsi` | halaman Event saja | ya | ya |
| `label` | ya (contoh: Tersedia) | ya (contoh: TUR DUNIA) | ya (contoh: Gathnas 2024) |

**`galeri` (kolom `bagian` menentukan section):**

| Kolom | di_balik_layar | galeri_foto |
|---|---|---|
| `gambar` | ya | ya |
| `judul`, `keterangan` | ya | – |
| `jenis` (Foto/Video) | ya, sebagai label | – (selalu Foto) |
| `kategori` | – | ya, untuk tombol filter |

### Kolom khusus

- `kiriman_sahabat.status`: kiriman baru berstatus `menunggu` dan belum tampil. Klik **Tampilkan** di Admin > Kiriman.
- `kiriman_sahabat.kategori`: Cerita Penggemar dan Cover Lagu tampil di Cerita Sahabat; Karya Penggemar tampil di Karya Sahabat (wajib ada foto).
- `album.lagu_unggulan`, `setlist.lagu_utama`, `setlist.lagu_encore`: satu lagu per baris (tekan Enter di antara lagu).
- `berita.jadi_utama`: hanya satu berita yang bernilai 1 (Artikel Utama).
- `produk.produk_baru` dan `produk.edisi_terbatas`: 1 = tampilkan badge Baru / Terbatas. `stok` hanya dipakai untuk edisi terbatas; stok 0 tampil "Habis".

### Isi yang sengaja tetap statis

Tidak disimpan di database karena jarang berubah: hero beranda, Album Pilihan (beserta durasi lagu), profil band, timeline sejarah, penghargaan, pengaruh & warisan, Materi Pers, kontak manajemen, peta, banner dan info pengiriman toko, angka "100+ Event/Tahun", serta feed foto di beranda.

## Admin panel

| Menu | Bisa apa |
|---|---|
| Berita | tambah, lihat, edit, hapus (dengan upload gambar) |
| Event | tambah, lihat, edit, hapus |
| Kiriman | lihat, tampilkan/sembunyikan di website, hapus |
| Pemesanan | lihat, hapus |
| Pelanggan | lihat, hapus |

Tabel lain diubah lewat phpMyAdmin (Laragon: klik **Database** di jendela Laragon).

## Masalah yang sering muncul

| Pesan | Penyebab | Solusi |
|---|---|---|
| `ERROR 2003 Can't connect to MySQL server on 'localhost:3306'` | MySQL belum menyala | Klik **Start All** di Laragon, tunggu beberapa detik, lalu coba lagi |
| `Unexpected token '-u'` di PowerShell | Path program dalam tanda kutip tanpa `&` | Awali perintah dengan `&` (lihat contoh di atas) |
| `Table 'event' already exists` | Database sudah pernah di-import | Jalankan `DROP DATABASE IF EXISTS noah_db` dulu |
| `Database belum bisa dihubungi` di website | Isi `koneksi.php` tidak cocok atau database belum di-import | Cek MySQL menyala dan import sudah berhasil |
| Konser Mendatang kosong | Semua konser tanggalnya sudah lewat | Tambah konser baru lewat Admin > Event |
| Upload foto gagal | Folder `images/` tidak bisa ditulis, atau file bukan JPG/PNG/WEBP maksimal 2 MB | Cek izin folder dan jenis file |
