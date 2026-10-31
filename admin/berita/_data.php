<?php
// Aturan data berita, dipakai bersama oleh tambah.php dan edit.php.

// Pilihan kategori. Diambil dari pill yang ada di HTML website.
const KATEGORI_BERITA = ['Pengumuman', 'Album', 'Tur', 'Media', 'Musik', 'Konser', 'Pribadi'];

/**
 * Ambil isi form dari $_POST lalu periksa.
 * Mengembalikan dua hal: [data yang sudah dirapikan, daftar pesan error].
 * Kalau daftar error kosong, berarti data aman untuk disimpan.
 */
function baca_input_berita(): array
{
    $data = [
        'judul'          => trim($_POST['judul'] ?? ''),
        'kategori'       => $_POST['kategori'] ?? '',
        'ringkasan'      => trim($_POST['ringkasan'] ?? ''),
        'tanggal_terbit' => $_POST['tanggal_terbit'] ?? '',
        'jadi_utama'     => isset($_POST['jadi_utama']) ? 1 : 0,   // checkbox: dicentang = 1
    ];

    $error = [];
    if ($data['judul'] === '' || mb_strlen($data['judul']) > 200) {
        $error[] = 'Judul wajib diisi, maksimal 200 huruf.';
    }
    if (!in_array($data['kategori'], KATEGORI_BERITA, true)) {
        $error[] = 'Pilih kategori dari daftar.';
    }
    if ($data['ringkasan'] === '') {
        $error[] = 'Ringkasan wajib diisi.';
    }
    $tanggal = DateTime::createFromFormat('Y-m-d', $data['tanggal_terbit']);
    if (!$tanggal || $tanggal->format('Y-m-d') !== $data['tanggal_terbit']) {
        $error[] = 'Tanggal terbit tidak valid.';
    }

    return [$data, $error];
}

/**
 * Hanya boleh ada satu Artikel Utama.
 * Kalau berita ini dicentang sebagai utama, lepas tanda utama dari berita lain dulu.
 */
function lepas_berita_utama(mysqli $koneksi): void
{
    mysqli_query($koneksi, 'UPDATE berita SET jadi_utama = 0 WHERE jadi_utama = 1');
}
