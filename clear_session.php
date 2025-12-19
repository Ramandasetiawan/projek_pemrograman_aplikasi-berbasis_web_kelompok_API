<?php
// Clear all sessions
session_start();
session_unset();
session_destroy();

// Redirect ke halaman utama
header('Location: index.php');
exit;
?>
