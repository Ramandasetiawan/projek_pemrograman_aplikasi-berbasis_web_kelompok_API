-- Fitur Tambahan untuk etectstore
-- Wishlist dan Review System

USE etectstore;

-- Tabel Wishlist (Produk Favorit)
CREATE TABLE IF NOT EXISTS wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product_wishlist (user_id, product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Product Reviews (Rating dan Review Produk)
CREATE TABLE IF NOT EXISTS product_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    order_id INT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    review_title VARCHAR(200),
    review_text TEXT,
    is_verified_purchase BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
    UNIQUE KEY unique_user_product_review (user_id, product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel untuk menyimpan avatar user (opsional)
ALTER TABLE users ADD COLUMN avatar VARCHAR(255) NULL AFTER address;

-- Index untuk performa
CREATE INDEX idx_wishlist_user ON wishlist(user_id);
CREATE INDEX idx_wishlist_product ON wishlist(product_id);
CREATE INDEX idx_reviews_product ON product_reviews(product_id);
CREATE INDEX idx_reviews_user ON product_reviews(user_id);
CREATE INDEX idx_reviews_rating ON product_reviews(rating);

-- View untuk statistik review produk
CREATE OR REPLACE VIEW product_review_stats AS
SELECT 
    p.id as product_id,
    p.name as product_name,
    COUNT(pr.id) as total_reviews,
    COALESCE(AVG(pr.rating), 0) as average_rating,
    SUM(CASE WHEN pr.rating = 5 THEN 1 ELSE 0 END) as five_star,
    SUM(CASE WHEN pr.rating = 4 THEN 1 ELSE 0 END) as four_star,
    SUM(CASE WHEN pr.rating = 3 THEN 1 ELSE 0 END) as three_star,
    SUM(CASE WHEN pr.rating = 2 THEN 1 ELSE 0 END) as two_star,
    SUM(CASE WHEN pr.rating = 1 THEN 1 ELSE 0 END) as one_star
FROM products p
LEFT JOIN product_reviews pr ON p.id = pr.product_id
GROUP BY p.id, p.name;

-- Contoh data review (opsional untuk testing)
-- INSERT INTO product_reviews (product_id, user_id, rating, review_title, review_text, is_verified_purchase)
-- VALUES 
-- (1, 2, 5, 'Produk Sangat Bagus!', 'Kualitas top, pengiriman cepat. Highly recommended!', TRUE),
-- (1, 3, 4, 'Sesuai Ekspektasi', 'Barang sesuai deskripsi, packaging rapi.', TRUE);
