<?php
session_start();
include './config/db.php';

// Proteksi agar hanya admin yang bisa masuk
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    header("Location: products.php");
    exit;
}

$message = '';

// Ambil data produk lama
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    die("Produk tidak ditemukan!");
}

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $category = $_POST['category'];
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $imgPath = $product['image'];

    // Jika ada upload gambar baru
    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $name = 'prod_' . time() . '.' . $ext;
        $targetDir = __DIR__ . '/src/img/products/';
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
        $targetFile = $targetDir . $name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            // Hapus gambar lama kalau ada
            if (!empty($product['image']) && file_exists(__DIR__ . '/' . $product['image'])) {
                unlink(__DIR__ . '/' . $product['image']);
            }
            $imgPath = 'src/img/products/' . $name;
        }
    }

    // Update data produk
    $update = $conn->prepare("UPDATE products SET title=?, category=?, description=?, price=?, image=? WHERE id=?");
    $update->bind_param("sssisi", $title, $category, $description, $price, $imgPath, $id);

    if ($update->execute()) {
        $message = "Produk berhasil diperbarui.";
        // Refresh data
        $product = ['title' => $title, 'category' => $category, 'description' => $description, 'price' => $price, 'image' => $imgPath];
    } else {
        $message = "Gagal memperbarui produk.";
    }
    $update->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Edit Produk | Spirit Guide</title>
  <link rel="stylesheet" href="./src/css/style.css">
  <style>
    body {
      font-family: Poppins, sans-serif;
      background-color: #0a0a0a;
      color: #fff;
      padding: 40px;
    }
    .container {
      max-width: 800px;
      margin: auto;
      background: rgba(255, 255, 255, 0.05);
      padding: 20px;
      border-radius: 15px;
    }
    input, select, textarea {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      border-radius: 8px;
      border: 1px solid rgba(255,255,255,0.1);
      background: #fff;
      color: #000;
    }
    img.preview {
      width: 180px;
      border-radius: 8px;
      margin-top: 10px;
    }
    .btn {
      display: inline-block;
      background: linear-gradient(90deg,#FFD700,#FFA500);
      color: #000;
      padding: 10px 15px;
      text-decoration: none;
      border-radius: 8px;
      font-weight: 600;
    }
    .message {
      background: rgba(34,197,94,0.2);
      color: #fff;
      padding: 10px;
      border-radius: 8px;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2 style="color:#FFD700">Edit Produk</h2>

    <?php if ($message): ?>
      <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <label>Judul</label>
      <input type="text" name="title" value="<?= htmlspecialchars($product['title']) ?>" required>

      <label>Kategori</label>
      <select name="category">
        <option <?= $product['category'] === 'Fashion' ? 'selected' : '' ?>>Fashion</option>
        <option <?= $product['category'] === 'Food' ? 'selected' : '' ?>>Food</option>
        <option <?= $product['category'] === 'Aksesoris' ? 'selected' : '' ?>>Aksesoris</option>
        <option <?= $product['category'] === 'Other' ? 'selected' : '' ?>>Other</option>
      </select>

      <label>Deskripsi</label>
      <textarea name="description" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>

      <label>Harga (contoh: 150000.00)</label>
      <input type="text" name="price" value="<?= htmlspecialchars($product['price']) ?>" required>

      <label>Gambar Saat Ini</label><br>
      <?php if ($product['image']): ?>
        <img class="preview" src="<?= htmlspecialchars($product['image']) ?>" alt="preview">
      <?php endif; ?>

      <label>Ganti Gambar (Opsional)</label>
      <input type="file" name="image" accept="image/*">

      <button class="btn" type="submit">Simpan Perubahan</button>
      <a class="btn" href="products.php">Kembali</a>
    </form>
  </div>
</body>
</html>
