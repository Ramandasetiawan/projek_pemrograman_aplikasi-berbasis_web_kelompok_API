<?php
require_once '../includes/header.php';
require_once '../config/db.php';

// Process form jika disubmit
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'Semua field wajib diisi';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid';
    } else {
        // Di sini bisa disimpan ke database atau kirim email
        // Untuk demo, kita anggap berhasil
        $success = true;
    }
}
?>

<main class="flex-shrink-0">
<!-- Hero Section -->
<div class="bg-primary text-white py-5">
  <div class="container text-center">
    <h1 class="display-4 fw-bold mb-3">Hubungi Kami</h1>
    <p class="lead">Kami siap membantu Anda 24/7</p>
  </div>
</div>

<div class="container my-5">
  <div class="row">
    <!-- Form Kontak -->
    <div class="col-md-7 mb-4">
      <div class="card">
        <div class="card-header">
          <h4 class="mb-0">Kirim Pesan</h4>
        </div>
        <div class="card-body">
          <?php if ($success): ?>
            <div class="alert alert-success">
              <h5>Pesan Berhasil Dikirim!</h5>
              <p>Terima kasih telah menghubungi kami. Tim kami akan segera merespon pesan Anda dalam 1x24 jam.</p>
            </div>
          <?php else: ?>
            <?php if ($error): ?>
              <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="POST">
              <div class="mb-3">
                <label class="form-label">Nama Lengkap *</label>
                <input type="text" name="name" class="form-control" required 
                       value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">
              </div>
              
              <div class="mb-3">
                <label class="form-label">Email *</label>
                <input type="email" name="email" class="form-control" required
                       value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
              </div>
              
              <div class="mb-3">
                <label class="form-label">Subjek *</label>
                <input type="text" name="subject" class="form-control" required
                       value="<?= isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : '' ?>">
              </div>
              
              <div class="mb-3">
                <label class="form-label">Pesan *</label>
                <textarea name="message" class="form-control" rows="5" required><?= isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '' ?></textarea>
              </div>
              
              <button type="submit" class="btn btn-primary">Kirim Pesan</button>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </div>
    
    <!-- Info Kontak -->
    <div class="col-md-5">
      <div class="card mb-4">
        <div class="card-header">
          <h4 class="mb-0">Informasi Kontak</h4>
        </div>
        <div class="card-body">
          <div class="mb-4">
            <h6 class="mb-2">📍 Alamat</h6>
            <p class="text-muted">
              Jl. Sudirman No. 123<br>
              Jakarta Pusat 10110<br>
              Indonesia
            </p>
          </div>
          
          <div class="mb-4">
            <h6 class="mb-2">📞 Telepon</h6>
            <p class="text-muted">
              <a href="tel:+622112345678" class="text-decoration-none">+62 21 1234 5678</a><br>
              <a href="tel:+628123456789" class="text-decoration-none">+62 812 3456 789</a>
            </p>
          </div>
          
          <div class="mb-4">
            <h6 class="mb-2">📧 Email</h6>
            <p class="text-muted">
              <a href="mailto:info@etectstore.com" class="text-decoration-none">info@etectstore.com</a><br>
              <a href="mailto:support@etectstore.com" class="text-decoration-none">support@etectstore.com</a>
            </p>
          </div>
          
          <div class="mb-4">
            <h6 class="mb-2">🕒 Jam Operasional</h6>
            <p class="text-muted">
              Senin - Jumat: 09.00 - 18.00 WIB<br>
              Sabtu: 09.00 - 15.00 WIB<br>
              Minggu: Libur
            </p>
          </div>
        </div>
      </div>
      
      <div class="card">
        <div class="card-header">
          <h4 class="mb-0">Media Sosial</h4>
        </div>
        <div class="card-body">
          <div class="d-grid gap-2">
            <a href="#" class="btn btn-outline-primary">
              📘 Facebook - etectstore
            </a>
            <a href="#" class="btn btn-outline-info">
              🐦 Twitter - @etectstore
            </a>
            <a href="#" class="btn btn-outline-danger">
              📷 Instagram - @etectstore
            </a>
            <a href="#" class="btn btn-outline-success">
              💬 WhatsApp - +62 812 3456 789
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Map -->
  <div class="row mt-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="mb-0">Lokasi Kami</h4>
        </div>
        <div class="card-body p-0">
          <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.521260322283!2d106.8195613!3d-6.1944491!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f436b8c94b07%3A0x6ea6d5398b7c82f6!2sJl.%20Sudirman%2C%20Jakarta!5e0!3m2!1sen!2sid!4v1234567890"
            width="100%" 
            height="400" 
            style="border:0;" 
            allowfullscreen="" 
            loading="lazy">
          </iframe>
        </div>
      </div>
    </div>
  </div>
  
  <!-- FAQ -->
  <div class="row mt-5">
    <div class="col-12">
      <h3 class="mb-4">Pertanyaan yang Sering Diajukan</h3>
      <div class="accordion" id="faqAccordion">
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
              Bagaimana cara memesan produk?
            </button>
          </h2>
          <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              Anda dapat memesan produk dengan mengklik tombol "Tambah ke Keranjang" pada produk yang diinginkan, kemudian lanjutkan ke checkout dan ikuti langkah-langkah pembayaran.
            </div>
          </div>
        </div>
        
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
              Berapa lama pengiriman produk?
            </button>
          </h2>
          <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              Pengiriman biasanya memakan waktu 2-5 hari kerja tergantung lokasi tujuan. Anda akan mendapatkan nomor resi untuk tracking pengiriman.
            </div>
          </div>
        </div>
        
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
              Apakah produk bergaransi?
            </button>
          </h2>
          <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              Ya, semua produk yang kami jual memiliki garansi resmi dari distributor/brand sesuai dengan ketentuan masing-masing produk.
            </div>
          </div>
        </div>
        
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
              Bagaimana cara retur produk?
            </button>
          </h2>
          <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              Anda dapat mengajukan retur dalam 7 hari setelah produk diterima dengan syarat produk masih dalam kondisi baru dan kemasan lengkap. Hubungi customer service kami untuk proses retur.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

</main>

<?php include '../includes/footer.php'; ?>
