<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}
include '../includes/header.php';
?>
<main class="flex-shrink-0">
<div class="container mt-4">
  <h2>Selamat Datang, Admin!</h2>
  <p><a href="products.php" class="btn btn-primary">Kelola Produk</a></p>
  <p><a href="../auth/logout.php" class="btn btn-outline-danger">Logout</a></p>
</div>
</main>
<?php include '../includes/footer.php'; ?>