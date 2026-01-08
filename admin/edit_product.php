<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}
require_once '../config/db.php';
require_once '../config/csrf.php';
require_once '../config/upload_helper.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: products.php?error=Produk tidak ditemukan');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    check_csrf_token();

    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
    $stock = filter_var($_POST['stock'], FILTER_VALIDATE_INT);

    if (empty($name) || $price === false || $stock === false) {
        $error = 'Data tidak valid. Pastikan semua field terisi dengan benar.';
    } elseif ($price < 0 || $stock < 0) {
        $error = 'Harga dan stok tidak boleh negatif.';
    } else {
        $image = $product['image']; // Keep old image by default

        if (!empty($_FILES['image']['name'])) {
            $upload_result = validate_and_save_image($_FILES['image']);

            if ($upload_result['success']) {

                delete_image_safe($product['image']);
                $image = $upload_result['filename'];
            } else {
                $error = $upload_result['message'];
            }
        }

        if (empty($error)) {
            try {
                $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, image = ?, stock = ? WHERE id = ?");
                $stmt->execute([$name, $desc, $price, $image, $stock, $id]);
                header('Location: products.php?success=Produk berhasil diupdate');
                exit;
            } catch (PDOException $e) {
                $error = 'Gagal mengupdate produk: ' . $e->getMessage();
            }
        }
    }
}

include '../includes/header.php';
?>
<main class="flex-shrink-0">
<div class="container mt-4">
  <h3>Edit Produk</h3>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>
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