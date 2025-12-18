<?php include '../includes/header.php'; ?>
<main class="flex-shrink-0">
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <h3 class="text-center mb-4">Login - etectstore</h3>
      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
      <?php endif; ?>
      <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
      <?php endif; ?>
      <form action="process_login.php" method="POST">
        <div class="mb-3">
          <input type="text" name="username" class="form-control" placeholder="Username atau Email" required>
        </div>
        <div class="mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <button type="submit" class="btn btn-success w-100">Login</button>
        <div class="text-center mt-3">
          Belum punya akun? <a href="register.php">Daftar</a>
        </div>
      </form>
    </div>
  </div>
</div>
</main>
<?php include '../includes/footer.php'; ?>