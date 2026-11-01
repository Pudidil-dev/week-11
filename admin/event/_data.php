<?php
// Aturan data event, dipakai bersama oleh tambah.php dan edit.php.

// Nilai kolom "jenis" => teks yang dilihat admin di dropdown.
// Nilai di kiri yang disimpan ke database dan dipakai di WHERE jenis = '...'.
const JENIS_EVENT = [
    'konser'    => 'Konser Mendatang (Beranda + halaman Event)',
    'arsip'     => 'Arsip Event (halaman Event)',
    'komunitas' => 'Event Komunitas (halaman Sahabat NOAH)',
];

/** Kolom yang boleh kosong: teks kosong diubah jadi NULL supaya rapi di database. */
function kosong_jadi_null(string $teks): ?string
{
    return $teks === '' ? null : $teks;
}

/**
 * Ambil isi form dari $_POST lalu periksa.
 * Mengembalikan [data yang sudah dirapikan, daftar pesan error].
 */
function baca_input_event(): array
{
    $data = [
        'jenis'     => $_POST['jenis'] ?? '',
        'judul'     => trim($_POST['judul'] ?? ''),
        'tanggal'   => $_POST['tanggal'] ?? '',
        'kota'      => trim($_POST['kota'] ?? ''),
        'venue'     => trim($_POST['venue'] ?? ''),
        'deskripsi' => trim($_POST['deskripsi'] ?? ''),
        'label'     => trim($_POST['label'] ?? ''),
    ];

    $error = [];
    if (!array_key_exists($data['jenis'], JENIS_EVENT)) {
        $error[] = 'Pilih jenis event dari daftar.';
    }
    if ($data['judul'] === '' || mb_strlen($data['judul']) > 150) {
        $error[] = 'Judul wajib diisi, maksimal 150 huruf.';
    }
    // Tanggal boleh kosong (arsip lama), tapi kalau diisi harus valid.
    if ($data['tanggal'] !== '') {
        $tanggal = DateTime::createFromFormat('Y-m-d', $data['tanggal']);
        if (!$tanggal || $tanggal->format('Y-m-d') !== $data['tanggal']) {
            $error[] = 'Tanggal tidak valid.';
        }
    }
    if (mb_strlen($data['kota']) > 255 || mb_strlen($data['venue']) > 150 || mb_strlen($data['label']) > 50) {
        $error[] = 'Kota maksimal 255 huruf, venue 150, label 50.';
    }

    return [$data, $error];
}
