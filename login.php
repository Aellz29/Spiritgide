<?php
session_start();
include './config/db.php'; // koneksi

$message = ''; // agar selalu ada

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $identifier = trim($_POST['identifier'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($identifier === '' || $password === '') {
        $message = "Silakan isi semua kolom.";
    } else {
        // Prepared statement: cari user berdasarkan username atau email
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
        $stmt->bind_param("ss", $identifier, $identifier);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verifikasi password:
            // 1) hashed modern -> password_verify()
            // 2) fallback md5 ($user['password'] menyimpan md5)
            // 3) fallback plain text (jika masih ada)
            $dbpass = $user['password'];

            if (password_verify($password, $dbpass) || md5($password) === $dbpass || $password === $dbpass) {
                // login success
                // Simpan session sebagai array (aman & konsisten)
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ];
                $_SESSION['role'] = $user['role'];

                // Arahkan berdasarkan role
                if ($user['role'] === 'admin') {
                    header("Location: dashboard_admin.php");
                    exit;
                } else {
                    header("Location: index.html");
                    exit;
                }
            } else {
                $message = "Password salah.";
            }
        } else {
            $message = "Username atau email tidak ditemukan.";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login | Spirit Guide</title>
  <link href="./src/css/style.css" rel="stylesheet">
  <style>
    /* --- (tetap sama seperti yang kamu pakai) --- */
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #000;
      color: white;
      margin: 0;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
    }

    img.bg-image {
      position: fixed;
      inset: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      opacity: 0.5;
      z-index: 0;
    }

    .bg-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.6);
      z-index: 1;
    }

    .form-container {
      position: relative;
      z-index: 2;
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(15px);
      border-radius: 20px;
      border: 1px solid rgba(255, 255, 255, 0.2);
      padding: 2.5rem;
      width: 90%;
      max-width: 400px;
      box-shadow: 0 10px 40px rgba(255, 215, 0, 0.3);
      text-align: center;
      transition: 0.3s ease;
    }

    .form-container:hover {
      transform: scale(1.02);
      box-shadow: 0 15px 50px rgba(255, 215, 0, 0.4);
    }

    h2 {
      font-size: 1.8rem;
      font-weight: 700;
      color: #FFD700;
      margin-bottom: 1.5rem;
    }

    .input-field {
      width: 100%;
      padding: 12px 14px;
      border: none;
      border-radius: 10px;
      background: rgba(255, 255, 255, 0.9);
      color: #000;
      font-size: 1rem;
      margin-bottom: 15px;
      outline: none;
      transition: 0.2s;
    }

    .input-field:focus {
      box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.5);
    }

    .btn-submit {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 10px;
      background: linear-gradient(90deg, #FFD700, #FFA500);
      font-weight: bold;
      color: #000;
      font-size: 1rem;
      cursor: pointer;
      transition: 0.3s;
    }

    .btn-submit:hover {
      background: linear-gradient(90deg, #FFB700, #FF8C00);
      transform: translateY(-2px);
      box-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
    }

    .msg-box {
      padding: 10px;
      border-radius: 8px;
      margin-bottom: 15px;
      font-weight: 600;
    }

    .msg-box.error { background-color: rgba(239, 68, 68, 0.7); }

    .footer-text {
      margin-top: 1rem;
      font-size: 0.9rem;
      color: #ddd;
    }

    .footer-text a {
      color: #FFD700;
      font-weight: 600;
      text-decoration: none;
    }

    .footer-text a:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body>
  <img src="./src/img/SpiritGuide.jpg" alt="Spirit Guide Background" class="bg-image">
  <div class="bg-overlay"></div>

  <div class="form-container">
    <h2>Login ke Spirit Guide</h2>

    <?php if (!empty($message)): ?>
      <div class="msg-box error"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST">
      <input type="text" name="identifier" placeholder="Masukkan Username atau Email" required class="input-field">
      <input type="password" name="password" placeholder="Masukkan Password" required class="input-field">
      <button type="submit" class="btn-submit">Masuk</button>
    </form>

    <p class="footer-text">
      Belum punya akun? <a href="daftar.php">Daftar sekarang</a>
    </p>
  </div>
</body>
</html>
