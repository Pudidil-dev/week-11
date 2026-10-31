# Panduan Admin Panel NOAH

Admin panel ini PHP native, tanpa framework dan tanpa JavaScript. Isinya login, dashboard, dan CRUD lengkap untuk dua tabel: `berita` dan `event`. Dua tabel itu sengaja memakai pola yang sama, jadi untuk tabel lain cukup salin satu folder lalu ganti nama kolomnya.

## Cara menjalankan (XAMPP)

1. Salin seluruh folder proyek ke `C:\xampp\htdocs\noah\`.
2. Nyalakan **Apache** dan **MySQL** di XAMPP Control Panel.
3. Buka `http://localhost/phpmyadmin`, klik tab **Import**, pilih `database/noah_db.sql`, klik **Go**.
   Kalau database `noah_db` sudah ada dari percobaan sebelumnya, hapus dulu (Drop), baru import ulang.
4. Buka `http://localhost/noah/admin/`.
5. Login dengan akun yang tertulis di komentar bagian bawah `database/noah_db.sql`.

Kalau MySQL Anda memakai password, ubah `$db_pass` di [`koneksi.php`](../koneksi.php).

## Isi folder

```
koneksi.php            koneksi ke MySQL, dipakai semua halaman
admin/
  fungsi.php           fungsi bantu: cek login, token form, upload gambar
  _atas.php            kepala halaman + menu (di-include)
  _bawah.php           penutup halaman (di-include)
  admin.css            tampilan
  login.php            form login
  logout.php           keluar
  index.php            dashboard: jumlah data tiap tabel
  berita/
    index.php          R  daftar berita
    tambah.php         C  tambah berita
    edit.php           U  ubah berita
    hapus.php          D  hapus berita
    _form.php          tampilan form, dipakai tambah.php dan edit.php
    _data.php          daftar kategori + pengecekan isi form
  event/
    (susunan file sama persis dengan berita/)
```

File yang namanya diawali garis bawah (`_form.php`, `_atas.php`) bukan halaman yang dibuka langsung. File ini hanya potongan yang di-`require` oleh halaman lain.

## Alur tiap operasi

### Login

```
login.php
  1. Admin mengisi username + password, lalu klik Masuk
  2. PHP:  SELECT ... FROM admin WHERE username = ?
  3. PHP:  password_verify(password yang diketik, hash di database)
  4. Cocok   → simpan admin_id di $_SESSION, pindah ke dashboard
     Salah   → tampilkan "Username atau password salah."
```

Setelah login, PHP mengingat admin lewat `$_SESSION['admin_id']`. Setiap halaman admin memanggil `wajib_login()` di baris paling atas. Fungsi itu memeriksa apakah `admin_id` ada, dan kalau tidak ada, pengunjung dilempar ke halaman login.

### Read (daftar)

`berita/index.php` menjalankan `SELECT * FROM berita ORDER BY tanggal_terbit DESC`, lalu `while` membuat satu baris `<tr>` per berita. Link **Edit** dan **Hapus** membawa `id` berita lewat alamat, misalnya `edit.php?id=3`.

### Create (tambah)

```
tambah.php
  Dibuka biasa (GET)   → tampilkan form kosong
  Form dikirim (POST)  → 1. cek_token()            pastikan form asli dari halaman admin
                          2. baca_input_berita()    ambil $_POST lalu periksa
                          3. ada error?             tampilkan form lagi + pesan error, isian tidak hilang
                          4. upload_gambar()        kalau ada file
                          5. INSERT INTO berita ... VALUES (?, ?, ...)
                          6. pindah ke index.php + pesan "berhasil"
```

### Update (edit)

Sama dengan tambah, dengan dua perbedaan:

- Sebelum form tampil, PHP mengambil data lama dengan `SELECT * FROM berita WHERE id = ?`, lalu memasukkannya ke form.
- Query yang dipakai `UPDATE berita SET ... WHERE id = ?`. Kalau admin tidak memilih gambar baru, kolom `gambar` diisi path gambar lama.

### Delete (hapus)

Link **Hapus** tidak langsung menghapus. `hapus.php` menampilkan halaman konfirmasi dulu, dan baru menjalankan `DELETE FROM berita WHERE id = ?` setelah tombol **Ya, Hapus** ditekan (POST). Dengan cara ini, membuka link secara tidak sengaja tidak akan menghapus data.

File gambar di folder `images/` tidak ikut dihapus, karena satu gambar bisa dipakai beberapa berita. Di data awal, misalnya, `news-1.jpg` dipakai dua berita.

