<?php
session_start();
require_once '../config/db.php';
require_once '../config/csrf.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/cart.php');
    exit;
}

// Verify CSRF token
check_csrf_token();

$cart_id = (int)$_POST['cart_id'];
$action = $_POST['action'];

// Ambil data cart
$stmt = $pdo->prepare("SELECT c.*, p.stock FROM cart c JOIN products p ON c.product_id = p.id WHERE c.id = ? AND c.user_id = ?");
$stmt->execute([$cart_id, $_SESSION['user_id']]);
$cart = $stmt->fetch();

if (!$cart) {
    header('Location: cart.php?error=Item tidak ditemukan');
    exit;
}

if ($action === 'increase') {
    $new_quantity = $cart['quantity'] + 1;
    if ($new_quantity > $cart['stock']) {
        header('Location: cart.php?error=Stok tidak mencukupi');
        exit;
    }
    $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $stmt->execute([$new_quantity, $cart_id]);
} elseif ($action === 'decrease') {
    $new_quantity = $cart['quantity'] - 1;
    if ($new_quantity < 1) {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ?");
        $stmt->execute([$cart_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $stmt->execute([$new_quantity, $cart_id]);
    }
}

header('Location: ../pages/cart.php');
exit;
?>
