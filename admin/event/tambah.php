<?php
// CREATE: tampilkan form kosong, lalu simpan event baru.
require __DIR__ . '/../fungsi.php';
require __DIR__ . '/_data.php';
wajib_login();

$data = ['jenis' => '', 'judul' => '', 'tanggal' => '', 'kota' => '', 'venue' => '', 'deskripsi' => '', 'label' => ''];
$error = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cek_token();
    [$data, $error] = baca_input_event();

    if (!$error) {
        $stmt = mysqli_prepare(
            $koneksi,
            'INSERT INTO event (jenis, judul, tanggal, kota, venue, deskripsi, label)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        // Kolom yang boleh kosong disimpan sebagai NULL.
        $tanggal   = kosong_jadi_null($data['tanggal']);
        $kota      = kosong_jadi_null($data['kota']);
        $venue     = kosong_jadi_null($data['venue']);
        $deskripsi = kosong_jadi_null($data['deskripsi']);
        $label     = kosong_jadi_null($data['label']);

        mysqli_stmt_bind_param(
            $stmt, 'sssssss',
            $data['jenis'], $data['judul'], $tanggal, $kota, $venue, $deskripsi, $label
        );
        mysqli_stmt_execute($stmt);

        set_pesan('Event berhasil ditambahkan.');
        header('Location: index.php');
        exit;
    }
}

$judul_halaman = 'Tambah Event';
$tombol = 'Simpan';
require __DIR__ . '/../_atas.php';
?>

<h1>Tambah Event</h1>
<?php require __DIR__ . '/_form.php'; ?>

<?php require __DIR__ . '/../_bawah.php'; ?>
