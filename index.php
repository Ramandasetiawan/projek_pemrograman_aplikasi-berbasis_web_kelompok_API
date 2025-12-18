<?php include 'includes/header.php'; ?>
<main class="flex-shrink-0">
<div class="container mt-4">
  <h2 class="mb-4">Produk Terbaru - etectstore</h2>
  
  <?php
  require_once 'config/db.php';
  $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 9");
  $products = $stmt->fetchAll();
  ?>

  <?php if (!empty($products)): ?>
    <div class="row">
      <?php foreach ($products as $p): ?>
        <div class="col-md-4 mb-4">
          <div class="card">
            <img src="assets/images/<?= htmlspecialchars($p['image']) ?>" 
                 class="card-img-top" height="200"
                 onerror="this.src='assets/images/default.jpg'">
            <div class="card-body">
              <h5><?= htmlspecialchars($p['name']) ?></h5>
              <p class="text-success">Rp <?= number_format($p['price'], 0, ',', '.') ?></p>
              <p class="text-muted">Stok: <?= htmlspecialchars($p['stock']) ?></p>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="alert alert-info">Belum ada produk tersedia.</div>
  <?php endif; ?>

  <div class="text-center mt-4">
    <?php if (isset($_SESSION['user_id'])): ?>
      <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="admin/dashboard.php" class="btn btn-primary">Dashboard Admin</a>
      <?php endif; ?>
    <?php else: ?>
      <p>Belum punya akun? <a href="auth/register.php">Daftar sekarang</a></p>
    <?php endif; ?>
  </div>
</div>
</main>
<?php include 'includes/footer.php'; ?>