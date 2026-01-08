  <footer class="bg-dark text-light py-5 mt-auto">
    <div class="container">
      <div class="row">
        <div class="col-md-4 mb-4">
          <h5><i class="bi bi-laptop"></i> etectstore</h5>
          <p class="text-light-50">Toko komponen komputer terpercaya dengan harga terbaik dan kualitas terjamin.</p>
          <div class="social-links">
            <a href="#" class="text-light me-3"><i class="bi bi-facebook"></i></a>
            <a href="#" class="text-light me-3"><i class="bi bi-instagram"></i></a>
            <a href="#" class="text-light me-3"><i class="bi bi-twitter"></i></a>
            <a href="#" class="text-light"><i class="bi bi-youtube"></i></a>
          </div>
        </div>
        <div class="col-md-2 mb-4">
          <h5>Menu</h5>
          <ul class="list-unstyled">
            <li><a href="<?= $base_path ?>public/home.php" class="text-light">Home</a></li>
            <li><a href="<?= $base_path ?>pages/products.php" class="text-light">Produk</a></li>
            <li><a href="<?= $base_path ?>pages/tentang_kami.php" class="text-light">Tentang Kami</a></li>
            <li><a href="<?= $base_path ?>pages/kontak.php" class="text-light">Kontak</a></li>
          </ul>
        </div>
        <div class="col-md-3 mb-4">
          <h5>Layanan</h5>
          <ul class="list-unstyled">
            <?php if (isset($_SESSION['user_id'])): ?>
              <li><a href="<?= $base_path ?>pages/akun_saya.php" class="text-light">Pesanan Saya</a></li>
              <li><a href="<?= $base_path ?>pages/cart.php" class="text-light">Keranjang</a></li>
            <?php else: ?>
              <li><a href="<?= $base_path ?>auth/login.php" class="text-light">Login</a></li>
              <li><a href="<?= $base_path ?>auth/register.php" class="text-light">Daftar</a></li>
            <?php endif; ?>
            <li><a href="#" class="text-light">FAQ</a></li>
            <li><a href="#" class="text-light">Kebijakan Privasi</a></li>
          </ul>
        </div>
        <div class="col-md-3 mb-4">
          <h5>Kontak</h5>
          <ul class="list-unstyled text-light">
            <li><i class="bi bi-geo-alt"></i> Jakarta, Indonesia</li>
            <li><i class="bi bi-telephone"></i> +62 123 4567 890</li>
            <li><i class="bi bi-envelope"></i> info@etectstore.com</li>
            <li><i class="bi bi-clock"></i> Senin - Sabtu, 09:00 - 18:00</li>
          </ul>
        </div>
      </div>
      <hr class="border-light">
      <div class="row">
        <div class="col-md-6 text-center text-md-start">
          <p class="mb-0">&copy; 2025 etectstore - All Rights Reserved</p>
        </div>
        <div class="col-md-6 text-center text-md-end">
          <p class="mb-0">Made with <i class="bi bi-heart-fill text-danger"></i> by Development Team</p>
        </div>
      </div>
    </div>
  </footer>

  <!-- Scroll to Top Button -->
  <button id="scroll-to-top" onclick="etectstore.scrollToTop()" 
          style="display: none; position: fixed; bottom: 20px; right: 20px; z-index: 999; 
                 width: 50px; height: 50px; border-radius: 50%; border: none; 
                 background: var(--primary-color); color: white; font-size: 1.5rem; 
                 box-shadow: 0 4px 6px rgba(0,0,0,0.1); cursor: pointer; transition: all 0.3s;">
    <i class="bi bi-arrow-up"></i>
  </button>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?= $base_path ?><?= $assets_path ?>/js/main.js"></script>
</body>
</html>