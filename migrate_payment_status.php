<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Database.php';

$db = Database::getInstance();
try {
    $db->query("ALTER TABLE orders ADD COLUMN payment_status VARCHAR(50) DEFAULT 'Pending' AFTER status");
    echo "Successfully added payment_status column.\n";
    
    // Also, update existing 'status' from 'Pending' to 'New' to match the new convention
    $db->query("UPDATE orders SET status = 'New' WHERE status = 'Pending'");
    echo "Updated existing orders to have status 'New'.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
