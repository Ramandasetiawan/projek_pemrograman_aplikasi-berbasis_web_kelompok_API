-- Sample Data untuk etectstore
-- Menambahkan data contoh untuk testing dan demo

USE etectstore;

-- Insert sample products dengan data yang lebih lengkap
INSERT INTO products (category_id, name, brand, description, specifications, price, stock, image) VALUES
-- Processor
(1, 'Intel Core i9-13900K', 'Intel', 'Processor flagship Intel generasi ke-13 dengan performa maksimal', 'Socket LGA1700, 24 Cores (8P+16E), 32 Threads, Base Clock 3.0GHz, Max Turbo 5.8GHz', 9500000, 15, 'https://images.unsplash.com/photo-1555617981-dac3880eac6e?w=500'),
(1, 'AMD Ryzen 9 7950X', 'AMD', 'Processor AMD terbaru dengan teknologi Zen 4', '16 Cores, 32 Threads, Base Clock 4.5GHz, Max Boost 5.7GHz, AM5 Socket', 8900000, 20, 'https://images.unsplash.com/photo-1591799264318-7e6ef8ddb7ea?w=500'),
(1, 'Intel Core i7-13700K', 'Intel', 'Processor gaming powerful dengan 16 cores', 'Socket LGA1700, 16 Cores, 24 Threads, Base Clock 3.4GHz, Max Turbo 5.4GHz', 6500000, 25, 'https://images.unsplash.com/photo-1587202372634-32705e3bf49c?w=500'),
(1, 'AMD Ryzen 7 7700X', 'AMD', 'Processor AMD 8-core untuk gaming dan productivity', '8 Cores, 16 Threads, Base Clock 4.5GHz, Max Boost 5.4GHz, AM5 Socket', 4800000, 30, 'https://images.unsplash.com/photo-1555617981-dac3880eac6e?w=500'),

-- Motherboard
(2, 'ASUS ROG Maximus Z790 Hero', 'ASUS', 'Motherboard premium untuk Intel Gen 13', 'Socket LGA1700, DDR5 Support, PCIe 5.0, WiFi 6E, 2.5Gb LAN', 7500000, 10, 'https://images.unsplash.com/photo-1591488320449-011701bb6704?w=500'),
(2, 'MSI MAG B760 Tomahawk', 'MSI', 'Motherboard mainstream dengan fitur lengkap', 'Socket LGA1700, DDR5, PCIe 4.0, WiFi 6, RGB Lighting', 3200000, 18, 'https://images.unsplash.com/photo-1589362168347-5ec7d7233b0c?w=500'),
(2, 'Gigabyte X670E Aorus Master', 'Gigabyte', 'Motherboard flagship untuk AMD Ryzen 7000', 'Socket AM5, DDR5, PCIe 5.0, WiFi 6E, 10Gb LAN', 6800000, 12, 'https://images.unsplash.com/photo-1591488320449-011701bb6704?w=500'),

-- RAM
(3, 'Corsair Vengeance DDR5 32GB', 'Corsair', 'RAM DDR5 high performance 32GB kit', '2x16GB, 6000MHz, CL36, RGB Lighting, XMP 3.0', 2800000, 40, 'https://images.unsplash.com/photo-1541329249956-b039152e1d8c?w=500'),
(3, 'G.Skill Trident Z5 RGB 32GB', 'G.Skill', 'RAM DDR5 premium dengan RGB', '2x16GB, 6400MHz, CL32, Extreme Performance', 3200000, 35, 'https://images.unsplash.com/photo-1562976540-1502c2145186?w=500'),
(3, 'Kingston Fury Beast DDR5 16GB', 'Kingston', 'RAM DDR5 value for money', '2x8GB, 5200MHz, CL40, Plug and Play', 1400000, 50, 'https://images.unsplash.com/photo-1541329249956-b039152e1d8c?w=500'),
(3, 'Corsair Dominator Platinum RGB', 'Corsair', 'RAM DDR4 premium untuk enthusiast', '2x16GB, 3600MHz, CL18, RGB Premium', 2200000, 30, 'https://images.unsplash.com/photo-1562976540-1502c2145186?w=500'),

