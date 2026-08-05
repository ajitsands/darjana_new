<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Database.php';

try {
    $db = Database::getInstance();
    $db->query("ALTER TABLE products ADD COLUMN is_active TINYINT(1) DEFAULT 1;");
    echo "Column is_active added successfully.\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Column already exists.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
