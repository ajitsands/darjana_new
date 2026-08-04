<?php
// Database Configuration for Dar Jana Fashion

define('DB_HOST', 'localhost');
define('DB_NAME', 'darjana_portal_db');
define('DB_USER', 'root');
define('DB_PASS', 'S@nds1@b');
define('DB_CHARSET', 'utf8mb4');

define('SITE_NAME', 'Dar Jana Fashion');

    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    define('BASE_URL', $protocol . '://' . $host);