-- Storage
(4, 'Samsung 990 PRO 2TB', 'Samsung', 'SSD NVMe Gen4 tercepat dari Samsung', 'PCIe 4.0 NVMe, Read 7450MB/s, Write 6900MB/s, 2TB', 3500000, 25, 'https://images.unsplash.com/photo-1597872200969-2b65d56bd16b?w=500'),
(4, 'WD Black SN850X 1TB', 'Western Digital', 'SSD gaming dengan heatsink', 'PCIe 4.0 NVMe, Read 7300MB/s, Write 6300MB/s, 1TB, Heatsink', 1800000, 35, 'https://images.unsplash.com/photo-1531492746076-161ca9bcad58?w=500'),
(4, 'Crucial P5 Plus 500GB', 'Crucial', 'SSD NVMe budget friendly', 'PCIe 4.0 NVMe, Read 6600MB/s, Write 5000MB/s, 500GB', 850000, 45, 'https://images.unsplash.com/photo-1597872200969-2b65d56bd16b?w=500'),
(4, 'Seagate Barracuda 2TB HDD', 'Seagate', 'Hard disk untuk storage massal', '3.5", SATA III, 7200RPM, 256MB Cache, 2TB', 650000, 60, 'https://images.unsplash.com/photo-1531492746076-161ca9bcad58?w=500'),

-- VGA Card
(5, 'NVIDIA RTX 4090 24GB', 'NVIDIA', 'Graphics card flagship untuk gaming 4K', '24GB GDDR6X, Ada Lovelace, Ray Tracing, DLSS 3.0', 28500000, 8, 'https://images.unsplash.com/photo-1587202372583-49330a15584d?w=500'),
(5, 'AMD RX 7900 XTX 24GB', 'AMD', 'Graphics card AMD flagship untuk gaming', '24GB GDDR6, RDNA 3, Ray Tracing, FSR 3.0', 18500000, 12, 'https://images.unsplash.com/photo-1591488320449-011701bb6704?w=500'),
(5, 'NVIDIA RTX 4070 Ti 12GB', 'NVIDIA', 'Graphics card untuk gaming 1440p', '12GB GDDR6X, Ada Lovelace, Ray Tracing, DLSS 3.0', 12800000, 20, 'https://images.unsplash.com/photo-1587202372583-49330a15584d?w=500'),
(5, 'AMD RX 6700 XT 12GB', 'AMD', 'Graphics card value untuk gaming', '12GB GDDR6, RDNA 2, Ray Tracing, FSR 2.0', 5800000, 25, 'https://images.unsplash.com/photo-1591488320449-011701bb6704?w=500'),

-- Power Supply
(6, 'Corsair HX1000i 1000W', 'Corsair', 'PSU modular 80+ Platinum', '1000W, 80+ Platinum, Full Modular, Digital Monitor', 3200000, 15, 'https://images.unsplash.com/photo-1563991655280-cb95c90ca2fb?w=500'),
(6, 'Seasonic Focus GX-850 850W', 'Seasonic', 'PSU 80+ Gold reliable', '850W, 80+ Gold, Full Modular, Silent Fan', 1850000, 30, 'https://images.unsplash.com/photo-1563991655280-cb95c90ca2fb?w=500'),
(6, 'EVGA SuperNOVA 750W', 'EVGA', 'PSU gaming dengan garansi panjang', '750W, 80+ Gold, Full Modular, 10 Years Warranty', 1650000, 25, 'https://images.unsplash.com/photo-1563991655280-cb95c90ca2fb?w=500'),

-- Casing
(7, 'NZXT H510 Elite', 'NZXT', 'Casing minimalis dengan tempered glass', 'Mid Tower, Tempered Glass, RGB Lighting, Cable Management', 1850000, 20, 'https://images.unsplash.com/photo-1587202372634-32705e3bf49c?w=500'),
(7, 'Lian Li O11 Dynamic EVO', 'Lian Li', 'Casing premium untuk water cooling', 'Mid Tower, Dual Chamber, Tempered Glass, RGB', 2500000, 15, 'https://images.unsplash.com/photo-1587202372634-32705e3bf49c?w=500'),
(7, 'Cooler Master MasterBox Q300L', 'Cooler Master', 'Casing compact budget', 'Micro ATX, Acrylic Window, Compact Design', 650000, 35, 'https://images.unsplash.com/photo-1587202372634-32705e3bf49c?w=500'),

-- Cooling
(8, 'Noctua NH-D15', 'Noctua', 'CPU Cooler terbaik air cooling', 'Dual Tower, Dual 140mm Fans, Low Noise, Premium Quality', 1450000, 25, 'https://images.unsplash.com/photo-1587202372634-32705e3bf49c?w=500'),
(8, 'Corsair iCUE H150i Elite', 'Corsair', 'AIO liquid cooler 360mm RGB', '360mm Radiator, RGB Pump, iCUE Software Control', 2850000, 18, 'https://images.unsplash.com/photo-1587202372634-32705e3bf49c?w=500'),
(8, 'Arctic Freezer 34 eSports', 'Arctic', 'CPU Cooler budget dengan performa baik', 'Tower Design, 120mm Fan, Multi-socket Support', 450000, 40, 'https://images.unsplash.com/photo-1587202372634-32705e3bf49c?w=500'),

