<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    
    $image = 'default.jpg';
    if (!empty($_FILES['image']['name'])) {
        $image = uniqid() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], '../assets/images/' . $image);
    }

    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image, stock) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $desc, $price, $image, $stock]);
    header('Location: products.php?success=Produk berhasil ditambahkan');
    exit;
}

include '../includes/header.php';
?>
<main class="flex-shrink-0">
<div class="container mt-4">
  <h3>Tambah Produk Baru</h3>
  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label>Nama Produk</label>
      <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Deskripsi</label>
      <textarea name="description" class="form-control" rows="3"></textarea>
    </div>
    <div class="mb-3">
      <label>Harga (Rp)</label>
      <input type="number" name="price" class="form-control" required min="0">
    </div>
    <div class="mb-3">
      <label>Stok</label>
      <input type="number" name="stock" class="form-control" required min="0">
    </div>
    <div class="mb-3">
      <label>Gambar (opsional)</label>
      <input type="file" name="image" class="form-control" accept="image/*">
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="products.php" class="btn btn-secondary">Batal</a>
  </form>
</div>
</main>
<?php include '../includes/footer.php'; ?>