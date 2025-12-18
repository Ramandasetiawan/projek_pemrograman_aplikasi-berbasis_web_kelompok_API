<?php include '../includes/header.php'; ?>
<main class="flex-shrink-0">
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <h3 class="text-center mb-4">Daftar - etectstore</h3>
      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
      <?php endif; ?>
      <form action="process_register.php" method="POST">
        <div class="mb-3">
          <input type="text" name="username" class="form-control" placeholder="Username" required>
        </div>
        <div class="mb-3">
          <input type="email" name="email" class="form-control" placeholder="Email" required>
        </div>
        <div class="mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password (min 6 karakter)" required minlength="6">
        </div>
        <button type="submit" class="btn btn-primary w-100">Daftar</button>
        <div class="text-center mt-3">
          Sudah punya akun? <a href="login.php">Login</a>
        </div>
      </form>
    </div>
  </div>
</div>
</main>
<?php include '../includes/footer.php'; ?>