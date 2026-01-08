<?php
require_once '../includes/header.php';
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($order_id == 0) {
    header('Location: akun_saya.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT o.*, u.full_name, u.email, u.phone 
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.id = ? AND o.user_id = ?
");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: akun_saya.php?error=Pesanan tidak ditemukan');
    exit;
}

$stmt = $pdo->prepare("
    SELECT oi.*, p.name, p.image, p.brand
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();

$status_steps = [
    'pending' => ['label' => 'Pending', 'icon' => '🕐', 'description' => 'Pesanan sedang diproses'],
    'processing' => ['label' => 'Diproses', 'icon' => '📦', 'description' => 'Pesanan sedang dikemas'],
    'shipped' => ['label' => 'Dikirim', 'icon' => '🚚', 'description' => 'Pesanan dalam pengiriman'],
    'delivered' => ['label' => 'Selesai', 'icon' => '✅', 'description' => 'Pesanan telah diterima'],
];

$cancelled_step = ['label' => 'Dibatalkan', 'icon' => '❌', 'description' => 'Pesanan dibatalkan'];

$current_status = $order['status'];
$status_order = ['pending', 'processing', 'shipped', 'delivered'];
$current_step = array_search($current_status, $status_order);
?>

<main class="flex-shrink-0">
<div class="container mt-4 mb-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../public/home.php">Home</a></li>
            <li class="breadcrumb-item"><a href="akun_saya.php">Pesanan Saya</a></li>
            <li class="breadcrumb-item active">Detail Pesanan #<?= $order_id ?></li>
        </ol>
    </nav>

    <!-- Order Header -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <h2><i class="bi bi-receipt"></i> Detail Pesanan #<?= $order_id ?></h2>
            <p class="text-muted">
                Tanggal Pemesanan: <strong><?= date('d F Y, H:i', strtotime($order['order_date'])) ?></strong>
            </p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <?php
            $status_badges = [
                'pending' => 'warning',
                'processing' => 'info',
                'shipped' => 'primary',
                'delivered' => 'success',
                'cancelled' => 'danger'
            ];
            $badge_class = $status_badges[$order['status']] ?? 'secondary';
            ?>
            <h3>
                <span class="badge bg-<?= $badge_class ?>">
                    <?= ucfirst($order['status']) ?>
                </span>
            </h3>
        </div>
    </div>

    <!-- Order Tracking Timeline -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-truck"></i> Status Pengiriman</h5>
        </div>
        <div class="card-body">
            <?php if ($current_status === 'cancelled'): ?>
                <div class="alert alert-danger">
                    <h5><?= $cancelled_step['icon'] ?> <?= $cancelled_step['label'] ?></h5>
                    <p class="mb-0"><?= $cancelled_step['description'] ?></p>
                    <?php if ($order['notes']): ?>
                        <hr>
                        <small><strong>Catatan:</strong> <?= htmlspecialchars($order['notes']) ?></small>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="tracking-timeline">
                    <div class="row">
                        <?php foreach ($status_steps as $key => $step): ?>
                            <?php
                            $step_index = array_search($key, $status_order);
                            $is_completed = $step_index <= $current_step;
                            $is_current = $step_index == $current_step;
                            ?>
                            <div class="col-md-3 col-6">
                                <div class="tracking-step <?= $is_completed ? 'completed' : '' ?> <?= $is_current ? 'current' : '' ?>">
                                    <div class="tracking-icon">
                                        <?= $step['icon'] ?>
                                    </div>
                                    <div class="tracking-label">
                                        <strong><?= $step['label'] ?></strong>
                                    </div>
                                    <div class="tracking-description">
                                        <small><?= $step['description'] ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <!-- Order Items -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-box-seam"></i> Item Pesanan</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($items as $item): ?>
                        <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                            <?php 
                            $imageSrc = (strpos($item['image'], 'http') === 0) ? $item['image'] : '../public/assets/images/' . $item['image'];
                            ?>
                            <img src="<?= htmlspecialchars($imageSrc) ?>" 
                                 alt="<?= htmlspecialchars($item['name']) ?>"
                                 style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;"
                                 onerror="this.src='https://via.placeholder.com/100?text=No+Image'">
                            <div class="flex-grow-1">
                                <h6><?= htmlspecialchars($item['name']) ?></h6>
                                <?php if ($item['brand']): ?>
                                    <p class="text-muted small mb-1">
                                        <i class="bi bi-tag"></i> <?= htmlspecialchars($item['brand']) ?>
                                    </p>
                                <?php endif; ?>
                                <p class="mb-1">
                                    <strong>Rp <?= number_format($item['price'], 0, ',', '.') ?></strong> x <?= $item['quantity'] ?>
                                </p>
                                <p class="text-primary fw-bold mb-0">
                                    Subtotal: Rp <?= number_format($item['subtotal'], 0, ',', '.') ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Shipping Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-geo-alt"></i> Informasi Pengiriman</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6>Penerima:</h6>
                            <p class="mb-1"><strong><?= htmlspecialchars($order['full_name']) ?></strong></p>
                            <?php if ($order['phone']): ?>
                                <p class="mb-1">
                                    <i class="bi bi-telephone"></i> <?= htmlspecialchars($order['phone']) ?>
                                </p>
                            <?php endif; ?>
                            <p class="mb-0">
                                <i class="bi bi-envelope"></i> <?= htmlspecialchars($order['email']) ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Alamat Pengiriman:</h6>
                            <p><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
                        </div>
                    </div>
                    <?php if ($order['notes']): ?>
                        <hr>
                        <h6>Catatan:</h6>
                        <p class="text-muted"><?= nl2br(htmlspecialchars($order['notes'])) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-calculator"></i> Ringkasan Pesanan</h5>
                </div>
                <div class="card-body">
                    <?php
                    $subtotal = 0;
                    foreach ($items as $item) {
                        $subtotal += $item['subtotal'];
                    }
                    $shipping_cost = 25000;
                    ?>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal (<?= count($items) ?> item)</span>
                        <strong>Rp <?= number_format($subtotal, 0, ',', '.') ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Ongkos Kirim</span>
                        <strong>Rp <?= number_format($shipping_cost, 0, ',', '.') ?></strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <h5 class="mb-0">Total</h5>
                        <h5 class="text-primary mb-0">Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></h5>
                    </div>

                    <div class="d-grid gap-2">
                        <div class="alert alert-info mb-0">
                            <small>
                                <i class="bi bi-credit-card"></i> Metode Pembayaran:<br>
                                <strong><?= htmlspecialchars($order['payment_method']) ?></strong>
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-tools"></i> Aksi</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="akun_saya.php" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left"></i> Kembali ke Pesanan
                        </a>
                        <button class="btn btn-outline-secondary" onclick="window.print()">
                            <i class="bi bi-printer"></i> Cetak Invoice
                        </button>
                        <?php if ($order['status'] === 'delivered'): ?>
                            <a href="products.php" class="btn btn-primary">
                                <i class="bi bi-shop"></i> Belanja Lagi
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</main>

<style>
.tracking-timeline {
    padding: 2rem 0;
}

.tracking-step {
    text-align: center;
    position: relative;
    padding: 1rem 0;
}

.tracking-step::before {
    content: '';
    position: absolute;
    top: 35px;
    left: 50%;
    width: 100%;
    height: 4px;
    background: #dee2e6;
    z-index: 1;
}

.tracking-step:first-child::before {
    left: 50%;
    width: 50%;
}

.tracking-step:last-child::before {
    right: 50%;
    width: 50%;
    left: 0;
}

.tracking-step.completed::before {
    background: var(--primary-color);
}

.tracking-icon {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: #dee2e6;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    margin: 0 auto 0.5rem;
    position: relative;
    z-index: 2;
    border: 4px solid white;
}

.tracking-step.completed .tracking-icon {
    background: var(--primary-color);
    color: white;
}

.tracking-step.current .tracking-icon {
    background: var(--warning-color);
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 0 0 10px rgba(255, 193, 7, 0);
    }
}

.tracking-label {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.tracking-description {
    color: #6c757d;
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .tracking-step::before {
        display: none;
    }

    .tracking-icon {
        width: 50px;
        height: 50px;
        font-size: 1.5rem;
    }
}
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<?php include '../includes/footer.php'; ?>
