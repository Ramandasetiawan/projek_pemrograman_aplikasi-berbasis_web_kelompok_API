<?php
require_once '../includes/header.php';
require_once '../config/db.php';

$stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll();

$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$min_price = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 0;
$brand = isset($_GET['brand']) ? trim($_GET['brand']) : '';

$items_per_page = 12;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $items_per_page;

$count_sql = "SELECT COUNT(*) as total FROM products p WHERE 1=1";
$params_count = [];

$sql = "SELECT p.*, c.name as category_name FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE 1=1";
$params = [];

if ($category_filter > 0) {
    $sql .= " AND p.category_id = ?";
    $count_sql .= " AND p.category_id = ?";
    $params[] = $category_filter;
    $params_count[] = $category_filter;
}

if (!empty($search)) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ? OR p.brand LIKE ?)";
    $count_sql .= " AND (p.name LIKE ? OR p.description LIKE ? OR p.brand LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params_count[] = $search_param;
    $params_count[] = $search_param;
    $params_count[] = $search_param;
}

if ($min_price > 0) {
    $sql .= " AND p.price >= ?";
    $count_sql .= " AND p.price >= ?";
    $params[] = $min_price;
    $params_count[] = $min_price;
}
if ($max_price > 0) {
    $sql .= " AND p.price <= ?";
    $count_sql .= " AND p.price <= ?";
    $params[] = $max_price;
    $params_count[] = $max_price;
}

if (!empty($brand)) {
    $sql .= " AND p.brand = ?";
    $count_sql .= " AND p.brand = ?";
    $params[] = $brand;
    $params_count[] = $brand;
}

$stmt_count = $pdo->prepare($count_sql);
$stmt_count->execute($params_count);
$total_products = $stmt_count->fetch()['total'];
$total_pages = ceil($total_products / $items_per_page);

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

$sql .= " LIMIT ? OFFSET ?";
$params[] = $items_per_page;
$params[] = $offset;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$stmt = $pdo->query("SELECT DISTINCT brand FROM products WHERE brand IS NOT NULL AND brand != '' ORDER BY brand");
$brands = $stmt->fetchAll(PDO::FETCH_COLUMN);

function build_filter_url($new_params = []) {
    global $category_filter, $search, $sort, $min_price, $max_price, $brand;

    $params = [];
    if ($category_filter > 0) $params['category'] = $category_filter;
    if (!empty($search)) $params['search'] = $search;
    if ($sort != 'newest') $params['sort'] = $sort;
    if ($min_price > 0) $params['min_price'] = $min_price;
    if ($max_price > 0) $params['max_price'] = $max_price;
    if (!empty($brand)) $params['brand'] = $brand;

    foreach ($new_params as $key => $value) {
        if ($value === null || $value === '') {
            unset($params[$key]);
        } else {
            $params[$key] = $value;
        }
    }

    return 'products.php' . (count($params) > 0 ? '?' . http_build_query($params) : '');
}
?>

