<?php
require_once '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

require_once '../config/db.php';

// Ambil data user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Update profil jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    
    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
    if ($stmt->execute([$full_name, $email, $phone, $address, $_SESSION['user_id']])) {
        $success = "Profil berhasil diupdate!";
        // Refresh data
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
    }
}

// Ubah password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (!password_verify($current_password, $user['password'])) {
        $password_error = "Password lama salah";
    } elseif ($new_password !== $confirm_password) {
        $password_error = "Password baru tidak cocok";
    } elseif (strlen($new_password) < 6) {
        $password_error = "Password minimal 6 karakter";
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        if ($stmt->execute([$hashed, $_SESSION['user_id']])) {
            $password_success = "Password berhasil diubah!";
        }
    }
}

// Ambil riwayat pesanan
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();

// Ambil statistik
$stmt = $pdo->prepare("SELECT COUNT(*) as total_orders, SUM(total_amount) as total_spent FROM orders WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$stats = $stmt->fetch();
?>

<main class="flex-shrink-0">
<div class="container mt-4">
    <h2 class="mb-4">Akun Saya</h2>
    
    <div class="row">
        <!-- Sidebar Menu -->
        <div class="col-md-3 mb-4">
            <div class="list-group">
                <a href="#profil" class="list-group-item list-group-item-action active" data-bs-toggle="list">
                    👤 Profil Saya
                </a>
                <a href="#password" class="list-group-item list-group-item-action" data-bs-toggle="list">
                    🔒 Ubah Password
                </a>
                <a href="#pesanan" class="list-group-item list-group-item-action" data-bs-toggle="list">
                    📦 Riwayat Pesanan
                </a>
                <a href="cart.php" class="list-group-item list-group-item-action">
                    🛒 Keranjang Belanja
                </a>
                <a href="auth/logout.php" class="list-group-item list-group-item-action text-danger">
                    🚪 Logout
                </a>
            </div>
            
            <!-- Stats Card -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Statistik</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Total Pesanan:</strong><br><?= $stats['total_orders'] ?? 0 ?> pesanan</p>
                    <p class="mb-2"><strong>Total Belanja:</strong><br>Rp <?= number_format($stats['total_spent'] ?? 0, 0, ',', '.') ?></p>
                    <p class="mb-0"><strong>Bergabung:</strong><br><?= date('d/m/Y', strtotime($user['created_at'])) ?></p>
                </div>
            </div>
        </div>
        
        <!-- Content -->
        <div class="col-md-9">
            <div class="tab-content">
                <!-- Profil -->
                <div class="tab-pane fade show active" id="profil">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Informasi Profil</h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($success)): ?>
                                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                            <?php endif; ?>
                            
                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Username</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" disabled>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Role</label>
                                        <input type="text" class="form-control" value="<?= ucfirst($user['role']) ?>" disabled>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Nama Lengkap *</label>
                                    <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Email *</label>
                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">No. Telepon</label>
                                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="+62">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Alamat Lengkap</label>
                                    <textarea name="address" class="form-control" rows="3" placeholder="Alamat lengkap dengan kode pos"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                                </div>
                                
                                <button type="submit" name="update_profile" class="btn btn-primary">
                                    💾 Simpan Perubahan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Password -->
                <div class="tab-pane fade" id="password">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Ubah Password</h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($password_success)): ?>
                                <div class="alert alert-success"><?= htmlspecialchars($password_success) ?></div>
                            <?php endif; ?>
                            <?php if (isset($password_error)): ?>
                                <div class="alert alert-danger"><?= htmlspecialchars($password_error) ?></div>
                            <?php endif; ?>
                            
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Password Lama *</label>
                                    <input type="password" name="current_password" class="form-control" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Password Baru *</label>
                                    <input type="password" name="new_password" class="form-control" minlength="6" required>
                                    <small class="text-muted">Minimal 6 karakter</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Konfirmasi Password Baru *</label>
                                    <input type="password" name="confirm_password" class="form-control" minlength="6" required>
                                </div>
                                
                                <button type="submit" name="change_password" class="btn btn-primary">
                                    🔒 Ubah Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Pesanan -->
                <div class="tab-pane fade" id="pesanan">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Riwayat Pesanan</h5>
                        </div>
                        <div class="card-body">
                            <?php if (count($orders) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID Pesanan</th>
                                                <th>Tanggal</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th>Pembayaran</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($orders as $order): ?>
                                                <tr>
                                                    <td><strong>#<?= $order['id'] ?></strong></td>
                                                    <td><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></td>
                                                    <td>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></td>
                                                    <td>
                                                        <?php
                                                        $badge_class = match($order['status']) {
                                                            'delivered' => 'success',
                                                            'cancelled' => 'danger',
                                                            'pending' => 'warning',
                                                            default => 'info'
                                                        };
                                                        ?>
                                                        <span class="badge bg-<?= $badge_class ?>">
                                                            <?= ucfirst($order['status']) ?>
                                                        </span>
                                                    </td>
                                                    <td><?= htmlspecialchars($order['payment_method'] ?? '-') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <div style="font-size: 4rem;">📦</div>
                                    <h5 class="mt-3">Belum Ada Pesanan</h5>
                                    <p class="text-muted">Anda belum pernah melakukan pemesanan</p>
                                    <a href="products.php" class="btn btn-primary">Mulai Belanja</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</main>

<?php include '../includes/footer.php'; ?>
