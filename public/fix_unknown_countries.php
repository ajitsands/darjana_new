<?php
/**
 * Script to fix "Unknown Country" entries in the database.
 * Run this from the browser or command line on your live server to backfill location data.
 */
// Prevent timeout and flush output to browser immediately
set_time_limit(0);
ob_implicit_flush(true);
while (ob_get_level()) { ob_end_flush(); }

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../app/Models/Product.php';

$db = Database::getInstance();
$productModel = new Product();

$tables = ['product_views', 'product_share_clicks'];
$updated = 0;
$ipCache = []; // Cache IPs to avoid duplicate API calls

echo "<h2>Fixing Unknown Countries</h2>\n";

foreach ($tables as $table) {
    echo "<h3>Updating table: $table</h3>\n";
    // Get unique IPs first to reduce API calls
    $stmt = $db->query("SELECT DISTINCT ip_address FROM $table WHERE country = 'Unknown Country' OR country IS NULL");
    $ips = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Found " . count($ips) . " unique IPs to resolve in $table.<br><br>\n";
    
    foreach ($ips as $ip) {
        if (!isset($ipCache[$ip])) {
            $loc = $productModel->resolveGeoLocation($ip);
            $ipCache[$ip] = $loc;
            usleep(300000); // 300ms delay only for new API calls
        } else {
            $loc = $ipCache[$ip];
        }
        
        if ($loc['country'] !== 'Unknown Country') {
            $updateStmt = $db->prepare("UPDATE $table SET country = ?, country_code = ?, city = ? WHERE ip_address = ? AND (country = 'Unknown Country' OR country IS NULL)");
            $updateStmt->execute([$loc['country'], $loc['country_code'], $loc['city'], $ip]);
            $count = $updateStmt->rowCount();
            echo "Fixed $count records for IP: $ip -> {$loc['country']}<br>\n";
            $updated += $count;
        } else {
            echo "<span style='color:red;'>Could not resolve IP: $ip</span><br>\n";
        }
        
        // Output padding so browser renders immediately
        echo str_repeat(" ", 1024) . "\n"; 
        flush();
    }
}

echo "<br><b>Done. Total records updated: $updated</b>\n";

