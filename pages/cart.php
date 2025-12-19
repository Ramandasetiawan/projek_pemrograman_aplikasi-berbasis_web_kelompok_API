<?php
require_once '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

require_once '../config/db.php';

// Ambil data cart dengan join ke products
$stmt = $pdo->prepare("
    SELECT c.*, p.name, p.price, p.image, p.stock 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ?
    ORDER BY c.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll();

// Hitung total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<main class="flex-shrink-0">
<div class="container mt-4">
    <h2 class="mb-4">Keranjang Belanja</h2>
    
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>
    
    <?php if (count($cart_items) > 0): ?>
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <?php foreach ($cart_items as $item): ?>
                            <?php 
                            $imageSrc = (strpos($item['image'], 'http') === 0) ? $item['image'] : 'assets/images/' . $item['image'];
                            $subtotal = $item['price'] * $item['quantity'];
                            ?>
                            <div class="row mb-3 pb-3 border-bottom">
                                <div class="col-md-2">
                                    <img src="<?= htmlspecialchars($imageSrc) ?>" 
                                         class="img-fluid rounded" 
                                         style="object-fit: cover; height: 80px;"
                                         onerror="this.src='https://via.placeholder.com/100x100?text=No+Image'">
                                </div>
                                <div class="col-md-4">
                                    <h6><?= htmlspecialchars($item['name']) ?></h6>
                                    <p class="text-muted mb-0">Rp <?= number_format($item['price'], 0, ',', '.') ?></p>
                                </div>
                                <div class="col-md-3">
                                    <form action="../handlers/cart_update.php" method="POST" class="d-flex align-items-center">
                                        <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                                        <button type="submit" name="action" value="decrease" class="btn btn-sm btn-outline-secondary">-</button>
                                        <input type="number" name="quantity" value="<?= $item['quantity'] ?>" 
                                               min="1" max="<?= $item['stock'] ?>" 
                                               class="form-control form-control-sm mx-2 text-center" 
                                               style="width: 60px;">
                                        <button type="submit" name="action" value="increase" class="btn btn-sm btn-outline-secondary">+</button>
                                    </form>
                                </div>
                                <div class="col-md-2 text-end">
                                    <strong>Rp <?= number_format($subtotal, 0, ',', '.') ?></strong>
                                </div>
                                <div class="col-md-1 text-end">
                                    <form action="../handlers/cart_remove.php" method="POST">
                                        <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Hapus produk ini dari keranjang?')">
                                            <i>🗑️</i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Ringkasan Belanja</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <strong>Rp <?= number_format($total, 0, ',', '.') ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Ongkos Kirim</span>
                            <span class="text-muted">Dihitung di checkout</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total</strong>
                            <strong class="text-primary">Rp <?= number_format($total, 0, ',', '.') ?></strong>
                        </div>
                        <a href="checkout.php" class="btn btn-primary w-100 mb-2">Lanjut ke Checkout</a>
                        <a href="home.php" class="btn btn-outline-secondary w-100">Lanjut Belanja</a>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <h5>Keranjang belanja Anda kosong</h5>
            <p>Silakan tambahkan produk ke keranjang terlebih dahulu</p>
            <a href="home.php" class="btn btn-primary">Belanja Sekarang</a>
        </div>
    <?php endif; ?>
</div>
</main>

<?php include '../includes/footer.php'; ?>
