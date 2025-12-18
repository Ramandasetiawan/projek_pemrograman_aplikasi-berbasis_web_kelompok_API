<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>etectstore - Toko Alat Komputer</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
      <a class="navbar-brand" href="index.php">etectstore</a>
      <div class="navbar-nav ms-auto">
        <?php
        session_start();
        if (isset($_SESSION['user_id'])):
        ?>
          <span class="navbar-text me-3">Halo, <?= htmlspecialchars($_SESSION['username']) ?></span>
          <a href="auth/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        <?php else: ?>
          <a href="auth/login.php" class="btn btn-light btn-sm">Login</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>