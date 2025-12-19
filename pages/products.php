<?php
require_once '../includes/header.php';
require_once '../config/db.php';

// Ambil kategori untuk filter
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll();

// Filter
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Build query
$sql = "SELECT p.*, c.name as category_name FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE 1=1";
$params = [];

if ($category_filter > 0) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category_filter;
}

if (!empty($search)) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ? OR p.brand LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

// Sorting
switch ($sort) {
    case 'price_asc':
        $sql .= " ORDER BY p.price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY p.price DESC";
        break;
    case 'name':
        $sql .= " ORDER BY p.name ASC";
        break;
    default:
        $sql .= " ORDER BY p.created_at DESC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<main class="flex-shrink-0">
<div class="container mt-4">
    <div class="row">
        <!-- Sidebar Filter -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Filter</h5>
                </div>
                <div class="card-body">
                    <!-- Search -->
                    <form method="GET" class="mb-4">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Cari produk..." 
                               value="<?= htmlspecialchars($search) ?>">
                        <button type="submit" class="btn btn-primary btn-sm w-100 mt-2">Cari</button>
                    </form>
                    
                    <!-- Kategori -->
                    <h6 class="mb-3">Kategori</h6>
                    <div class="list-group mb-4">
                        <a href="products.php" 
                           class="list-group-item list-group-item-action <?= $category_filter == 0 ? 'active' : '' ?>">
                            Semua Produk
                        </a>
                        <?php foreach ($categories as $cat): ?>
                            <a href="products.php?category=<?= $cat['id'] ?>" 
                               class="list-group-item list-group-item-action <?= $category_filter == $cat['id'] ? 'active' : '' ?>">
                                <?= htmlspecialchars($cat['name']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Sort -->
                    <h6 class="mb-3">Urutkan</h6>
                    <form method="GET">
                        <?php if ($category_filter): ?>
                            <input type="hidden" name="category" value="<?= $category_filter ?>">
                        <?php endif; ?>
                        <?php if ($search): ?>
                            <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                        <?php endif; ?>
                        <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="newest" <?= $sort == 'newest' ? 'selected' : '' ?>>Terbaru</option>
                            <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Harga Terendah</option>
                            <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Harga Tertinggi</option>
                            <option value="name" <?= $sort == 'name' ? 'selected' : '' ?>>Nama A-Z</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Produk -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <?php if ($category_filter > 0): ?>
                        <?php
                        $cat_name = array_filter($categories, fn($c) => $c['id'] == $category_filter);
                        echo htmlspecialchars(reset($cat_name)['name']);
                        ?>
                    <?php elseif ($search): ?>
                        Hasil Pencarian: "<?= htmlspecialchars($search) ?>"
                    <?php else: ?>
                        Semua Produk
                    <?php endif; ?>
                </h2>
                <span class="badge bg-secondary"><?= count($products) ?> Produk</span>
            </div>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>
            
            <?php if (count($products) > 0): ?>
                <div class="row">
                    <?php foreach ($products as $p): ?>
                        <div class="col-md-4 col-6 mb-4">
                            <div class="card h-100">
                                <?php 
                                $imageSrc = (strpos($p['image'], 'http') === 0) ? $p['image'] : 'assets/images/' . $p['image'];
                                ?>
                                <a href="product_detail.php?id=<?= $p['id'] ?>">
                                    <img src="<?= htmlspecialchars($imageSrc) ?>" 
                                         class="card-img-top" height="200" style="object-fit: cover;"
                                         onerror="this.src='https://via.placeholder.com/500x300?text=No+Image'">
                                </a>
                                <div class="card-body">
                                    <span class="badge bg-secondary small mb-2"><?= htmlspecialchars($p['category_name']) ?></span>
                                    <a href="product_detail.php?id=<?= $p['id'] ?>" class="text-decoration-none text-dark">
                                        <h6 class="card-title"><?= htmlspecialchars($p['name']) ?></h6>
                                    </a>
                                    <?php if ($p['brand']): ?>
                                        <p class="text-muted small mb-1">Brand: <?= htmlspecialchars($p['brand']) ?></p>
                                    <?php endif; ?>
                                    <p class="text-primary fw-bold mb-1">Rp <?= number_format($p['price'], 0, ',', '.') ?></p>
                                    <p class="text-muted small mb-2">Stok: <?= $p['stock'] ?></p>
                                    
                                    <div class="d-grid gap-2">
                                        <a href="product_detail.php?id=<?= $p['id'] ?>" class="btn btn-outline-primary btn-sm">
                                            Detail
                                        </a>
                                        <?php if (isset($_SESSION['user_id'])): ?>
                                            <?php if ($p['stock'] > 0): ?>
                                                <form action="../handlers/cart_add.php" method="POST">
                                                    <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                                    <button type="submit" class="btn btn-primary btn-sm w-100">Tambah ke Keranjang</button>
                                                </form>
                                            <?php else: ?>
                                                <button class="btn btn-secondary btn-sm" disabled>Stok Habis</button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <h5>Produk tidak ditemukan</h5>
                    <p>Coba kata kunci lain atau lihat kategori lainnya</p>
                    <a href="products.php" class="btn btn-primary">Lihat Semua Produk</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</main>

<?php include '../includes/footer.php'; ?>
