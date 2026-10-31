# Rancangan Database Website NOAH

File SQL siap pakai: [`noah_db.sql`](noah_db.sql). Sudah dicoba import di MySQL 8.4 dan jalan tanpa error.

Diagram di FigJam: https://www.figma.com/board/elVNs7DODomUgnizLkYwAx (isinya ERD 9 tabel + peta halaman ke tabel).

Hasil akhirnya **9 tabel, nol relasi**. Tiap tabel berdiri sendiri, jadi setiap halaman cukup memanggil `SELECT ... FROM satu_tabel` tanpa JOIN.

---

## Yang dianalisis

| File | Dianalisis? | Catatan |
|---|---|---|
| `index.html` | Ya | Beranda |
| `cerita-band.html` | Ya | |
| `diskografi.html` | Ya | |
| `tur-event.html` | Ya | |
| `berita-media.html` | Ya | |
| `sahabat-noah.html` | Ya | |
| `toko.html` | Ya | |
| `style.css` | Ya, sekilas | Hanya tampilan. Tidak ada data yang disimpan di CSS |
| `tes.html` | Ya | Cuma halaman berisi tombol ke beranda. Tidak butuh database |
| Folder `stitch_*` | Tidak | Ini mockup desain dari Google Stitch (pakai Tailwind CDN), bukan website yang dipakai |

**JavaScript: tidak ada.** Saya cek semua file `.html` di root dan tidak menemukan satu pun `<script>`. `todo.md` juga menulis "project tetap HTML/CSS native tanpa JavaScript". Jadi semua "fungsi interaktif" di website ini berasal dari HTML biasa: link, `<details>`, dan `<form>`.

Ada **5 jenis form** di website:

1. Pencarian (ada di navbar semua halaman), `action="berita-media.html"`, `name="q"`
2. Berlangganan email di beranda
3. "Beritahu Saya" di banner hiatus halaman Event
4. Pemesanan & Kerja Sama di halaman Event
5. Kirim Cerita atau Karyamu di halaman Sahabat NOAH

Form nomor 2 sampai 5 **belum punya** `action`, `method`, dan atribut `name` di input-nya. Sekarang tombol Kirim tidak mengirim ke mana pun. Ini yang paling wajib dibenahi waktu pindah ke PHP.

---

## Tahap 1: Inventarisasi seluruh section

Navbar dan footer isinya sama persis di ketujuh halaman (sudah saya bandingkan dengan `diff`), jadi saya tulis sekali saja.

