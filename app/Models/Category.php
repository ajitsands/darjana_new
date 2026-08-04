<?php
require_once __DIR__ . '/../../core/Model.php';

class Category extends Model {
    public function getAll() {
        return $this->fetchAll("SELECT * FROM categories ORDER BY id ASC");
    }

    public function getBySlug($slug) {
        return $this->fetchOne("SELECT * FROM categories WHERE slug = ?", [$slug]);
    }
}
