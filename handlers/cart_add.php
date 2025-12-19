<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../public/index.php');
    exit;
}

$product_id = (int)$_POST['product_id'];
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

// Cek stok produk
$stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product || $product['stock'] < $quantity) {
    header('Location: index.php?error=Stok tidak mencukupi');
    exit;
}

// Cek apakah produk sudah ada di cart
$stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
$stmt->execute([$_SESSION['user_id'], $product_id]);
$existing = $stmt->fetch();

if ($existing) {
    // Update quantity
    $new_quantity = $existing['quantity'] + $quantity;
    if ($new_quantity > $product['stock']) {
        header('Location: index.php?error=Stok tidak mencukupi');
        exit;
    }
    $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $stmt->execute([$new_quantity, $existing['id']]);
} else {
    // Insert baru
    $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $product_id, $quantity]);
}

header('Location: ../pages/cart.php?success=Produk ditambahkan ke keranjang');
exit;
?>
