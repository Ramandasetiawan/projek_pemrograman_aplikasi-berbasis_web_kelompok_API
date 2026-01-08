<?php

require_once 'config/db.php';

$new_password = 'password';
$new_hash = password_hash($new_password, PASSWORD_DEFAULT);

echo "<h3>Mengupdate Password Admin...</h3>";

try {

    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
    $stmt->execute([$new_hash]);

    echo "<span style='color: green; font-weight: bold;'>✓ Password admin berhasil diupdate!</span><br><br>";
    echo "Username: <b>admin</b><br>";
    echo "Password: <b>password</b><br><br>";

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = 'admin'");
    $stmt->execute();
    $user = $stmt->fetch();

    if (password_verify($new_password, $user['password'])) {
        echo "<span style='color: green;'>✓ Verifikasi berhasil! Password sudah benar.</span><br><br>";
    } else {
        echo "<span style='color: red;'>✗ Verifikasi gagal!</span><br>";
    }

    echo "<a href='auth/login.php' class='btn btn-primary'>Kembali ke Login</a>";

} catch (Exception $e) {
    echo "<span style='color: red;'>Error: " . $e->getMessage() . "</span>";
}
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<div style="margin: 20px;"></div>
