<?php
session_start();

// proteksi: harus login dan role = admin
if (!isset($_SESSION['user']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['user']['username'] ?? ($_SESSION['user'] ?? 'Tidak diketahui');
$email = $_SESSION['user']['email'] ?? ($_SESSION['email'] ?? 'Belum ada email');
$role = $_SESSION['role'] ?? 'guest';

if ($role !== 'admin') {
    header("Location: index.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Dashboard Admin | Spirit Guide</title>
  <link href="./src/css/style.css" rel="stylesheet">
  <style>
    body { font-family:'Poppins',sans-serif; background: linear-gradient(135deg,#000,#111); color:white; margin:0; min-height:100vh; display:flex; justify-content:center; align-items:center; }
    .dashboard-container { background: rgba(255,255,255,0.06); padding:2.5rem; border-radius:16px; width:90%; max-width:800px; text-align:left; border:1px solid rgba(255,215,0,0.12); box-shadow:0 10px 40px rgba(0,0,0,0.6); }
    h1 { color:#FFD700; margin:0 0 1rem 0; }
    .info { background: rgba(255,255,255,0.03); padding:1rem; border-radius:10px; margin-bottom:1rem; }
    .btn { display:inline-block; padding:10px 18px; border-radius:10px; background:linear-gradient(90deg,#FFD700,#FFA500); color:#000; font-weight:700; text-decoration:none; }
  </style>
</head>
<body>
  <div class="dashboard-container">
    <h1>Selamat Datang, Admin 👑</h1>
    <div class="info">
      <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
      <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
      <p><strong>Role:</strong> <?php echo htmlspecialchars(ucfirst($role)); ?></p>
    </div>
    <div class="menu">
  <a href="products.php" class="btn">Kelola Produk</a>
  <a href="admin_users.php" class="btn">Kelola Pengguna</a>
</div>

    <?php
    // contoh menampilkan tabel users (opsional). Hapus jika tidak perlu.
    include './config/db.php';
    $res = $conn->query("SELECT id, username, email, role, created_at FROM users ORDER BY id ASC");
    if ($res && $res->num_rows > 0) {
        echo "<h3 style='color:#FFD700'>Daftar User</h3>";
        echo "<table style='width:100%; border-collapse:collapse; color:#fff;'>";
        echo "<thead><tr style='border-bottom:1px solid rgba(255,255,255,0.1);'><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Created</th></tr></thead><tbody>";
        while ($r = $res->fetch_assoc()) {
            echo "<tr style='border-bottom:1px solid rgba(255,255,255,0.04);'><td>{$r['id']}</td><td>".htmlspecialchars($r['username'])."</td><td>".htmlspecialchars($r['email'])."</td><td>".htmlspecialchars($r['role'])."</td><td>".htmlspecialchars($r['created_at'])."</td></tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>Tidak ada user terdaftar.</p>";
    }
    $conn->close();
    ?>

    <p style="margin-top:1rem;">
      <a class="btn" href="logout.php">Logout</a>
    </p>
  </div>
</body>
</html>
