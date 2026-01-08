<?php
require_once '../includes/header.php';
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

$stats = [];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM products");
$stats['products'] = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'customer'");
$stats['users'] = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM orders");
$stats['orders'] = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT SUM(total_amount) as total FROM orders WHERE status != 'cancelled'");
$stats['revenue'] = $stmt->fetch()['total'] ?? 0;

$stmt = $pdo->query("SELECT COUNT(*) as total FROM products WHERE stock < 10");
$stats['low_stock'] = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'");
$stats['pending_orders'] = $stmt->fetch()['total'];

$stmt = $pdo->query("
    SELECT o.*, u.username, u.full_name 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.order_date DESC 
    LIMIT 5
");
$recent_orders = $stmt->fetchAll();

$stmt = $pdo->query("
    SELECT p.*, SUM(oi.quantity) as total_sold 
    FROM products p
    JOIN order_items oi ON p.id = oi.product_id
    GROUP BY p.id
    ORDER BY total_sold DESC
    LIMIT 5
");
$top_products = $stmt->fetchAll();

$stmt = $pdo->query("
    SELECT DATE(order_date) as date, COUNT(*) as total_orders, SUM(total_amount) as total_sales
    FROM orders
    WHERE order_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY DATE(order_date)
    ORDER BY date ASC
");
$sales_data = $stmt->fetchAll();

$chart_labels = [];
$chart_orders = [];
$chart_sales = [];

foreach ($sales_data as $data) {
    $chart_labels[] = date('d M', strtotime($data['date']));
    $chart_orders[] = $data['total_orders'];
    $chart_sales[] = $data['total_sales'];
}
?>

<main class="flex-shrink-0">
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-1">Dashboard Admin</h2>
            <p class="text-muted">Selamat datang, <?= htmlspecialchars($_SESSION['full_name']) ?>!</p>
        </div>
        <div class="col-auto">
            <a href="products.php" class="btn btn-primary">
                <i class="bi bi-box-seam"></i> Kelola Produk
            </a>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon">📦</div>
                <h3><?= number_format($stats['products']) ?></h3>
                <p>Total Produk</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card success">
                <div class="stat-icon">👥</div>
                <h3><?= number_format($stats['users']) ?></h3>
                <p>Total Pelanggan</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card warning">
                <div class="stat-icon">🛒</div>
                <h3><?= number_format($stats['orders']) ?></h3>
                <p>Total Pesanan</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card info">
                <div class="stat-icon">💰</div>
                <h3>Rp <?= number_format($stats['revenue'] / 1000000, 1) ?>M</h3>
                <p>Total Pendapatan</p>
            </div>
        </div>
    </div>

    <!-- Alert Section -->
    <div class="row mb-4">
        <?php if ($stats['low_stock'] > 0): ?>
        <div class="col-md-6 mb-3">
            <div class="alert alert-warning mb-0">
                <h6 class="alert-heading">⚠️ Peringatan Stok Rendah</h6>
                <p class="mb-0">Ada <strong><?= $stats['low_stock'] ?></strong> produk dengan stok di bawah 10 unit. 
                <a href="products.php" class="alert-link">Lihat Produk</a></p>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($stats['pending_orders'] > 0): ?>
        <div class="col-md-6 mb-3">
            <div class="alert alert-info mb-0">
                <h6 class="alert-heading">📋 Pesanan Menunggu</h6>
                <p class="mb-0">Ada <strong><?= $stats['pending_orders'] ?></strong> pesanan yang menunggu diproses.</p>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Charts Section -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">📈 Grafik Penjualan 7 Hari Terakhir</h5>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">🏆 Produk Terlaris</h5>
                </div>
                <div class="card-body">
                    <?php if (count($top_products) > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($top_products as $index => $product): ?>
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-primary me-2">#<?= $index + 1 ?></span>
                                            <strong><?= htmlspecialchars(substr($product['name'], 0, 25)) ?><?= strlen($product['name']) > 25 ? '...' : '' ?></strong>
                                        </div>
                                        <span class="badge bg-success"><?= $product['total_sold'] ?> terjual</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center">Belum ada data penjualan</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">🕐 Pesanan Terbaru</h5>
                </div>
                <div class="card-body">
                    <?php if (count($recent_orders) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Pelanggan</th>
                                        <th>Tanggal</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Metode Pembayaran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_orders as $order): ?>
                                        <tr>
                                            <td><strong>#<?= $order['id'] ?></strong></td>
                                            <td><?= htmlspecialchars($order['full_name']) ?></td>
                                            <td><?= date('d M Y H:i', strtotime($order['order_date'])) ?></td>
                                            <td><strong>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></strong></td>
                                            <td>
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
                                                <span class="badge bg-<?= $badge_class ?>"><?= ucfirst($order['status']) ?></span>
                                            </td>
                                            <td><?= htmlspecialchars($order['payment_method']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center">Belum ada pesanan</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</main>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

const ctx = document.getElementById('salesChart');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($chart_labels) ?>,
        datasets: [{
            label: 'Total Penjualan (Rp)',
            data: <?= json_encode($chart_sales) ?>,
            borderColor: 'rgb(13, 110, 253)',
            backgroundColor: 'rgba(13, 110, 253, 0.1)',
            tension: 0.4,
            fill: true,
            yAxisID: 'y',
        }, {
            label: 'Jumlah Order',
            data: <?= json_encode($chart_orders) ?>,
            borderColor: 'rgb(25, 135, 84)',
            backgroundColor: 'rgba(25, 135, 84, 0.1)',
            tension: 0.4,
            fill: true,
            yAxisID: 'y1',
        }]
    },
    options: {
        responsive: true,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        plugins: {
            legend: {
                display: true,
                position: 'top',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        if (context.parsed.y !== null) {
                            if (context.datasetIndex === 0) {
                                label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                            } else {
                                label += context.parsed.y + ' order';
                            }
                        }
                        return label;
                    }
                }
            }
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'Penjualan (Rp)'
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'Jumlah Order'
                },
                grid: {
                    drawOnChartArea: false,
                }
            }
        }
    }
});
</script>

<?php include '../includes/footer.php'; ?>