<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

$username = trim($_POST['username']);
$email = trim($_POST['email']);
$password = $_POST['password'];

if (empty($username) || empty($email) || empty($password)) {
    header('Location: register.php?error=Semua kolom wajib diisi');
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
$stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
$stmt->execute([$username, $email, $hashed]);

header('Location: login.php?success=Registrasi berhasil! Silakan login.');
exit;
?>