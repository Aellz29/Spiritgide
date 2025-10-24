<?php
session_start();
include './config/db.php';

// proteksi admin
if (!isset($_SESSION['user']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$message = '';

// Handle create
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $title = trim($_POST['title']);
    $category = $_POST['category'];
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);

    // handle upload
    $imgPath = null;
    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $name = 'prod_'.time().'.'.$ext;
        $targetDir = __DIR__ . '/src/img/products/';
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
        $targetFile = $targetDir . $name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imgPath = 'src/img/products/' . $name;
        }
    }

    $stmt = $conn->prepare("INSERT INTO products (title, category, description, price, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssis", $title, $category, $description, $price, $imgPath);
    if ($stmt->execute()) $message = "Produk berhasil dibuat.";
    else $message = "Gagal membuat produk.";
    $stmt->close();
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // hapus gambar dulu
    $r = $conn->query("SELECT image FROM products WHERE id=$id");
    if ($r && $r->num_rows) {
        $row = $r->fetch_assoc();
        if (!empty($row['image']) && file_exists(__DIR__.'/'.$row['image'])) unlink(__DIR__.'/'.$row['image']);
    }
    $conn->query("DELETE FROM products WHERE id=$id");
    header("Location: products.php");
    exit;
}

// fetch products
$res = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Kelola Produk | Spirit Guide</title>
  <link href="./src/css/style.css" rel="stylesheet">
  <style>
    body{font-family:Poppins, sans-serif; padding:30px; background:#060606; color:#fff;}
    .wrap{max-width:1000px; margin:0 auto;}
    .card{background:rgba(255,255,255,0.03); padding:16px; border-radius:12px; margin-bottom:16px;}
    input, select, textarea { width:100%; padding:8px; margin:6px 0; border-radius:8px; border:1px solid rgba(255,255,255,0.06); background:#fff; color:#000;}
    img.thumb{width:120px; border-radius:8px;}
    .btn{display:inline-block;padding:8px 12px;border-radius:8px;background:linear-gradient(90deg,#FFD700,#FFA500);color:#000;text-decoration:none;font-weight:700;}
    table{width:100%;border-collapse:collapse;margin-top:12px;}
    th,td{padding:8px;border-bottom:1px solid rgba(255,255,255,0.04); text-align:left;}
  </style>
</head>
<body>
  <div class="wrap">
    <h1 style="color:#FFD700">Kelola Produk</h1>

    <?php if ($message): ?>
      <div style="padding:10px;background:rgba(34,197,94,0.2);color:#fff;border-radius:8px;margin-bottom:12px;"><?=htmlspecialchars($message)?></div>
    <?php endif; ?>

    <div class="card">
      <h3>Tambah Produk Baru</h3>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="create">
        <label>Judul</label>
        <input type="text" name="title" required>
        <label>Kategori</label>
        <select name="category">
          <option>Fashion</option>
          <option>Food</option>
          <option>Aksesoris</option>
          <option>Other</option>
        </select>
        <label>Deskripsi</label>
        <textarea name="description" rows="4"></textarea>
        <label>Harga (contoh: 150000.00)</label>
        <input type="text" name="price" value="0.00" required>
        <label>Gambar</label>
        <input type="file" name="image" accept="image/*">
        <button class="btn" type="submit">Simpan</button>
      </form>
    </div>

    <div class="card">
      <h3>Daftar Produk</h3>
      <table>
        <thead><tr><th>ID</th><th>Gambar</th><th>Judul</th><th>Kategori</th><th>Harga</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php while($p = $res->fetch_assoc()): ?>
          <tr>
            <td><?= $p['id'] ?></td>
            <td><?php if($p['image']): ?><img class="thumb" src="<?=htmlspecialchars($p['image'])?>"><?php endif;?></td>
            <td><?=htmlspecialchars($p['title'])?></td>
            <td><?=htmlspecialchars($p['category'])?></td>
            <td><?=number_format($p['price'],2,',','.')?></td>
            <td>
              <a class="btn" href="products_edit.php?id=<?=$p['id']?>">Edit</a>
              <a class="btn" href="products.php?delete=<?=$p['id']?>" onclick="return confirm('Yakin hapus produk?')">Hapus</a>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <p><a class="btn" href="dashboard_admin.php">Kembali ke Dashboard</a></p>
  </div>
</body>
</html>
