<?php
require_once '../includes/header.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    header('Location: ../public/index.php');
    exit;
}

require_once '../config/db.php';

$order_id = (int)$_GET['order_id'];

// Ambil data order
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: index.php');
    exit;
}

// Ambil order items
$stmt = $pdo->prepare("
    SELECT oi.*, p.name 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();
?>

<main class="flex-shrink-0">
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-4">
                        <span style="font-size: 64px;">✅</span>
                    </div>
                    <h2 class="text-success mb-3">Pesanan Berhasil!</h2>
                    <p class="lead">Terima kasih telah berbelanja di etectstore</p>
                    <p class="text-muted">Pesanan Anda sedang diproses</p>
                    
                    <div class="alert alert-info mt-4">
                        <h5>ID Pesanan: #<?= $order_id ?></h5>
                        <p class="mb-0">Total: <strong>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></strong></p>
                    </div>
                    
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">Detail Pesanan</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Produk</th>
                                            <th>Jumlah</th>
                                            <th>Harga</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $item): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($item['name']) ?></td>
                                                <td><?= $item['quantity'] ?></td>
                                                <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                                                <td>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <hr>
                            <p><strong>Alamat Pengiriman:</strong></p>
                            <p><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
                            
                            <p><strong>Metode Pembayaran:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
                            
                            <?php if ($order['notes']): ?>
                                <p><strong>Catatan:</strong> <?= htmlspecialchars($order['notes']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="profile.php" class="btn btn-primary">Lihat Riwayat Pesanan</a>
                        <a href="home.php" class="btn btn-outline-secondary">Kembali Belanja</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</main>

<?php include '../includes/footer.php'; ?>