| Halaman | Section | Isi yang ditemukan | Database | Alasan |
|---|---|---|---|---|
| Semua | Navbar + menu mobile `<details>` | Logo, 6 link menu, form cari | Tidak | Link menu jarang berubah. Cukup jadi `header.php` lalu di-`include` |
| Semua | Footer | 4 kolom link, copyright | Tidak | Sama, jadi `footer.php` |
| Semua | Form pencarian | `<input name="q">` menuju berita-media | Pakai tabel `berita` | Tidak perlu tabel sendiri. PHP baca `$_GET['q']` lalu cari di judul berita |
| Beranda | Hero | Judul, subjudul, 2 tombol | Tidak | Teks tetap |
| Beranda | Album Pilihan (`#player`) | Cover, judul album, 3 lagu + durasi, 3 link musik | Opsional | Hanya 1 album dan jarang ganti. Durasi lagu tidak ada di tempat lain |
| Beranda | Konser Mendatang | 3 card: tanggal, kota, venue, label "Tersedia" | **Wajib** → `event` | Data yang sama dengan halaman Event. Kalau statis, harus edit di 2 tempat |
| Beranda | Evolusi Musik (profil) | Foto + 1 paragraf | Tidak | Teks tetap |
| Beranda | Berita Terbaru | 3 card: gambar, kategori, tanggal, judul, ringkasan | **Wajib** → `berita` | Berita terus bertambah |
| Beranda | Bergabung dengan Komunitas | Teks, 4 ikon sosial, **form email**, 4 foto feed | **Wajib** (form) → `pelanggan_email` | Email yang dikirim pengunjung harus disimpan |
| Beranda | Feed 4 foto | `feed-1.jpg` s/d `feed-4.jpg` | Opsional | Bisa ambil 4 foto terbaru dari `galeri`, tapi statis juga cukup |
| Beranda | Informasi Situs | Kebijakan Privasi, Ketentuan Layanan | Tidak | Teks legal tetap |
| Cerita Band | Page header + breadcrumb | Judul, subjudul | Tidak | |
| Cerita Band | Narasi pembuka | 2 paragraf + foto | Tidak | |
| Cerita Band | Tonggak Sejarah | 5 item timeline (tahun, judul, deskripsi) | Opsional | Sejarah tidak bertambah tiap minggu. Lima item ditulis langsung di HTML lebih gampang |
| Cerita Band | Kenali Anggota | 3 card: foto, nama, posisi, deskripsi | Opsional | Hanya 3 orang dan sangat jarang berubah |
| Cerita Band | Penghargaan | 6 angka statistik | Tidak | Angka tetap |
| Cerita Band | Pengaruh & Warisan | 5 tag band, 1 paragraf | Tidak | |
| Diskografi | Judul + tombol filter | Filter Era (3) dan Tipe (4), masih `<button>` tanpa fungsi | Pakai `diskografi` | Filter butuh kolom `tipe` dan `tahun` |
| Diskografi | Grid Album | 6 card: cover, judul, tahun, jumlah lagu, tipe, 3 lagu | **Wajib** → `diskografi` | Katalog yang bisa bertambah |
| Diskografi | Tabel Single | 5 baris: judul, tahun, album | **Wajib** → `diskografi` | Digabung dengan album, dibedakan `tipe = 'Single'` |
| Event | Banner hiatus | Teks + **form email** "Beritahu Saya" | **Wajib** (form) → `pelanggan_email` | Fungsinya sama dengan form di beranda |
| Event | Konser Mendatang | 3 card: tanggal + tahun, kota, venue, deskripsi, label | **Wajib** → `event` | |
| Event | Arsip Event | 3 card: judul, label, daftar kota | **Wajib** → `event` | Bentuknya mirip konser, jadi masuk tabel yang sama |
| Event | Arsip Setlist | 3 dropdown `<details>`. Baru yang pertama yang berisi lagu, venue, tanggal, tamu | **Wajib** → `setlist` | Isinya daftar lagu, beda dengan card event |
| Event | Form Pemesanan & Kerja Sama | 9 input: organisasi, email, telepon, tanggal, tipe acara, estimasi, venue, anggaran, catatan | **Wajib** → `pemesanan_event` | Form yang datanya harus tersimpan |
| Event | Kontak Manajemen | Email + nomor WA | Tidak | Teks tetap |
| Event | Peta | `<iframe>` Google Maps | Tidak | |
| Event | Technical Rider | Tombol yang menuju `#pemesanan-event` | Tidak, **Perlu Konfirmasi** | Belum ada file untuk diunduh. Kalau nanti ada PDF, cukup taruh di folder dan link langsung |
| Media | Artikel Utama | Kategori, judul, ringkasan, tanggal | **Wajib** → `berita` | Satu berita yang ditandai `jadi_utama = 1` |
| Media | Berita Terbaru + pagination | 3 card + tombol halaman 1, 2, 3 | **Wajib** → `berita` | Pagination butuh data dari database (`LIMIT` / `OFFSET`) |
| Media | Di Balik Layar | 3 card: gambar, label Video/Foto, judul, keterangan | **Wajib** → `galeri` | |
| Media | Galeri Foto | 8 foto + 5 tombol filter | **Wajib** → `galeri` | Filter butuh kolom `kategori` |
| Media | Materi Pers | 3 baris: nama file, format, ukuran | Opsional | Hanya 3 file, tombolnya juga belum menuju file sungguhan |
| Sahabat NOAH | Hero + statistik | 50K+ anggota, 32 wilayah, 100+ event | Tidak | Angka bulat yang ditulis manual |
| Sahabat NOAH | Cerita Sahabat | 3 card: foto, nama, kota, kutipan | **Wajib** → `kiriman_sahabat` | Isinya adalah kiriman dari form di bawahnya |
| Sahabat NOAH | Karya Sahabat | 8 gambar | **Wajib** → `kiriman_sahabat` | Sama, kiriman kategori "Karya Penggemar" |
| Sahabat NOAH | Komunitas Regional | 6 card: kota, jumlah anggota, ketua | Opsional | Bisa jadi tabel kalau angka anggota sering diperbarui. Untuk tugas ini statis sudah cukup |
| Sahabat NOAH | Event Komunitas | 2 card: tanggal, label, judul, deskripsi | **Wajib** → `event` | Masuk tabel `event` dengan `jenis = 'komunitas'` |
| Sahabat NOAH | Form Kirim Cerita | nama, email, kategori, pesan | **Wajib** → `kiriman_sahabat` | |
| Toko | Banner promo | Judul, teks, gambar | Tidak | |
| Toko | Info pengiriman | 3 teks keunggulan | Tidak | |
| Toko | Sidebar filter | Kategori + jumlah, slider harga, dropdown urutkan | Pakai `produk` | Jumlah per kategori dihitung dengan `COUNT`, tidak perlu tabel kategori |
| Toko | Grid Produk | 6 card: gambar, badge, nama, deskripsi, harga | **Wajib** → `produk` | |
| Toko | Edisi Terbatas | 2 card: label, sisa stok, nama, deskripsi, harga | **Wajib** → `produk` | Produk biasa yang `label = 'Terbatas'` |

