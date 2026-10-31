<?php
require __DIR__ . '/fungsi.php';

// Sudah login? Langsung ke dashboard.
if (!empty($_SESSION['admin_id'])) {
    header('Location: ' . url_admin('index.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cek_token();

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // 1. Cari admin berdasarkan username.
    $stmt = mysqli_prepare($koneksi, 'SELECT id, username, password FROM admin WHERE username = ?');
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $admin = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    // 2. Cocokkan password yang diketik dengan hash di database.
    if ($admin && password_verify($password, $admin['password'])) {
        // 3. Ganti ID session supaya session lama tidak bisa dipakai orang lain.
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        header('Location: ' . url_admin('index.php'));
        exit;
    }

    // Pesan sengaja sama untuk username salah maupun password salah,
    // supaya orang lain tidak bisa menebak username mana yang ada.
    $error = 'Username atau password salah.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin NOAH</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body class="halaman-login">
  <form class="kotak-login" method="post">
    <h1>Login Admin</h1>

    <?php if ($error !== '') : ?>
      <p class="pesan pesan-error"><?= e($error) ?></p>
    <?php endif; ?>

    <?= input_token() ?>

    <label for="username">Username</label>
    <input id="username" type="text" name="username" required autofocus>

    <label for="password">Password</label>
    <input id="password" type="password" name="password" required>

    <button type="submit" class="tombol">Masuk</button>
  </form>
</body>
</html>
