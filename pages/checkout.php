<?php
require_once '../includes/header.php';
require_once '../config/csrf.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

require_once '../config/db.php';

// Ambil data cart
$stmt = $pdo->prepare("
    SELECT c.*, p.name, p.price, p.image 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll();

if (count($cart_items) === 0) {
    header('Location: cart.php?error=Keranjang kosong');
    exit;
}

// Hitung total
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$shipping_cost = 25000; // Ongkir flat
$total = $subtotal + $shipping_cost;

// Ambil data user untuk default alamat
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Process order jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    check_csrf_token();
    
    $shipping_address = trim($_POST['shipping_address']);
    $payment_method = $_POST['payment_method'];
    $notes = trim($_POST['notes'] ?? '');
    
    if (empty($shipping_address)) {
        $error = "Alamat pengiriman harus diisi";
    } else {
        try {
            $pdo->beginTransaction();
            
            // Validasi ulang stok untuk setiap item dengan row locking
            $stock_errors = [];
            foreach ($cart_items as $item) {
                // Lock row untuk prevent race condition
                $stmt = $pdo->prepare("SELECT id, stock FROM products WHERE id = ? FOR UPDATE");
                $stmt->execute([$item['product_id']]);
                $product = $stmt->fetch();
                
                if (!$product) {
                    $stock_errors[] = $item['name'] . " tidak ditemukan";
                } elseif ($product['stock'] < $item['quantity']) {
                    $stock_errors[] = $item['name'] . " stok tidak mencukupi (tersisa: " . $product['stock'] . ")";
                }
            }
            
            // Jika ada error stok, rollback dan tampilkan error
            if (!empty($stock_errors)) {
                $pdo->rollBack();
                $error = "Gagal checkout: " . implode(", ", $stock_errors);
            } else {
                // Insert order
                $stmt = $pdo->prepare("
                    INSERT INTO orders (user_id, total_amount, status, payment_method, shipping_address, notes) 
                    VALUES (?, ?, 'pending', ?, ?, ?)
                ");
                $stmt->execute([$_SESSION['user_id'], $total, $payment_method, $shipping_address, $notes]);
                $order_id = $pdo->lastInsertId();
                
                // Insert order items dan update stok
                foreach ($cart_items as $item) {
                    $subtotal_item = $item['price'] * $item['quantity'];
                    $stmt = $pdo->prepare("
                        INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) 
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price'], $subtotal_item]);
                    
                    // Update stok produk (already locked by FOR UPDATE)
                    $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                    $stmt->execute([$item['quantity'], $item['product_id']]);
                }
                
                // Hapus cart
                $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                
                $pdo->commit();
                
                header('Location: order_success.php?order_id=' . $order_id);
                exit;
            }
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}
?>

<main class="flex-shrink-0">
<div class="container mt-4">
    <h2 class="mb-4">Checkout</h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <?= csrf_field() ?>
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi Pengiriman</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($user['full_name']) ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '-') ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat Pengiriman *</label>
                            <textarea name="shipping_address" class="form-control" rows="3" required><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                            <small class="text-muted">Harap masukkan alamat lengkap termasuk kode pos</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan (Opsional)</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Catatan untuk penjual..."></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Metode Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" value="Transfer Bank" id="bank" checked>
                            <label class="form-check-label" for="bank">
                                <strong>Transfer Bank</strong><br>
                                <small class="text-muted">BCA, Mandiri, BNI, BRI</small>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" value="E-Wallet" id="ewallet">
                            <label class="form-check-label" for="ewallet">
                                <strong>E-Wallet</strong><br>
                                <small class="text-muted">GoPay, OVO, Dana, ShopeePay</small>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" value="COD" id="cod">
                            <label class="form-check-label" for="cod">
                                <strong>COD (Cash on Delivery)</strong><br>
                                <small class="text-muted">Bayar saat barang diterima</small>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Ringkasan Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="d-flex justify-content-between mb-2">
                                <span><?= htmlspecialchars($item['name']) ?> (x<?= $item['quantity'] ?>)</span>
                                <span>Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></span>
                            </div>
                        <?php endforeach; ?>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span>Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Ongkos Kirim</span>
                            <span>Rp <?= number_format($shipping_cost, 0, ',', '.') ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total</strong>
                            <strong class="text-primary">Rp <?= number_format($total, 0, ',', '.') ?></strong>
                        </div>
                        <button type="submit" class="btn btn-success w-100 mb-2">Buat Pesanan</button>
                        <a href="cart.php" class="btn btn-outline-secondary w-100">Kembali ke Keranjang</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
</main>

<?php include '../includes/footer.php'; ?>
