<?php
session_start();
require_once '../config/db.php';
require_once '../config/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

check_csrf_token();

$full_name = trim($_POST['full_name']);
$username = trim($_POST['username']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$password = $_POST['password'];

if (empty($full_name) || empty($username) || empty($email) || empty($password)) {
    header('Location: register.php?error=Semua kolom wajib diisi kecuali telepon dan alamat');
    exit;
}

if (strlen($password) < 6) {
    header('Location: register.php?error=Password minimal 6 karakter');
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
$stmt->execute([$username, $email]);
if ($stmt->fetch()) {
    header('Location: register.php?error=Username atau email sudah terdaftar');
    exit;
}

$hashed = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, phone, address, role) VALUES (?, ?, ?, ?, ?, ?, 'customer')");
$stmt->execute([$username, $email, $hashed, $full_name, $phone, $address]);

header('Location: login.php?success=Registrasi berhasil! Silakan login.');
exit;
?>