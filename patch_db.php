<?php
require_once __DIR__ . '/config/database.php';

echo "<h2>Database Patcher</h2>";

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    $queries = [
        "ALTER TABLE orders ADD COLUMN tracking_number VARCHAR(100) DEFAULT NULL",
        "ALTER TABLE orders ADD COLUMN shipping_provider VARCHAR(100) DEFAULT NULL",
        "ALTER TABLE orders ADD COLUMN shipping_attachment VARCHAR(255) DEFAULT NULL"
    ];

    foreach ($queries as $q) {
        try {
            $pdo->exec($q);
            echo "<p style='color:green'>Successfully executed: $q</p>";
        } catch (PDOException $e) {
            echo "<p style='color:orange'>Skipped (maybe already exists): $q<br><small>" . $e->getMessage() . "</small></p>";
        }
    }
    
    echo "<h3>Patch Complete! You can delete this file now.</h3>";
} catch (PDOException $e) {
    echo "<p style='color:red'>Connection failed: " . $e->getMessage() . "</p>";
}
