<?php
require_once __DIR__ . '/../../core/Model.php';

class Setting extends Model {
    /**
     * Get a setting value by key
     */
    public function get($key, $default = null) {
        $row = $this->fetchOne("SELECT setting_value FROM settings WHERE setting_key = ?", [$key]);
        return $row ? $row['setting_value'] : $default;
    }

    /**
     * Update or insert a setting value
     */
    public function set($key, $value) {
        // MySQL specific UPSERT (INSERT ... ON DUPLICATE KEY UPDATE)
        $sql = "INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";
        return $this->query($sql, [$key, $value]);
    }

    /**
     * Get all settings as an associative array
     */
    public function getAll() {
        $rows = $this->fetchAll("SELECT * FROM settings");
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }
}
