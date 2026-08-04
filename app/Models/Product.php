<?php
require_once __DIR__ . '/../../core/Model.php';

class Product extends Model {
    // 1 BHD = 1.23 KWD (prices stored in DB as base KWD)
    private $bhdExchangeRate = 1.23;

    public function __construct() {
        parent::__construct();
        $this->ensureVariantColumns();
    }

    private function ensureVariantColumns() {
        try {
            $this->db->query("SELECT colors FROM products LIMIT 1");
        } catch (Exception $e) {
            try { $this->db->exec("ALTER TABLE products ADD COLUMN colors VARCHAR(255) DEFAULT 'Black,Navy Blue,Beige,Green & Red,Blue & Gray'"); } catch (Exception $ex) {}
        }

        try {
            $this->db->query("SELECT sizes FROM products LIMIT 1");
        } catch (Exception $e) {
            try { $this->db->exec("ALTER TABLE products ADD COLUMN sizes VARCHAR(255) DEFAULT 'S,M,L,XL,XXL'"); } catch (Exception $ex) {}
        }

        try {
            $this->db->query("SELECT lengths FROM products LIMIT 1");
        } catch (Exception $e) {
            try { $this->db->exec("ALTER TABLE products ADD COLUMN lengths VARCHAR(255) DEFAULT '52,54,55,56,57,58,60'"); } catch (Exception $ex) {}
        }

        try {
            $this->db->query("SELECT offer_tag_type FROM products LIMIT 1");
        } catch (Exception $e) {
            try { $this->db->exec("ALTER TABLE products ADD COLUMN offer_tag_type VARCHAR(50) DEFAULT 'percentage'"); } catch (Exception $ex) {}
        }

        try {
            $this->db->query("SELECT description_ar FROM products LIMIT 1");
        } catch (Exception $e) {
            try { $this->db->exec("ALTER TABLE products ADD COLUMN description_ar TEXT DEFAULT NULL"); } catch (Exception $ex) {}
        }

        try {
            $this->db->query("SELECT media FROM products LIMIT 1");
        } catch (Exception $e) {
            try { $this->db->exec("ALTER TABLE products ADD COLUMN media TEXT DEFAULT '[]'"); } catch (Exception $ex) {}
        }
    }

    public function getAll($limit = null, $offset = 0, $sort = 'featured', $minPrice = null, $maxPrice = null) {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";
        
        $params = [];
        if ($minPrice !== null && $minPrice !== '') {
            $minKwd = (float)$minPrice / $this->bhdExchangeRate;
            $sql .= " AND COALESCE(p.sale_price, p.price) >= ?";
            $params[] = $minKwd;
        }
        if ($maxPrice !== null && $maxPrice !== '') {
            $maxKwd = (float)$maxPrice / $this->bhdExchangeRate;
            $sql .= " AND COALESCE(p.sale_price, p.price) <= ?";
            $params[] = $maxKwd;
        }

        $sql .= " " . $this->getOrderByClause($sort);

        if ($limit) {
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }

        return $this->fetchAll($sql, $params);
    }

    public function getFeatured() {
        return $this->fetchAll(
            "SELECT p.*, c.name as category_name, c.slug as category_slug 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.is_featured = 1 
             ORDER BY p.id DESC"
        );
    }

    public function getByCategorySlug($slug, $limit = null, $offset = 0, $sort = 'featured', $minPrice = null, $maxPrice = null) {
        if ($slug === 'all-abaya' || $slug === 'all-dresses') {
            return $this->getAll($limit, $offset, $sort, $minPrice, $maxPrice);
        }

        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
                FROM products p 
                JOIN categories c ON p.category_id = c.id 
                WHERE c.slug = ?";
        
        $params = [$slug];

        if ($minPrice !== null && $minPrice !== '') {
            $minKwd = (float)$minPrice / $this->bhdExchangeRate;
            $sql .= " AND COALESCE(p.sale_price, p.price) >= ?";
            $params[] = $minKwd;
        }
        if ($maxPrice !== null && $maxPrice !== '') {
            $maxKwd = (float)$maxPrice / $this->bhdExchangeRate;
            $sql .= " AND COALESCE(p.sale_price, p.price) <= ?";
            $params[] = $maxKwd;
        }

        $sql .= " " . $this->getOrderByClause($sort);

        if ($limit) {
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }

        return $this->fetchAll($sql, $params);
    }

