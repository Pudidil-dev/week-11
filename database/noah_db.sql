-- =====================================================================
-- Database website NOAH (migrasi HTML statis ke PHP + MySQL)
-- Bisa langsung di-import lewat phpMyAdmin: tab "Import" > pilih file ini.
--
-- Ada 10 tabel (9 konten/form + 1 admin). Semuanya BERDIRI SENDIRI:
--   tidak ada foreign key, tidak perlu JOIN.
--   Setiap halaman cukup memakai SELECT ke satu tabel.
-- =====================================================================

CREATE DATABASE IF NOT EXISTS noah_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE noah_db;

-- ---------------------------------------------------------------------
-- 1. event
--    Dipakai: index.html (Konser Mendatang),
--             tur-event.html (Konser Mendatang + Arsip Event),
--             sahabat-noah.html (Event Komunitas)
--    Kolom "jenis" yang membedakan section mana yang menampilkan baris ini.
-- ---------------------------------------------------------------------
CREATE TABLE event (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  jenis       VARCHAR(20)  NOT NULL,          -- 'konser' / 'arsip' / 'komunitas'
  judul       VARCHAR(150) NOT NULL,
  tanggal     DATE         NULL,              -- boleh kosong (arsip lama hanya tahu tahunnya)
  kota        VARCHAR(100) NULL,
  venue       VARCHAR(150) NULL,
  deskripsi   TEXT         NULL,
  label       VARCHAR(50)  NULL               -- teks badge kecil di card: 'Tersedia', 'TUR DUNIA', 'Gathnas 2024'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 2. setlist
--    Dipakai: tur-event.html (Arsip Setlist, dropdown <details>)
--    Daftar lagu disimpan sebagai teks, satu lagu per baris.
-- ---------------------------------------------------------------------
CREATE TABLE setlist (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  judul        VARCHAR(150) NOT NULL,
  venue        VARCHAR(150) NULL,
  tanggal      DATE         NULL,
  tamu         VARCHAR(150) NULL,
  lagu_utama   TEXT         NULL,             -- satu lagu per baris (Enter)
  lagu_encore  TEXT         NULL              -- satu lagu per baris (Enter)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 3. diskografi
--    Dipakai: diskografi.html (Grid Album + Tabel Single)
--    Album dan single ada di tabel yang sama, dibedakan kolom "tipe".
-- ---------------------------------------------------------------------
CREATE TABLE diskografi (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  judul          VARCHAR(150) NOT NULL,
  tahun          YEAR         NOT NULL,
  tipe           VARCHAR(20)  NOT NULL,       -- 'Studio' / 'Kompilasi' / 'Lagu Tema' / 'Single'
  jumlah_lagu    INT          NULL,           -- khusus album
  lagu_unggulan  TEXT         NULL,           -- khusus album, satu lagu per baris
  gambar_cover   VARCHAR(255) NULL,           -- khusus album
  album_asal     VARCHAR(150) NULL            -- khusus single, cukup teks nama album
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 4. berita
--    Dipakai: index.html (Berita Terbaru), berita-media.html
--             (Artikel Utama + Berita Terbaru + pagination + pencarian ?q=)
-- ---------------------------------------------------------------------
CREATE TABLE berita (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  judul           VARCHAR(200) NOT NULL,
  kategori        VARCHAR(50)  NOT NULL,      -- 'Album', 'Tur', 'Musik', 'Pengumuman', dst.
  ringkasan       TEXT         NOT NULL,
  gambar          VARCHAR(255) NULL,
  tanggal_terbit  DATE         NOT NULL,
  jadi_utama      TINYINT(1)   NOT NULL DEFAULT 0   -- 1 = tampil besar di Artikel Utama
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 5. galeri
--    Dipakai: berita-media.html (Di Balik Layar + Galeri Foto)
-- ---------------------------------------------------------------------
CREATE TABLE galeri (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  bagian      VARCHAR(20)  NOT NULL,          -- 'galeri_foto' / 'di_balik_layar'
  gambar      VARCHAR(255) NOT NULL,
  judul       VARCHAR(150) NULL,              -- dipakai di Di Balik Layar
  keterangan  VARCHAR(255) NULL,              -- dipakai di Di Balik Layar
  jenis       VARCHAR(10)  NOT NULL DEFAULT 'Foto',  -- 'Foto' / 'Video'
  kategori    VARCHAR(50)  NULL               -- tombol filter: 'Peterpan Era', 'NOAH Era', 'Konser', 'Studio'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 6. produk
--    Dipakai: toko.html (Grid Produk + Edisi Terbatas + sidebar filter)
-- ---------------------------------------------------------------------
CREATE TABLE produk (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  nama_produk  VARCHAR(150) NOT NULL,
  kategori     VARCHAR(50)  NOT NULL,         -- 'Kaos', 'Hoodie', 'Jaket', 'Aksesoris', 'CD & Vinyl'
  deskripsi    TEXT         NOT NULL,
  harga        INT          NOT NULL,         -- rupiah tanpa titik: 250000
  gambar       VARCHAR(255) NOT NULL,
  label        VARCHAR(20)  NULL,             -- 'Baru' / 'Terbatas' / kosong
  stok         INT          NULL              -- diisi untuk edisi terbatas ("Sisa 23 item")
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 7. kiriman_sahabat
--    Dipakai: sahabat-noah.html
--      - Form "Kirim Cerita atau Karyamu"  -> INSERT
--      - Section Cerita Sahabat            -> SELECT kategori 'Cerita Penggemar'
--      - Section Karya Sahabat             -> SELECT kategori 'Karya Penggemar'
--    Kiriman baru berstatus 'menunggu'. Ubah ke 'tampil' lewat phpMyAdmin
--    supaya muncul di halaman.
-- ---------------------------------------------------------------------
CREATE TABLE kiriman_sahabat (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  nama_lengkap  VARCHAR(100) NOT NULL,
  email         VARCHAR(150) NOT NULL,
  kategori      VARCHAR(30)  NOT NULL,        -- 'Cerita Penggemar' / 'Karya Penggemar' / 'Cover Lagu'
  pesan         TEXT         NOT NULL,
  kota          VARCHAR(100) NULL,
  gambar        VARCHAR(255) NULL,            -- foto profil (cerita) atau gambar karya
  status        VARCHAR(10)  NOT NULL DEFAULT 'menunggu',  -- 'menunggu' / 'tampil'
  waktu_kirim   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 8. pemesanan_event
--    Dipakai: tur-event.html (Form Pemesanan & Kerja Sama) -> INSERT
-- ---------------------------------------------------------------------
CREATE TABLE pemesanan_event (
  id                  INT AUTO_INCREMENT PRIMARY KEY,
  nama_organisasi     VARCHAR(150) NOT NULL,
  email               VARCHAR(150) NOT NULL,
  telepon             VARCHAR(20)  NOT NULL,
  tanggal_acara       DATE         NULL,
  tipe_acara          VARCHAR(30)  NOT NULL,  -- 'Festival Musik' / 'Acara Korporat' / 'Konser Privat'
  estimasi_kehadiran  INT          NULL,
  venue_lokasi        VARCHAR(200) NULL,
  rentang_anggaran    VARCHAR(100) NULL,      -- opsional di form
  catatan             TEXT         NULL,
  waktu_kirim         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 9. pelanggan_email
--    Dipakai: index.html (form Berlangganan)
--             tur-event.html (form "Beritahu Saya" di banner hiatus)
--    UNIQUE supaya satu email tidak tercatat dua kali.
-- ---------------------------------------------------------------------
CREATE TABLE pelanggan_email (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  email        VARCHAR(150) NOT NULL UNIQUE,
  asal_form    VARCHAR(20)  NOT NULL,         -- 'beranda' / 'event'
  waktu_daftar DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 10. admin
--     Dipakai: admin/login.php
--     Kolom password berisi HASH dari password_hash(), bukan password asli.
--     Kalau database bocor, password asli tetap tidak terbaca.
-- ---------------------------------------------------------------------
CREATE TABLE admin (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  username  VARCHAR(50)  NOT NULL UNIQUE,
  password  VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =====================================================================
-- DATA AWAL (diambil langsung dari isi HTML yang sekarang)
-- Tabel form (kiriman_sahabat bagian form, pemesanan_event,
-- pelanggan_email) tidak diisi karena datanya datang dari pengunjung.
-- =====================================================================

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

INSERT INTO setlist (judul, venue, tanggal, tamu, lagu_utama, lagu_encore) VALUES
('The Great Journey of NOAH - Jakarta (2023)', 'Ancol Beach City International Stadium', '2023-12-03', 'Reza (Drummer Asli)',
 'Topeng\nAda Gajah Dibalik Batu (Intro)\nSeparuh Aku\nMungkin Nanti\nMenghapus Jejakmu\nTak Bisakah\nYang Terdalam\nBintang di Surga\nKhayalan Tingkat Tinggi\nWanitaku',
 'Walau Habis Terang\nMimpi Yang Sempurna'),
('Tur Dunia 2015 - Los Angeles (AS)', NULL, NULL, NULL, NULL, NULL),
('Asia Song Festival - Busan (Korea)', NULL, NULL, NULL, NULL, NULL);

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

INSERT INTO berita (judul, kategori, ringkasan, gambar, tanggal_terbit, jadi_utama) VALUES
('NOAH Umumkan Hiatus: Apa yang Terjadi Selanjutnya?', 'Pengumuman', 'Setelah dekade penuh kesuksesan, Ariel dan kawan-kawan memutuskan untuk mengambil jeda panjang untuk eksplorasi kreatif baru.', NULL, '2024-01-15', 1),
('Kolaborasi Spesial NOAH dengan Orkestra Nasional', 'Musik', 'Sebuah mahakarya baru yang menggabungkan elemen rock alternatif dengan kemegahan simfoni klasik.', 'images/news-1.jpg', '2024-01-12', 0),
('Tiket Konser ''The Final Show'' Terjual Habis dalam 5 Menit', 'Konser', 'Antusiasme penggemar memuncak menyambut pertunjukan terakhir sebelum masa hiatus resmi dimulai.', 'images/news-2.jpg', '2024-01-08', 0),
('Ariel Berbagi Cerita Dibalik Lirik Lagu Teranyar', 'Pribadi', 'Wawancara eksklusif mengenai proses kreatif dan refleksi perjalanan karir musiknya.', 'images/news-3.jpg', '2024-01-05', 0),
('Dibalik Layar Pembuatan Aransemen Baru "Keterkaitan Keterikatan"', 'Album', 'Eksplorasi sound terbaru NOAH yang menggabungkan elemen orkestra dengan synthesizer modern...', 'images/news-1.jpg', '2024-09-12', 0),
('Pengalaman Tak Terlupakan Tur "The Great Journey" di Singapura', 'Tur', 'Ribuan Sahabat NOAH memadati Star Performing Arts Centre dalam malam yang penuh haru...', 'images/news-2.jpg', '2024-09-05', 0),
('Eksklusif: Sesi Bermusik NOAH Bersama Musisi Legendaris Mancanegara', 'Media', 'Kolaborasi lintas genre yang memberikan warna baru pada lagu-lagu klasik NOAH...', 'images/news-3.jpg', '2024-08-28', 0);

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

INSERT INTO produk (nama_produk, kategori, deskripsi, harga, gambar, label, stok) VALUES
('NOAH Logo Tee Black',                 'Kaos',       'Kaos katun premium dengan logo NOAH minimalis.',            250000, 'images/product-1.jpg', 'Baru',     NULL),
('NOAH Logo Tee White',                 'Kaos',       'Varian warna putih ikonik untuk tampilan bersih.',          250000, 'images/product-2.jpg', NULL,       NULL),
('Bintang di Surga Anniversary Hoodie', 'Hoodie',     'Edisi spesial perayaan album legendaris.',                  550000, 'images/product-3.jpg', 'Terbatas', NULL),
('NOAH Snapback Cap',                   'Aksesoris',  'Topi snapback kualitas premium dengan bordir 3D.',          185000, 'images/product-4.jpg', NULL,       NULL),
('Keterkaitan Keterikatan Vinyl',       'CD & Vinyl', 'Piringan hitam kualitas audiophile untuk kolektor.',        450000, 'images/product-5.jpg', NULL,       NULL),
('NOAH Tote Bag',                       'Aksesoris',  'Tas kanvas tebal tahan lama untuk kegiatan sehari-hari.',   120000, 'images/product-6.jpg', NULL,       NULL),
('Box Set Edisi Terbatas',              'CD & Vinyl', 'Paket eksklusif berisi 3 vinyl, buku foto dokumentasi tur, dan sertifikat keaslian bertandatangan personil.', 1500000, 'images/limited-1.jpg', 'Terbatas', 23),
('Peterpan Legacy Tee',                 'Kaos',       'Kaos edisi khusus mengenang perjalanan awal karir dengan desain grafis arsip original tahun 2003.', 350000, 'images/limited-2.jpg', 'Terbatas', 12);

-- Cerita Sahabat yang sudah tampil di HTML, dimasukkan sebagai kiriman berstatus 'tampil'.
-- Email aslinya tidak ada di HTML, jadi diisi alamat contoh.
INSERT INTO kiriman_sahabat (nama_lengkap, email, kategori, pesan, kota, gambar, status) VALUES
('Andhika Prasetya', 'contoh1@example.com', 'Cerita Penggemar', 'NOAH mengubah hidup saya. Lirik-liriknya selalu menemani saat saya berada di titik terendah.', 'Jakarta', 'images/fan-1.jpg', 'tampil'),
('Siti Rahma',       'contoh2@example.com', 'Cerita Penggemar', 'Menemukan keluarga baru di komunitas ini. Sahabat NOAH Bandung sangat suportif dan kreatif.', 'Bandung', 'images/fan-2.jpg', 'tampil'),
('Reza Kurniawan',   'contoh3@example.com', 'Cerita Penggemar', 'Koleksi produk resmi saya sudah mencapai 100 item. Semua berawal dari kecintaan pada melodi Ariel.', 'Surabaya', 'images/fan-3.jpg', 'tampil');

-- Akun admin awal untuk latihan.
--   username : admin
--   password : noah-30a23f
-- Ganti password setelah login pertama dengan membuat hash baru:
--   php -r "echo password_hash('password_baru', PASSWORD_DEFAULT);"
-- lalu tempel hasilnya ke kolom password lewat phpMyAdmin.
INSERT INTO admin (username, password) VALUES
('admin', '$2y$10$AUK9C0E7t9jkt1Rda/2OJ.11XkNSsl2oNz/vFnu/jhyCC5p/9uwCW');
