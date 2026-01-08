<?php

require_once 'config/db.php';

echo "<h3>Test Koneksi Database</h3>";
echo "Database terkoneksi! ✓<br><br>";

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
$stmt->execute(['admin']);
$user = $stmt->fetch();

if ($user) {
    echo "<h3>Data User Admin:</h3>";
    echo "ID: " . $user['id'] . "<br>";
    echo "Username: " . $user['username'] . "<br>";
    echo "Email: " . $user['email'] . "<br>";
    echo "Role: " . $user['role'] . "<br>";
    echo "Password Hash (first 30 chars): " . substr($user['password'], 0, 30) . "...<br><br>";

    $test_password = 'password';
    echo "<h3>Test Password Verification:</h3>";
    echo "Testing password: '$test_password'<br>";

    if (password_verify($test_password, $user['password'])) {
        echo "<span style='color: green; font-weight: bold;'>✓ Password BENAR! Login seharusnya berhasil.</span><br>";
    } else {
        echo "<span style='color: red; font-weight: bold;'>✗ Password SALAH!</span><br>";
        echo "Hash yang tersimpan mungkin berbeda.<br>";

        $new_hash = password_hash($test_password, PASSWORD_DEFAULT);
        echo "<br><b>Hash baru untuk password 'password':</b><br>";
        echo $new_hash . "<br>";
    }
} else {
    echo "<span style='color: red;'>User admin tidak ditemukan!</span>";
}

echo "<br><br><a href='auth/login.php'>Kembali ke Login</a>";
?>
