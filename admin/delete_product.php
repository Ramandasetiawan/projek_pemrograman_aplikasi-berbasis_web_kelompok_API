<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') exit;
require_once '../config/db.php';

$id = $_GET['id'] ?? 0;
// Opsional: hapus gambar
$stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
$stmt->execute([$id]);
$image = $stmt->fetchColumn();
if ($image && $image !== 'default.jpg') {
    unlink('../assets/images/' . $image);
}

$pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
header('Location: products.php?success=Produk berhasil dihapus');
exit;
?>