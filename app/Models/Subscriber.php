<?php
require_once __DIR__ . '/../../core/Model.php';

class Subscriber extends Model {
    public function subscribe($email) {
        $existing = $this->fetchOne("SELECT id FROM subscribers WHERE email = ?", [$email]);
        if ($existing) {
            return true;
        }
        return $this->query("INSERT INTO subscribers (email) VALUES (?)", [$email]);
    }
}
