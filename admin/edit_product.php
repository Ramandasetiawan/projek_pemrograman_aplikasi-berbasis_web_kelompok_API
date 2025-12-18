<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}
require_once '../config/db.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: products.php?error=Produk tidak ditemukan');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    
    $image = $product['image']; // pertahankan gambar lama jika tidak diubah
    if (!empty($_FILES['image']['name'])) {
        // Hapus gambar lama (opsional)
        if ($product['image'] !== 'default.jpg') {
            unlink('../assets/images/' . $product['image']);
        }
        $image = uniqid() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], '../assets/images/' . $image);
    }

    $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, image = ?, stock = ? WHERE id = ?");
    $stmt->execute([$name, $desc, $price, $image, $stock, $id]);
    header('Location: products.php?success=Produk berhasil diupdate');
    exit;
}

include '../includes/header.php';
?>
<main class="flex-shrink-0">
<div class="container mt-4">
  <h3>Edit Produk</h3>
  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label>Nama Produk</label>
      <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
    </div>
    <div class="mb-3">
      <label>Deskripsi</label>
      <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($product['description']) ?></textarea>
    </div>
    <div class="mb-3">
      <label>Harga (Rp)</label>
      <input type="number" name="price" class="form-control" value="<?= $product['price'] ?>" required min="0">
    </div>
    <div class="mb-3">
      <label>Stok</label>
      <input type="number" name="stock" class="form-control" value="<?= $product['stock'] ?>" required min="0">
    </div>
    <div class="mb-3">
      <label>Gambar Saat Ini</label><br>
      <img src="../assets/images/<?= htmlspecialchars($product['image']) ?>" height="100" onerror="this.src='../assets/images/default.jpg'">
    </div>
    <div class="mb-3">
      <label>Ganti Gambar (opsional)</label>
      <input type="file" name="image" class="form-control" accept="image/*">
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="products.php" class="btn btn-secondary">Batal</a>
  </form>
</div>
</main>
<?php include '../includes/footer.php'; ?>