    public function countByCategorySlug($slug, $minPrice = null, $maxPrice = null) {
        if ($slug === 'all-abaya' || $slug === 'all-dresses') {
            $sql = "SELECT COUNT(*) as total FROM products p WHERE 1=1";
            $params = [];
        } else {
            $sql = "SELECT COUNT(*) as total FROM products p JOIN categories c ON p.category_id = c.id WHERE c.slug = ?";
            $params = [$slug];
        }

        if ($minPrice !== null && $minPrice !== '') {
            $minKwd = (float)$minPrice / $this->bhdExchangeRate;
            $sql .= " AND COALESCE(p.sale_price, p.price) >= ?";
            $params[] = $minKwd;
        }
        if ($maxPrice !== null && $maxPrice !== '') {
            $maxKwd = (float)$maxPrice / $this->bhdExchangeRate;
            $sql .= " AND COALESCE(p.sale_price, p.price) <= ?";
            $params[] = $maxKwd;
        }

        $row = $this->fetchOne($sql, $params);
        return $row ? (int)$row['total'] : 0;
    }

    private function getOrderByClause($sort) {
        switch ($sort) {
            case 'title_asc':
                return "ORDER BY p.name ASC";
            case 'title_desc':
                return "ORDER BY p.name DESC";
            case 'price_asc':
                return "ORDER BY COALESCE(p.sale_price, p.price) ASC";
            case 'price_desc':
                return "ORDER BY COALESCE(p.sale_price, p.price) DESC";
            case 'date_asc':
                return "ORDER BY p.id ASC";
            case 'date_desc':
                return "ORDER BY p.id DESC";
            case 'best_selling':
                return "ORDER BY p.is_featured DESC, p.id DESC";
            case 'relevant':
                return "ORDER BY p.is_featured DESC, p.id DESC";
            case 'featured':
            default:
                return "ORDER BY p.is_featured DESC, p.id DESC";
        }
    }

    public function getBySlug($slug) {
        return $this->fetchOne(
            "SELECT p.*, c.name as category_name, c.slug as category_slug 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.slug = ?",
            [$slug]
        );
    }

    public function getById($id) {
        return $this->fetchOne("SELECT * FROM products WHERE id = ?", [$id]);
    }

    public function search($term) {
        $likeTerm = '%' . $term . '%';
        return $this->fetchAll(
            "SELECT p.*, c.name as category_name 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.name LIKE ? OR p.product_code LIKE ? OR p.description LIKE ?
             ORDER BY p.id DESC",
            [$likeTerm, $likeTerm, $likeTerm]
        );
    }

    public function create($data) {
        $sql = "INSERT INTO products (category_id, product_code, name, slug, price, sale_price, image, secondary_image, description, description_ar, offer_tag_type, colors, sizes, lengths, is_featured, stock, media) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $this->query($sql, [
            $data['category_id'],
            $data['product_code'],
            $data['name'],
            $data['slug'],
            $data['price'],
            $data['sale_price'] ?: null,
            $data['image'],
            $data['secondary_image'] ?: $data['image'],
            $data['description'],
            $data['description_ar'] ?: null,
            $data['offer_tag_type'] ?? 'percentage',
            $data['colors'] ?? 'Black,Navy Blue,Beige',
            $data['sizes'] ?? 'S,M,L,XL,XXL',
            $data['lengths'] ?? '52,54,55,56,57,58,60',
            $data['is_featured'] ?? 0,
            $data['stock'] ?? 50,
            $data['media'] ?? '[]'
        ]);
        return $this->db->lastInsertId();
    }

    public function updateOfferTagType($productId, $tagType) {
        return $this->query("UPDATE products SET offer_tag_type = ? WHERE id = ?", [$tagType, $productId]);
    }

    public function updateVariants($productId, $colors, $sizes, $lengths) {
        return $this->query("UPDATE products SET colors = ?, sizes = ?, lengths = ? WHERE id = ?", [$colors, $sizes, $lengths, $productId]);
    }

    public function update($id, $data) {
        $sql = "UPDATE products SET 
                category_id = ?, product_code = ?, name = ?, slug = ?, price = ?, 
                sale_price = ?, image = ?, secondary_image = ?, description = ?, description_ar = ?, 
                offer_tag_type = ?, colors = ?, sizes = ?, lengths = ?, is_featured = ?, stock = ?, media = ? 
                WHERE id = ?";
        
        $this->query($sql, [
            $data['category_id'],
            $data['product_code'],
            $data['name'],
            $data['slug'],
            $data['price'],
            $data['sale_price'] ?: null,
            $data['image'],
            $data['secondary_image'] ?: $data['image'],
            $data['description'],
            $data['description_ar'] ?: null,
            $data['offer_tag_type'] ?? 'percentage',
            $data['colors'] ?? 'Black,Navy Blue,Beige',
            $data['sizes'] ?? 'S,M,L,XL,XXL',
            $data['lengths'] ?? '52,54,55,56,57,58,60',
            $data['is_featured'] ?? 0,
            $data['stock'] ?? 50,
            $data['media'] ?? '[]',
            $id
        ]);
        return true;
    }

    public function delete($id) {
        $this->query("DELETE FROM products WHERE id = ?", [$id]);
        return true;
    }
}
