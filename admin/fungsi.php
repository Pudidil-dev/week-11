<?php
// Fungsi bantu yang dipakai semua halaman admin.
// Setiap halaman admin cukup menulis: require __DIR__ . '/fungsi.php';

session_start();
require_once __DIR__ . '/../koneksi.php';

/**
 * Lempar ke halaman login kalau belum login.
 * Panggil di baris paling atas setiap halaman admin (kecuali login.php).
 */
function wajib_login(): void
{
    if (empty($_SESSION['admin_id'])) {
        header('Location: ' . url_admin('login.php'));
        exit;
    }
}

/** Buat alamat lengkap ke file di dalam folder admin. */
function url_admin(string $file): string
{
    return admin_base() . '/' . $file;
}

/** Cari alamat folder admin dari alamat halaman yang sedang dibuka. */
function admin_base(): string
{
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);   // contoh: /noah/admin/berita/index.php
    $posisi = strrpos($script, '/admin/');
    return substr($script, 0, $posisi + strlen('/admin'));       // hasil:  /noah/admin
}

/** Singkatan htmlspecialchars. Wajib dipakai saat menampilkan data dari database. */
function e(?string $teks): string
{
    return htmlspecialchars($teks ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Token CSRF: kode acak yang disimpan di session dan diselipkan di setiap form.
 * Tujuannya supaya form admin hanya bisa dikirim dari halaman admin sendiri,
 * bukan dari website lain yang memancing admin mengklik sesuatu.
 */
function input_token(): string
{
    if (empty($_SESSION['token'])) {
        $_SESSION['token'] = bin2hex(random_bytes(16));
    }
    return '<input type="hidden" name="token" value="' . $_SESSION['token'] . '">';
}

function cek_token(): void
{
    $token = $_POST['token'] ?? '';
    if (empty($_SESSION['token']) || !hash_equals($_SESSION['token'], $token)) {
        http_response_code(400);
        die('Form kedaluwarsa. Kembali dan coba kirim ulang.');
    }
}

/** Simpan pesan singkat untuk ditampilkan sekali di halaman berikutnya. */
function set_pesan(string $pesan): void
{
    $_SESSION['pesan'] = $pesan;
}

function ambil_pesan(): string
{
    $pesan = $_SESSION['pesan'] ?? '';
    unset($_SESSION['pesan']);
    return $pesan;
}

/**
 * Upload satu gambar ke folder images/.
 * Mengembalikan path seperti "images/berita-1730390000.jpg",
 * string kosong kalau tidak ada file yang dipilih,
 * atau melempar Exception kalau file tidak valid.
 */
function upload_gambar(string $nama_input, string $awalan): string
{
    $file = $_FILES[$nama_input] ?? null;

    if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return '';
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Upload gagal. Coba file lain.');
    }
    if ($file['size'] > 2 * 1024 * 1024) {
        throw new Exception('Ukuran gambar maksimal 2 MB.');
    }

    // Cek isi file yang sebenarnya, bukan cuma nama/ekstensinya.
    $tipe_asli = mime_content_type($file['tmp_name']);
    $ekstensi = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];
    if (!isset($ekstensi[$tipe_asli])) {
        throw new Exception('Gambar harus JPG, PNG, atau WEBP.');
    }

    // Nama file dibuat ulang supaya tidak bentrok dan tidak bisa disisipi nama aneh.
    $nama_baru = $awalan . '-' . time() . '-' . bin2hex(random_bytes(2)) . '.' . $ekstensi[$tipe_asli];
    $tujuan = __DIR__ . '/../images/' . $nama_baru;

    if (!move_uploaded_file($file['tmp_name'], $tujuan)) {
        throw new Exception('Gambar tidak bisa disimpan ke folder images/.');
    }
    return 'images/' . $nama_baru;
}