### Hal yang perlu konfirmasi

- **Keranjang belanja.** Tombol "Tambah ke Keranjang" dan "Beli Sekarang" hanya menuju `#produk`. Tidak ada halaman keranjang, checkout, atau pembayaran. Saya **tidak** membuat tabel pesanan. Kalau dosen minta fitur beli, itu tugas tambahan.
- **"Baca Selengkapnya" di berita** hanya loncat ke card yang sama. Tidak ada halaman detail, jadi saya tidak membuat kolom `isi` lengkap. Kalau nanti dibuat `detail-berita.php`, tambahkan satu kolom `isi TEXT`.
- **Upload gambar** di form Sahabat NOAH tidak ada. Form hanya punya teks. Kolom `gambar` di `kiriman_sahabat` diisi manual lewat phpMyAdmin.
- **Video** di Di Balik Layar hanya berupa gambar. Tidak ada link video di HTML.
- **Kategori foto** di Galeri Foto (Peterpan Era, NOAH Era, Konser, Studio) tidak tertulis per foto, jadi di data awal saya biarkan kosong.
- **Urutkan "Populer"** di toko butuh data penjualan yang tidak ada. Pilihan ini tidak bisa jalan tanpa fitur pesanan.
- **Tahun event "Lokakarya Musik & Kopi"** hanya tertulis "12 November". Di data awal saya isi 2024 sebagai perkiraan.

---

## Tahap 2: Daftar tabel

| No | Nama Tabel | Sumber | Fungsi | Alasan |
|---|---|---|---|---|
| 1 | `event` | Beranda: Konser Mendatang. Event: Konser Mendatang, Arsip Event. Sahabat: Event Komunitas | Semua jadwal dan arsip acara | Empat section ini punya bentuk card yang sama (tanggal, judul, tempat, deskripsi, badge). Satu tabel + kolom `jenis` lebih mudah daripada tiga tabel |
| 2 | `setlist` | Event: Arsip Setlist | Daftar lagu tiap pertunjukan | Isinya daftar lagu + encore, tidak cocok dimasukkan ke card event |
| 3 | `diskografi` | Diskografi: Grid Album + Tabel Single | Album dan single | Single juga rilisan. Tombol filter "Tipe" di HTML sudah menaruh Single sejajar dengan Studio dan Kompilasi |
| 4 | `berita` | Beranda: Berita Terbaru. Media: Artikel Utama, Berita Terbaru. Form pencarian | Artikel berita | Paling sering bertambah |
| 5 | `galeri` | Media: Di Balik Layar, Galeri Foto | Foto dan video | |
| 6 | `produk` | Toko: Grid Produk, Edisi Terbatas, sidebar | Barang yang dijual | |
| 7 | `kiriman_sahabat` | Sahabat: Form Kirim Cerita, Cerita Sahabat, Karya Sahabat | Kiriman penggemar | Form menyimpan ke sini, dua section membaca dari sini |
| 8 | `pemesanan_event` | Event: Form Pemesanan | Permintaan booking | Data dari form |
| 9 | `pelanggan_email` | Beranda: form Berlangganan. Event: form Beritahu Saya | Daftar email | Dua form yang isinya sama-sama satu email |

### Kenapa tidak ada relasi?

Saya sempat mempertimbangkan dua relasi:

- `setlist` menyambung ke `event`. Tapi judul setlist di HTML ("The Great Journey of NOAH - Jakarta 2023", "Asia Song Festival - Busan") tidak ada di Arsip Event. Memaksakan relasi berarti mengarang event baru.
- Single menyambung ke album. Tapi single "Tak Bisakah" album asalnya "OST Alexandria", yang tidak ada di grid album. Jadi `album_asal` cukup berupa teks nama album, bukan ID.

Hasilnya, tiap tabel bisa dibaca sendirian. Di diagram Figma, garis panah hanya menghubungkan **section halaman ke tabel**, bukan tabel ke tabel.

---

