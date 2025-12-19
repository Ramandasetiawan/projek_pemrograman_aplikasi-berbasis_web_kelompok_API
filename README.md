# etectstore - Toko Komponen Komputer

E-commerce website untuk penjualan komponen komputer yang dibangun dengan PHP dan MySQL.

## Struktur Folder

```
tugas-12_pemrograman_aplikasi-berbasis_web_kelompok_API/
│
├── admin/                      # Panel Admin
│   ├── add_product.php        # Tambah produk baru
│   ├── dashboard.php          # Dashboard admin
│   ├── delete_product.php     # Hapus produk
│   ├── edit_product.php       # Edit produk
│   └── products.php           # Kelola produk
│
├── auth/                       # Autentikasi
│   ├── login.php              # Halaman login
│   ├── logout.php             # Proses logout
│   ├── process_login.php      # Proses login
│   ├── process_register.php   # Proses registrasi
│   └── register.php           # Halaman registrasi
│
├── config/                     # Konfigurasi
│   ├── db.php                 # Koneksi database
│   └── paths.php              # Konfigurasi path
│
├── database/                   # Database SQL
│   └── etectstore.sql         # Database schema & data
│
├── handlers/                   # Backend Handlers
│   ├── cart_add.php           # Tambah ke keranjang
│   ├── cart_remove.php        # Hapus dari keranjang
│   └── cart_update.php        # Update keranjang
│
├── includes/                   # Template Components
│   ├── footer.php             # Footer global
│   └── header.php             # Header & navbar global
│
├── pages/                      # Halaman Utama
│   ├── akun_saya.php          # Dashboard user
│   ├── cart.php               # Keranjang belanja
│   ├── checkout.php           # Proses checkout
│   ├── kontak.php             # Halaman kontak
│   ├── order_success.php      # Konfirmasi order
│   ├── product_detail.php     # Detail produk
│   ├── products.php           # Daftar produk
│   ├── profile.php            # Profil user
│   └── tentang_kami.php       # Tentang kami
│
├── public/                     # Public Assets & Entry Point
│   ├── assets/                # Static files
│   │   └── css/
│   │       └── style.css      # Custom CSS
│   ├── home.php               # Homepage
│   └── index.php              # Entry point
│
├── index.php                   # Root redirect ke public/
├── clear_session.php           # Helper: clear sessions
├── reset_password.php          # Helper: reset password admin
└── test_login.php              # Helper: test login

```

## Instalasi

### 1. Import Database
- Buka phpMyAdmin
- Buat database baru bernama `etectstore`
- Import file `database/etectstore.sql`

### 2. Konfigurasi Database
Edit file `config/db.php` jika perlu mengubah kredensial database:
```php
$host = 'localhost';
$dbname = 'etectstore';
$username = 'root';
$password = '';
```

### 3. Jalankan Aplikasi
- Pastikan Laragon/XAMPP sudah running
- Akses: `http://localhost/tugas-12_pemrograman_aplikasi-berbasis_web_kelompok_API/`
- Atau langsung: `http://localhost/tugas-12_pemrograman_aplikasi-berbasis_web_kelompok_API/public/home.php`

## Login Admin
- **Username:** admin
- **Password:** password

## Fitur Utama

### User Features
- ✅ Registrasi & Login
- ✅ Browse produk dengan filter kategori
- ✅ Pencarian produk
- ✅ Detail produk dengan spesifikasi lengkap
- ✅ Keranjang belanja
- ✅ Checkout & order
- ✅ Riwayat pesanan
- ✅ Profil user
- ✅ Halaman kontak dengan FAQ

### Admin Features
- ✅ Dashboard admin
- ✅ Kelola produk (CRUD)
- ✅ Tambah/Edit/Hapus produk

## Teknologi yang Digunakan
- **Backend:** PHP 7.4+
- **Database:** MySQL 8.0
- **Frontend:** Bootstrap 5.3.2
- **Icons:** Unicode Emoji
- **Images:** Unsplash CDN

## Database Schema

### Table: users
- id, username, password, email, full_name, address, phone, role, created_at

### Table: categories
- id, name, description, created_at

### Table: products
- id, category_id, name, description, price, stock, image, image_url, specifications, created_at, updated_at

### Table: orders
- id, user_id, total_amount, shipping_address, payment_method, status, created_at

### Table: order_items
- id, order_id, product_id, quantity, price

### Table: cart
- id, user_id, product_id, quantity, created_at

## Helper Files
- `clear_session.php` - Membersihkan semua session (untuk debugging)
- `reset_password.php` - Reset password admin ke default "password"
- `test_login.php` - Test koneksi database dan data user

## URL Structure
- Homepage: `/public/home.php`
- Products: `/pages/products.php`
- Product Detail: `/pages/product_detail.php?id=X`
- Cart: `/pages/cart.php`
- Checkout: `/pages/checkout.php`
- My Account: `/pages/akun_saya.php`
- Profile: `/pages/profile.php`
- About Us: `/pages/tentang_kami.php`
- Contact: `/pages/kontak.php`
- Login: `/auth/login.php`
- Register: `/auth/register.php`
- Admin Dashboard: `/admin/dashboard.php`

## Kategori Produk
1. Processor
2. Motherboard
3. RAM
4. Storage (SSD/HDD)
5. VGA Card
6. Power Supply
7. Casing
8. Monitor
9. Keyboard
10. Mouse

## Notes
- Semua password di-hash menggunakan `password_hash()`
- Session-based authentication
- Responsive design dengan Bootstrap
- Product images menggunakan Unsplash URLs
- Base path detection otomatis untuk berbagai folder

## Maintenance
Jika ada masalah:
1. Jalankan `clear_session.php` untuk clear session
2. Jalankan `reset_password.php` untuk reset password admin
3. Jalankan `test_login.php` untuk test database connection
4. Cek error di browser console atau PHP error log

## Credits
Dikembangkan untuk Tugas Pemrograman Aplikasi Berbasis Web - Kelompok API
