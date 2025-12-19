<?php
require_once '../includes/header.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}
require_once '../config/db.php';
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll();
?>
<main class="flex-shrink-0">
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Daftar Produk</h2>
    <a href="add_product.php" class="btn btn-success">+ Tambah Produk</a>
  </div>

  <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
  <?php endif; ?>

  <?php if (count($products) === 0): ?>
    <div class="alert alert-info">Belum ada produk.</div>
  <?php else: ?>
    <div class="row">
      <?php foreach ($products as $p): ?>
        <div class="col-md-4 mb-4">
          <div class="card">
            <?php 
            $imageSrc = (strpos($p['image'], 'http') === 0) ? $p['image'] : '../assets/images/' . $p['image'];
            ?>
            <img src="<?= htmlspecialchars($imageSrc) ?>" 
                 class="card-img-top" height="200" style="object-fit: cover;" 
                 onerror="this.src='https://via.placeholder.com/500x300?text=No+Image'">
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($p['name']) ?></h5>
              <p class="text-muted">Rp <?= number_format($p['price'], 0, ',', '.') ?> | Stok: <?= $p['stock'] ?></p>
              <a href="edit_product.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
              <a href="delete_product.php?id=<?= $p['id'] ?>" 
                 class="btn btn-sm btn-danger"
                 onclick="return confirm('Hapus produk ini?')">Hapus</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
</main>
<?php include '../includes/footer.php'; ?>