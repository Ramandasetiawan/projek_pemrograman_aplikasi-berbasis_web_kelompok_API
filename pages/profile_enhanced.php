<?php
require_once '../includes/header.php';
require_once '../config/csrf.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

require_once '../config/db.php';

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    check_csrf_token();

    if ($_POST['action'] === 'update_profile') {
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $_SESSION['user_id']]);
        if ($stmt->fetch()) {
            $error = "Email sudah digunakan oleh user lain!";
        } else {
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
            if ($stmt->execute([$full_name, $email, $phone, $address, $_SESSION['user_id']])) {
                $_SESSION['full_name'] = $full_name;
                $success = "Profil berhasil diupdate!";

                $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $user = $stmt->fetch();
            } else {
                $error = "Gagal update profil!";
            }
        }
    }

    if ($_POST['action'] === 'change_password') {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (!password_verify($current_password, $user['password'])) {
            $password_error = "Password saat ini salah!";
        } elseif (strlen($new_password) < 6) {
            $password_error = "Password baru minimal 6 karakter!";
        } elseif ($new_password !== $confirm_password) {
            $password_error = "Konfirmasi password tidak cocok!";
        } else {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            if ($stmt->execute([$hashed, $_SESSION['user_id']])) {
                $password_success = "Password berhasil diubah!";
            } else {
                $password_error = "Gagal mengubah password!";
            }
        }
    }
}

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM orders WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$total_orders = $stmt->fetch()['total'];

$stmt = $pdo->prepare("SELECT SUM(total_amount) as total FROM orders WHERE user_id = ? AND status != 'cancelled'");
$stmt->execute([$_SESSION['user_id']]);
$total_spent = $stmt->fetch()['total'] ?? 0;

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM orders WHERE user_id = ? AND status = 'pending'");
$stmt->execute([$_SESSION['user_id']]);
$pending_orders = $stmt->fetch()['total'];

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC LIMIT 5");
$stmt->execute([$_SESSION['user_id']]);
$recent_orders = $stmt->fetchAll();
?>

