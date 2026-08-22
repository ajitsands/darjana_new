<?php
/**
 * Script to fix "Unknown Country" entries in the database.
 * Run this from the browser or command line on your live server to backfill location data.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../app/Models/Product.php';

$db = Database::getInstance();
$productModel = new Product();

$tables = ['product_views', 'product_share_clicks'];
$updated = 0;

foreach ($tables as $table) {
    echo "Updating $table...<br>\n";
    $stmt = $db->query("SELECT id, ip_address FROM $table WHERE country = 'Unknown Country' OR country IS NULL");
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($records as $record) {
        // Resolve again using the improved method
        $loc = $productModel->resolveGeoLocation($record['ip_address']);
        
        if ($loc['country'] !== 'Unknown Country') {
            $updateStmt = $db->prepare("UPDATE $table SET country = ?, country_code = ?, city = ? WHERE id = ?");
            $updateStmt->execute([$loc['country'], $loc['country_code'], $loc['city'], $record['id']]);
            echo "Fixed ID {$record['id']} IP: {$record['ip_address']} -> {$loc['country']}<br>\n";
            $updated++;
        }
        
        // Add a small delay to avoid rate limits on free IP APIs
        usleep(300000); // 300ms
    }
}

echo "<br><b>Done. Total records updated: $updated</b>\n";
