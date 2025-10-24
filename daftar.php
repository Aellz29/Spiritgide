<?php
session_start();
include './config/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $email === '' || $password === '') {
        $message = "Semua kolom wajib diisi.";
    } else {
        // cek email atau username sudah digunakan
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows > 0) {
            $message = "Username atau Email sudah digunakan.";
            $stmt->close();
        } else {
            $stmt->close();
            // hash password modern
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $ins = $conn->prepare("INSERT INTO users (username, email, password, role, created_at) VALUES (?, ?, ?, 'customer', NOW())");
            $ins->bind_param("sss", $username, $email, $hash);
            if ($ins->execute()) {
                $message = "Pendaftaran berhasil. Silakan login.";
            } else {
                $message = "Terjadi kesalahan saat mendaftar.";
            }
            $ins->close();
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar | Spirit Guide</title>
  <link href="./src/css/style.css" rel="stylesheet">
  <style>
    /* gaya sesuai style yang sudah kamu pakai (glass/center) */
    body { font-family: 'Poppins', sans-serif; background-color: #000; color: white; margin:0; min-height:100vh; display:flex; justify-content:center; align-items:center; overflow:hidden; }
    img.bg-image { position: fixed; inset:0; width:100%; height:100%; object-fit:cover; opacity:0.5; z-index:0; }
    .bg-overlay { position: fixed; inset:0; background: rgba(0,0,0,0.6); z-index:1; }
    .form-container { position: relative; z-index:2; background: rgba(255,255,255,0.1); backdrop-filter: blur(12px); border-radius:20px; padding:2.5rem; width:90%; max-width:400px; text-align:center; border:1px solid rgba(255,255,255,0.15); box-shadow:0 10px 40px rgba(255,215,0,0.2); }
    input.input-field { width:100%; padding:12px 14px; border:none; border-radius:10px; background: rgba(255,255,255,0.9); color:#000; margin-bottom:15px; }
    button.btn-submit { width:100%; padding:12px; border:none; border-radius:10px; background: linear-gradient(90deg,#FFD700,#FFA500); font-weight:bold; color:#000; cursor:pointer; }
    .msg-box { padding:10px; border-radius:8px; margin-bottom:15px; font-weight:600; }
    .msg-box.success { background: rgba(34,197,94,0.7); }
    .msg-box.error { background: rgba(239,68,68,0.7); }
  </style>
</head>
<body>
  <img src="./src/img/SpiritGuide.jpg" class="bg-image" alt="">
  <div class="bg-overlay"></div>

  <div class="form-container">
    <h2 style="color:#FFD700; margin-bottom:1rem;">Daftar Akun Spirit Guide</h2>

    <?php if (!empty($message)): ?>
      <div class="msg-box <?php echo (strpos($message, 'berhasil') !== false) ? 'success' : 'error'; ?>">
        <?php echo htmlspecialchars($message); ?>
      </div>
    <?php endif; ?>

    <form method="POST">
      <input class="input-field" type="text" name="username" placeholder="Username" required>
      <input class="input-field" type="email" name="email" placeholder="Email" required>
      <input class="input-field" type="password" name="password" placeholder="Password" required>
      <button class="btn-submit" type="submit">Daftar Sekarang</button>
    </form>

    <p style="margin-top:1rem; color:#ddd;">Sudah punya akun? <a href="login.php" style="color:#FFD700; font-weight:600;">Login</a></p>
  </div>
</body>
</html>
