-- Database: etectstore
-- Sistem Penjualan Komponen Komputer

CREATE DATABASE IF NOT EXISTS etectstore;
USE etectstore;

-- Tabel Users (Admin dan Customer)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('admin', 'customer') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Categories (Kategori Komponen)
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Products (Produk Komponen Komputer)
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    brand VARCHAR(100),
    description TEXT,
    specifications TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Tabel Orders (Pesanan)
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_method VARCHAR(50),
    shipping_address TEXT NOT NULL,
    notes TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabel Order Items (Detail Pesanan)
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Tabel Cart (Keranjang Belanja)
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id)
);

-- Insert Data Kategori
INSERT INTO categories (name, description) VALUES
('Processor', 'CPU dan processor untuk komputer'),
('Motherboard', 'Mainboard dan motherboard komputer'),
('RAM', 'Memory RAM untuk komputer'),
('Storage', 'Hardisk, SSD, dan storage lainnya'),
('VGA Card', 'Kartu grafis dan VGA card'),
('Power Supply', 'PSU dan power supply'),
('Casing', 'Casing dan cabinet komputer'),
('Cooling', 'Kipas dan sistem pendingin'),
('Monitor', 'Monitor dan layar komputer'),
('Keyboard & Mouse', 'Perangkat input keyboard dan mouse');

