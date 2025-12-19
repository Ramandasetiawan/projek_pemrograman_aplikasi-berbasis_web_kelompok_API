<?php
// Path configuration
define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('PAGES_PATH', ROOT_PATH . '/pages');
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('HANDLERS_PATH', ROOT_PATH . '/handlers');
define('ADMIN_PATH', ROOT_PATH . '/admin');
define('AUTH_PATH', ROOT_PATH . '/auth');

// URL configuration
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$base_url = $protocol . '://' . $host . '/tugas-12_pemrograman_aplikasi-berbasis_web_kelompok_API';

define('BASE_URL', $base_url);
define('PUBLIC_URL', $base_url . '/public');
define('ASSETS_URL', $base_url . '/public/assets');
?>
