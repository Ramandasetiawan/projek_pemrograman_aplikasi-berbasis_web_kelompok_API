<?php
session_start();
require_once '../config/db.php';
require_once '../config/csrf.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/products.php');
    exit;
}

check_csrf_token();

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$action = isset($_POST['action']) ? $_POST['action'] : 'add';

if ($product_id === 0) {
    header('Location: ../pages/products.php?error=Produk tidak valid');
    exit;
}

$stmt = $pdo->prepare("SELECT id, name FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: ../pages/products.php?error=Produk tidak ditemukan');
    exit;
}

try {
    if ($action === 'add') {

        $stmt = $pdo->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
        try {
            $stmt->execute([$_SESSION['user_id'], $product_id]);
            $message = urlencode($product['name'] . ' ditambahkan ke wishlist!');
        } catch (PDOException $e) {

            if ($e->getCode() == 23000) {
                $message = urlencode($product['name'] . ' sudah ada di wishlist!');
            } else {
                throw $e;
            }
        }
    } else if ($action === 'remove') {

        $stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$_SESSION['user_id'], $product_id]);
        $message = urlencode($product['name'] . ' dihapus dari wishlist!');
    } else if ($action === 'toggle') {

        $stmt = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$_SESSION['user_id'], $product_id]);

        if ($stmt->fetch()) {

            $stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$_SESSION['user_id'], $product_id]);
            $message = urlencode($product['name'] . ' dihapus dari wishlist!');
        } else {

            $stmt = $pdo->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
            $stmt->execute([$_SESSION['user_id'], $product_id]);
            $message = urlencode($product['name'] . ' ditambahkan ke wishlist!');
        }
    }

    $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../pages/products.php';

    $separator = (strpos($redirect, '?') !== false) ? '&' : '?';
    header("Location: $redirect{$separator}success=$message");
    exit;

} catch (PDOException $e) {
    header('Location: ../pages/products.php?error=' . urlencode('Terjadi kesalahan: ' . $e->getMessage()));
    exit;
}
?>
