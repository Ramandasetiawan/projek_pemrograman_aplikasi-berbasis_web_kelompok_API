<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$base_path = '';
$assets_path = '../public/assets';

if (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) {
    $base_path = '../';
} elseif (strpos($_SERVER['PHP_SELF'], '/auth/') !== false) {
    $base_path = '../';
} elseif (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) {
    $base_path = '../';
} elseif (strpos($_SERVER['PHP_SELF'], '/public/') !== false) {
    $base_path = '../';
    $assets_path = 'assets';
} elseif (strpos($_SERVER['PHP_SELF'], '/handlers/') !== false) {
    $base_path = '../';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>etectstore - Toko Komponen Komputer</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="<?= $base_path ?><?= $assets_path ?>/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
      <a class="navbar-brand" href="<?= $base_path ?>public/home.php">🖥️ etectstore</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link" href="<?= $base_path ?>public/home.php">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= $base_path ?>pages/products.php">Produk</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= $base_path ?>pages/tentang_kami.php">Tentang Kami</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= $base_path ?>pages/kontak.php">Kontak</a>
          </li>
        </ul>
        <ul class="navbar-nav ms-auto">
          <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['role'] === 'admin'): ?>
              <li class="nav-item">
                <a class="nav-link" href="<?= $base_path ?>admin/dashboard.php">
                  <i class="bi bi-speedometer2"></i> Dashboard
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="<?= $base_path ?>admin/products.php">
                  <i class="bi bi-box-seam"></i> Kelola Produk
                </a>
              </li>
            <?php else: ?>
              <li class="nav-item">
                <a class="nav-link" href="<?= $base_path ?>pages/wishlist.php">
                  <i class="bi bi-heart"></i> Wishlist
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="<?= $base_path ?>pages/cart.php">
                  <i class="bi bi-cart3"></i> Keranjang
                </a>
              </li>
            <?php endif; ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['username']) ?>
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="<?= $base_path ?>pages/akun_saya.php">
                  <i class="bi bi-receipt"></i> Pesanan Saya
                </a></li>
                <li><a class="dropdown-item" href="<?= $base_path ?>pages/profile_enhanced.php">
                  <i class="bi bi-person"></i> Profil Saya
                </a></li>
                <?php if ($_SESSION['role'] !== 'admin'): ?>
                <li><a class="dropdown-item" href="<?= $base_path ?>pages/wishlist.php">
                  <i class="bi bi-heart"></i> Wishlist
                </a></li>
                <?php endif; ?>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="<?= $base_path ?>auth/logout.php">
                  <i class="bi bi-box-arrow-right"></i> Logout
                </a></li>
              </ul>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a href="<?= $base_path ?>auth/login.php" class="btn btn-light btn-sm me-2">
                <i class="bi bi-box-arrow-in-right"></i> Login
              </a>
            </li>
            <li class="nav-item">
              <a href="<?= $base_path ?>auth/register.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-person-plus"></i> Daftar
              </a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>