## Tahap 3: Detail tiap tabel

### Tabel `event`

Menyimpan semua acara: konser yang akan datang, arsip konser lama, dan event komunitas.

| Field | Tipe | Keterangan | Contoh |
|---|---|---|---|
| id | INT | Nomor unik, otomatis | 1 |
| jenis | VARCHAR(20) | Menentukan section tempat tampil: `konser`, `arsip`, atau `komunitas` | konser |
| judul | VARCHAR(150) | Judul card. Untuk konser, isinya nama kota | Jakarta |
| tanggal | DATE | Tanggal acara. Boleh kosong untuk arsip yang hanya tahu tahunnya | 2026-08-24 |
| kota | VARCHAR(100) | Kota acara | Jakarta |
| venue | VARCHAR(150) | Nama tempat | JIExpo Kemayoran |
| deskripsi | TEXT | Teks kecil di card. Untuk arsip, isinya daftar kota | Malam karya NOAH dengan... |
| label | VARCHAR(50) | Badge kecil di card | Tersedia |

- Banyak baris (sekarang 8).
- CRUD: Read di 3 halaman. Create, Update, Delete lewat phpMyAdmin.
- PHP: `SELECT * FROM event WHERE jenis = 'konser' ORDER BY tanggal LIMIT 3` untuk beranda. Di halaman Event, ganti `LIMIT 3` jadi tanpa batas, dan jalankan query kedua dengan `jenis = 'arsip'`.

**Catatan:** Ketiga konser di data awal bertanggal Agustus sampai Oktober 2026, dan hari ini sudah lewat. Kalau PHP memakai `WHERE tanggal >= CURDATE()`, section Konser Mendatang langsung kosong. Pilih salah satu: tambah data konser baru, atau jangan pakai filter tanggal dulu.

### Tabel `setlist`

Menyimpan daftar lagu yang dibawakan di satu pertunjukan.

| Field | Tipe | Keterangan | Contoh |
|---|---|---|---|
| id | INT | Nomor unik | 1 |
| judul | VARCHAR(150) | Teks di `<summary>` dropdown | The Great Journey of NOAH - Jakarta (2023) |
| venue | VARCHAR(150) | Tempat | Ancol Beach City International Stadium |
| tanggal | DATE | Tanggal tampil | 2023-12-03 |
| tamu | VARCHAR(150) | Bintang tamu | Reza (Drummer Asli) |
| lagu_utama | TEXT | Satu judul lagu per baris | Topeng (Enter) Separuh Aku (Enter) ... |
| lagu_encore | TEXT | Satu judul lagu per baris | Walau Habis Terang |

- Banyak baris (3).
- CRUD: Read. Sisanya lewat phpMyAdmin.
- PHP: ambil semua baris, lalu pecah daftar lagu jadi `<li>`:
  ```php
  foreach (explode("\n", $baris['lagu_utama']) as $lagu) {
      echo '<li>' . htmlspecialchars($lagu) . '</li>';
  }
  ```

Kenapa lagu tidak dibuat tabel sendiri? Karena lagu di setlist hanya ditampilkan berurutan, tidak pernah dicari, diedit, atau dihitung satu per satu. Teks per baris sudah cukup dan tidak butuh JOIN.

### Tabel `diskografi`

Menyimpan album dan single dalam satu tempat.

| Field | Tipe | Keterangan | Contoh |
|---|---|---|---|
| id | INT | Nomor unik | 1 |
| judul | VARCHAR(150) | Judul album atau lagu single | Keterkaitan Keterikatan |
| tahun | YEAR | Tahun rilis. Dipakai juga untuk filter Era (Peterpan sampai 2008, NOAH mulai 2012) | 2019 |
| tipe | VARCHAR(20) | `Studio`, `Kompilasi`, `Lagu Tema`, atau `Single` | Studio |
| jumlah_lagu | INT | Hanya untuk album | 8 |
| lagu_unggulan | TEXT | 3 lagu yang tampil di card, satu per baris. Hanya album | Wanitaku (Enter) Kupeluk Hatimu ... |
| gambar_cover | VARCHAR(255) | Path cover. Hanya album | images/album-1.jpg |
| album_asal | VARCHAR(150) | Nama album untuk kolom "Album" di tabel single. Hanya single | Seperti Seharusnya |

- Banyak baris (6 album + 5 single).
- CRUD: Read.
- PHP: grid album pakai `WHERE tipe <> 'Single'`, tabel single pakai `WHERE tipe = 'Single'`. Tombol filter Era tidak butuh kolom baru: `WHERE tahun <= 2008` untuk Peterpan, `WHERE tahun >= 2012` untuk NOAH.

