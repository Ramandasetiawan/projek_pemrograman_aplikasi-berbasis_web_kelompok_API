<?php
require_once '../includes/header.php';
require_once '../config/db.php';

// Ambil produk terbaru
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 8");
$latest_products = $stmt->fetchAll();

// Ambil kategori
$stmt = $pdo->query("SELECT * FROM categories LIMIT 6");
$categories = $stmt->fetchAll();

// Ambil produk terlaris (simulasi dengan random)
$stmt = $pdo->query("SELECT * FROM products ORDER BY RAND() LIMIT 4");
$featured_products = $stmt->fetchAll();
?>

<main class="flex-shrink-0">
<!-- Hero Section -->
<div class="bg-primary text-white py-5">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-md-6">
        <h1 class="display-4 fw-bold mb-3">Toko Komponen Komputer</h1>
        <p class="lead mb-4">Temukan berbagai komponen komputer berkualitas dengan harga terbaik</p>
        <a href="../pages/products.php" class="btn btn-light btn-lg me-2">Belanja Sekarang</a>
        <?php if (!isset($_SESSION['user_id'])): ?>
          <a href="auth/register.php" class="btn btn-outline-light btn-lg">Daftar Gratis</a>
        <?php endif; ?>
      </div>
      <div class="col-md-6 text-center">
        <img src="https://images.unsplash.com/photo-1587202372634-32705e3bf49c?w=600" 
             alt="Computer Components" 
             class="img-fluid rounded shadow"
             style="max-height: 400px; object-fit: cover;">
      </div>
    </div>
  </div>
</div>

<!-- Kategori -->
<div class="container my-5">
  <h2 class="text-center mb-4">Kategori Populer</h2>
  <div class="row g-3">
    <?php foreach ($categories as $cat): ?>
      <div class="col-md-2 col-6">
        <a href="../pages/products.php?category=<?= $cat['id'] ?>" class="text-decoration-none">
          <div class="card text-center h-100 hover-shadow">
            <div class="card-body">
              <div class="mb-2" style="font-size: 2rem;">
                <?php
                $icons = ['💻', '🖥️', '💾', '💿', '🎮', '⚡', '📦', '❄️', '🖱️', '⌨️'];
                echo $icons[$cat['id'] % 10];
                ?>
              </div>
              <h6 class="card-title small"><?= htmlspecialchars($cat['name']) ?></h6>
            </div>
          </div>
        </a>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- Produk Unggulan -->
<div class="bg-light py-5">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Produk Unggulan</h2>
      <a href="../pages/products.php" class="btn btn-outline-primary">Lihat Semua</a>
    </div>
    <div class="row">
      <?php foreach ($featured_products as $p): ?>
        <div class="col-md-3 col-6 mb-4">
          <div class="card h-100 hover-shadow">
            <?php 
            $imageSrc = (strpos($p['image'], 'http') === 0) ? $p['image'] : 'assets/images/' . $p['image'];
            ?>
            <a href="../pages/product_detail.php?id=<?= $p['id'] ?>">
              <img src="<?= htmlspecialchars($imageSrc) ?>" 
                   class="card-img-top" height="200" style="object-fit: cover;"
                   onerror="this.src='https://via.placeholder.com/500x300?text=No+Image'">
            </a>
            <div class="card-body">
              <a href="../pages/product_detail.php?id=<?= $p['id'] ?>" class="text-decoration-none text-dark">
                <h6 class="card-title"><?= htmlspecialchars($p['name']) ?></h6>
              </a>
              <p class="text-primary fw-bold mb-1">Rp <?= number_format($p['price'], 0, ',', '.') ?></p>
              <p class="text-muted small mb-2">Stok: <?= $p['stock'] ?></p>
              <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($p['stock'] > 0): ?>
                  <form action="../handlers/cart_add.php" method="POST">
                    <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                    <button type="submit" class="btn btn-primary btn-sm w-100">Tambah ke Keranjang</button>
                  </form>
                <?php else: ?>
                  <button class="btn btn-secondary btn-sm w-100" disabled>Stok Habis</button>
                <?php endif; ?>
              <?php else: ?>
                <a href="auth/login.php" class="btn btn-outline-primary btn-sm w-100">Login untuk Membeli</a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- Produk Terbaru -->
<div class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Produk Terbaru</h2>
    <a href="../pages/products.php" class="btn btn-outline-primary">Lihat Semua</a>
  </div>
  <div class="row">
    <?php foreach ($latest_products as $p): ?>
      <div class="col-md-3 col-6 mb-4">
        <div class="card h-100 hover-shadow">
          <?php 
          $imageSrc = (strpos($p['image'], 'http') === 0) ? $p['image'] : 'assets/images/' . $p['image'];
          ?>
          <a href="../pages/product_detail.php?id=<?= $p['id'] ?>">
            <img src="<?= htmlspecialchars($imageSrc) ?>" 
                 class="card-img-top" height="200" style="object-fit: cover;"
                 onerror="this.src='https://via.placeholder.com/500x300?text=No+Image'">
          </a>
          <div class="card-body">
            <a href="../pages/product_detail.php?id=<?= $p['id'] ?>" class="text-decoration-none text-dark">
              <h6 class="card-title"><?= htmlspecialchars($p['name']) ?></h6>
            </a>
            <p class="text-primary fw-bold mb-1">Rp <?= number_format($p['price'], 0, ',', '.') ?></p>
            <p class="text-muted small mb-2">Stok: <?= $p['stock'] ?></p>
            <?php if (isset($_SESSION['user_id'])): ?>
              <?php if ($p['stock'] > 0): ?>
                <form action="../handlers/cart_add.php" method="POST">
                  <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                  <button type="submit" class="btn btn-primary btn-sm w-100">Tambah ke Keranjang</button>
                </form>
              <?php else: ?>
                <button class="btn btn-secondary btn-sm w-100" disabled>Stok Habis</button>
              <?php endif; ?>
            <?php else: ?>
              <a href="auth/login.php" class="btn btn-outline-primary btn-sm w-100">Login untuk Membeli</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- Keunggulan -->
<div class="bg-light py-5">
  <div class="container">
    <div class="row text-center">
      <div class="col-md-3 mb-4">
        <div class="mb-3" style="font-size: 3rem;">🚚</div>
        <h5>Pengiriman Cepat</h5>
        <p class="text-muted">Gratis ongkir untuk pembelian tertentu</p>
      </div>
      <div class="col-md-3 mb-4">
        <div class="mb-3" style="font-size: 3rem;">💯</div>
        <h5>100% Original</h5>
        <p class="text-muted">Produk bergaransi resmi</p>
      </div>
      <div class="col-md-3 mb-4">
        <div class="mb-3" style="font-size: 3rem;">💳</div>
        <h5>Pembayaran Aman</h5>
        <p class="text-muted">Berbagai metode pembayaran</p>
      </div>
      <div class="col-md-3 mb-4">
        <div class="mb-3" style="font-size: 3rem;">🎧</div>
        <h5>Layanan 24/7</h5>
        <p class="text-muted">Customer service siap membantu</p>
      </div>
    </div>
  </div>
</div>

</main>

<style>
.hover-shadow {
  transition: all 0.3s ease;
}
.hover-shadow:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}
</style>

<?php include '../includes/footer.php'; ?>
