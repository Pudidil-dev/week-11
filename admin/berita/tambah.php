<?php
// CREATE: tampilkan form kosong, lalu simpan berita baru.
require __DIR__ . '/../fungsi.php';
require __DIR__ . '/_data.php';
wajib_login();

// Isi awal form: kosong, tanggal terbit = hari ini.
$data = ['judul' => '', 'kategori' => '', 'ringkasan' => '', 'tanggal_terbit' => date('Y-m-d'), 'jadi_utama' => 0, 'gambar' => ''];
$error = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cek_token();
    [$data, $error] = baca_input_berita();

    if (!$error) {
        try {
            $gambar = upload_gambar('gambar', 'berita');
            $gambar = $gambar === '' ? null : $gambar;   // tidak ada file = simpan NULL

            if ($data['jadi_utama']) {
                lepas_berita_utama($koneksi);
            }

            $stmt = mysqli_prepare(
                $koneksi,
                'INSERT INTO berita (judul, kategori, ringkasan, gambar, tanggal_terbit, jadi_utama)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            // "sssssi" = 5 teks (string) lalu 1 angka (integer), urut sesuai tanda ?
            mysqli_stmt_bind_param(
                $stmt, 'sssssi',
                $data['judul'], $data['kategori'], $data['ringkasan'],
                $gambar, $data['tanggal_terbit'], $data['jadi_utama']
            );
            mysqli_stmt_execute($stmt);

            set_pesan('Berita berhasil ditambahkan.');
            header('Location: index.php');
            exit;
        } catch (Exception $e) {
            $error[] = $e->getMessage();
        }
    }
}

$judul_halaman = 'Tambah Berita';
$tombol = 'Simpan';
require __DIR__ . '/../_atas.php';
?>

<h1>Tambah Berita</h1>
<?php require __DIR__ . '/_form.php'; ?>

<?php require __DIR__ . '/../_bawah.php'; ?>
