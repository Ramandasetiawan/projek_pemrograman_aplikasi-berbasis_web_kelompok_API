<?php
require_once '../includes/header.php';
require_once '../config/db.php';

if (!isset($_GET['id'])) {
    header('Location: products.php');
    exit;
}

$product_id = (int)$_GET['id'];

$stmt = $pdo->prepare("
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.id = ?
");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: products.php');
    exit;
}

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM products WHERE category_id = ? AND id != ?");
$stmt->execute([$product['category_id'], $product_id]);
$total = $stmt->fetchColumn();

$related_products = [];
if ($total > 0) {

    $offset = max(0, rand(0, $total - min(4, $total)));
    $stmt = $pdo->prepare("
        SELECT * FROM products 
        WHERE category_id = ? AND id != ? 
        ORDER BY id
        LIMIT 4 OFFSET ?
    ");
    $stmt->execute([$product['category_id'], $product_id, $offset]);
    $related_products = $stmt->fetchAll();
}

$imageSrc = (strpos($product['image'], 'http') === 0) ? $product['image'] : 'assets/images/' . $product['image'];
?>

<main class="flex-shrink-0">
<div class="container mt-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item"><a href="products.php">Produk</a></li>
            <li class="breadcrumb-item"><a href="products.php?category=<?= $product['category_id'] ?>"><?= htmlspecialchars($product['category_name']) ?></a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($product['name']) ?></li>
        </ol>
    </nav>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Gambar Produk -->
        <div class="col-md-5">
            <div class="card">
                <img src="<?= htmlspecialchars($imageSrc) ?>" 
                     class="card-img-top" 
                     style="object-fit: contain; height: 400px; padding: 20px;"
                     onerror="this.src='https://via.placeholder.com/500x500?text=No+Image'">
            </div>
        </div>

        <!-- Detail Produk -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <span class="badge bg-primary mb-2"><?= htmlspecialchars($product['category_name']) ?></span>
                    <h2 class="card-title mb-3"><?= htmlspecialchars($product['name']) ?></h2>

                    <?php if ($product['brand']): ?>
                        <p class="text-muted mb-2">
                            <strong>Brand:</strong> <?= htmlspecialchars($product['brand']) ?>
                        </p>
                    <?php endif; ?>

                    <div class="mb-3">
                        <h3 class="text-primary">Rp <?= number_format($product['price'], 0, ',', '.') ?></h3>
                    </div>

                    <div class="mb-3">
                        <strong>Stok:</strong> 
                        <?php if ($product['stock'] > 0): ?>
                            <span class="badge bg-success"><?= $product['stock'] ?> tersedia</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Stok Habis</span>
                        <?php endif; ?>
                    </div>

                    <?php if ($product['description']): ?>
                        <div class="mb-3">
                            <h5>Deskripsi</h5>
                            <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if ($product['specifications']): ?>
                        <div class="mb-3">
                            <h5>Spesifikasi</h5>
                            <p class="text-muted"><?= nl2br(htmlspecialchars($product['specifications'])) ?></p>
                        </div>
                    <?php endif; ?>

                    <hr>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($product['stock'] > 0): ?>
                            <form action="../handlers/cart_add.php" method="POST" class="mb-3">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Jumlah:</label>
                                        <input type="number" name="quantity" class="form-control" 
                                               value="1" min="1" max="<?= $product['stock'] ?>">
                                    </div>
                                    <div class="col-md-9 d-flex align-items-end">
                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                        <button type="submit" class="btn btn-primary btn-lg w-100">
                                            🛒 Tambah ke Keranjang
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <div class="d-grid">
                                <a href="checkout.php" class="btn btn-success btn-lg">Beli Sekarang</a>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                Produk ini sedang tidak tersedia
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <p class="mb-2">Silakan login untuk melakukan pembelian</p>
                            <a href="auth/login.php" class="btn btn-primary">Login</a>
                            <a href="auth/register.php" class="btn btn-outline-primary">Daftar</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Produk Terkait -->
    <?php if (count($related_products) > 0): ?>
        <div class="mt-5">
            <h3 class="mb-4">Produk Terkait</h3>
            <div class="row">
                <?php foreach ($related_products as $p): ?>
                    <div class="col-md-3 col-6 mb-4">
                        <div class="card h-100">
                            <?php 
                            $relatedImageSrc = (strpos($p['image'], 'http') === 0) ? $p['image'] : 'assets/images/' . $p['image'];
                            ?>
                            <a href="product_detail.php?id=<?= $p['id'] ?>">
                                <img src="<?= htmlspecialchars($relatedImageSrc) ?>" 
                                     class="card-img-top" height="180" style="object-fit: cover;"
                                     onerror="this.src='https://via.placeholder.com/300x300?text=No+Image'">
                            </a>
                            <div class="card-body">
                                <a href="product_detail.php?id=<?= $p['id'] ?>" class="text-decoration-none text-dark">
                                    <h6 class="card-title"><?= htmlspecialchars($p['name']) ?></h6>
                                </a>
                                <p class="text-primary fw-bold mb-2">Rp <?= number_format($p['price'], 0, ',', '.') ?></p>
                                <a href="product_detail.php?id=<?= $p['id'] ?>" class="btn btn-outline-primary btn-sm w-100">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
</main>

<?php include '../includes/footer.php'; ?>