## Pengaman yang dipakai, dan alasannya

| Pengaman | Di mana | Mencegah apa |
|---|---|---|
| `password_hash()` / `password_verify()` | tabel `admin`, `login.php` | Kalau database bocor, password asli tetap tidak terbaca |
| `session_regenerate_id()` | `login.php` | Session lama milik orang lain tidak bisa dipakai setelah login |
| `wajib_login()` | baris atas tiap halaman admin | Orang yang belum login membuka halaman admin langsung lewat alamat |
| Prepared statement (`?` + `bind_param`) | semua query yang memakai input | SQL injection, yaitu input yang sengaja disusun untuk mengubah query |
| `e()` = `htmlspecialchars()` | semua data yang ditampilkan | Teks seperti `<script>` di database dijalankan oleh browser |
| Token CSRF (`input_token()` / `cek_token()`) | semua form | Website lain mengirim form atas nama admin yang sedang login |
| Pilihan kategori dicek dengan `in_array` | `_data.php` | Nilai di luar daftar dikirim dengan mengakali form |
| Cek isi file dengan `mime_content_type` | `upload_gambar()` | File berbahaya yang diganti namanya jadi `.jpg` |

Semua pengaman di atas sudah diuji: login salah ditolak, akses tanpa login dilempar ke halaman login, form tanpa token ditolak, kategori palsu ditolak, file teks bernama `.jpg` ditolak, dan judul berisi `<b>` tampil sebagai teks biasa.

## Menyalin pola ke tabel lain (contoh: `produk`)

1. Salin folder `admin/event/` menjadi `admin/produk/`.
2. Di `_data.php`:
   - Ganti `JENIS_EVENT` menjadi daftar kategori produk: `'Kaos', 'Hoodie', 'Jaket', 'Aksesoris', 'CD & Vinyl'`.
   - Ganti nama fungsi `baca_input_event()` menjadi `baca_input_produk()`, lalu sesuaikan kolomnya: `nama_produk`, `kategori`, `deskripsi`, `harga`, `label`, `stok`.
   - Periksa `harga` dengan `ctype_digit()`, supaya yang masuk hanya angka.
3. Di `_form.php`, ganti input sesuai kolom. Untuk harga pakai `<input type="number" min="0">`. Untuk gambar, salin bagian upload dari `admin/berita/_form.php`, termasuk `enctype="multipart/form-data"` di tag `<form>`.
4. Di `tambah.php` dan `edit.php`, sesuaikan query `INSERT`/`UPDATE` dan huruf tipe di `bind_param`:
   `s` untuk teks, `i` untuk angka bulat. Contoh: `nama, kategori, deskripsi, harga, gambar, label, stok` → `'sssissi'`.
5. Di `index.php` dan `hapus.php`, ganti `event` menjadi `produk` dan `judul` menjadi `nama_produk`.
6. Tambahkan link **Produk** di menu `_atas.php`.

Tabel yang diisi pengunjung (`pemesanan_event`, `pelanggan_email`) cukup dibuatkan `index.php` dan `hapus.php`. Untuk `kiriman_sahabat`, tambahkan tombol di daftar yang menjalankan `UPDATE kiriman_sahabat SET status = 'tampil' WHERE id = ?` lewat form POST, dengan pola yang sama seperti `hapus.php`.

## Hal yang perlu diketahui

- **Hanya satu berita yang bisa jadi Artikel Utama.** Mencentang "Jadikan Artikel Utama" otomatis melepas tanda dari berita lain. Kebalikannya juga berlaku: kalau Anda mengedit berita utama dan menghapus centangnya, tidak ada berita utama sama sekali, sehingga section Artikel Utama di halaman Media kosong.
- **Belum ada fitur tambah admin atau ganti password dari panel.** Kedua hal itu dilakukan lewat phpMyAdmin dengan hash baru (caranya tertulis di komentar `noah_db.sql`).
- **Halaman publik (`index.html` dan lainnya) belum membaca database.** Admin panel sudah bisa mengubah isi tabel, tetapi website masih menampilkan HTML statis. Langkah berikutnya adalah mengubah `.html` menjadi `.php`, dengan contoh di `database/RANCANGAN-DATABASE.md` bagian Tahap 5.
- **Folder `images/` harus bisa ditulisi** oleh Apache. Di XAMPP Windows, folder ini biasanya sudah bisa ditulisi.
