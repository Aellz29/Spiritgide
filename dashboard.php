<?php
session_start();
if (!isset($_SESSION['user']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}
if ($_SESSION['role'] !== 'customer' && $_SESSION['role'] !== 'user') {
    // jika role berbeda, arahkan sesuai
    header("Location: index.html");
    exit;
}
$username = $_SESSION['user']['username'] ?? ($_SESSION['user'] ?? 'User');
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="utf-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/><title>Dashboard | Spirit Guide</title></head>
<body style="font-family:Poppins, sans-serif; background:#f7f7f7; color:#222; display:flex; align-items:center; justify-content:center; min-height:100vh;">
  <div style="background:#fff; padding:2rem; border-radius:12px; box-shadow:0 6px 24px rgba(0,0,0,0.08);">
    <h2>Halo, <?php echo htmlspecialchars($username); ?></h2>
    <p>Ini dashboard customer. Kembali ke <a href="index.html">beranda</a>.</p>
    <p><a href="logout.php">Logout</a></p>
  </div>
</body>
</html>
