-- Database website NOAH
-- Import lewat phpMyAdmin: tab Import > pilih file ini > Go.
-- Kalau database noah_db sudah ada, hapus dulu (Drop) supaya tidak bentrok.
-- 13 tabel, semuanya berdiri sendiri (tanpa relasi / JOIN).

CREATE DATABASE IF NOT EXISTS noah_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE noah_db;


-- 1. event: Konser Mendatang, Arsip Event, Event Komunitas
CREATE TABLE event (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  jenis       ENUM('konser','arsip','komunitas') NOT NULL,
  judul       VARCHAR(150) NOT NULL,
  tanggal     DATE         NULL,              -- arsip lama boleh kosong
  kota        VARCHAR(255) NULL,              -- arsip: beberapa kota dipisah koma
  venue       VARCHAR(150) NULL,
  deskripsi   TEXT         NULL,
  label       VARCHAR(50)  NULL               -- badge di card, contoh: Tersedia
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. setlist: Arsip Setlist di halaman Event
CREATE TABLE setlist (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  judul        VARCHAR(150) NOT NULL,
  venue        VARCHAR(150) NULL,
  tanggal      DATE         NULL,
  tamu         VARCHAR(150) NULL,
  lagu_utama   TEXT         NULL,             -- satu lagu per baris
  lagu_encore  TEXT         NULL              -- satu lagu per baris
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. album: Grid Album di halaman Diskografi
CREATE TABLE album (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  judul          VARCHAR(150) NOT NULL,
  tahun_rilis    YEAR         NOT NULL,
  kategori       ENUM('Studio','Kompilasi','Lagu Tema') NOT NULL,
  jumlah_lagu    INT          NOT NULL,
  lagu_unggulan  TEXT         NULL,           -- 3 lagu di card, satu per baris
  foto_album     VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. single: Tabel Single di halaman Diskografi
CREATE TABLE `single` (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  judul        VARCHAR(150) NOT NULL,
  tahun_rilis  YEAR         NOT NULL,
  album_asal   VARCHAR(150) NULL              -- teks biasa, contoh: OST Alexandria
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. berita: Artikel Utama dan Berita Terbaru
CREATE TABLE berita (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  judul           VARCHAR(200) NOT NULL,
  kategori        VARCHAR(50)  NOT NULL,
  ringkasan       TEXT         NOT NULL,
  gambar          VARCHAR(255) NULL,
  tanggal_terbit  DATE         NOT NULL,
  jadi_utama      BOOLEAN      NOT NULL DEFAULT FALSE   -- TRUE = Artikel Utama
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. galeri: Di Balik Layar dan Galeri Foto
CREATE TABLE galeri (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  bagian      ENUM('galeri_foto','di_balik_layar') NOT NULL,
  gambar      VARCHAR(255) NOT NULL,
  judul       VARCHAR(150) NULL,              -- khusus di_balik_layar
  keterangan  VARCHAR(255) NULL,              -- khusus di_balik_layar
  jenis       ENUM('Foto','Video') NOT NULL DEFAULT 'Foto',
  kategori    ENUM('Peterpan Era','NOAH Era','Konser','Studio') NULL   -- tombol filter
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. produk: Toko dan Edisi Terbatas
CREATE TABLE produk (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  nama_produk     VARCHAR(150) NOT NULL,
  kategori        VARCHAR(50)  NOT NULL,
  deskripsi       TEXT         NOT NULL,
  harga           INT          NOT NULL,      -- tanpa titik: 250000
  gambar          VARCHAR(255) NOT NULL,
  produk_baru     BOOLEAN      NOT NULL DEFAULT FALSE,   -- TRUE = badge Baru
  edisi_terbatas  BOOLEAN      NOT NULL DEFAULT FALSE,   -- TRUE = badge Terbatas
  stok            INT          NULL           -- ditampilkan untuk edisi terbatas
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. kiriman_sahabat: form Kirim Cerita, Cerita Sahabat, Karya Sahabat
CREATE TABLE kiriman_sahabat (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  nama_lengkap  VARCHAR(100) NOT NULL,
  email         VARCHAR(150) NOT NULL,
  kota          VARCHAR(100) NULL,
  kategori      ENUM('Cerita Penggemar','Karya Penggemar','Cover Lagu') NOT NULL,
  pesan         TEXT         NOT NULL,
  foto          VARCHAR(255) NULL,            -- foto profil (cerita) atau foto karya
  status        ENUM('menunggu','tampil') NOT NULL DEFAULT 'menunggu',
  waktu_kirim   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. pemesanan_event: form Pemesanan & Kerja Sama
CREATE TABLE pemesanan_event (
  id                  INT AUTO_INCREMENT PRIMARY KEY,
  nama_organisasi     VARCHAR(150) NOT NULL,
  email               VARCHAR(150) NOT NULL,
  telepon             VARCHAR(20)  NOT NULL,
  tanggal_acara       DATE         NULL,
  tipe_acara          ENUM('Festival Musik','Acara Korporat','Konser Privat') NOT NULL,
  estimasi_kehadiran  INT          NULL,
  venue_lokasi        VARCHAR(200) NULL,
  rentang_anggaran    VARCHAR(100) NULL,
  catatan             TEXT         NULL,
  waktu_kirim         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. pelanggan_email: form Berlangganan dan Beritahu Saya
CREATE TABLE pelanggan_email (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  email         VARCHAR(150) NOT NULL UNIQUE,
  asal_form     ENUM('beranda','event') NOT NULL,
  waktu_daftar  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. anggota: Kenali Anggota di halaman Cerita Band
CREATE TABLE anggota (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  nama       VARCHAR(100) NOT NULL,
  posisi     VARCHAR(100) NOT NULL,
  deskripsi  TEXT         NOT NULL,
  foto       VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. komunitas_regional: Komunitas Regional di halaman Sahabat NOAH
CREATE TABLE komunitas_regional (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  kota            VARCHAR(100) NOT NULL,
  jumlah_anggota  INT          NOT NULL DEFAULT 0,
  ketua           VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. admin: login admin panel
CREATE TABLE admin (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  username  VARCHAR(50)  NOT NULL UNIQUE,
  password  VARCHAR(255) NOT NULL             -- hash dari password_hash()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =====================================================================
-- DATA AWAL
-- \n = pindah baris, '' = tanda kutip di dalam teks
-- =====================================================================

-- Konser = contoh jadwal (website fan). Web hanya menampilkan konser yang tanggalnya belum lewat.
INSERT INTO event (jenis, judul, tanggal, kota, venue, deskripsi, label) VALUES
('konser',    'Jakarta',  '2027-08-24', 'Jakarta',  'JIExpo Kemayoran',           'Malam karya NOAH dengan sesi arsip visual, bernyanyi bersama, dan ruang temu Sahabat.', 'Tersedia'),
('konser',    'Bandung',  '2027-09-12', 'Bandung',  'SABUGA',                     'Temu Sahabat NOAH Bandung dengan pemutaran arsip konser dan sesi musik akustik.', 'Tersedia'),
('konser',    'Surabaya', '2027-10-03', 'Surabaya', 'Grand City Convention Hall', 'Malam tribut NOAH bersama komunitas regional dan kurasi lagu lintas era.', 'Tersedia'),
('arsip',     '6 Kota dalam 24 Jam (2004)', NULL,    'Medan, Padang, Pekanbaru, Lampung, Semarang, Surabaya', NULL, NULL, 'EVENT BERSEJARAH'),
('arsip',     '5 Negara dalam 24 Jam', '2012-11-15', 'Melbourne, Hong Kong, Kuala Lumpur, Singapore, Jakarta', NULL, NULL, NULL),
('arsip',     'Tur AS (2015)', NULL,                 'Los Angeles, San Francisco, Washington DC, New York', NULL, NULL, 'TUR DUNIA'),
('komunitas', 'Sahabat NOAH Nasional', '2024-10-24', 'Jakarta', NULL, 'Pertemuan akbar tahunan seluruh komunitas wilayah di Jakarta. Sesi bermain musik bersama, diskusi, dan kejutan spesial.', 'Gathnas 2024'),
-- tahun 2024 perkiraan, di HTML hanya tertulis "12 November"
('komunitas', 'Lokakarya Musik & Kopi', '2024-11-12', 'Bandung', NULL, 'Bedah instrumen lagu-lagu NOAH bersama musisi lokal dan sesi berbagi antar Sahabat.', NULL);

INSERT INTO setlist (judul, venue, tanggal, tamu, lagu_utama, lagu_encore) VALUES
('The Great Journey of NOAH - Jakarta (2023)', 'Ancol Beach City International Stadium', '2023-12-03', 'Reza (Drummer Asli)',
 'Topeng\nAda Gajah Dibalik Batu (Intro)\nSeparuh Aku\nMungkin Nanti\nMenghapus Jejakmu\nTak Bisakah\nYang Terdalam\nBintang di Surga\nKhayalan Tingkat Tinggi\nWanitaku',
 'Walau Habis Terang\nMimpi Yang Sempurna'),
('Tur Dunia 2015 - Los Angeles (AS)', NULL, NULL, NULL, NULL, NULL),
('Asia Song Festival - Busan (Korea)', NULL, NULL, NULL, NULL, NULL);

INSERT INTO album (judul, tahun_rilis, kategori, jumlah_lagu, lagu_unggulan, foto_album) VALUES
('Keterkaitan Keterikatan',    2019, 'Studio',    8,  'Wanitaku\nKupeluk Hatimu\nMendekati Lugu',                 'images/album-1.jpg'),
('Second Chance',              2014, 'Studio',    12, 'Hero\nSeperti Kemarin\nSuara Pikiranku',                   'images/album-2.jpg'),
('Sings Legends',              2016, 'Kompilasi', 9,  'Andaikan Kau Datang\nSajadah Panjang\nBiarlah Sendiri',    'images/album-3.jpg'),
('Seperti Seharusnya',         2012, 'Studio',    10, 'Separuh Aku\nHidup Untukmu, Mati Tanpamu\nJika Engkau',    'images/album-cover.jpg'),
('Bintang di Surga',           2004, 'Studio',    10, 'Mungkin Nanti\nAda Apa Denganmu\nKukatakan Dengan Indah',  'images/gallery-2.jpg'),
('Sebuah Nama, Sebuah Cerita', 2008, 'Kompilasi', 11, 'Menunggumu\nTak Bisakah\nWalau Habis Terang',              'images/gallery-4.jpg');

INSERT INTO `single` (judul, tahun_rilis, album_asal) VALUES
('Separuh Aku',             2012, 'Seperti Seharusnya'),
('Tak Bisakah',             2005, 'OST Alexandria'),
('Menunggumu',              2008, 'Sebuah Nama, Sebuah Cerita'),
('Khayalan Tingkat Tinggi', 2004, 'Bintang di Surga'),
('Wanitaku',                2019, 'Keterkaitan Keterikatan');

INSERT INTO berita (judul, kategori, ringkasan, gambar, tanggal_terbit, jadi_utama) VALUES
('NOAH Umumkan Hiatus: Apa yang Terjadi Selanjutnya?', 'Pengumuman', 'Setelah dekade penuh kesuksesan, Ariel dan kawan-kawan memutuskan untuk mengambil jeda panjang untuk eksplorasi kreatif baru.', 'images/hero-news.jpg', '2024-01-15', TRUE),
('Kolaborasi Spesial NOAH dengan Orkestra Nasional', 'Musik', 'Sebuah mahakarya baru yang menggabungkan elemen rock alternatif dengan kemegahan simfoni klasik.', 'images/news-1.jpg', '2024-01-12', FALSE),
('Tiket Konser ''The Final Show'' Terjual Habis dalam 5 Menit', 'Konser', 'Antusiasme penggemar memuncak menyambut pertunjukan terakhir sebelum masa hiatus resmi dimulai.', 'images/news-2.jpg', '2024-01-08', FALSE),
('Ariel Berbagi Cerita Dibalik Lirik Lagu Teranyar', 'Pribadi', 'Wawancara eksklusif mengenai proses kreatif dan refleksi perjalanan karir musiknya.', 'images/news-3.jpg', '2024-01-05', FALSE),
('Dibalik Layar Pembuatan Aransemen Baru "Keterkaitan Keterikatan"', 'Album', 'Eksplorasi sound terbaru NOAH yang menggabungkan elemen orkestra dengan synthesizer modern...', 'images/news-1.jpg', '2024-09-12', FALSE),
('Pengalaman Tak Terlupakan Tur "The Great Journey" di Singapura', 'Tur', 'Ribuan Sahabat NOAH memadati Star Performing Arts Centre dalam malam yang penuh haru...', 'images/news-2.jpg', '2024-09-05', FALSE),
('Eksklusif: Sesi Bermusik NOAH Bersama Musisi Legendaris Mancanegara', 'Media', 'Kolaborasi lintas genre yang memberikan warna baru pada lagu-lagu klasik NOAH...', 'images/news-3.jpg', '2024-08-28', FALSE);

INSERT INTO galeri (bagian, gambar, judul, keterangan, jenis, kategori) VALUES
('di_balik_layar', 'images/gallery-1.jpg', 'Proses Rekaman Studio',    'Melihat lebih dekat pembuatan album terakhir.', 'Video', NULL),
('di_balik_layar', 'images/gallery-2.jpg', 'Momen Belakang Panggung',  'Persiapan sebelum naik ke panggung megah.',     'Foto',  NULL),
('di_balik_layar', 'images/gallery-3.jpg', 'Pembuatan ''Wanitaku''',   'Eksplorasi desain suara yang mendalam.',        'Video', NULL),
('galeri_foto', 'images/gallery-1.jpg', NULL, NULL, 'Foto', 'Konser'),
('galeri_foto', 'images/gallery-2.jpg', NULL, NULL, 'Foto', 'NOAH Era'),
('galeri_foto', 'images/gallery-3.jpg', NULL, NULL, 'Foto', 'Studio'),
('galeri_foto', 'images/gallery-4.jpg', NULL, NULL, 'Foto', 'NOAH Era'),
('galeri_foto', 'images/feed-1.jpg',    NULL, NULL, 'Foto', 'Konser'),
('galeri_foto', 'images/feed-2.jpg',    NULL, NULL, 'Foto', 'Konser'),
('galeri_foto', 'images/feed-3.jpg',    NULL, NULL, 'Foto', 'Peterpan Era'),
('galeri_foto', 'images/feed-4.jpg',    NULL, NULL, 'Foto', 'Studio');

INSERT INTO produk (nama_produk, kategori, deskripsi, harga, gambar, produk_baru, edisi_terbatas, stok) VALUES
('NOAH Logo Tee Black',                 'Kaos',       'Kaos katun premium dengan logo NOAH minimalis.',            250000, 'images/product-1.jpg', TRUE,  FALSE, NULL),
('NOAH Logo Tee White',                 'Kaos',       'Varian warna putih ikonik untuk tampilan bersih.',          250000, 'images/product-2.jpg', FALSE, FALSE, NULL),
('Bintang di Surga Anniversary Hoodie', 'Hoodie',     'Edisi spesial perayaan album legendaris.',                  550000, 'images/product-3.jpg', FALSE, TRUE,  NULL),
('NOAH Snapback Cap',                   'Aksesoris',  'Topi snapback kualitas premium dengan bordir 3D.',          185000, 'images/product-4.jpg', FALSE, FALSE, NULL),
('Keterkaitan Keterikatan Vinyl',       'CD & Vinyl', 'Piringan hitam kualitas audiophile untuk kolektor.',        450000, 'images/product-5.jpg', FALSE, FALSE, NULL),
('NOAH Tote Bag',                       'Aksesoris',  'Tas kanvas tebal tahan lama untuk kegiatan sehari-hari.',   120000, 'images/product-6.jpg', FALSE, FALSE, NULL),
('Box Set Edisi Terbatas',              'CD & Vinyl', 'Paket eksklusif berisi 3 vinyl, buku foto dokumentasi tur, dan sertifikat keaslian bertandatangan personil.', 1500000, 'images/limited-1.jpg', FALSE, TRUE, 23),
('Peterpan Legacy Tee',                 'Kaos',       'Kaos edisi khusus mengenang perjalanan awal karir dengan desain grafis arsip original tahun 2003.', 350000, 'images/limited-2.jpg', FALSE, TRUE, 12);

-- email contoh, aslinya tidak ada di HTML
INSERT INTO kiriman_sahabat (nama_lengkap, email, kota, kategori, pesan, foto, status) VALUES
('Andhika Prasetya', 'contoh1@example.com', 'Jakarta',  'Cerita Penggemar', 'NOAH mengubah hidup saya. Lirik-liriknya selalu menemani saat saya berada di titik terendah.', 'images/fan-1.jpg', 'tampil'),
('Siti Rahma',       'contoh2@example.com', 'Bandung',  'Cerita Penggemar', 'Menemukan keluarga baru di komunitas ini. Sahabat NOAH Bandung sangat suportif dan kreatif.', 'images/fan-2.jpg', 'tampil'),
('Reza Kurniawan',   'contoh3@example.com', 'Surabaya', 'Cerita Penggemar', 'Koleksi produk resmi saya sudah mencapai 100 item. Semua berawal dari kecintaan pada melodi Ariel.', 'images/fan-3.jpg', 'tampil'),
('Dewi Lestari',     'contoh4@example.com', NULL, 'Karya Penggemar', 'Ilustrasi digital terinspirasi sampul album NOAH.', 'images/artwork-1.jpg', 'tampil'),
('Fajar Nugroho',    'contoh5@example.com', NULL, 'Karya Penggemar', 'Foto still life gitar dan ampli, tribut untuk Lukman.', 'images/artwork-2.jpg', 'tampil'),
('Nadia Putri',      'contoh6@example.com', NULL, 'Karya Penggemar', 'Desain ulang logo NOAH dengan gaya tajam.', 'images/artwork-3.jpg', 'tampil'),
('Bagas Saputra',    'contoh7@example.com', NULL, 'Karya Penggemar', 'Lukisan digital Ariel di atas panggung.', 'images/artwork-4.jpg', 'tampil');

INSERT INTO anggota (nama, posisi, deskripsi, foto) VALUES
('Ariel',  'Vokal / Frontman', 'Sang visioner dan penulis lirik utama yang suaranya telah menjadi lagu tema bagi jutaan orang.', 'images/member-ariel.jpg'),
('Lukman', 'Gitar',            'Arsitek melodi gitar yang memberikan nuansa atmosferik khas pada setiap lagu NOAH.',             'images/member-lukman.jpg'),
('David',  'Keyboard',         'Master harmonisasi yang membawa sentuhan modern lewat permainan keyboard yang elegan.',          'images/member-david.jpg');

INSERT INTO komunitas_regional (kota, jumlah_anggota, ketua) VALUES
('Jakarta',    8420, 'Hendry Kusuma'),
('Bandung',    6150, 'Asep Kurnia'),
('Surabaya',   4890, 'Budi Santoso'),
('Yogyakarta', 3720, 'Rarasati'),
('Medan',      2900, 'Iwan Ginting'),
('Makassar',   2140, 'Zulkifli');

-- akun latihan: username admin, password noah-30a23f (segera ganti)
INSERT INTO admin (username, password) VALUES
('admin', '$2y$10$AUK9C0E7t9jkt1Rda/2OJ.11XkNSsl2oNz/vFnu/jhyCC5p/9uwCW');