Kolom yang kosong untuk single (`jumlah_lagu`, `gambar_cover`) memang sengaja NULL. Ini harga yang wajar untuk menghindari tabel kedua.

### Tabel `berita`

| Field | Tipe | Keterangan | Contoh |
|---|---|---|---|
| id | INT | Nomor unik | 1 |
| judul | VARCHAR(200) | Judul berita | Kolaborasi Spesial NOAH dengan Orkestra Nasional |
| kategori | VARCHAR(50) | Teks di pill | Musik |
| ringkasan | TEXT | Paragraf pendek di card | Sebuah mahakarya baru... |
| gambar | VARCHAR(255) | Path gambar. Boleh kosong (artikel utama tidak pakai gambar) | images/news-1.jpg |
| tanggal_terbit | DATE | Untuk urutan terbaru | 2024-01-12 |
| jadi_utama | TINYINT(1) | Isi 1 kalau berita ini tampil besar di Artikel Utama, 0 kalau biasa | 0 |

- Banyak baris (7).
- CRUD: Read, termasuk pencarian. Sisanya lewat phpMyAdmin.
- PHP:
  - Beranda: `ORDER BY tanggal_terbit DESC LIMIT 3`
  - Artikel utama: `WHERE jadi_utama = 1 LIMIT 1`
  - Pagination: `LIMIT 3 OFFSET 3` untuk halaman 2, `OFFSET 6` untuk halaman 3
  - Pencarian: `WHERE judul LIKE ?` dengan isi `'%' . $_GET['q'] . '%'`. Pakai prepared statement, jangan tempel `$_GET` langsung ke query.

`TINYINT(1)` itu cuma angka kecil yang dipakai sebagai ya/tidak (1/0). MySQL tidak punya tipe "boolean" asli.

### Tabel `galeri`

| Field | Tipe | Keterangan | Contoh |
|---|---|---|---|
| id | INT | Nomor unik | 1 |
| bagian | VARCHAR(20) | `galeri_foto` atau `di_balik_layar` | di_balik_layar |
| gambar | VARCHAR(255) | Path gambar | images/gallery-1.jpg |
| judul | VARCHAR(150) | Hanya dipakai Di Balik Layar | Proses Rekaman Studio |
| keterangan | VARCHAR(255) | Hanya dipakai Di Balik Layar | Melihat lebih dekat pembuatan album terakhir. |
| jenis | VARCHAR(10) | `Foto` atau `Video`, tampil sebagai pill | Video |
| kategori | VARCHAR(50) | Untuk tombol filter galeri | NOAH Era |

- Banyak baris (11).
- CRUD: Read.
- PHP: `WHERE bagian = 'di_balik_layar'` dan `WHERE bagian = 'galeri_foto'`. Tombol filter diubah jadi link `?kategori=Konser`, lalu `AND kategori = ?`.

### Tabel `produk`

| Field | Tipe | Keterangan | Contoh |
|---|---|---|---|
| id | INT | Nomor unik. Angka lebih besar = produk lebih baru, dipakai untuk "Urutkan: Terbaru" | 1 |
| nama_produk | VARCHAR(150) | Nama | NOAH Logo Tee Black |
| kategori | VARCHAR(50) | Isi sidebar kategori | Kaos |
| deskripsi | TEXT | Teks di card | Kaos katun premium... |
| harga | INT | Rupiah, tanpa titik | 250000 |
| gambar | VARCHAR(255) | Path gambar | images/product-1.jpg |
| label | VARCHAR(20) | Badge: `Baru`, `Terbatas`, atau kosong | Baru |
| stok | INT | Hanya diisi untuk edisi terbatas, tampil sebagai "Sisa 23 item" | 23 |

- Banyak baris (8).
- CRUD: Read.
- PHP:
  - Grid utama: `SELECT * FROM produk`
  - Edisi Terbatas: `WHERE label = 'Terbatas'`
  - Angka di sidebar: `SELECT kategori, COUNT(*) FROM produk GROUP BY kategori`
  - Slider harga: `WHERE harga <= ?`
  - Urutkan harga: `ORDER BY harga ASC` atau `DESC`
  - Tampilkan harga: `'Rp ' . number_format($harga, 0, ',', '.')` menghasilkan `Rp 250.000`

Harga pakai `INT` karena rupiah tidak punya sen. `DECIMAL` baru perlu kalau ada angka di belakang koma.

