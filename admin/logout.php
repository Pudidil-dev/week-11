<?php
require __DIR__ . '/fungsi.php';

// Hapus semua data session, lalu kembali ke halaman login.
$_SESSION = [];
session_destroy();

header('Location: ' . url_admin('login.php'));
exit;
