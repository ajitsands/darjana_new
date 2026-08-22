<?php
/**
 * Script to fix "Unknown Country" entries in the database using Batch API.
 */
set_time_limit(0);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Database.php';

$db = Database::getInstance();
$tables = ['product_views', 'product_share_clicks'];
$updated = 0;

echo "<h2>Fixing Unknown Countries (Batch Mode)</h2>\n";

foreach ($tables as $table) {
    echo "<h3>Updating table: $table</h3>\n";
    $stmt = $db->query("SELECT DISTINCT ip_address FROM $table WHERE country = 'Unknown Country' OR country IS NULL");
    $ips = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Found " . count($ips) . " unique IPs to resolve in $table.<br>\n";
    
    // Chunk IPs into groups of 100 for batch API
    $chunks = array_chunk($ips, 100);
    
    foreach ($chunks as $index => $chunk) {
        $url = 'http://ip-api.com/batch?fields=query,status,country,countryCode,city';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($chunk));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        curl_close($ch);
        
        if ($response) {
            $data = json_decode($response, true);
            if (is_array($data)) {
                foreach ($data as $res) {
                    if (isset($res['status']) && $res['status'] === 'success') {
                        $country = $res['country'] ?? 'Unknown Country';
                        $countryCode = strtoupper($res['countryCode'] ?? 'UN');
                        $city = $res['city'] ?? 'Unknown City';
                        $ip = $res['query'];
                        
                        if ($country !== 'Unknown Country') {
                            $updateStmt = $db->prepare("UPDATE $table SET country = ?, country_code = ?, city = ? WHERE ip_address = ? AND (country = 'Unknown Country' OR country IS NULL)");
                            $updateStmt->execute([$country, $countryCode, $city, $ip]);
                            $count = $updateStmt->rowCount();
                            $updated += $count;
                        }
                    }
                }
                echo "Processed batch " . ($index + 1) . " of " . count($chunks) . ".<br>\n";
            }
        }
        
        // Sleep slightly to respect rate limits (batch limit is 15/min)
        sleep(2);
    }
}

echo "<br><b>Done. Total records updated: $updated</b>\n";


