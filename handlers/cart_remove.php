<?php
session_start();
require_once '../config/db.php';
require_once '../config/csrf.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/cart.php');
    exit;
}

check_csrf_token();

$cart_id = (int)$_POST['cart_id'];

$stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
$stmt->execute([$cart_id, $_SESSION['user_id']]);

header('Location: ../pages/cart.php?success=Produk dihapus dari keranjang');
exit;
?>
