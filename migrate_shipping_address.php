<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Database.php';

$db = Database::getInstance();
try {
    $db->query("ALTER TABLE orders ADD COLUMN shipping_address TEXT NULL AFTER country");
    $db->query("ALTER TABLE orders ADD COLUMN shipping_city VARCHAR(100) NULL AFTER shipping_address");
    $db->query("ALTER TABLE orders ADD COLUMN shipping_country VARCHAR(100) NULL AFTER shipping_city");
    echo "Successfully added shipping_address, shipping_city, shipping_country columns.\n";
    
    // Fill existing orders with their billing addresses
    $db->query("UPDATE orders SET shipping_address = address, shipping_city = city, shipping_country = country");
    echo "Successfully copied existing addresses to shipping addresses.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
