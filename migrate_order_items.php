<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Database.php';

$db = Database::getInstance();
try {
    $db->exec("ALTER TABLE order_items ADD COLUMN product_code VARCHAR(100)");
    $db->exec("ALTER TABLE order_items ADD COLUMN color VARCHAR(100)");
    $db->exec("ALTER TABLE order_items ADD COLUMN length VARCHAR(20)");
    $db->exec("ALTER TABLE order_items ADD COLUMN note TEXT");
    echo "Migration successful!";
} catch (Exception $e) {
    echo "Migration error: " . $e->getMessage();
}
