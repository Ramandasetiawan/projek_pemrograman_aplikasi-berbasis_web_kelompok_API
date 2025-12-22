-- Optimasi Database untuk etectstore
-- Jalankan query ini untuk meningkatkan performa database

-- 1. Tambah Index pada Foreign Keys
ALTER TABLE `cart` ADD INDEX `idx_user_product` (`user_id`, `product_id`);
ALTER TABLE `cart` ADD INDEX `idx_product` (`product_id`);

ALTER TABLE `orders` ADD INDEX `idx_user_status` (`user_id`, `status`);
ALTER TABLE `orders` ADD INDEX `idx_created` (`created_at`);

ALTER TABLE `order_items` ADD INDEX `idx_order` (`order_id`);
ALTER TABLE `order_items` ADD INDEX `idx_product` (`product_id`);

ALTER TABLE `products` ADD INDEX `idx_category` (`category_id`);
ALTER TABLE `products` ADD INDEX `idx_stock` (`stock`);
ALTER TABLE `products` ADD INDEX `idx_created` (`created_at`);
ALTER TABLE `products` ADD INDEX `idx_price` (`price`);

-- 2. Tambah Index untuk Search
ALTER TABLE `products` ADD FULLTEXT INDEX `idx_search` (`name`, `description`, `brand`);

-- 3. Tambah Index untuk Users
ALTER TABLE `users` ADD INDEX `idx_email` (`email`);
ALTER TABLE `users` ADD INDEX `idx_role` (`role`);

-- 4. Optimize Tables
OPTIMIZE TABLE `users`;
OPTIMIZE TABLE `products`;
OPTIMIZE TABLE `categories`;
OPTIMIZE TABLE `cart`;
OPTIMIZE TABLE `orders`;
OPTIMIZE TABLE `order_items`;

-- 5. Check Table Status
SHOW TABLE STATUS;
