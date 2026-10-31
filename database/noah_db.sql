-- =====================================================================
--  DATABASE WEBSITE NOAH
-- =====================================================================
--
--  File ini membuat database "noah_db" lengkap dengan 10 tabel dan
--  data awalnya. Isinya diambil dari website HTML yang lama.
--
--  CARA PAKAI
--    1. Buka phpMyAdmin (http://localhost/phpmyadmin)
--    2. Klik tab "Import"
--    3. Pilih file ini, lalu klik "Go"
--    Kalau database noah_db sudah ada, hapus dulu (Drop) supaya tidak bentrok.
--
--  CARA MEMBACA FILE INI
--    Baris yang diawali dua tanda minus (--) adalah KOMENTAR.
--    Komentar hanya catatan untuk manusia, MySQL tidak menjalankannya.
--
--  PENTING: SEMUA TABEL BERDIRI SENDIRI
--    Tidak ada tabel yang saling terhubung (tidak ada foreign key).
--    Jadi setiap halaman cukup mengambil data dari SATU tabel,
--    tanpa perlu JOIN.
--
-- ---------------------------------------------------------------------
--  KAMUS SINGKAT (istilah yang sering muncul di bawah)
-- ---------------------------------------------------------------------
--
--  TIPE DATA (jenis isi sebuah kolom)
--    INT           angka bulat, contoh: 1, 250000
--    VARCHAR(100)  teks pendek, maksimal 100 huruf, contoh: nama, judul
--    TEXT          teks panjang tanpa batas pendek, contoh: deskripsi, isi
--    DATE          tanggal saja, format tahun-bulan-hari: 2026-08-24
--    DATETIME      tanggal + jam: 2026-10-31 20:15:00
--    YEAR          tahun saja: 2019
--    TINYINT(1)    angka kecil, dipakai sebagai ya/tidak: 1 = ya, 0 = tidak
--
--  ATURAN KOLOM
--    NOT NULL        kolom WAJIB diisi
--    NULL            kolom BOLEH dikosongkan
--    DEFAULT x       kalau tidak diisi, otomatis bernilai x
--    PRIMARY KEY     nomor pengenal unik tiap baris (selalu kolom id)
--    AUTO_INCREMENT  nomor id terisi sendiri: 1, 2, 3, dan seterusnya
--    UNIQUE          isi kolom ini tidak boleh sama dengan baris lain
--    CURRENT_TIMESTAMP  waktu saat data disimpan
--
--  BARIS PENUTUP SETIAP TABEL
--    ENGINE=InnoDB     mesin penyimpanan standar MySQL
--    CHARSET=utf8mb4   supaya semua huruf (termasuk emoji) bisa disimpan
--    Cukup disalin apa adanya, tidak perlu diubah.
--
-- =====================================================================


-- Buat database baru bernama noah_db (kalau belum ada).
CREATE DATABASE IF NOT EXISTS noah_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Pakai database noah_db untuk semua perintah di bawah ini.
USE noah_db;


-- =====================================================================
--  BAGIAN 1: MEMBUAT TABEL
-- =====================================================================


-- ---------------------------------------------------------------------
-- TABEL 1: event
-- Isi       : semua acara NOAH (konser, arsip konser lama, event komunitas)
-- Tampil di : index.html        > Konser Mendatang
--             tur-event.html    > Konser Mendatang dan Arsip Event
--             sahabat-noah.html > Event Komunitas
-- Kuncinya  : kolom "jenis" menentukan baris ini tampil di section mana.
--             Contoh PHP: SELECT * FROM event WHERE jenis = 'konser'
-- ---------------------------------------------------------------------
CREATE TABLE event (
  id          INT AUTO_INCREMENT PRIMARY KEY,  -- nomor unik, terisi otomatis
  jenis       VARCHAR(20)  NOT NULL,           -- pilih salah satu: konser / arsip / komunitas
  judul       VARCHAR(150) NOT NULL,           -- judul card (untuk konser: nama kota)
  tanggal     DATE         NULL,               -- boleh kosong, karena arsip lama kadang cuma tahu tahunnya
  kota        VARCHAR(100) NULL,               -- kota acara
  venue       VARCHAR(150) NULL,               -- nama tempat, contoh: JIExpo Kemayoran
  deskripsi   TEXT         NULL,               -- teks kecil di card
  label       VARCHAR(50)  NULL                -- badge kecil di card, contoh: Tersedia, TUR DUNIA
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ---------------------------------------------------------------------
-- TABEL 2: setlist
-- Isi       : daftar lagu yang dibawakan di satu konser
-- Tampil di : tur-event.html > Arsip Setlist (dropdown yang bisa dibuka-tutup)
-- Catatan   : daftar lagu ditulis dalam satu kolom, satu lagu per baris.
--             PHP memecahnya per baris dengan explode("\n", ...).
-- ---------------------------------------------------------------------
CREATE TABLE setlist (
  id           INT AUTO_INCREMENT PRIMARY KEY, -- nomor unik, terisi otomatis
  judul        VARCHAR(150) NOT NULL,          -- judul dropdown, contoh: The Great Journey of NOAH - Jakarta (2023)
  venue        VARCHAR(150) NULL,              -- tempat konser
  tanggal      DATE         NULL,              -- tanggal konser
  tamu         VARCHAR(150) NULL,              -- bintang tamu
  lagu_utama   TEXT         NULL,              -- lagu set utama, satu lagu per baris
  lagu_encore  TEXT         NULL               -- lagu encore (lagu tambahan di akhir), satu lagu per baris
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ---------------------------------------------------------------------
-- TABEL 3: diskografi
-- Isi       : album DAN single NOAH/Peterpan dalam satu tabel
-- Tampil di : diskografi.html > Grid Album dan Tabel Single
-- Kuncinya  : kolom "tipe" membedakan album dan single.
--             Album  : WHERE tipe <> 'Single'   (<> artinya "tidak sama dengan")
--             Single : WHERE tipe = 'Single'
-- Catatan   : beberapa kolom hanya diisi untuk album, ada juga yang hanya
--             untuk single. Kolom yang tidak dipakai dibiarkan kosong (NULL).
-- ---------------------------------------------------------------------
CREATE TABLE diskografi (
  id             INT AUTO_INCREMENT PRIMARY KEY, -- nomor unik, terisi otomatis
  judul          VARCHAR(150) NOT NULL,          -- judul album atau judul lagu single
  tahun          YEAR         NOT NULL,          -- tahun rilis, juga dipakai untuk filter era
  tipe           VARCHAR(20)  NOT NULL,          -- pilih salah satu: Studio / Kompilasi / Lagu Tema / Single
  jumlah_lagu    INT          NULL,              -- KHUSUS ALBUM: jumlah lagu
  lagu_unggulan  TEXT         NULL,              -- KHUSUS ALBUM: 3 lagu di card, satu per baris
  gambar_cover   VARCHAR(255) NULL,              -- KHUSUS ALBUM: lokasi file gambar cover
  album_asal     VARCHAR(150) NULL               -- KHUSUS SINGLE: nama album asalnya (teks biasa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ---------------------------------------------------------------------
-- TABEL 4: berita
-- Isi       : artikel berita
-- Tampil di : index.html        > Berita Terbaru (3 paling baru)
--             berita-media.html > Artikel Utama, Berita Terbaru, dan pencarian
-- Kuncinya  : kolom "jadi_utama" = 1 berarti berita ini tampil besar
--             sebagai Artikel Utama. Hanya satu berita yang bernilai 1.
-- Bisa dikelola lewat admin panel: admin/berita/
-- ---------------------------------------------------------------------
CREATE TABLE berita (
  id              INT AUTO_INCREMENT PRIMARY KEY, -- nomor unik, terisi otomatis
  judul           VARCHAR(200) NOT NULL,          -- judul berita
  kategori        VARCHAR(50)  NOT NULL,          -- teks di label kecil: Pengumuman, Album, Tur, Musik, dst.
  ringkasan       TEXT         NOT NULL,          -- paragraf pendek di card
  gambar          VARCHAR(255) NULL,              -- lokasi file gambar, contoh: images/news-1.jpg
  tanggal_terbit  DATE         NOT NULL,          -- dipakai untuk mengurutkan dari yang terbaru
  jadi_utama      TINYINT(1)   NOT NULL DEFAULT 0 -- 1 = Artikel Utama, 0 = berita biasa
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ---------------------------------------------------------------------
-- TABEL 5: galeri
-- Isi       : foto dan video
-- Tampil di : berita-media.html > Di Balik Layar dan Galeri Foto
-- Kuncinya  : kolom "bagian" menentukan section tempat tampil.
--             Contoh PHP: SELECT * FROM galeri WHERE bagian = 'galeri_foto'
-- ---------------------------------------------------------------------
CREATE TABLE galeri (
  id          INT AUTO_INCREMENT PRIMARY KEY,  -- nomor unik, terisi otomatis
  bagian      VARCHAR(20)  NOT NULL,           -- pilih salah satu: galeri_foto / di_balik_layar
  gambar      VARCHAR(255) NOT NULL,           -- lokasi file gambar
  judul       VARCHAR(150) NULL,               -- hanya dipakai di Di Balik Layar
  keterangan  VARCHAR(255) NULL,               -- hanya dipakai di Di Balik Layar
  jenis       VARCHAR(10)  NOT NULL DEFAULT 'Foto', -- Foto / Video (kalau tidak diisi = Foto)
  kategori    VARCHAR(50)  NULL                -- untuk tombol filter: Peterpan Era, NOAH Era, Konser, Studio
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ---------------------------------------------------------------------
-- TABEL 6: produk
-- Isi       : barang yang dijual di toko
-- Tampil di : toko.html > Grid Produk, Edisi Terbatas, sidebar kategori
-- Kuncinya  : produk dengan label 'Terbatas' tampil di section Edisi Terbatas.
--             Angka di sidebar dihitung otomatis:
--             SELECT kategori, COUNT(*) FROM produk GROUP BY kategori
-- ---------------------------------------------------------------------
CREATE TABLE produk (
  id           INT AUTO_INCREMENT PRIMARY KEY, -- nomor unik, terisi otomatis
  nama_produk  VARCHAR(150) NOT NULL,          -- nama barang
  kategori     VARCHAR(50)  NOT NULL,          -- Kaos / Hoodie / Jaket / Aksesoris / CD & Vinyl
  deskripsi    TEXT         NOT NULL,          -- penjelasan singkat di card
  harga        INT          NOT NULL,          -- harga rupiah TANPA titik: tulis 250000, bukan 250.000
  gambar       VARCHAR(255) NOT NULL,          -- lokasi file gambar
  label        VARCHAR(20)  NULL,              -- Baru / Terbatas / kosong
  stok         INT          NULL               -- hanya untuk edisi terbatas, tampil sebagai "Sisa 23 item"
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ---------------------------------------------------------------------
-- TABEL 7: kiriman_sahabat
-- Isi       : cerita dan karya yang dikirim penggemar lewat form
-- Dipakai   : sahabat-noah.html
--               Form "Kirim Cerita atau Karyamu" -> MENYIMPAN ke sini (INSERT)
--               Section Cerita Sahabat         -> MEMBACA kategori Cerita Penggemar
--               Section Karya Sahabat          -> MEMBACA kategori Karya Penggemar
-- Alurnya   : kiriman baru berstatus 'menunggu' dan BELUM tampil.
--             Ubah status jadi 'tampil' lewat phpMyAdmin supaya muncul di website.
--             Ini mencegah spam langsung tampil di halaman.
-- ---------------------------------------------------------------------
CREATE TABLE kiriman_sahabat (
  id            INT AUTO_INCREMENT PRIMARY KEY, -- nomor unik, terisi otomatis
  nama_lengkap  VARCHAR(100) NOT NULL,          -- dari input Nama Lengkap
  email         VARCHAR(150) NOT NULL,          -- dari input Email (tidak ditampilkan di website)
  kategori      VARCHAR(30)  NOT NULL,          -- Cerita Penggemar / Karya Penggemar / Cover Lagu
  pesan         TEXT         NOT NULL,          -- isi cerita, tampil sebagai kutipan
  kota          VARCHAR(100) NULL,              -- tidak ada di form, diisi manual oleh pengelola
  gambar        VARCHAR(255) NULL,              -- foto profil atau gambar karya, diisi manual
  status        VARCHAR(10)  NOT NULL DEFAULT 'menunggu', -- menunggu / tampil
  waktu_kirim   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP -- terisi otomatis saat form dikirim
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ---------------------------------------------------------------------
-- TABEL 8: pemesanan_event
-- Isi       : permintaan booking konser dari penyelenggara acara
-- Dipakai   : tur-event.html > Form Pemesanan & Kerja Sama (INSERT)
-- Catatan   : setiap kolom = satu kotak isian di form tersebut.
-- ---------------------------------------------------------------------
CREATE TABLE pemesanan_event (
  id                  INT AUTO_INCREMENT PRIMARY KEY, -- nomor unik, terisi otomatis
  nama_organisasi     VARCHAR(150) NOT NULL,  -- isian Nama Organisasi
  email               VARCHAR(150) NOT NULL,  -- isian Email
  telepon             VARCHAR(20)  NOT NULL,  -- isian Telepon. Pakai VARCHAR (teks), bukan INT,
                                              -- supaya angka 0 di depan dan tanda + tidak hilang
  tanggal_acara       DATE         NULL,      -- isian tanggal
  tipe_acara          VARCHAR(30)  NOT NULL,  -- Festival Musik / Acara Korporat / Konser Privat
  estimasi_kehadiran  INT          NULL,      -- isian Estimasi Kehadiran (jumlah orang)
  venue_lokasi        VARCHAR(200) NULL,      -- isian Venue & Lokasi
  rentang_anggaran    VARCHAR(100) NULL,      -- isian Rentang Anggaran (opsional, teks bebas)
  catatan             TEXT         NULL,      -- isian Catatan Tambahan
  waktu_kirim         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP -- terisi otomatis
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ---------------------------------------------------------------------
-- TABEL 9: pelanggan_email
-- Isi       : email pengunjung yang ingin mendapat kabar terbaru
-- Dipakai   : index.html     > form Berlangganan (INSERT)
--             tur-event.html > form "Beritahu Saya" di banner hiatus (INSERT)
-- Catatan   : kolom email memakai UNIQUE. Kalau email yang sama dikirim lagi,
--             MySQL menolaknya dengan error 1062. PHP bisa menangkap error ini
--             lalu menampilkan pesan "Email sudah terdaftar".
-- ---------------------------------------------------------------------
CREATE TABLE pelanggan_email (
  id           INT AUTO_INCREMENT PRIMARY KEY,  -- nomor unik, terisi otomatis
  email        VARCHAR(150) NOT NULL UNIQUE,    -- alamat email, tidak boleh dobel
  asal_form    VARCHAR(20)  NOT NULL,           -- dari form mana: beranda / event
  waktu_daftar DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP -- terisi otomatis
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ---------------------------------------------------------------------
-- TABEL 10: admin
-- Isi       : akun untuk login ke admin panel
-- Dipakai   : admin/login.php
-- Keamanan  : kolom password TIDAK berisi password asli, melainkan HASH
--             (hasil acakan dari fungsi PHP password_hash). Hash tidak bisa
--             dikembalikan jadi password asli, jadi kalau database bocor,
--             password tetap aman. Saat login, PHP mencocokkan dengan
--             password_verify.
-- ---------------------------------------------------------------------
CREATE TABLE admin (
  id        INT AUTO_INCREMENT PRIMARY KEY,  -- nomor unik, terisi otomatis
  username  VARCHAR(50)  NOT NULL UNIQUE,    -- nama login, tidak boleh dobel
  password  VARCHAR(255) NOT NULL            -- HASH password, panjangnya sekitar 60 huruf
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =====================================================================
--  BAGIAN 2: DATA AWAL
-- =====================================================================
--
--  Data di bawah disalin dari isi website HTML yang lama, supaya
--  setelah di-import, halaman PHP langsung punya isi.
--
--  CARA MEMBACA PERINTAH INSERT
--    INSERT INTO nama_tabel (kolom1, kolom2) VALUES
--    ('isi kolom1 baris 1', 'isi kolom2 baris 1'),
--    ('isi kolom1 baris 2', 'isi kolom2 baris 2');
--    Urutan isi di dalam kurung harus SAMA dengan urutan nama kolom.
--    Kolom id tidak ditulis karena terisi otomatis (AUTO_INCREMENT).
--
--  TANDA KHUSUS DI DALAM TEKS
--    NULL   = kosong (tanpa tanda kutip)
--    \n     = pindah baris (Enter). Dipakai di daftar lagu.
--    ''     = dua tanda kutip satu berturut-turut artinya satu tanda kutip
--             di dalam teks. Contoh: 'Pembuatan ''Wanitaku''' tersimpan
--             sebagai: Pembuatan 'Wanitaku'
--
--  Tabel pemesanan_event dan pelanggan_email sengaja TIDAK diisi,
--  karena datanya nanti datang dari form yang diisi pengunjung.
--
-- =====================================================================


-- Data event: 3 konser mendatang, 3 arsip, 2 event komunitas.
-- Arsip yang hanya diketahui tahunnya, tanggalnya diisi NULL (kosong).
INSERT INTO event (jenis, judul, tanggal, kota, venue, deskripsi, label) VALUES
('konser',    'Jakarta',  '2026-08-24', 'Jakarta',  'JIExpo Kemayoran',           'Malam karya NOAH dengan sesi arsip visual, bernyanyi bersama, dan ruang temu Sahabat.', 'Tersedia'),
('konser',    'Bandung',  '2026-09-12', 'Bandung',  'SABUGA',                     'Temu Sahabat NOAH Bandung dengan pemutaran arsip konser dan sesi musik akustik.', 'Tersedia'),
('konser',    'Surabaya', '2026-10-03', 'Surabaya', 'Grand City Convention Hall', 'Malam tribut NOAH bersama komunitas regional dan kurasi lagu lintas era.', 'Tersedia'),
('arsip',     '6 Kota dalam 24 Jam (2004)', NULL,   NULL, NULL, 'Medan, Padang, Pekanbaru, Lampung, Semarang, Surabaya', 'EVENT BERSEJARAH'),
('arsip',     '5 Negara dalam 24 Jam', '2012-11-15', NULL, NULL, 'Melbourne, Hong Kong, Kuala Lumpur, Singapore, Jakarta', NULL),
('arsip',     'Tur AS (2015)', NULL,                NULL, NULL, 'Los Angeles, San Francisco, Washington DC, New York', 'TUR DUNIA'),
('komunitas', 'Sahabat NOAH Nasional', '2024-10-24', 'Jakarta', NULL, 'Pertemuan akbar tahunan seluruh komunitas wilayah di Jakarta. Sesi bermain musik bersama, diskusi, dan kejutan spesial.', 'Gathnas 2024'),
-- Tahun untuk event di bawah tidak tertulis di HTML ("12 November"), 2024 di sini hanya perkiraan.
('komunitas', 'Lokakarya Musik & Kopi', '2024-11-12', 'Bandung', NULL, 'Bedah instrumen lagu-lagu NOAH bersama musisi lokal dan sesi berbagi antar Sahabat.', 'Bandung');


-- Data setlist: hanya setlist pertama yang punya daftar lagu di HTML.
-- Dua setlist lainnya baru berisi judul, sisanya NULL (silakan dilengkapi).
-- Perhatikan \n di daftar lagu: itu tanda pindah baris antar lagu.
INSERT INTO setlist (judul, venue, tanggal, tamu, lagu_utama, lagu_encore) VALUES
('The Great Journey of NOAH - Jakarta (2023)', 'Ancol Beach City International Stadium', '2023-12-03', 'Reza (Drummer Asli)',
 'Topeng\nAda Gajah Dibalik Batu (Intro)\nSeparuh Aku\nMungkin Nanti\nMenghapus Jejakmu\nTak Bisakah\nYang Terdalam\nBintang di Surga\nKhayalan Tingkat Tinggi\nWanitaku',
 'Walau Habis Terang\nMimpi Yang Sempurna'),
('Tur Dunia 2015 - Los Angeles (AS)', NULL, NULL, NULL, NULL, NULL),
('Asia Song Festival - Busan (Korea)', NULL, NULL, NULL, NULL, NULL);


-- Data diskografi: 6 album (baris atas) lalu 5 single (baris bawah).
-- Untuk single, kolom khusus album diisi NULL.
-- Untuk album, kolom album_asal diisi NULL.
INSERT INTO diskografi (judul, tahun, tipe, jumlah_lagu, lagu_unggulan, gambar_cover, album_asal) VALUES
('Keterkaitan Keterikatan',    2019, 'Studio',    8,  'Wanitaku\nKupeluk Hatimu\nMendekati Lugu',                 'images/album-1.jpg',     NULL),
('Second Chance',              2014, 'Studio',    12, 'Hero\nSeperti Kemarin\nSuara Pikiranku',                   'images/album-2.jpg',     NULL),
('Sings Legends',              2016, 'Kompilasi', 9,  'Andaikan Kau Datang\nSajadah Panjang\nBiarlah Sendiri',    'images/album-3.jpg',     NULL),
('Seperti Seharusnya',         2012, 'Studio',    10, 'Separuh Aku\nHidup Untukmu, Mati Tanpamu\nJika Engkau',    'images/album-cover.jpg', NULL),
('Bintang di Surga',           2004, 'Studio',    10, 'Mungkin Nanti\nAda Apa Denganmu\nKukatakan Dengan Indah',  'images/gallery-2.jpg',   NULL),
('Sebuah Nama, Sebuah Cerita', 2008, 'Kompilasi', 11, 'Menunggumu\nTak Bisakah\nWalau Habis Terang',              'images/gallery-4.jpg',   NULL),
('Separuh Aku',                2012, 'Single', NULL, NULL, NULL, 'Seperti Seharusnya'),
('Tak Bisakah',                2005, 'Single', NULL, NULL, NULL, 'OST Alexandria'),
('Menunggumu',                 2008, 'Single', NULL, NULL, NULL, 'Sebuah Nama, Sebuah Cerita'),
('Khayalan Tingkat Tinggi',    2004, 'Single', NULL, NULL, NULL, 'Bintang di Surga'),
('Wanitaku',                   2019, 'Single', NULL, NULL, NULL, 'Keterkaitan Keterikatan');


-- Data berita: 7 berita. Baris pertama (angka 1 di akhir) adalah Artikel Utama.
-- Artikel Utama tidak memakai gambar, jadi kolom gambarnya NULL.
INSERT INTO berita (judul, kategori, ringkasan, gambar, tanggal_terbit, jadi_utama) VALUES
('NOAH Umumkan Hiatus: Apa yang Terjadi Selanjutnya?', 'Pengumuman', 'Setelah dekade penuh kesuksesan, Ariel dan kawan-kawan memutuskan untuk mengambil jeda panjang untuk eksplorasi kreatif baru.', NULL, '2024-01-15', 1),
('Kolaborasi Spesial NOAH dengan Orkestra Nasional', 'Musik', 'Sebuah mahakarya baru yang menggabungkan elemen rock alternatif dengan kemegahan simfoni klasik.', 'images/news-1.jpg', '2024-01-12', 0),
('Tiket Konser ''The Final Show'' Terjual Habis dalam 5 Menit', 'Konser', 'Antusiasme penggemar memuncak menyambut pertunjukan terakhir sebelum masa hiatus resmi dimulai.', 'images/news-2.jpg', '2024-01-08', 0),
('Ariel Berbagi Cerita Dibalik Lirik Lagu Teranyar', 'Pribadi', 'Wawancara eksklusif mengenai proses kreatif dan refleksi perjalanan karir musiknya.', 'images/news-3.jpg', '2024-01-05', 0),
('Dibalik Layar Pembuatan Aransemen Baru "Keterkaitan Keterikatan"', 'Album', 'Eksplorasi sound terbaru NOAH yang menggabungkan elemen orkestra dengan synthesizer modern...', 'images/news-1.jpg', '2024-09-12', 0),
('Pengalaman Tak Terlupakan Tur "The Great Journey" di Singapura', 'Tur', 'Ribuan Sahabat NOAH memadati Star Performing Arts Centre dalam malam yang penuh haru...', 'images/news-2.jpg', '2024-09-05', 0),
('Eksklusif: Sesi Bermusik NOAH Bersama Musisi Legendaris Mancanegara', 'Media', 'Kolaborasi lintas genre yang memberikan warna baru pada lagu-lagu klasik NOAH...', 'images/news-3.jpg', '2024-08-28', 0);


-- Data galeri: 3 item Di Balik Layar, lalu 8 foto Galeri Foto.
-- Kategori foto (Peterpan Era / NOAH Era / Konser / Studio) tidak tertulis
-- di HTML, jadi dibiarkan NULL. Isi sendiri sesuai isi fotonya.
INSERT INTO galeri (bagian, gambar, judul, keterangan, jenis, kategori) VALUES
('di_balik_layar', 'images/gallery-1.jpg', 'Proses Rekaman Studio',    'Melihat lebih dekat pembuatan album terakhir.', 'Video', NULL),
('di_balik_layar', 'images/gallery-2.jpg', 'Momen Belakang Panggung',  'Persiapan sebelum naik ke panggung megah.',     'Foto',  NULL),
('di_balik_layar', 'images/gallery-3.jpg', 'Pembuatan ''Wanitaku''',   'Eksplorasi desain suara yang mendalam.',        'Video', NULL),
('galeri_foto', 'images/gallery-1.jpg', NULL, NULL, 'Foto', NULL),
('galeri_foto', 'images/gallery-2.jpg', NULL, NULL, 'Foto', NULL),
('galeri_foto', 'images/gallery-3.jpg', NULL, NULL, 'Foto', NULL),
('galeri_foto', 'images/gallery-4.jpg', NULL, NULL, 'Foto', NULL),
('galeri_foto', 'images/feed-1.jpg',    NULL, NULL, 'Foto', NULL),
('galeri_foto', 'images/feed-2.jpg',    NULL, NULL, 'Foto', NULL),
('galeri_foto', 'images/feed-3.jpg',    NULL, NULL, 'Foto', NULL),
('galeri_foto', 'images/feed-4.jpg',    NULL, NULL, 'Foto', NULL);


-- Data produk: 6 produk biasa, lalu 2 produk edisi terbatas (punya stok).
-- Harga ditulis tanpa titik. PHP yang nanti menambahkan titik saat ditampilkan.
INSERT INTO produk (nama_produk, kategori, deskripsi, harga, gambar, label, stok) VALUES
('NOAH Logo Tee Black',                 'Kaos',       'Kaos katun premium dengan logo NOAH minimalis.',            250000, 'images/product-1.jpg', 'Baru',     NULL),
('NOAH Logo Tee White',                 'Kaos',       'Varian warna putih ikonik untuk tampilan bersih.',          250000, 'images/product-2.jpg', NULL,       NULL),
('Bintang di Surga Anniversary Hoodie', 'Hoodie',     'Edisi spesial perayaan album legendaris.',                  550000, 'images/product-3.jpg', 'Terbatas', NULL),
('NOAH Snapback Cap',                   'Aksesoris',  'Topi snapback kualitas premium dengan bordir 3D.',          185000, 'images/product-4.jpg', NULL,       NULL),
('Keterkaitan Keterikatan Vinyl',       'CD & Vinyl', 'Piringan hitam kualitas audiophile untuk kolektor.',        450000, 'images/product-5.jpg', NULL,       NULL),
('NOAH Tote Bag',                       'Aksesoris',  'Tas kanvas tebal tahan lama untuk kegiatan sehari-hari.',   120000, 'images/product-6.jpg', NULL,       NULL),
('Box Set Edisi Terbatas',              'CD & Vinyl', 'Paket eksklusif berisi 3 vinyl, buku foto dokumentasi tur, dan sertifikat keaslian bertandatangan personil.', 1500000, 'images/limited-1.jpg', 'Terbatas', 23),
('Peterpan Legacy Tee',                 'Kaos',       'Kaos edisi khusus mengenang perjalanan awal karir dengan desain grafis arsip original tahun 2003.', 350000, 'images/limited-2.jpg', 'Terbatas', 12);


-- Data kiriman_sahabat: 3 cerita yang sudah tampil di HTML lama,
-- dimasukkan dengan status 'tampil' supaya langsung muncul.
-- Email aslinya tidak ada di HTML, jadi diisi alamat contoh.
INSERT INTO kiriman_sahabat (nama_lengkap, email, kategori, pesan, kota, gambar, status) VALUES
('Andhika Prasetya', 'contoh1@example.com', 'Cerita Penggemar', 'NOAH mengubah hidup saya. Lirik-liriknya selalu menemani saat saya berada di titik terendah.', 'Jakarta', 'images/fan-1.jpg', 'tampil'),
('Siti Rahma',       'contoh2@example.com', 'Cerita Penggemar', 'Menemukan keluarga baru di komunitas ini. Sahabat NOAH Bandung sangat suportif dan kreatif.', 'Bandung', 'images/fan-2.jpg', 'tampil'),
('Reza Kurniawan',   'contoh3@example.com', 'Cerita Penggemar', 'Koleksi produk resmi saya sudah mencapai 100 item. Semua berawal dari kecintaan pada melodi Ariel.', 'Surabaya', 'images/fan-3.jpg', 'tampil');


-- Data admin: satu akun awal untuk latihan.
--   username : admin
--   password : noah-30a23f
-- Teks panjang yang diawali $2y$10$ di bawah adalah HASH dari password itu,
-- bukan password-nya sendiri.
--
-- Cara mengganti password:
--   1. Buka Command Prompt, jalankan:
--        php -r "echo password_hash('password_baru', PASSWORD_DEFAULT);"
--   2. Salin hasilnya (teks yang diawali $2y$)
--   3. Di phpMyAdmin, buka tabel admin, klik Edit, tempel ke kolom password
INSERT INTO admin (username, password) VALUES
('admin', '$2y$10$AUK9C0E7t9jkt1Rda/2OJ.11XkNSsl2oNz/vFnu/jhyCC5p/9uwCW');