Satu perbedaan dengan HTML sekarang: hoodie "Bintang di Surga Anniversary" punya badge Terbatas, jadi dengan query di atas ia ikut tampil di section Edisi Terbatas. Angka "Edisi Terbatas 3" di sidebar HTML justru cocok dengan ini (hoodie + box set + legacy tee).

### Tabel `kiriman_sahabat`

| Field | Tipe | Keterangan | Contoh |
|---|---|---|---|
| id | INT | Nomor unik | 1 |
| nama_lengkap | VARCHAR(100) | Dari input Nama Lengkap | Siti Rahma |
| email | VARCHAR(150) | Dari input Email. Tidak ditampilkan di halaman | siti@example.com |
| kategori | VARCHAR(30) | Dari dropdown: `Cerita Penggemar`, `Karya Penggemar`, `Cover Lagu` | Cerita Penggemar |
| pesan | TEXT | Dari textarea. Tampil sebagai kutipan | Menemukan keluarga baru... |
| kota | VARCHAR(100) | Tampil di bawah nama. Tidak ada di form, diisi manual | Bandung |
| gambar | VARCHAR(255) | Foto profil (cerita) atau gambar karya. Diisi manual | images/fan-2.jpg |
| status | VARCHAR(10) | `menunggu` saat baru masuk, ganti jadi `tampil` supaya muncul | tampil |
| waktu_kirim | DATETIME | Terisi otomatis saat INSERT | 2026-10-31 20:15:00 |

- Banyak baris.
- CRUD: **Create** dari form. Read di dua section. Update (ganti status) lewat phpMyAdmin.
- PHP:
  - Form: `INSERT INTO kiriman_sahabat (nama_lengkap, email, kategori, pesan) VALUES (?, ?, ?, ?)`
  - Cerita Sahabat: `WHERE kategori = 'Cerita Penggemar' AND status = 'tampil'`
  - Karya Sahabat: `WHERE kategori = 'Karya Penggemar' AND status = 'tampil'`

Kenapa ada `status`? Tanpa kolom ini, siapa pun yang mengisi form langsung muncul di halaman, termasuk spam.

### Tabel `pemesanan_event`

Setiap field sama dengan satu input di form Pemesanan & Kerja Sama.

| Field | Tipe | Input di form | Contoh |
|---|---|---|---|
| id | INT | (otomatis) | 1 |
| nama_organisasi | VARCHAR(150) | Nama Organisasi | Universitas X |
| email | VARCHAR(150) | Email | panitia@kampus.ac.id |
| telepon | VARCHAR(20) | Telepon. Pakai VARCHAR, bukan INT, supaya angka 0 di depan dan tanda + tidak hilang | 081234567890 |
| tanggal_acara | DATE | input tanggal | 2027-02-14 |
| tipe_acara | VARCHAR(30) | dropdown Tipe Acara | Festival Musik |
| estimasi_kehadiran | INT | Estimasi Kehadiran | 5000 |
| venue_lokasi | VARCHAR(200) | Venue & Lokasi | GOR Kampus, Bandung |
| rentang_anggaran | VARCHAR(100) | Rentang Anggaran (opsional). Teks bebas karena isinya rentang, misal "100-200 juta" | 100-200 juta |
| catatan | TEXT | Catatan Tambahan | Butuh sound system sendiri |
| waktu_kirim | DATETIME | (otomatis) | 2026-10-31 20:15:00 |

- Banyak baris.
- CRUD: **Create** dari form. Read lewat phpMyAdmin oleh pengelola.

### Tabel `pelanggan_email`

| Field | Tipe | Keterangan | Contoh |
|---|---|---|---|
| id | INT | Nomor unik | 1 |
| email | VARCHAR(150) | `UNIQUE`, jadi email yang sama tidak bisa masuk dua kali | fans@example.com |
| asal_form | VARCHAR(20) | `beranda` atau `event` | event |
| waktu_daftar | DATETIME | Otomatis | 2026-10-31 20:15:00 |

- Banyak baris.
- CRUD: **Create** dari dua form.
- PHP: kalau email sudah ada, MySQL menolak dengan error 1062. Tangkap error itu dan tampilkan "Email sudah terdaftar". Ini sudah saya uji: insert kedua dengan email sama memang ditolak.

---

## Tahap 4: SQL

Ada di [`noah_db.sql`](noah_db.sql). Isinya:

- `CREATE DATABASE noah_db` dengan `utf8mb4`
- 9 `CREATE TABLE`, semua `AUTO_INCREMENT PRIMARY KEY`, tanpa foreign key
- `INSERT` data awal yang disalin dari isi HTML sekarang