-- Monitor
(9, 'ASUS ROG Swift PG279QM', 'ASUS', 'Monitor gaming 1440p 240Hz', '27", IPS, 2560x1440, 240Hz, G-SYNC, HDR400', 8500000, 12, 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=500'),
(9, 'LG UltraGear 27GP950', 'LG', 'Monitor gaming 4K 144Hz', '27", Nano IPS, 3840x2160, 144Hz, G-SYNC, HDR600', 9800000, 10, 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=500'),
(9, 'Samsung Odyssey G5', 'Samsung', 'Monitor curved 1440p budget', '27", VA Curved, 2560x1440, 144Hz, FreeSync', 3200000, 20, 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=500'),

-- Keyboard & Mouse
(10, 'Logitech G Pro X Keyboard', 'Logitech', 'Mechanical keyboard pro gaming', 'TKL, Hot-Swappable Switch, RGB, Aluminum Frame', 1850000, 30, 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=500'),
(10, 'Razer DeathAdder V3 Pro', 'Razer', 'Wireless gaming mouse', 'Wireless, 30000 DPI, Focus Pro Sensor, 90 Hours Battery', 1950000, 25, 'https://images.unsplash.com/photo-1527814050087-3793815479db?w=500'),
(10, 'Keychron K8 Pro', 'Keychron', 'Mechanical keyboard wireless', 'TKL, Hot-Swappable, RGB, Multi-device', 1450000, 35, 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=500'),
(10, 'SteelSeries Rival 5', 'SteelSeries', 'Gaming mouse ergonomic', 'Wired, 18000 CPI, 9 Programmable Buttons', 850000, 40, 'https://images.unsplash.com/photo-1527814050087-3793815479db?w=500');

-- Insert sample customer user
INSERT INTO users (username, password, email, full_name, phone, address, role) VALUES
('customer1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer1@example.com', 'Budi Santoso', '081234567890', 'Jl. Sudirman No. 123, Jakarta Pusat', 'customer'),
('customer2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer2@example.com', 'Ani Wijaya', '082345678901', 'Jl. Gatot Subroto No. 456, Jakarta Selatan', 'customer'),
('customer3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer3@example.com', 'Candra Prakoso', '083456789012', 'Jl. Thamrin No. 789, Jakarta Pusat', 'customer');
-- Password untuk semua: password

-- Insert sample orders untuk testing
INSERT INTO orders (user_id, total_amount, status, payment_method, shipping_address, notes) VALUES
(2, 12500000, 'delivered', 'Transfer Bank', 'Jl. Sudirman No. 123, Jakarta Pusat, 10110', 'Mohon dikirim dengan bubble wrap'),
(2, 5800000, 'shipped', 'Credit Card', 'Jl. Sudirman No. 123, Jakarta Pusat, 10110', NULL),
(2, 2650000, 'processing', 'COD', 'Jl. Sudirman No. 123, Jakarta Pusat, 10110', NULL),
(3, 28500000, 'pending', 'Transfer Bank', 'Jl. Gatot Subroto No. 456, Jakarta Selatan, 12190', 'Harap konfirmasi ketersediaan stok'),
(3, 9800000, 'delivered', 'Credit Card', 'Jl. Gatot Subroto No. 456, Jakarta Selatan, 12190', NULL);

-- Insert order items
INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) VALUES
-- Order 1
(1, 5, 1, 12800000, 12800000),
-- Order 2
(2, 16, 1, 5800000, 5800000),
-- Order 3
(3, 8, 1, 1400000, 1400000),
(3, 13, 1, 850000, 850000),
-- Order 4
(4, 17, 1, 28500000, 28500000),
-- Order 5
(5, 23, 1, 9800000, 9800000);

-- Insert sample wishlist items
INSERT INTO wishlist (user_id, product_id) VALUES
(2, 1),  -- Customer1 menyimpan Intel Core i9
(2, 17), -- Customer1 menyimpan RTX 4090
(2, 11), -- Customer1 menyimpan Samsung 990 PRO
(3, 2),  -- Customer2 menyimpan AMD Ryzen 9
(3, 18), -- Customer2 menyimpan AMD RX 7900 XTX
(4, 19); -- Customer3 menyimpan RTX 4070 Ti

-- Update stock yang sudah terjual
UPDATE products SET stock = stock - 1 WHERE id IN (5, 16, 17, 23);
UPDATE products SET stock = stock - 1 WHERE id IN (8, 13);

-- Verify data
SELECT 'Products Count:' as Info, COUNT(*) as Total FROM products
UNION ALL
SELECT 'Orders Count:', COUNT(*) FROM orders
UNION ALL
SELECT 'Wishlist Count:', COUNT(*) FROM wishlist;
