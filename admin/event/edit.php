<?php
// UPDATE: ambil event lama berdasarkan id, tampilkan di form, lalu simpan perubahan.
require __DIR__ . '/../fungsi.php';
require __DIR__ . '/_data.php';
wajib_login();

$id = (int) ($_GET['id'] ?? 0);

$stmt = mysqli_prepare($koneksi, 'SELECT * FROM event WHERE id = ?');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$lama = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$lama) {
    set_pesan('Event tidak ditemukan.');
    header('Location: index.php');
    exit;
}

$data = $lama;
$error = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cek_token();
    [$data, $error] = baca_input_event();

    if (!$error) {
        $stmt = mysqli_prepare(
            $koneksi,
            'UPDATE event
             SET jenis = ?, judul = ?, tanggal = ?, kota = ?, venue = ?, deskripsi = ?, label = ?
             WHERE id = ?'
        );
        $tanggal   = kosong_jadi_null($data['tanggal']);
        $kota      = kosong_jadi_null($data['kota']);
        $venue     = kosong_jadi_null($data['venue']);
        $deskripsi = kosong_jadi_null($data['deskripsi']);
        $label     = kosong_jadi_null($data['label']);

        mysqli_stmt_bind_param(
            $stmt, 'sssssssi',
            $data['jenis'], $data['judul'], $tanggal, $kota, $venue, $deskripsi, $label, $id
        );
        mysqli_stmt_execute($stmt);

        set_pesan('Event berhasil diperbarui.');
        header('Location: index.php');
        exit;
    }
}

$judul_halaman = 'Edit Event';
$tombol = 'Simpan Perubahan';
require __DIR__ . '/../_atas.php';
?>

<h1>Edit Event</h1>
<?php require __DIR__ . '/_form.php'; ?>

<?php require __DIR__ . '/../_bawah.php'; ?>
