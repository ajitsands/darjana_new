<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Database.php';

$db = Database::getInstance();
try {
    $stmt = $db->query("SHOW COLUMNS FROM orders");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($columns);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
