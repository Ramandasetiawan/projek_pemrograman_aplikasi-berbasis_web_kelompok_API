<?php
require_once '../includes/header.php';
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT w.*, p.name, p.price, p.image, p.stock, p.brand, c.name as category_name
    FROM wishlist w
    JOIN products p ON w.product_id = p.id
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE w.user_id = ?
    ORDER BY w.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$wishlist_items = $stmt->fetchAll();
?>

<main class="flex-shrink-0">
<div class="container mt-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../public/home.php">Home</a></li>
            <li class="breadcrumb-item active">Wishlist Saya</li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col">
            <h2><i class="bi bi-heart-fill text-danger"></i> Wishlist Saya</h2>
            <p class="text-muted">Produk favorit yang Anda simpan</p>
        </div>
        <div class="col-auto">
            <span class="badge bg-primary" style="font-size: 1rem; padding: 0.5rem 1rem;">
                <?= count($wishlist_items) ?> Item
            </span>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= htmlspecialchars($_GET['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (count($wishlist_items) > 0): ?>
        <div class="row g-3">
            <?php foreach ($wishlist_items as $item): ?>
                <div class="col-lg-3 col-md-4 col-6">
                    <div class="card h-100 product-card">
                        <!-- Wishlist Button -->
                        <form action="../handlers/wishlist_handler.php" method="POST" style="position: absolute; top: 10px; right: 10px; z-index: 10;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                            <input type="hidden" name="action" value="remove">
                            <button type="submit" class="btn btn-sm btn-danger rounded-circle" 
                                    style="width: 40px; height: 40px; padding: 0;"
                                    title="Hapus dari Wishlist">
                                <i class="bi bi-heart-fill"></i>
                            </button>
                        </form>

                        <?php if ($item['stock'] <= 5 && $item['stock'] > 0): ?>
                            <span class="badge bg-warning" style="position: absolute; top: 10px; left: 10px; z-index: 10;">
                                Stok Terbatas!
                            </span>
                        <?php elseif ($item['stock'] == 0): ?>
                            <span class="badge bg-danger" style="position: absolute; top: 10px; left: 10px; z-index: 10;">
                                Habis
                            </span>
                        <?php endif; ?>

                        <?php 
                        $imageSrc = (strpos($item['image'], 'http') === 0) ? $item['image'] : '../public/assets/images/' . $item['image'];
                        ?>
                        <a href="product_detail.php?id=<?= $item['product_id'] ?>">
                            <img src="<?= htmlspecialchars($imageSrc) ?>" 
                                 class="card-img-top" height="220" style="object-fit: cover;"
                                 onerror="this.src='https://via.placeholder.com/500x300?text=<?= urlencode($item['name']) ?>'">
                        </a>

                        <div class="card-body d-flex flex-column">
                            <span class="badge bg-secondary small mb-2 align-self-start">
                                <?= htmlspecialchars($item['category_name']) ?>
                            </span>
                            <a href="product_detail.php?id=<?= $item['product_id'] ?>" class="text-decoration-none text-dark">
                                <h6 class="card-title"><?= htmlspecialchars($item['name']) ?></h6>
                            </a>
                            <?php if ($item['brand']): ?>
                                <p class="text-muted small mb-2">
                                    <i class="bi bi-tag"></i> <?= htmlspecialchars($item['brand']) ?>
                                </p>
                            <?php endif; ?>
                            <div class="price-tag mb-2">
                                Rp <?= number_format($item['price'], 0, ',', '.') ?>
                            </div>
                            <p class="small mb-3">
                                <?php if ($item['stock'] > 10): ?>
                                    <span class="stock-badge stock-available">Tersedia</span>
                                <?php elseif ($item['stock'] > 0): ?>
                                    <span class="stock-badge stock-low">Stok: <?= $item['stock'] ?></span>
                                <?php else: ?>
                                    <span class="stock-badge stock-out">Habis</span>
                                <?php endif; ?>
                            </p>

                            <div class="mt-auto">
                                <div class="d-grid gap-2">
                                    <?php if ($item['stock'] > 0): ?>
                                        <form action="../handlers/cart_add.php" method="POST">
                                            <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                                <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm" disabled>
                                            <i class="bi bi-x-circle"></i> Stok Habis
                                        </button>
                                    <?php endif; ?>
                                    <a href="product_detail.php?id=<?= $item['product_id'] ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye"></i> Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-4">
            <a href="products.php" class="btn btn-outline-primary">
                <i class="bi bi-shop"></i> Lanjut Belanja
            </a>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <div style="font-size: 5rem; opacity: 0.3;">💔</div>
                <h3 class="text-muted mb-3">Wishlist Kosong</h3>
                <p class="text-muted mb-4">Anda belum menambahkan produk ke wishlist</p>
                <a href="products.php" class="btn btn-primary">
                    <i class="bi bi-shop"></i> Mulai Belanja
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>
</main>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<?php include '../includes/footer.php'; ?>
