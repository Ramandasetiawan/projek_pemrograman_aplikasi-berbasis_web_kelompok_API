# Panduan Optimasi & Deployment - etectstore

## 🔧 Optimasi yang Telah Diterapkan

### 1. Security Improvements

#### ✅ CSRF Protection
- **File:** `config/csrf.php`
- **Implementasi:** Token CSRF ditambahkan ke semua form
- **Fungsi:**
  - `generate_csrf_token()` - Generate token baru
  - `verify_csrf_token()` - Validasi token
  - `csrf_field()` - Output hidden field untuk form
  - `check_csrf_token()` - Check token dari POST request

#### ✅ Secure File Upload
- **File:** `config/upload_helper.php`
- **Validasi:**
  - MIME type checking
  - File extension validation
  - File size limit (2MB default)
  - Safe filename generation
  - Directory creation with proper permissions
- **Fungsi:**
  - `validate_and_save_image()` - Upload validation & save
  - `delete_image_safe()` - Safe file deletion
  - `sanitize_filename()` - Sanitize filename

#### ✅ Input Validation & Sanitization
- **Implementasi:** 
  - `filter_var()` untuk validasi input
  - `trim()` untuk sanitasi string
  - `htmlspecialchars()` untuk output escaping
  - Prepared statements untuk SQL injection prevention

#### ✅ Race Condition Prevention
- **File:** `pages/checkout.php`
- **Implementasi:** Row locking dengan `FOR UPDATE`
- **Benefit:** Prevent overselling products

#### ✅ Session Security
- **Implementasi:** 
  - `session_regenerate_id()` setelah login
  - `ob_start()` untuk prevent header issues
  - Session cookie dengan httponly flag

---

### 2. Performance Optimizations

#### ✅ Database Optimization
- **File:** `database/optimize.sql`
- **Optimasi:**
  - Index pada foreign keys
  - Index pada frequently queried columns
  - Fulltext index untuk search
  - Table optimization

#### ✅ Query Optimization
- **Perubahan:**
  - Replace `ORDER BY RAND()` dengan random offset
  - Efficient JOIN queries
  - Proper use of LIMIT dan OFFSET

#### ✅ Caching System
- **File:** `config/cache.php`
- **Implementasi:** File-based caching
- **Fungsi:**
  - `cache_remember()` - Get or set cache
  - `cache_invalidate()` - Delete cache
  - `cache_clear_all()` - Clear all cache
- **Default TTL:** 5 minutes (300 seconds)

#### ✅ Browser Caching & Compression
- **File:** `.htaccess`
- **Optimasi:**
  - Gzip compression untuk text files
  - Browser caching untuk static assets
  - Expires headers untuk images, CSS, JS

---

## 🚀 Cara Deploy ke Production

### Langkah 1: Persiapan Database

```sql
-- 1. Import database
mysql -u root -p < database/etectstore.sql

-- 2. Jalankan optimasi
mysql -u root -p etectstore < database/optimize.sql

-- 3. Verifikasi indexes
SHOW INDEX FROM products;
SHOW INDEX FROM cart;
SHOW INDEX FROM orders;
```

### Langkah 2: Konfigurasi Database

Edit `config/db.php`:

```php
<?php
$host = 'localhost';
$db   = 'etectstore';
$user = 'your_db_user';      // GANTI dengan user production
$pass = 'your_secure_pass';  // GANTI dengan password aman
```

### Langkah 3: File Permissions

```bash
# Set proper permissions
chmod 755 public/
chmod 755 public/assets/
chmod 755 public/assets/images/
chmod 777 cache/  # Cache directory (create if not exists)
chmod 644 .htaccess
chmod 600 config/db.php  # Protect database config
```

### Langkah 4: Enable Security Features

Edit `.htaccess`:

```apache
# Uncomment untuk force HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Enable Content Security Policy
Header set Content-Security-Policy "default-src 'self'; ..."
```

Edit `config/db.php` untuk production error handling:

```php
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Production: Don't expose error details
    error_log("Database connection failed: " . $e->getMessage());
    die("System error. Please contact administrator.");
}
```

### Langkah 5: PHP Configuration

Edit `php.ini` atau `.htaccess`:

```ini
; Error handling
display_errors = Off
log_errors = On
error_log = /path/to/error.log

; Security
expose_php = Off
allow_url_fopen = Off
allow_url_include = Off

; Session
session.cookie_httponly = 1
session.cookie_secure = 1  ; Only if using HTTPS
session.use_only_cookies = 1

; Upload
upload_max_filesize = 2M
post_max_size = 8M
```

### Langkah 6: Create Required Directories

```bash
mkdir -p cache
mkdir -p public/assets/images
mkdir -p logs
```

### Langkah 7: Security Checklist

- [ ] HTTPS enabled (SSL certificate installed)
- [ ] Database credentials secured
- [ ] File permissions properly set
- [ ] Error display disabled
- [ ] Session security configured
- [ ] CSRF protection enabled
- [ ] File upload validation active
- [ ] Backup strategy in place

---

## 📊 Monitoring & Maintenance

### Log Files

```bash
# Check error logs
tail -f logs/error.log

# Check PHP errors
tail -f /var/log/php_errors.log

# Check Apache/Nginx logs
tail -f /var/log/apache2/error.log
```

### Database Maintenance

```sql
-- Weekly maintenance
OPTIMIZE TABLE users;
OPTIMIZE TABLE products;
OPTIMIZE TABLE orders;
OPTIMIZE TABLE order_items;
OPTIMIZE TABLE cart;

-- Check table status
ANALYZE TABLE products;
CHECK TABLE products;

-- Check slow queries
SHOW PROCESSLIST;
SHOW FULL PROCESSLIST;
```

### Cache Management

```php
// Clear cache programmatically
require_once 'config/cache.php';
cache_clear_all();

// Or create admin page untuk clear cache
if ($_SESSION['role'] === 'admin' && isset($_POST['clear_cache'])) {
    cache_clear_all();
    echo "Cache cleared!";
}
```

---

## 🔍 Performance Testing

### Tools untuk Testing:

1. **GTmetrix** - https://gtmetrix.com/
2. **Google PageSpeed Insights** - https://pagespeed.web.dev/
3. **Pingdom** - https://tools.pingdom.com/

### Load Testing:

```bash
# Apache Bench
ab -n 1000 -c 10 http://yoursite.com/

# Siege
siege -c 10 -r 100 http://yoursite.com/
```

---

## 🐛 Known Issues & Todo

### Fixed Issues:
- ✅ CSRF vulnerability
- ✅ File upload security
- ✅ Race condition in checkout
- ✅ Insecure redirects
- ✅ Missing error handling
- ✅ Inefficient queries

### Todo (Future Improvements):
- [ ] Rate limiting untuk login attempts
- [ ] Email verification
- [ ] Password reset functionality
- [ ] Order status tracking
- [ ] Admin dashboard analytics
- [ ] Product reviews & ratings
- [ ] Wishlist feature
- [ ] Coupon/discount system
- [ ] Payment gateway integration
- [ ] SMS notification
- [ ] Export orders to Excel/PDF

---

## 📞 Support

Jika menemukan bug atau masalah:
1. Check error logs
2. Review TESTING_REPORT.md
3. Check database connections
4. Verify file permissions
5. Clear cache

---

**Last Updated:** 22 Desember 2025  
**Version:** 1.0.0  
**Status:** Production Ready ✅
