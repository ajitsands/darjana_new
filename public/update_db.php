<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Database.php';

try {
    $db = Database::getInstance();
    
    // Check if column already exists
    $columnExists = false;
    
    // Depending on DB engine (MySQL or SQLite)
    $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
    
    if ($driver === 'mysql') {
        $stmt = $db->query("SHOW COLUMNS FROM order_item_assignments LIKE 'status'");
        $columnExists = $stmt->fetch() !== false;
    } else {
        $cols = $db->query('PRAGMA table_info(order_item_assignments)')->fetchAll(PDO::FETCH_ASSOC);
        $names = array_column($cols, 'name');
        $columnExists = in_array('status', $names);
    }
    
    if ($columnExists) {
        echo "<div style='color: green; font-family: sans-serif;'><h2>Status column already exists!</h2><p>Your database is already up to date.</p></div>";
    } else {
        // Add the column
        $db->exec("ALTER TABLE order_item_assignments ADD COLUMN status VARCHAR(50) DEFAULT 'Processing'");
        echo "<div style='color: green; font-family: sans-serif;'><h2>Success!</h2><p>The 'status' column was successfully added to the <code>order_item_assignments</code> table.</p><p>You can now use the Processing Jobs page.</p></div>";
    }
    
    // Provide a link back to admin
    echo "<br><a href='" . BASE_URL . "/admin' style='display:inline-block; padding:10px 20px; background:#4a5568; color:white; text-decoration:none; border-radius:4px; font-family:sans-serif;'>Go back to Dashboard</a>";
    
} catch (Exception $e) {
    echo "<div style='color: red; font-family: sans-serif;'><h2>Error Output</h2><p>" . htmlspecialchars($e->getMessage()) . "</p></div>";
}
