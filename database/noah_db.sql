-- Database website NOAH
-- Import lewat phpMyAdmin: tab Import > pilih file ini > Go.
-- PERHATIAN: import ulang file ini menghapus semua tabel lama beserta isinya
-- (termasuk kiriman form), lalu membuat ulang dari awal.
--
-- 15 tabel. Kebanyakan berdiri sendiri. Yang punya relasi (FOREIGN KEY):
--   album   1 ── n  lagu_album     (lagu_album.album_id)
--   setlist 1 ── n  lagu_setlist   (lagu_setlist.setlist_id)
--   event   1 ── n  setlist        (setlist.event_id, boleh kosong)

CREATE DATABASE IF NOT EXISTS noah_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE noah_db;

-- Hapus tabel lama. Cek foreign key dimatikan sebentar supaya urutan hapus bebas.
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS event, setlist, lagu_setlist, diskografi, album, lagu_album, `single`,
                     berita, galeri, produk, kiriman_sahabat, pemesanan_event,
                     pelanggan_email, anggota, komunitas_regional, admin;
SET FOREIGN_KEY_CHECKS = 1;


-- 1. event: Konser Mendatang, Arsip Event, Event Komunitas
--    Satu tabel, dibedakan kolom jenis. Bentuk card-nya saja yang beda di website.
CREATE TABLE event (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  jenis       ENUM('konser','arsip','komunitas') NOT NULL,
  judul       VARCHAR(150) NOT NULL,
  tanggal     DATE         NULL,              -- arsip lama boleh kosong
  kota        VARCHAR(255) NULL,              -- arsip: beberapa kota dipisah koma
  venue       VARCHAR(150) NULL,
  deskripsi   TEXT         NULL,
  label       VARCHAR(50)  NULL               -- badge di card, contoh: Tersedia, TUR DUNIA
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. setlist: judul dan detail satu pertunjukan di Arsip Setlist
CREATE TABLE setlist (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  event_id  INT          NULL,                -- arsip event pemilik setlist ini (boleh kosong)
  judul     VARCHAR(150) NOT NULL,
  venue     VARCHAR(150) NULL,
  tanggal   DATE         NULL,
  tamu      VARCHAR(150) NULL,
  FOREIGN KEY (event_id) REFERENCES event(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. lagu_setlist: satu baris = satu lagu yang dibawakan
--    Ambil lagu untuk setlist 1: SELECT * FROM lagu_setlist WHERE setlist_id = 1 ORDER BY urutan
CREATE TABLE lagu_setlist (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  setlist_id  INT          NOT NULL,
  urutan      TINYINT UNSIGNED NOT NULL,      -- nomor lagu di website (1, 2, 3, ...)
  judul_lagu  VARCHAR(150) NOT NULL,
  bagian      ENUM('utama','encore') NOT NULL DEFAULT 'utama',
  FOREIGN KEY (setlist_id) REFERENCES setlist(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. album: grid album di halaman Diskografi
CREATE TABLE album (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  judul        VARCHAR(150) NOT NULL,
  tahun_rilis  YEAR         NOT NULL,
  kategori     ENUM('Studio','Kompilasi') NOT NULL,
  jumlah_lagu  TINYINT UNSIGNED NOT NULL,
  foto_album   VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. lagu_album: lagu unggulan yang tampil di card album (cukup 3 per album)
CREATE TABLE lagu_album (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  album_id    INT          NOT NULL,
  urutan      TINYINT UNSIGNED NOT NULL,
  judul_lagu  VARCHAR(150) NOT NULL,
  FOREIGN KEY (album_id) REFERENCES album(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. single: tabel Single di halaman Diskografi
--    album_asal berupa teks karena ada single dari luar album (contoh: OST Alexandria)
CREATE TABLE `single` (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  judul        VARCHAR(150) NOT NULL,
  tahun_rilis  YEAR         NOT NULL,
  album_asal   VARCHAR(150) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. berita: Artikel Utama dan Berita Terbaru (Beranda + Media)
CREATE TABLE berita (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  judul           VARCHAR(200) NOT NULL,
  kategori        VARCHAR(50)  NOT NULL,      -- Pengumuman / Album / Tur / Media / Musik / Konser / Pribadi
  ringkasan       TEXT         NOT NULL,
  gambar          VARCHAR(255) NULL,          -- artikel utama boleh tanpa gambar
  tanggal_terbit  DATE         NOT NULL,
  jadi_utama      BOOLEAN      NOT NULL DEFAULT FALSE   -- TRUE = tampil besar di Artikel Utama
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. galeri: Di Balik Layar dan Galeri Foto
CREATE TABLE galeri (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  bagian      ENUM('galeri_foto','di_balik_layar') NOT NULL,
  gambar      VARCHAR(255) NOT NULL,
  judul       VARCHAR(150) NULL,              -- khusus di_balik_layar
  keterangan  VARCHAR(255) NULL,              -- khusus di_balik_layar
  jenis       ENUM('Foto','Video') NOT NULL DEFAULT 'Foto',
  kategori    ENUM('Peterpan Era','NOAH Era','Konser','Studio') NULL   -- tombol filter Galeri Foto
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. produk: Toko dan Edisi Terbatas
--    Label "Baru" tidak disimpan: dihitung dari tanggal_ditambahkan (umur <= 14 hari).
CREATE TABLE produk (
  id                   INT AUTO_INCREMENT PRIMARY KEY,
  nama_produk          VARCHAR(150) NOT NULL,
  kategori             VARCHAR(50)  NOT NULL,   -- Kaos / Hoodie / Aksesoris / CD & Vinyl
  deskripsi            TEXT         NOT NULL,
  harga                INT UNSIGNED NOT NULL,   -- tanpa titik: 250000
  gambar               VARCHAR(255) NOT NULL,
  edisi_terbatas       BOOLEAN      NOT NULL DEFAULT FALSE,  -- TRUE = label Terbatas
  stok                 INT UNSIGNED NULL,       -- ditampilkan untuk edisi terbatas
  tanggal_ditambahkan  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. kiriman_sahabat: form Kirim Cerita atau Karya, Cerita Sahabat, Karya Sahabat
CREATE TABLE kiriman_sahabat (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  nama_lengkap  VARCHAR(100) NOT NULL,
  email         VARCHAR(150) NOT NULL,
  kategori      ENUM('Cerita Penggemar','Karya Penggemar','Cover Lagu') NOT NULL,
  pesan         TEXT         NOT NULL,
  foto_karya    VARCHAR(255) NULL,            -- wajib untuk Karya Penggemar
  status        ENUM('menunggu','tampil') NOT NULL DEFAULT 'menunggu',
  waktu_kirim   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. pemesanan_event: form Pemesanan & Kerja Sama
CREATE TABLE pemesanan_event (
  id                  INT AUTO_INCREMENT PRIMARY KEY,
  nama_organisasi     VARCHAR(150) NOT NULL,
  email               VARCHAR(150) NOT NULL,
  telepon             VARCHAR(20)  NOT NULL,   -- teks, karena bisa diawali 0 atau +62
  tanggal_acara       DATE         NULL,
  tipe_acara          ENUM('Festival Musik','Acara Korporat','Konser Privat') NOT NULL,
  estimasi_kehadiran  INT UNSIGNED NULL,
  venue_lokasi        VARCHAR(200) NULL,
  rentang_anggaran    VARCHAR(100) NULL,
  catatan             TEXT         NULL,
  waktu_kirim         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. pelanggan_email: form Berlangganan (Beranda) dan Beritahu Saya (Event)
CREATE TABLE pelanggan_email (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  email         VARCHAR(150) NOT NULL UNIQUE,
  asal_form     ENUM('beranda','event') NOT NULL,   -- form mana yang dipakai mendaftar
  waktu_daftar  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. anggota: Kenali Anggota di halaman Cerita Band
CREATE TABLE anggota (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  nama       VARCHAR(100) NOT NULL,
  posisi     VARCHAR(100) NOT NULL,
  deskripsi  TEXT         NOT NULL,
  foto       VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. komunitas_regional: Komunitas Regional di halaman Sahabat NOAH
CREATE TABLE komunitas_regional (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  kota            VARCHAR(100) NOT NULL UNIQUE,
  jumlah_anggota  INT UNSIGNED NOT NULL DEFAULT 0,
  ketua           VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. admin: login admin panel
CREATE TABLE admin (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  username  VARCHAR(50)  NOT NULL UNIQUE,
  password  VARCHAR(255) NOT NULL             -- hash dari password_hash()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =====================================================================
-- DATA AWAL
-- '' = tanda kutip di dalam teks
-- id ditulis manual supaya tabel relasi bisa merujuk ke id yang pasti.
-- =====================================================================

INSERT INTO event (id, jenis, judul, tanggal, kota, venue, deskripsi, label) VALUES
(1, 'konser',    'Jakarta',  '2026-08-24', 'Jakarta',  'JIExpo Kemayoran',           'Malam karya NOAH dengan sesi arsip visual, bernyanyi bersama, dan ruang temu Sahabat.', 'Tersedia'),
(2, 'konser',    'Bandung',  '2026-09-12', 'Bandung',  'SABUGA',                     'Temu Sahabat NOAH Bandung dengan pemutaran arsip konser dan sesi musik akustik.', 'Tersedia'),
(3, 'konser',    'Surabaya', '2026-10-03', 'Surabaya', 'Grand City Convention Hall', 'Malam tribut NOAH bersama komunitas regional dan kurasi lagu lintas era.', 'Tersedia'),
(4, 'arsip',     '6 Kota dalam 24 Jam (2004)', NULL,   'Medan, Padang, Pekanbaru, Lampung, Semarang, Surabaya', NULL, NULL, 'EVENT BERSEJARAH'),
(5, 'arsip',     '5 Negara dalam 24 Jam', '2012-11-15', 'Melbourne, Hong Kong, Kuala Lumpur, Singapore, Jakarta', NULL, NULL, NULL),
(6, 'arsip',     'Tur AS (2015)', NULL,                'Los Angeles, San Francisco, Washington DC, New York', NULL, NULL, 'TUR DUNIA'),
(7, 'komunitas', 'Sahabat NOAH Nasional', '2024-10-24', 'Jakarta', NULL, 'Pertemuan akbar tahunan seluruh komunitas wilayah di Jakarta. Sesi bermain musik bersama, diskusi, dan kejutan spesial.', 'Gathnas 2024'),
(8, 'komunitas', 'Lokakarya Musik & Kopi', '2024-11-12', 'Bandung', NULL, 'Bedah instrumen lagu-lagu NOAH bersama musisi lokal dan sesi berbagi antar Sahabat.', 'Bandung');

INSERT INTO setlist (id, event_id, judul, venue, tanggal, tamu) VALUES
(1, NULL, 'The Great Journey of NOAH - Jakarta (2023)', 'Ancol Beach City International Stadium', '2023-12-03', 'Reza (Drummer Asli)'),
(2, 6,    'Tur Dunia 2015 - Los Angeles (AS)', NULL, NULL, NULL),
(3, NULL, 'Asia Song Festival - Busan (Korea)', NULL, NULL, NULL);

INSERT INTO lagu_setlist (setlist_id, urutan, judul_lagu, bagian) VALUES
(1,  1, 'Topeng',                             'utama'),
(1,  2, 'Ada Gajah Dibalik Batu (Intro)',     'utama'),
(1,  3, 'Separuh Aku',                        'utama'),
(1,  4, 'Mungkin Nanti',                      'utama'),
(1,  5, 'Menghapus Jejakmu',                  'utama'),
(1,  6, 'Tak Bisakah',                        'utama'),
(1,  7, 'Yang Terdalam',                      'utama'),
(1,  8, 'Bintang di Surga',                   'utama'),
(1,  9, 'Khayalan Tingkat Tinggi',            'utama'),
(1, 10, 'Wanitaku',                           'utama'),
(1, 11, 'Walau Habis Terang',                 'encore'),
(1, 12, 'Mimpi Yang Sempurna',                'encore');

INSERT INTO album (id, judul, tahun_rilis, kategori, jumlah_lagu, foto_album) VALUES
(1, 'Keterkaitan Keterikatan',    2019, 'Studio',    8,  'images/album-1.jpg'),
(2, 'Second Chance',              2014, 'Studio',    12, 'images/album-2.jpg'),
(3, 'Sings Legends',              2016, 'Kompilasi', 9,  'images/album-3.jpg'),
(4, 'Seperti Seharusnya',         2012, 'Studio',    10, 'images/album-cover.jpg'),
(5, 'Bintang di Surga',           2004, 'Studio',    10, 'images/gallery-2.jpg'),
(6, 'Sebuah Nama, Sebuah Cerita', 2008, 'Kompilasi', 11, 'images/gallery-4.jpg');

INSERT INTO lagu_album (album_id, urutan, judul_lagu) VALUES
(1, 1, 'Wanitaku'),            (1, 2, 'Kupeluk Hatimu'),              (1, 3, 'Mendekati Lugu'),
(2, 1, 'Hero'),                (2, 2, 'Seperti Kemarin'),             (2, 3, 'Suara Pikiranku'),
(3, 1, 'Andaikan Kau Datang'), (3, 2, 'Sajadah Panjang'),             (3, 3, 'Biarlah Sendiri'),
(4, 1, 'Separuh Aku'),         (4, 2, 'Hidup Untukmu, Mati Tanpamu'), (4, 3, 'Jika Engkau'),
(5, 1, 'Mungkin Nanti'),       (5, 2, 'Ada Apa Denganmu'),            (5, 3, 'Kukatakan Dengan Indah'),
(6, 1, 'Menunggumu'),          (6, 2, 'Tak Bisakah'),                 (6, 3, 'Walau Habis Terang');

INSERT INTO `single` (judul, tahun_rilis, album_asal) VALUES
('Separuh Aku',             2012, 'Seperti Seharusnya'),
('Tak Bisakah',             2005, 'OST Alexandria'),
('Menunggumu',              2008, 'Sebuah Nama, Sebuah Cerita'),
('Khayalan Tingkat Tinggi', 2004, 'Bintang di Surga'),
('Wanitaku',                2019, 'Keterkaitan Keterikatan');

-- 7 berita: 3 dari Beranda (Sep 2024) + Artikel Utama + 3 dari halaman Media (Jan 2024).
-- Beranda menampilkan 3 terbaru; Media menampilkan semua dengan pagination 3 per halaman.
INSERT INTO berita (judul, kategori, ringkasan, gambar, tanggal_terbit, jadi_utama) VALUES
('NOAH Umumkan Hiatus: Apa yang Terjadi Selanjutnya?', 'Pengumuman', 'Setelah dekade penuh kesuksesan, Ariel dan kawan-kawan memutuskan untuk mengambil jeda panjang untuk eksplorasi kreatif baru.', 'images/hero-news.jpg', '2024-01-15', TRUE),
('Kolaborasi Spesial NOAH dengan Orkestra Nasional', 'Musik', 'Sebuah mahakarya baru yang menggabungkan elemen rock alternatif dengan kemegahan simfoni klasik.', 'images/news-1.jpg', '2024-01-12', FALSE),
('Tiket Konser ''The Final Show'' Terjual Habis dalam 5 Menit', 'Konser', 'Antusiasme penggemar memuncak menyambut pertunjukan terakhir sebelum masa hiatus resmi dimulai.', 'images/news-2.jpg', '2024-01-08', FALSE),
('Ariel Berbagi Cerita Dibalik Lirik Lagu Teranyar', 'Pribadi', 'Wawancara eksklusif mengenai proses kreatif dan refleksi perjalanan karir musiknya.', 'images/news-3.jpg', '2024-01-05', FALSE),
('Dibalik Layar Pembuatan Aransemen Baru "Keterkaitan Keterikatan"', 'Album', 'Eksplorasi sound terbaru NOAH yang menggabungkan elemen orkestra dengan synthesizer modern...', 'images/news-1.jpg', '2024-09-12', FALSE),
('Pengalaman Tak Terlupakan Tur "The Great Journey" di Singapura', 'Tur', 'Ribuan Sahabat NOAH memadati Star Performing Arts Centre dalam malam yang penuh haru...', 'images/news-2.jpg', '2024-09-05', FALSE),
('Eksklusif: Sesi Bermusik NOAH Bersama Musisi Legendaris Mancanegara', 'Media', 'Kolaborasi lintas genre yang memberikan warna baru pada lagu-lagu klasik NOAH...', 'images/news-3.jpg', '2024-08-28', FALSE);

-- kategori hanya untuk galeri_foto (tombol filter). Di Balik Layar tidak punya filter.
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

-- NOW() - INTERVAL 3 DAY: produk ini selalu terhitung "Baru" saat file di-import.
INSERT INTO produk (nama_produk, kategori, deskripsi, harga, gambar, edisi_terbatas, stok, tanggal_ditambahkan) VALUES
('NOAH Logo Tee Black',                 'Kaos',       'Kaos katun premium dengan logo NOAH minimalis.',            250000, 'images/product-1.jpg', FALSE, NULL, NOW() - INTERVAL 3 DAY),
('NOAH Logo Tee White',                 'Kaos',       'Varian warna putih ikonik untuk tampilan bersih.',          250000, 'images/product-2.jpg', FALSE, NULL, '2024-06-10 10:00:00'),
('Bintang di Surga Anniversary Hoodie', 'Hoodie',     'Edisi spesial perayaan album legendaris.',                  550000, 'images/product-3.jpg', TRUE,  NULL, '2024-06-05 10:00:00'),
('NOAH Snapback Cap',                   'Aksesoris',  'Topi snapback kualitas premium dengan bordir 3D.',          185000, 'images/product-4.jpg', FALSE, NULL, '2024-05-20 10:00:00'),
('Keterkaitan Keterikatan Vinyl',       'CD & Vinyl', 'Piringan hitam kualitas audiophile untuk kolektor.',        450000, 'images/product-5.jpg', FALSE, NULL, '2024-05-12 10:00:00'),
('NOAH Tote Bag',                       'Aksesoris',  'Tas kanvas tebal tahan lama untuk kegiatan sehari-hari.',   120000, 'images/product-6.jpg', FALSE, NULL, '2024-04-30 10:00:00'),
('Box Set Edisi Terbatas',              'CD & Vinyl', 'Paket eksklusif berisi 3 vinyl, buku foto dokumentasi tur, dan sertifikat keaslian bertandatangan personil.', 1500000, 'images/limited-1.jpg', TRUE, 23, '2024-04-15 10:00:00'),
('Peterpan Legacy Tee',                 'Kaos',       'Kaos edisi khusus mengenang perjalanan awal karir dengan desain grafis arsip original tahun 2003.', 350000, 'images/limited-2.jpg', TRUE, 12, '2024-04-01 10:00:00');

-- email contoh, aslinya tidak ada di HTML
INSERT INTO kiriman_sahabat (nama_lengkap, email, kategori, pesan, foto_karya, status) VALUES
('Andhika Prasetya', 'contoh1@example.com', 'Cerita Penggemar', 'NOAH mengubah hidup saya. Lirik-liriknya selalu menemani saat saya berada di titik terendah.', NULL, 'tampil'),
('Siti Rahma',       'contoh2@example.com', 'Cerita Penggemar', 'Menemukan keluarga baru di komunitas ini. Sahabat NOAH Bandung sangat suportif dan kreatif.', NULL, 'tampil'),
('Reza Kurniawan',   'contoh3@example.com', 'Cerita Penggemar', 'Koleksi produk resmi saya sudah mencapai 100 item. Semua berawal dari kecintaan pada melodi Ariel.', NULL, 'tampil'),
('Dewi Lestari',     'contoh4@example.com', 'Karya Penggemar',  'Ilustrasi digital terinspirasi sampul album NOAH.', 'images/artwork-1.jpg', 'tampil'),
('Fajar Nugroho',    'contoh5@example.com', 'Karya Penggemar',  'Foto still life gitar dan ampli, tribut untuk Lukman.', 'images/artwork-2.jpg', 'tampil'),
('Nadia Putri',      'contoh6@example.com', 'Karya Penggemar',  'Desain ulang logo NOAH dengan gaya tajam.', 'images/artwork-3.jpg', 'tampil'),
('Bagas Saputra',    'contoh7@example.com', 'Karya Penggemar',  'Lukisan digital Ariel di atas panggung.', 'images/artwork-4.jpg', 'tampil');

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
