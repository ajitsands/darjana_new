<?php
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $dbFile = __DIR__ . '/../database/darjanafashon.sqlite';
        $dbDir = dirname($dbFile);
        if (!file_exists($dbDir)) {
            mkdir($dbDir, 0777, true);
        }

        try {
            // Attempt MySQL first if available, fallback to SQLite for local development zero-config setup
            if (extension_loaded('pdo_mysql')) {
                try {
                    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                    $options = [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ];
                    $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
                    return;
                } catch (PDOException $e) {
                    // MySQL connection failed or DB not created yet; fallback to SQLite database file
                }
            }

            // SQLite Fallback so web application works instantly without manual MySQL setup
            $dsn = "sqlite:" . $dbFile;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];
            $this->pdo = new PDO($dsn, null, null, $options);
            $this->initSqliteSchema();
        } catch (PDOException $e) {
            die("Database Connection Error: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->pdo;
    }

    private function initSqliteSchema() {
        $schemaFile = __DIR__ . '/../database/schema.sql';
        if (file_exists($schemaFile)) {
            $sql = file_get_contents($schemaFile);
            // Replace MySQL specific syntax for SQLite compatibility
            $sqliteSql = str_replace(
                ['AUTO_INCREMENT', 'ENGINE=InnoDB', 'DEFAULT CHARSET=utf8mb4', 'VARCHAR(255)', 'VARCHAR(100)', 'TEXT', 'DATETIME DEFAULT CURRENT_TIMESTAMP', 'INSERT IGNORE'],
                ['AUTOINCREMENT', '', '', 'TEXT', 'TEXT', 'TEXT', 'DATETIME DEFAULT CURRENT_TIMESTAMP', 'INSERT OR IGNORE'],
                $sql
            );
            $this->pdo->exec($sqliteSql);
        }
    }
}
