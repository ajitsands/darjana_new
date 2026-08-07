<?php
require_once __DIR__ . '/../../core/Model.php';

class Subscriber extends Model {
    public function __construct() {
        parent::__construct();
        $this->ensureTable();
    }

    private function ensureTable() {
        try {
            $this->db->query("SELECT 1 FROM subscribers LIMIT 1");
        } catch (Exception $e) {
            $sql = "CREATE TABLE IF NOT EXISTS subscribers (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL UNIQUE,
                status VARCHAR(20) DEFAULT 'active',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_sub_email (email)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            try {
                $this->db->exec($sql);
            } catch (Exception $ex) {
                $sqlSqlite = "CREATE TABLE IF NOT EXISTS subscribers (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    email TEXT NOT NULL UNIQUE,
                    status TEXT DEFAULT 'active',
                    created_at DATETIME NOT NULL
                )";
                try { $this->db->exec($sqlSqlite); } catch (Exception $ex2) {}
            }
        }

        try {
            $this->db->query("SELECT created_at FROM subscribers LIMIT 1");
        } catch (Exception $e) {
            try { $this->db->exec("ALTER TABLE subscribers ADD COLUMN created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP"); } catch (Exception $ex) {}
            try { $this->db->exec("ALTER TABLE subscribers ADD COLUMN status VARCHAR(20) DEFAULT 'active'"); } catch (Exception $ex) {}
        }
    }

    public function subscribe($email) {
        $email = strtolower(trim($email));
        $existing = $this->fetchOne("SELECT id FROM subscribers WHERE email = ?", [$email]);
        if ($existing) {
            return true;
        }
        $now = date('Y-m-d H:i:s');
        return $this->query("INSERT INTO subscribers (email, status, created_at) VALUES (?, 'active', ?)", [$email, $now]);
    }

    public function getAllSubscribers() {
        return $this->fetchAll("SELECT * FROM subscribers ORDER BY id DESC");
    }

    public function getActiveSubscribers() {
        return $this->fetchAll("SELECT * FROM subscribers WHERE status = 'active' ORDER BY id DESC");
    }

    public function deleteSubscriber($id) {
        return $this->query("DELETE FROM subscribers WHERE id = ?", [(int)$id]);
    }

    public function countSubscribers() {
        $row = $this->fetchOne("SELECT COUNT(*) as total FROM subscribers");
        return $row ? (int)$row['total'] : 0;
    }
}