Cara pakai: buka phpMyAdmin, klik tab **Import**, pilih file `noah_db.sql`, klik **Go**.

---

## Tahap 5: Dari HTML ke PHP

| Halaman / Section | HTML sekarang | Tabel | Yang dilakukan PHP |
|---|---|---|---|
| Semua / Navbar + footer | Ditulis ulang di 7 file | (tidak ada) | Potong jadi `header.php` dan `footer.php`, panggil dengan `include` |
| Beranda / Konser Mendatang | 3 `<div class="card">` ditulis manual | `event` | Satu card di dalam `while`, berulang sesuai jumlah baris |
| Beranda / Berita Terbaru | 3 `<div class="berita">` | `berita` | Sama, `LIMIT 3` |
| Beranda / Form Berlangganan | `<form class="newsletter">` tanpa action | `pelanggan_email` | Tambah `method="post"`, `name="email"`, lalu INSERT |
| Diskografi / Grid + Single | 6 card + 5 `<tr>` | `diskografi` | Dua query, satu loop untuk card, satu untuk `<tr>` |
| Diskografi / Filter | `<button>` | `diskografi` | Ganti jadi `<a href="?tipe=Studio">`, PHP baca `$_GET['tipe']` |
| Event / Banner | `<form class="banner-form">` | `pelanggan_email` | Sama dengan beranda, `asal_form = 'event'` |
| Event / Konser + Arsip | Card manual | `event` | Dua query beda `jenis` |
| Event / Setlist | 3 `<details>` | `setlist` | Loop `<details>`, `explode("\n")` untuk lagu |
| Event / Form Pemesanan | `<form class="form">` tanpa action | `pemesanan_event` | Tambah `name` di 9 input, lalu INSERT |
| Media / Artikel Utama + Berita | Hero + 3 `<article>` | `berita` | Query `jadi_utama = 1` dan query halaman |
| Media / Pagination | `<button>` 1, 2, 3 | `berita` | Ganti jadi `<a href="?hal=2">` |
| Media / Pencarian | Sudah ada `name="q"` | `berita` | Baca `$_GET['q']`, pakai `LIKE` |
| Media / Di Balik Layar + Galeri | Card + `<img>` | `galeri` | Dua query beda `bagian` |
| Sahabat / Cerita + Karya | Card + `<img>` | `kiriman_sahabat` | Dua query beda `kategori`, syarat `status = 'tampil'` |
| Sahabat / Event Komunitas | 2 `event-card` | `event` | `WHERE jenis = 'komunitas'` |
| Sahabat / Form Kirim | `<form>` tanpa action, `<label>` tanpa `for` | `kiriman_sahabat` | Tambah `name`, lalu INSERT |
| Toko / Produk + Edisi Terbatas + Sidebar | Card manual, angka kategori ditulis tangan | `produk` | Loop card, `COUNT` untuk sidebar |

### Contoh lengkap: Konser Mendatang di beranda

HTML sekarang (diulang 3 kali dengan isi beda):

```html
<div class="card card-border-merah">
  <div class="card-atas">
    <div>
      <p class="tanggal-besar">24</p>
      <p class="bulan">AGU</p>
    </div>
    <span class="label-tersedia">Tersedia</span>
  </div>
  <div class="card-tengah">
    <h3 class="kota">Jakarta</h3>
    <p class="venue">JIExpo Kemayoran</p>
  </div>
  <a class="btn btn-merah lebar" href="tur-event.php#konser-mendatang">Info Acara</a>
</div>
```

Setelah jadi PHP (ditulis **sekali**):

```php
<?php
// koneksi.php
$koneksi = mysqli_connect('localhost', 'root', '', 'noah_db');
mysqli_set_charset($koneksi, 'utf8mb4');
```

```php
<?php
include 'koneksi.php';
$bulan = ['', 'JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'];
$hasil = mysqli_query($koneksi, "SELECT * FROM event WHERE jenis = 'konser' ORDER BY tanggal LIMIT 3");
?>
<div class="grid-3">
<?php while ($e = mysqli_fetch_assoc($hasil)) : ?>
  <div class="card card-border-merah">
    <div class="card-atas">
      <div>
        <p class="tanggal-besar"><?= date('d', strtotime($e['tanggal'])) ?></p>
        <p class="bulan"><?= $bulan[(int) date('n', strtotime($e['tanggal']))] ?></p>
      </div>
      <span class="label-tersedia"><?= htmlspecialchars($e['label']) ?></span>
    </div>
    <div class="card-tengah">
      <h3 class="kota"><?= htmlspecialchars($e['kota']) ?></h3>
      <p class="venue"><?= htmlspecialchars($e['venue']) ?></p>
    </div>
    <a class="btn btn-merah lebar" href="tur-event.php#konser-mendatang">Info Acara</a>
  </div>
<?php endwhile; ?>
</div>
```

