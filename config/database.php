<?php
// Database Configuration for Dar Jana Fashion

define('DB_HOST', 'localhost');
define('DB_PASS', 'S@nds1@b');
define('DB_CHARSET', 'utf8mb4');

if (isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false)) {
    // Local credentials
    define('DB_NAME', 'darjana_portal_db');
    define('DB_USER', 'root');
} else {
    // Live Server credentials
    define('DB_NAME', 'darjanafashion_online_store_db');
    define('DB_USER', 'darjanafashion_online_store_user');
}

define('SITE_NAME', 'Dar Jana Fashion');

    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    define('BASE_URL', $protocol . '://' . $host);