-- Insert Data Admin
INSERT INTO users (username, password, email, full_name, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@etectstore.com', 'Administrator', 'admin');
-- Password: password

-- Insert Data Produk Contoh
INSERT INTO products (category_id, name, brand, description, specifications, price, stock, image) VALUES
-- Processor
(1, 'Intel Core i5-13600K', 'Intel', 'Processor Intel Core i5 Generasi ke-13 dengan performa tinggi', '14 Core, 20 Thread, Base Clock 3.5GHz, Turbo 5.1GHz', 4500000.00, 15, 'https://images.unsplash.com/photo-1555680202-c86f0e12f086?w=500'),
(1, 'AMD Ryzen 5 7600X', 'AMD', 'Processor AMD Ryzen 5 dengan arsitektur Zen 4', '6 Core, 12 Thread, Base Clock 4.7GHz, Boost 5.3GHz', 3800000.00, 20, 'https://images.unsplash.com/photo-1591799264318-7e6ef8ddb7ea?w=500'),
(1, 'Intel Core i7-13700K', 'Intel', 'Processor Intel Core i7 untuk gaming dan multitasking', '16 Core, 24 Thread, Base Clock 3.4GHz, Turbo 5.4GHz', 6200000.00, 10, 'https://images.unsplash.com/photo-1555617981-dac3880eac6e?w=500'),
(1, 'AMD Ryzen 9 7900X', 'AMD', 'Processor AMD Ryzen 9 high-end untuk workstation', '12 Core, 24 Thread, Base Clock 4.7GHz, Boost 5.4GHz', 7500000.00, 8, 'https://images.unsplash.com/photo-1591488320449-011701bb6704?w=500'),

-- Motherboard
(2, 'ASUS ROG STRIX Z790-E', 'ASUS', 'Motherboard gaming premium dengan chipset Z790', 'Socket LGA1700, DDR5, PCIe 5.0, WiFi 6E', 5800000.00, 12, 'https://images.unsplash.com/photo-1587202372634-32705e3bf49c?w=500'),
(2, 'MSI MAG B650 TOMAHAWK', 'MSI', 'Motherboard AMD B650 dengan fitur lengkap', 'Socket AM5, DDR5, PCIe 4.0, WiFi 6', 3500000.00, 18, 'https://images.unsplash.com/photo-1587825140708-dfaf72ae4b04?w=500'),
(2, 'Gigabyte B760M DS3H', 'Gigabyte', 'Motherboard Intel B760 format Micro-ATX', 'Socket LGA1700, DDR4, PCIe 4.0', 2100000.00, 25, 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=500'),

-- RAM
(3, 'Corsair Vengeance DDR5 32GB (2x16GB) 6000MHz', 'Corsair', 'RAM DDR5 high performance untuk gaming', 'DDR5-6000, CL30, RGB, 32GB Kit', 2800000.00, 30, 'https://images.unsplash.com/photo-1541746972996-4e0b0f43e02a?w=500'),
(3, 'Kingston Fury Beast DDR4 16GB (2x8GB) 3200MHz', 'Kingston', 'RAM DDR4 value gaming', 'DDR4-3200, CL16, 16GB Kit', 950000.00, 45, 'https://images.unsplash.com/photo-1562976540-1502c2145186?w=500'),
(3, 'G.Skill Trident Z5 RGB DDR5 64GB (2x32GB) 6400MHz', 'G.Skill', 'RAM DDR5 premium dengan RGB', 'DDR5-6400, CL32, RGB, 64GB Kit', 5500000.00, 15, 'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?w=500'),

-- Storage
(4, 'Samsung 990 PRO 2TB NVMe SSD', 'Samsung', 'SSD NVMe Gen 4 tercepat dari Samsung', 'PCIe 4.0, Read 7450MB/s, Write 6900MB/s', 3200000.00, 22, 'https://images.unsplash.com/photo-1597872200969-2b65d56bd16b?w=500'),
(4, 'WD Black SN850X 1TB NVMe SSD', 'Western Digital', 'SSD gaming NVMe Gen 4', 'PCIe 4.0, Read 7300MB/s, Write 6300MB/s', 1850000.00, 35, 'https://images.unsplash.com/photo-1531492746076-161ca9bcad58?w=500'),
(4, 'Seagate Barracuda 2TB HDD', 'Seagate', 'Hardisk 2TB untuk storage tambahan', 'SATA 6Gb/s, 7200 RPM, 256MB Cache', 850000.00, 40, 'https://images.unsplash.com/photo-1529336953128-a85760f58cb5?w=500'),

-- VGA Card
(5, 'NVIDIA GeForce RTX 4070 Ti', 'NVIDIA', 'VGA Card high-end untuk gaming 4K', '12GB GDDR6X, 7680 CUDA Cores, Ray Tracing', 12500000.00, 8, 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?w=500'),
(5, 'AMD Radeon RX 7800 XT', 'AMD', 'VGA Card gaming dengan performa tinggi', '16GB GDDR6, Ray Tracing, FSR 3.0', 9800000.00, 12, 'https://images.unsplash.com/photo-1591488320449-011701bb6704?w=500'),
(5, 'NVIDIA GeForce RTX 4060', 'NVIDIA', 'VGA Card mainstream untuk gaming 1080p', '8GB GDDR6, DLSS 3, Ray Tracing', 5200000.00, 20, 'https://images.unsplash.com/photo-1602524206684-37a8dd6e3e9a?w=500'),

-- Power Supply
(6, 'Corsair RM850x 850W 80+ Gold', 'Corsair', 'PSU modular 850W dengan efisiensi tinggi', '850W, 80+ Gold, Fully Modular', 2100000.00, 25, 'https://images.unsplash.com/photo-1580927752452-89d86da3fa0a?w=500'),
(6, 'Seasonic Focus GX-750 750W 80+ Gold', 'Seasonic', 'PSU 750W berkualitas tinggi', '750W, 80+ Gold, Full Modular', 1850000.00, 30, 'https://images.unsplash.com/photo-1609091839311-d6f052b625a1?w=500'),
(6, 'Cooler Master MWE 650W 80+ Bronze', 'Cooler Master', 'PSU value 650W', '650W, 80+ Bronze, Non-Modular', 950000.00, 40, 'https://images.unsplash.com/photo-1612198188060-c7c2a3b66eae?w=500'),

-- Casing
(7, 'NZXT H510 Elite', 'NZXT', 'Casing Mid-Tower dengan tempered glass', 'Mid-Tower, Tempered Glass, RGB Fans', 2200000.00, 18, 'https://images.unsplash.com/photo-1587202372634-32705e3bf49c?w=500'),
(7, 'Lian Li O11 Dynamic', 'Lian Li', 'Casing premium untuk custom watercooling', 'Mid-Tower, Dual Tempered Glass', 2800000.00, 12, 'https://images.unsplash.com/photo-1587825140708-dfaf72ae4b04?w=500'),
(7, 'Fractal Design Meshify C', 'Fractal Design', 'Casing dengan airflow optimal', 'Mid-Tower, Mesh Front, Compact', 1500000.00, 22, 'https://images.unsplash.com/photo-1600861194942-f883de0dfe96?w=500'),

-- Cooling
(8, 'Noctua NH-D15 CPU Cooler', 'Noctua', 'CPU Cooler dual tower terbaik', 'Dual Tower, 2x 140mm Fan, Silent', 1450000.00, 20, 'https://images.unsplash.com/photo-1591488320449-011701bb6704?w=500'),
(8, 'Corsair iCUE H150i Elite LCD', 'Corsair', 'AIO Liquid Cooler 360mm dengan LCD', '360mm Radiator, RGB, LCD Display', 3500000.00, 15, 'https://images.unsplash.com/photo-1618472609497-2295816eb5f1?w=500'),
(8, 'Arctic Liquid Freezer II 280', 'Arctic', 'AIO Liquid Cooler 280mm', '280mm Radiator, Silent Operation', 1800000.00, 25, 'https://images.unsplash.com/photo-1609091839311-d6f052b625a1?w=500'),

-- Monitor
(9, 'ASUS TUF Gaming VG27AQ 27" 165Hz', 'ASUS', 'Monitor gaming 27 inch WQHD 165Hz', '27", 2560x1440, IPS, 165Hz, G-Sync', 4500000.00, 15, 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=500'),
(9, 'LG UltraGear 24GN600 24" 144Hz', 'LG', 'Monitor gaming 24 inch Full HD', '24", 1920x1080, IPS, 144Hz, FreeSync', 2300000.00, 25, 'https://images.unsplash.com/photo-1593640408182-31c70c8268f5?w=500'),
(9, 'Samsung Odyssey G7 32" 240Hz', 'Samsung', 'Monitor gaming curved 32 inch', '32", 2560x1440, VA Curved, 240Hz', 7500000.00, 8, 'https://images.unsplash.com/photo-1585792180666-f7347c490ee2?w=500'),

-- Keyboard & Mouse
(10, 'Logitech G Pro X Mechanical Keyboard', 'Logitech', 'Keyboard mekanik gaming profesional', 'Mechanical, Hot-Swap Switch, RGB', 1950000.00, 20, 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=500'),
(10, 'Razer DeathAdder V3 Pro', 'Razer', 'Mouse gaming wireless flagship', 'Wireless, 30K DPI, 90 Hour Battery', 2100000.00, 18, 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=500'),
(10, 'Keychron K8 Pro Wireless', 'Keychron', 'Keyboard mekanik wireless 75%', 'Wireless, Hot-Swap, RGB, TKL', 1650000.00, 22, 'https://images.unsplash.com/photo-1595225476474-87563907a212?w=500');

-- Insert Data Customer Contoh
INSERT INTO users (username, password, email, full_name, phone, address, role) VALUES
('johndoe', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'john@example.com', 'John Doe', '081234567890', 'Jl. Sudirman No. 123, Jakarta', 'customer'),
('janedoe', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'jane@example.com', 'Jane Doe', '081234567891', 'Jl. Thamrin No. 456, Jakarta', 'customer');
-- Password untuk semua user: password
