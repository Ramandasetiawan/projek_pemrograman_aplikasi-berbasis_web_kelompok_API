<?php
// Pastikan tidak ada output sebelum header
ob_start();
session_start();
require_once '../config/db.php';
require_once '../config/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// Verify CSRF token
check_csrf_token();

$input = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($input) || empty($password)) {
    header('Location: login.php?error=Username dan password harus diisi');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
$stmt->execute([$input, $input]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    // Regenerate session ID untuk keamanan
    session_regenerate_id(true);
    
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['full_name'] = $user['full_name'];
    
    // Redirect berdasarkan role
    if ($user['role'] === 'admin') {
        header('Location: ../admin/dashboard.php');
    } else {
        header('Location: ../home.php');
    }
    exit;
} else {
    header('Location: login.php?error=Username/email atau password salah');
    exit;
}
?>