<main class="flex-shrink-0">
<div class="container mt-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../public/home.php">Home</a></li>
            <li class="breadcrumb-item active">Produk</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Sidebar Filter -->
        <div class="col-lg-3 mb-4">
            <div class="filter-sidebar">
                <h5 class="mb-3">🔍 Filter & Pencarian</h5>

                <!-- Search -->
                <div class="filter-group">
                    <form method="GET" id="searchForm">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Cari produk..." 
                               value="<?= htmlspecialchars($search) ?>">
                        <?php if ($category_filter): ?>
                            <input type="hidden" name="category" value="<?= $category_filter ?>">
                        <?php endif; ?>
                        <button type="submit" class="btn btn-primary btn-sm w-100 mt-2">
                            <i class="bi bi-search"></i> Cari
                        </button>
                    </form>
                </div>

                <!-- Kategori -->
                <div class="filter-group">
                    <h6>📁 Kategori</h6>
                    <div class="filter-option">
                        <input type="radio" name="category" id="cat_all" 
                               <?= $category_filter == 0 ? 'checked' : '' ?>
                               onchange="window.location='<?= build_filter_url(['category' => null, 'page' => null]) ?>'">
                        <label for="cat_all">Semua Produk (<?= $total_products ?>)</label>
                    </div>
                    <?php foreach ($categories as $cat): ?>
                        <div class="filter-option">
                            <input type="radio" name="category" id="cat_<?= $cat['id'] ?>" 
                                   <?= $category_filter == $cat['id'] ? 'checked' : '' ?>
                                   onchange="window.location='<?= build_filter_url(['category' => $cat['id'], 'page' => null]) ?>'">
                            <label for="cat_<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Price Range -->
                <div class="filter-group">
                    <h6>💰 Rentang Harga</h6>
                    <form method="GET" id="priceForm">
                        <?php if ($category_filter): ?>
                            <input type="hidden" name="category" value="<?= $category_filter ?>">
                        <?php endif; ?>
                        <?php if ($search): ?>
                            <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                        <?php endif; ?>
                        <?php if ($brand): ?>
                            <input type="hidden" name="brand" value="<?= htmlspecialchars($brand) ?>">
                        <?php endif; ?>
                        <div class="mb-2">
                            <input type="number" name="min_price" class="form-control form-control-sm" 
                                   placeholder="Harga Min" value="<?= $min_price > 0 ? $min_price : '' ?>">
                        </div>
                        <div class="mb-2">
                            <input type="number" name="max_price" class="form-control form-control-sm" 
                                   placeholder="Harga Max" value="<?= $max_price > 0 ? $max_price : '' ?>">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">Terapkan</button>
                        <?php if ($min_price > 0 || $max_price > 0): ?>
                            <a href="<?= build_filter_url(['min_price' => null, 'max_price' => null, 'page' => null]) ?>" 
                               class="btn btn-outline-secondary btn-sm w-100 mt-2">Reset</a>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- Brand -->
                <?php if (count($brands) > 0): ?>
                <div class="filter-group">
                    <h6>🏷️ Brand</h6>
                    <div class="filter-option">
                        <input type="radio" name="brand" id="brand_all" 
                               <?= empty($brand) ? 'checked' : '' ?>
                               onchange="window.location='<?= build_filter_url(['brand' => null, 'page' => null]) ?>'">
                        <label for="brand_all">Semua Brand</label>
                    </div>
                    <?php foreach ($brands as $b): ?>
                        <div class="filter-option">
                            <input type="radio" name="brand" id="brand_<?= urlencode($b) ?>" 
                                   <?= $brand == $b ? 'checked' : '' ?>
                                   onchange="window.location='<?= build_filter_url(['brand' => $b, 'page' => null]) ?>'">
                            <label for="brand_<?= urlencode($b) ?>"><?= htmlspecialchars($b) ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Clear All Filters -->
                <?php if ($category_filter > 0 || !empty($search) || $min_price > 0 || $max_price > 0 || !empty($brand)): ?>
                <div class="mt-3">
                    <a href="products.php" class="btn btn-outline-danger btn-sm w-100">
                        <i class="bi bi-x-circle"></i> Hapus Semua Filter
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Produk -->
        <div class="col-lg-9">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <div>
                    <h2 class="mb-2">
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
                    <p class="text-muted small mb-0">
                        Menampilkan <?= count($products) ?> dari <?= $total_products ?> produk
                        <?php if ($total_pages > 1): ?>
                            (Halaman <?= $page ?> dari <?= $total_pages ?>)
                        <?php endif; ?>
                    </p>
                </div>

                <!-- Sort -->
                <div class="mt-2 mt-md-0">
                    <form method="GET" class="d-inline-block">
                        <?php if ($category_filter): ?>
                            <input type="hidden" name="category" value="<?= $category_filter ?>">
                        <?php endif; ?>
                        <?php if ($search): ?>
                            <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                        <?php endif; ?>
                        <?php if ($min_price > 0): ?>
                            <input type="hidden" name="min_price" value="<?= $min_price ?>">
                        <?php endif; ?>
                        <?php if ($max_price > 0): ?>
                            <input type="hidden" name="max_price" value="<?= $max_price ?>">
                        <?php endif; ?>
                        <?php if ($brand): ?>
                            <input type="hidden" name="brand" value="<?= htmlspecialchars($brand) ?>">
                        <?php endif; ?>
                        <input type="hidden" name="page" value="<?= $page ?>">
                        <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 180px;">
                            <option value="newest" <?= $sort == 'newest' ? 'selected' : '' ?>>⏱️ Terbaru</option>
                            <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>💰 Harga Terendah</option>
                            <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>💎 Harga Tertinggi</option>
                            <option value="name" <?= $sort == 'name' ? 'selected' : '' ?>>🔤 Nama A-Z</option>
                        </select>
                    </form>
                </div>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= htmlspecialchars($_GET['success']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (count($products) > 0): ?>
                <div class="row g-3 mb-4">
                    <?php foreach ($products as $p): ?>
                        <div class="col-lg-4 col-md-6 col-6">
                            <div class="card h-100 product-card">
                                <?php if ($p['stock'] <= 5 && $p['stock'] > 0): ?>
                                    <span class="badge bg-warning">Stok Terbatas!</span>
                                <?php elseif ($p['stock'] == 0): ?>
                                    <span class="badge bg-danger">Habis</span>
                                <?php endif; ?>

                                <?php 
                                $imageSrc = (strpos($p['image'], 'http') === 0) ? $p['image'] : '../public/assets/images/' . $p['image'];
                                ?>
                                <a href="product_detail.php?id=<?= $p['id'] ?>">
                                    <img src="<?= htmlspecialchars($imageSrc) ?>" 
                                         class="card-img-top" height="220" style="object-fit: cover;"
                                         onerror="this.src='https://via.placeholder.com/500x300?text=<?= urlencode($p['name']) ?>'">
                                </a>
                                <div class="card-body d-flex flex-column">
                                    <span class="badge bg-secondary small mb-2 align-self-start">
                                        <?= htmlspecialchars($p['category_name']) ?>
                                    </span>
                                    <a href="product_detail.php?id=<?= $p['id'] ?>" class="text-decoration-none text-dark">
                                        <h6 class="card-title"><?= htmlspecialchars($p['name']) ?></h6>
                                    </a>
                                    <?php if ($p['brand']): ?>
                                        <p class="text-muted small mb-2">
                                            <i class="bi bi-tag"></i> <?= htmlspecialchars($p['brand']) ?>
                                        </p>
                                    <?php endif; ?>
                                    <div class="price-tag mb-2">
                                        Rp <?= number_format($p['price'], 0, ',', '.') ?>
                                    </div>
                                    <p class="small mb-3">
                                        <?php if ($p['stock'] > 10): ?>
                                            <span class="stock-badge stock-available">Tersedia</span>
                                        <?php elseif ($p['stock'] > 0): ?>
                                            <span class="stock-badge stock-low">Stok: <?= $p['stock'] ?></span>
                                        <?php else: ?>
                                            <span class="stock-badge stock-out">Habis</span>
                                        <?php endif; ?>
                                    </p>

                                    <div class="mt-auto">
                                        <div class="d-grid gap-2">
                                            <a href="product_detail.php?id=<?= $p['id'] ?>" class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-eye"></i> Lihat Detail
                                            </a>
                                            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] !== 'admin'): ?>
                                                <?php if ($p['stock'] > 0): ?>
                                                    <form action="../handlers/cart_add.php" method="POST">
                                                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                                        <button type="submit" class="btn btn-primary btn-sm w-100">
                                                            <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <button class="btn btn-secondary btn-sm" disabled>Stok Habis</button>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <nav aria-label="Product pagination">
                    <ul class="pagination justify-content-center">
                        <!-- Previous -->
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= build_filter_url(['page' => $page - 1]) ?>">
                                &laquo; Previous
                            </a>
                        </li>

                        <!-- First Page -->
                        <?php if ($page > 3): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= build_filter_url(['page' => 1]) ?>">1</a>
                            </li>
                            <?php if ($page > 4): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- Page Numbers -->
                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="<?= build_filter_url(['page' => $i]) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <!-- Last Page -->
                        <?php if ($page < $total_pages - 2): ?>
                            <?php if ($page < $total_pages - 3): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= build_filter_url(['page' => $total_pages]) ?>"><?= $total_pages ?></a>
                            </li>
                        <?php endif; ?>

                        <!-- Next -->
                        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= build_filter_url(['page' => $page + 1]) ?>">
                                Next &raquo;
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-info">
                    <h5>😕 Produk tidak ditemukan</h5>
                    <p>Maaf, tidak ada produk yang sesuai dengan filter Anda.</p>
                    <a href="products.php" class="btn btn-primary">Lihat Semua Produk</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</main>

<?php include '../includes/footer.php'; ?>

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
