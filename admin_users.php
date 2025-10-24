<?php
session_start();
include './config/db.php';

// Proteksi agar hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$message = '';

// Hapus user jika ada parameter ?delete=id
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($id > 0) {
        $delete = $conn->prepare("DELETE FROM users WHERE id = ?");
        $delete->bind_param("i", $id);
        if ($delete->execute()) {
            $message = "User berhasil dihapus.";
        } else {
            $message = "Gagal menghapus user.";
        }
        $delete->close();
    }
}

// Ambil semua data user dari database
$result = $conn->query("SELECT id, username, email, role, created_at FROM users ORDER BY id DESC");
$users = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Pengguna | Spirit Guide</title>
  <link rel="stylesheet" href="./src/css/style.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #000;
      color: white;
      margin: 0;
      padding: 40px;
      min-height: 100vh;
    }

    .container {
      max-width: 1000px;
      margin: auto;
      background: rgba(255, 255, 255, 0.05);
      padding: 20px 30px;
      border-radius: 15px;
      box-shadow: 0 0 15px rgba(255, 215, 0, 0.2);
    }

    h2 {
      text-align: center;
      color: #FFD700;
      font-size: 1.8rem;
      margin-bottom: 25px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    table th, table td {
      padding: 12px 10px;
      text-align: left;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    table th {
      background: rgba(255, 215, 0, 0.15);
      color: #FFD700;
      text-transform: uppercase;
      font-size: 0.9rem;
    }

    table tr:hover {
      background: rgba(255, 255, 255, 0.05);
    }

    .btn {
      display: inline-block;
      background: linear-gradient(90deg, #FFD700, #FFA500);
      color: #000;
      padding: 8px 14px;
      text-decoration: none;
      border-radius: 8px;
      font-weight: 600;
      transition: 0.3s;
    }

    .btn:hover {
      background: linear-gradient(90deg, #FFB700, #FF8C00);
    }

    .btn-delete {
      background: rgba(239, 68, 68, 0.8);
      color: white;
      border: none;
      padding: 6px 10px;
      border-radius: 6px;
      cursor: pointer;
      transition: 0.2s;
    }

    .btn-delete:hover {
      background: rgba(239, 68, 68, 1);
    }

    .message {
      background: rgba(34, 197, 94, 0.2);
      padding: 10px;
      border-radius: 8px;
      margin-bottom: 20px;
      text-align: center;
    }

    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 10px;
    }

    .top-bar a {
      text-decoration: none;
      color: #FFD700;
      font-weight: 600;
    }

    .footer {
      text-align: center;
      margin-top: 25px;
      font-size: 0.9rem;
      color: #999;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="top-bar">
      <a href="dashboard_admin.php">← Kembali ke Dashboard</a>
      <span>Halo, <?= htmlspecialchars($_SESSION['user']['username']); ?> (Admin)</span>
    </div>

    <h2>Manajemen Pengguna</h2>

    <?php if ($message): ?>
      <div class="message"><?= htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <table>
      <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Email</th>
        <th>Role</th>
        <th>Dibuat</th>
        <th>Aksi</th>
      </tr>
      <?php if (!empty($users)): ?>
        <?php foreach ($users as $u): ?>
          <tr>
            <td><?= $u['id']; ?></td>
            <td><?= htmlspecialchars($u['username']); ?></td>
            <td><?= htmlspecialchars($u['email']); ?></td>
            <td><?= htmlspecialchars($u['role']); ?></td>
            <td><?= htmlspecialchars($u['created_at']); ?></td>
            <td>
              <?php if ($u['role'] !== 'admin'): ?>
                <a href="?delete=<?= $u['id']; ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus user ini?')">Hapus</a>
              <?php else: ?>
                <span style="color:#aaa;">(Admin)</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="6" style="text-align:center;">Belum ada pengguna terdaftar.</td></tr>
      <?php endif; ?>
    </table>

    <div class="footer">
      Created by <span style="color:#FFD700;">SpiritGuide</span>
    </div>
  </div>
</body>
</html>