<main class="flex-shrink-0">
<div class="container mt-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../public/home.php">Home</a></li>
            <li class="breadcrumb-item active">Profil Saya</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar-placeholder mb-3" style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); color: white; display: flex; align-items: center; justify-content: center; font-size: 3rem; margin: 0 auto;">
                        <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
                    </div>
                    <h5><?= htmlspecialchars($user['full_name']) ?></h5>
                    <p class="text-muted small mb-0">@<?= htmlspecialchars($user['username']) ?></p>
                    <p class="text-muted small">
                        <i class="bi bi-envelope"></i> <?= htmlspecialchars($user['email']) ?>
                    </p>
                    <span class="badge bg-<?= $user['role'] == 'admin' ? 'danger' : 'primary' ?>">
                        <?= ucfirst($user['role']) ?>
                    </span>
                </div>
                <div class="list-group list-group-flush">
                    <a href="#profile" class="list-group-item list-group-item-action active" onclick="showTab('profile')">
                        <i class="bi bi-person"></i> Profil Saya
                    </a>
                    <a href="#password" class="list-group-item list-group-item-action" onclick="showTab('password')">
                        <i class="bi bi-lock"></i> Ubah Password
                    </a>
                    <a href="#orders" class="list-group-item list-group-item-action" onclick="showTab('orders')">
                        <i class="bi bi-box-seam"></i> Riwayat Pesanan
                    </a>
                    <a href="akun_saya.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-receipt"></i> Pesanan Saya
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Statistics Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="stat-card warning">
                        <div class="stat-icon">📦</div>
                        <h3><?= number_format($total_orders) ?></h3>
                        <p>Total Pesanan</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card success">
                        <div class="stat-icon">💰</div>
                        <h3>Rp <?= number_format($total_spent / 1000, 0) ?>K</h3>
                        <p>Total Belanja</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card info">
                        <div class="stat-icon">⏱️</div>
                        <h3><?= number_format($pending_orders) ?></h3>
                        <p>Pesanan Pending</p>
                    </div>
                </div>
            </div>

            <!-- Profile Tab -->
            <div id="profile-tab" class="tab-content">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-person-circle"></i> Informasi Profil</h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($success)): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="bi bi-check-circle"></i> <?= htmlspecialchars($success) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" id="profileForm">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="update_profile">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><i class="bi bi-person"></i> Username</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" disabled>
                                    <small class="text-muted">Username tidak dapat diubah</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><i class="bi bi-person-badge"></i> Nama Lengkap *</label>
                                    <input type="text" name="full_name" class="form-control" 
                                           value="<?= htmlspecialchars($user['full_name']) ?>" 
                                           required minlength="3">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><i class="bi bi-envelope"></i> Email *</label>
                                    <input type="email" name="email" class="form-control" 
                                           value="<?= htmlspecialchars($user['email']) ?>" 
                                           required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><i class="bi bi-telephone"></i> No. Telepon</label>
                                    <input type="tel" name="phone" class="form-control" 
                                           value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                                           placeholder="Contoh: 08123456789">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><i class="bi bi-geo-alt"></i> Alamat Lengkap</label>
                                <textarea name="address" class="form-control" rows="4" 
                                          placeholder="Masukkan alamat lengkap termasuk kode pos"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                                <small class="text-muted">Alamat akan digunakan untuk pengiriman pesanan</small>
                            </div>

                            <div class="mb-3">
                                <p class="text-muted small mb-0">
                                    <i class="bi bi-calendar"></i> Bergabung sejak: 
                                    <strong><?= date('d F Y', strtotime($user['created_at'])) ?></strong>
                                </p>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Simpan Perubahan
                                </button>
                                <button type="reset" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Password Tab -->
            <div id="password-tab" class="tab-content" style="display: none;">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-shield-lock"></i> Ubah Password</h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($password_success)): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="bi bi-check-circle"></i> <?= htmlspecialchars($password_success) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($password_error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($password_error) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" id="passwordForm">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="change_password">

                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> Password minimal 6 karakter untuk keamanan akun Anda.
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><i class="bi bi-key"></i> Password Saat Ini *</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><i class="bi bi-key-fill"></i> Password Baru *</label>
                                <input type="password" name="new_password" class="form-control" required minlength="6" id="new_password">
                                <small class="text-muted">Minimal 6 karakter</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><i class="bi bi-key-fill"></i> Konfirmasi Password Baru *</label>
                                <input type="password" name="confirm_password" class="form-control" required minlength="6" id="confirm_password">
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-shield-check"></i> Ubah Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Orders Tab -->
            <div id="orders-tab" class="tab-content" style="display: none;">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Riwayat Pesanan Terbaru</h5>
                        <a href="akun_saya.php" class="btn btn-light btn-sm">Lihat Semua</a>
                    </div>
                    <div class="card-body">
                        <?php if (count($recent_orders) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Tanggal</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_orders as $order): ?>
                                            <tr>
                                                <td><strong>#<?= $order['id'] ?></strong></td>
                                                <td><?= date('d M Y', strtotime($order['order_date'])) ?></td>
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
                                                    <span class="badge bg-<?= $badge_class ?>">
                                                        <?= ucfirst($order['status']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="order_detail.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i> Detail
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <div style="font-size: 4rem; opacity: 0.3;">📦</div>
                                <h5 class="text-muted">Belum ada pesanan</h5>
                                <p class="text-muted">Mulai belanja sekarang!</p>
                                <a href="products.php" class="btn btn-primary">
                                    <i class="bi bi-shop"></i> Belanja Sekarang
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</main>

<script>
function showTab(tabName) {

    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.style.display = 'none';
    });

    document.querySelectorAll('.list-group-item').forEach(link => {
        link.classList.remove('active');
    });

    document.getElementById(tabName + '-tab').style.display = 'block';

    event.target.closest('.list-group-item').classList.add('active');
}

document.getElementById('passwordForm')?.addEventListener('submit', function(e) {
    const newPass = document.getElementById('new_password').value;
    const confirmPass = document.getElementById('confirm_password').value;

    if (newPass !== confirmPass) {
        e.preventDefault();
        alert('Konfirmasi password tidak cocok dengan password baru!');
        return false;
    }
});

document.getElementById('profileForm')?.addEventListener('submit', function(e) {
    const form = e.target;
    if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
    }
    form.classList.add('was-validated');
});
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<?php include '../includes/footer.php'; ?>
