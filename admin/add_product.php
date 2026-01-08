<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}
require_once '../config/db.php';
require_once '../config/csrf.php';
require_once '../config/upload_helper.php';

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
        $image = 'default.jpg';

        if (!empty($_FILES['image']['name'])) {
            $upload_result = validate_and_save_image($_FILES['image']);

            if ($upload_result['success']) {
                $image = $upload_result['filename'];
            } else {
                $error = $upload_result['message'];
            }
        }

        if (empty($error)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image, stock) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $desc, $price, $image, $stock]);
                header('Location: products.php?success=Produk berhasil ditambahkan');
                exit;
            } catch (PDOException $e) {
                $error = 'Gagal menyimpan produk: ' . $e->getMessage();
            }
        }
    }
}

include '../includes/header.php';
?>
<main class="flex-shrink-0">
<div class="container mt-4">
  <h3>Tambah Produk Baru</h3>

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