Alurnya: MySQL mengirim 3 baris, `while` mengulang card 3 kali, `$e['kota']` diganti isi kolom `kota` di baris itu. Tambah baris keempat di phpMyAdmin, lalu ganti `LIMIT 3` jadi `LIMIT 4`, dan card keempat muncul tanpa menyentuh HTML.

`htmlspecialchars()` mencegah teks dari database merusak HTML (misalnya kalau ada tanda `<`).

### Contoh lengkap: menyimpan form Berlangganan

```html
<form class="newsletter" method="post" action="simpan-email.php">
  <input type="email" name="email" placeholder="Alamat Email Anda" required>
  <input type="hidden" name="asal_form" value="beranda">
  <button type="submit" class="btn btn-merah">Berlangganan</button>
</form>
```

```php
<?php
// simpan-email.php
include 'koneksi.php';

$email = trim($_POST['email'] ?? '');
$asal  = ($_POST['asal_form'] ?? '') === 'event' ? 'event' : 'beranda';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die('Format email salah.');
}

$stmt = mysqli_prepare($koneksi, 'INSERT INTO pelanggan_email (email, asal_form) VALUES (?, ?)');
mysqli_stmt_bind_param($stmt, 'ss', $email, $asal);

try {
    mysqli_stmt_execute($stmt);
    echo 'Terima kasih, email tersimpan.';
} catch (mysqli_sql_exception $e) {
    echo $e->getCode() === 1062 ? 'Email sudah terdaftar.' : 'Gagal menyimpan.';
}
```

Tanda `?` di query itu tempat data pengunjung, dan `bind_param` yang mengisinya. Cara ini menutup celah SQL injection. Pola yang sama berlaku untuk form pemesanan dan form kirim cerita, hanya beda jumlah kolom.

---

## Tahap 6: Evaluasi ulang

**Ada tabel yang tidak perlu?** Saya cek satu per satu. Yang paling bisa dipertanyakan adalah `setlist`, karena baru 1 dari 3 setlist yang berisi lagu. Tetap saya pertahankan karena bentuk datanya (daftar lagu + encore + tamu) beda dengan card event, dan kalau digabung ke `event`, tabel itu dapat 3 kolom tambahan yang kosong di 7 dari 8 baris.

**Ada tabel yang bisa digabung?** Sudah saya gabung yang bisa:
- konser + arsip + event komunitas → `event`
- album + single → `diskografi`
- form kirim + cerita sahabat + karya sahabat → `kiriman_sahabat`
- dua form email → `pelanggan_email`
- di balik layar + galeri foto → `galeri`

Tanpa penggabungan ini jumlahnya sekitar 15 tabel.

**Yang sengaja tidak dibuat:** tabel `anggota`, `tonggak_sejarah`, `komunitas_regional`, `materi_pers`, `kategori_produk`, `pesanan`, `admin`/`user`. Empat yang pertama isinya sedikit dan jarang berubah. `kategori_produk` cukup jadi kolom teks. `pesanan` dan `admin` tidak punya dasar di HTML.

**Ada field tanpa fungsi?** Tidak. Tiap field punya pasangan di HTML (teks yang tampil atau input form), kecuali `status` dan `waktu_kirim`/`waktu_daftar` yang dibutuhkan untuk mengelola kiriman.

**Terlalu rumit untuk pemula?** Satu-satunya hal yang mungkin membingungkan adalah kolom pembeda (`jenis`, `tipe`, `bagian`, `kategori`). Polanya sama di semua tabel: **satu kolom menentukan baris ini tampil di section mana**, lalu tiap section memakai `WHERE kolom = 'nilai'`.

**Butuh JOIN?** Tidak ada satu pun query yang perlu JOIN.

**Duplikasi bermasalah?** Ada satu yang kecil: nama album di `album_asal` (untuk single) ditulis ulang sebagai teks. Kalau judul album diganti, teks di single tidak ikut berubah. Untuk 5 single, mengubahnya manual masih wajar.

**Siap dipakai PHP native?** Ya. SQL sudah dicoba import ke MySQL 8.4, data awal masuk, dan contoh query (`LIMIT`, `GROUP BY`, `UNIQUE`) jalan.
