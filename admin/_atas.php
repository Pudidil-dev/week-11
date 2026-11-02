<?php
// Bagian atas semua halaman admin (setelah login): <head>, judul, dan menu.
// Sebelum include file ini, isi dulu variabel $judul_halaman.
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($judul_halaman ?? 'Admin') ?> - Admin NOAH</title>
  <link rel="stylesheet" href="<?= url_admin('admin.css') ?>">
</head>
<body>
  <header class="admin-header">
    <a class="admin-logo" href="<?= url_admin('index.php') ?>">NOAH Admin</a>
    <nav class="admin-menu">
      <a href="<?= url_admin('index.php') ?>">Dashboard</a>
      <a href="<?= url_admin('berita/index.php') ?>">Berita</a>
      <a href="<?= url_admin('event/index.php') ?>">Event</a>
      <a href="<?= url_admin('kiriman/index.php') ?>">Kiriman</a>
      <a href="<?= url_admin('pemesanan/index.php') ?>">Pemesanan</a>
      <a href="<?= url_admin('pelanggan/index.php') ?>">Pelanggan</a>
      <a href="<?= url_admin('logout.php') ?>">Keluar (<?= e($_SESSION['admin_username'] ?? '') ?>)</a>
    </nav>
  </header>
  <main class="admin-isi">
    <?php $pesan = ambil_pesan(); if ($pesan !== '') : ?>
      <p class="pesan"><?= e($pesan) ?></p>
    <?php endif; ?>
