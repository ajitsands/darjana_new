<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Database.php';

$db = Database::getInstance();
// Let's add the table and columns manually just in case
try {
    $db->exec("CREATE TABLE IF NOT EXISTS settings (setting_key VARCHAR(100) PRIMARY KEY, setting_value TEXT)");
    $db->exec("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('vat_percentage', '5'), ('vat_type', 'exclusive'), ('timezone', 'Asia/Bahrain')");
    $db->exec("ALTER TABLE orders ADD COLUMN vat_amount DECIMAL(10, 2) DEFAULT 0.00");
    $db->exec("ALTER TABLE orders ADD COLUMN vat_type VARCHAR(20) DEFAULT 'exclusive'");
    echo "Migration successful!";
} catch (Exception $e) {
    echo "Migration error: " . $e->getMessage();
}
