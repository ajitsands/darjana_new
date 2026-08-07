<?php
require_once __DIR__ . '/../../core/Model.php';

class Product extends Model {
    // 1 BHD = 1.23 KWD (prices stored in DB as base KWD)
    private $bhdExchangeRate = 1.23;

    public function __construct() {
        parent::__construct();
        $this->ensureVariantColumns();
        $this->ensureShareClicksTable();
        $this->ensureProductViewsTable();
    }

    private function ensureProductViewsTable() {
        try {
            $this->db->query("SELECT 1 FROM product_views LIMIT 1");
        } catch (Exception $e) {
            $sql = "CREATE TABLE IF NOT EXISTS product_views (
                id INT AUTO_INCREMENT PRIMARY KEY,
                product_id INT NOT NULL,
                ip_address VARCHAR(45) NOT NULL,
                user_agent TEXT NULL,
                country VARCHAR(100) DEFAULT NULL,
                country_code VARCHAR(10) DEFAULT NULL,
                city VARCHAR(100) DEFAULT NULL,
                viewed_at DATETIME NOT NULL,
                INDEX idx_prod_ip_view (product_id, ip_address, viewed_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            try {
                $this->db->exec($sql);
            } catch (Exception $ex) {
                $sqlSqlite = "CREATE TABLE IF NOT EXISTS product_views (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    product_id INTEGER NOT NULL,
                    ip_address TEXT NOT NULL,
                    user_agent TEXT NULL,
                    country TEXT NULL,
                    country_code TEXT NULL,
                    city TEXT NULL,
                    viewed_at DATETIME NOT NULL
                )";
                try { $this->db->exec($sqlSqlite); } catch (Exception $ex2) {}
            }
        }
    }

    private function ensureShareClicksTable() {
        try {
            $this->db->query("SELECT 1 FROM product_share_clicks LIMIT 1");
        } catch (Exception $e) {
            $sql = "CREATE TABLE IF NOT EXISTS product_share_clicks (
                id INT AUTO_INCREMENT PRIMARY KEY,
                product_id INT NOT NULL,
                source VARCHAR(50) NOT NULL,
                ip_address VARCHAR(45) NOT NULL,
                user_agent TEXT NULL,
                country VARCHAR(100) DEFAULT NULL,
                country_code VARCHAR(10) DEFAULT NULL,
                city VARCHAR(100) DEFAULT NULL,
                clicked_at DATETIME NOT NULL,
                INDEX idx_prod_ip_src (product_id, ip_address, source, clicked_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            try {
                $this->db->exec($sql);
            } catch (Exception $ex) {
                $sqlSqlite = "CREATE TABLE IF NOT EXISTS product_share_clicks (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    product_id INTEGER NOT NULL,
                    source TEXT NOT NULL,
                    ip_address TEXT NOT NULL,
                    user_agent TEXT NULL,
                    country TEXT NULL,
                    country_code TEXT NULL,
                    city TEXT NULL,
                    clicked_at DATETIME NOT NULL
                )";
                try { $this->db->exec($sqlSqlite); } catch (Exception $ex2) {}
            }
        }

        try {
            $this->db->query("SELECT country FROM product_share_clicks LIMIT 1");
        } catch (Exception $e) {
            try { $this->db->exec("ALTER TABLE product_share_clicks ADD COLUMN country VARCHAR(100) DEFAULT NULL"); } catch (Exception $ex) {}
            try { $this->db->exec("ALTER TABLE product_share_clicks ADD COLUMN country_code VARCHAR(10) DEFAULT NULL"); } catch (Exception $ex) {}
            try { $this->db->exec("ALTER TABLE product_share_clicks ADD COLUMN city VARCHAR(100) DEFAULT NULL"); } catch (Exception $ex) {}
        }

        try {
            $this->db->query("SELECT recipient_email FROM product_share_clicks LIMIT 1");
        } catch (Exception $e) {
            try { $this->db->exec("ALTER TABLE product_share_clicks ADD COLUMN recipient_email VARCHAR(255) DEFAULT NULL"); } catch (Exception $ex) {}
        }
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

        try {
            $this->db->query("SELECT is_verified FROM products LIMIT 1");
        } catch (Exception $e) {
            try { $this->db->exec("ALTER TABLE products ADD COLUMN is_verified TINYINT(1) DEFAULT 1"); } catch (Exception $ex) {}
        }
    }

    public function getAll($limit = null, $offset = 0, $sort = 'featured', $minPrice = null, $maxPrice = null, $activeOnly = true) {
        $sql = "SELECT p.*, GROUP_CONCAT(c.name SEPARATOR ', ') as category_name, GROUP_CONCAT(c.slug SEPARATOR ',') as category_slug 
                FROM products p 
                LEFT JOIN categories c ON FIND_IN_SET(c.id, p.category_id) WHERE 1=1";
        
        if ($activeOnly) {
            $sql .= " AND p.is_active = 1 AND COALESCE(p.is_verified, 1) = 1";
        }
        $sql .= " GROUP BY p.id";

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
            "SELECT p.*, GROUP_CONCAT(c.name SEPARATOR ', ') as category_name, GROUP_CONCAT(c.slug SEPARATOR ',') as category_slug 
             FROM products p 
             LEFT JOIN categories c ON FIND_IN_SET(c.id, p.category_id) 
             WHERE p.is_featured = 1 AND p.is_active = 1 AND COALESCE(p.is_verified, 1) = 1
             GROUP BY p.id ORDER BY p.id DESC"
        );
    }

    public function getByCategorySlug($slug, $limit = null, $offset = 0, $sort = 'featured', $minPrice = null, $maxPrice = null, $activeOnly = true) {
        if ($slug === 'all-abaya' || $slug === 'all-dresses') {
            return $this->getAll($limit, $offset, $sort, $minPrice, $maxPrice, $activeOnly);
        }

        $sql = "SELECT p.*, GROUP_CONCAT(c2.name SEPARATOR ', ') as category_name, GROUP_CONCAT(c2.slug SEPARATOR ',') as category_slug 
                FROM products p 
                JOIN categories c ON FIND_IN_SET(c.id, p.category_id) 
                LEFT JOIN categories c2 ON FIND_IN_SET(c2.id, p.category_id) 
                WHERE c.slug = ?";
        
        if ($activeOnly) {
            $sql .= " AND p.is_active = 1 AND COALESCE(p.is_verified, 1) = 1";
        }
        
        $sql .= " GROUP BY p.id";
        
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

    public function countByCategorySlug($slug, $minPrice = null, $maxPrice = null, $activeOnly = true) {
        if ($slug === 'all-abaya' || $slug === 'all-dresses') {
            $sql = "SELECT COUNT(*) as total FROM products p WHERE 1=1";
            if ($activeOnly) {
                $sql .= " AND p.is_active = 1 AND COALESCE(p.is_verified, 1) = 1";
            }
            $params = [];
        } else {
            $sql = "SELECT COUNT(DISTINCT p.id) as total FROM products p JOIN categories c ON FIND_IN_SET(c.id, p.category_id) WHERE c.slug = ?";
            if ($activeOnly) {
                $sql .= " AND p.is_active = 1 AND COALESCE(p.is_verified, 1) = 1";
            }
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
            "SELECT p.*, GROUP_CONCAT(c.name SEPARATOR ', ') as category_name, GROUP_CONCAT(c.slug SEPARATOR ',') as category_slug 
             FROM products p 
             LEFT JOIN categories c ON FIND_IN_SET(c.id, p.category_id) 
             WHERE p.slug = ? GROUP BY p.id",
            [$slug]
        );
    }

    public function getById($id) {
        return $this->fetchOne("SELECT * FROM products WHERE id = ?", [$id]);
    }

    public function search($term, $activeOnly = true) {
        $likeTerm = '%' . $term . '%';
        $sql = "SELECT p.*, GROUP_CONCAT(c.name SEPARATOR ', ') as category_name 
             FROM products p 
             LEFT JOIN categories c ON FIND_IN_SET(c.id, p.category_id) 
             WHERE (p.name LIKE ? OR p.product_code LIKE ? OR p.description LIKE ?)";
        
        if ($activeOnly) {
            $sql .= " AND p.is_active = 1 AND COALESCE(p.is_verified, 1) = 1";
        }
        $sql .= " GROUP BY p.id ORDER BY p.id DESC";

        return $this->fetchAll($sql, [$likeTerm, $likeTerm, $likeTerm]);
    }

    public function togglePublishStatus($id, $isVerified) {
        $status = (int)$isVerified ? 1 : 0;
        return $this->query("UPDATE products SET is_verified = ? WHERE id = ?", [$status, $id]);
    }

    public function create($data) {
        $sql = "INSERT INTO products (category_id, product_code, name, name_ar, slug, price, sale_price, image, secondary_image, description, description_ar, offer_tag_type, colors, sizes, lengths, is_featured, is_active, is_verified, stock, media) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $this->query($sql, [
            $data['category_id'],
            $data['product_code'],
            $data['name'],
            $data['name_ar'] ?? null,
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
            $data['is_active'] ?? 1,
            isset($data['is_verified']) ? (int)$data['is_verified'] : 0,
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
                category_id = ?, product_code = ?, name = ?, name_ar = ?, slug = ?, price = ?, 
                sale_price = ?, image = ?, secondary_image = ?, description = ?, description_ar = ?, 
                offer_tag_type = ?, colors = ?, sizes = ?, lengths = ?, is_featured = ?, is_active = ?, is_verified = ?, stock = ?, media = ? 
                WHERE id = ?";
        
        $this->query($sql, [
            $data['category_id'],
            $data['product_code'],
            $data['name'],
            $data['name_ar'] ?? null,
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
            $data['is_active'] ?? 1,
            isset($data['is_verified']) ? (int)$data['is_verified'] : 1,
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

    /**
     * Resolve GeoLocation (Country, Country Code, City) from IP Address
     */
    public function resolveGeoLocation($ipAddress) {
        $cfCountry = $_SERVER['HTTP_CF_IPCOUNTRY'] ?? null;
        $cfCity = $_SERVER['HTTP_CF_IPCITY'] ?? null;
        if (!empty($cfCountry) && $cfCountry !== 'XX') {
            return [
                'country' => $cfCountry,
                'country_code' => strtoupper($cfCountry),
                'city' => $cfCity ?: 'Unknown City'
            ];
        }

        if (in_array($ipAddress, ['127.0.0.1', '::1']) || strpos($ipAddress, '192.168.') === 0 || strpos($ipAddress, '10.') === 0) {
            return [
                'country' => 'Localhost / Dev',
                'country_code' => 'LOCAL',
                'city' => 'Localhost'
            ];
        }

        try {
            $url = "http://ip-api.com/json/" . urlencode($ipAddress) . "?fields=status,country,countryCode,city";
            $ctx = stream_context_create([
                'http' => ['timeout' => 2]
            ]);
            $response = @file_get_contents($url, false, $ctx);
            if ($response) {
                $data = json_decode($response, true);
                if (isset($data['status']) && $data['status'] === 'success') {
                    return [
                        'country' => $data['country'] ?? 'Unknown Country',
                        'country_code' => strtoupper($data['countryCode'] ?? 'UN'),
                        'city' => $data['city'] ?? 'Unknown City'
                    ];
                }
            }
        } catch (Exception $e) {}

        return [
            'country' => 'Unknown Country',
            'country_code' => 'UN',
            'city' => 'Unknown City'
        ];
    }

    /**
     * Track a share link click with deduplication window configured by admin (default: 60 minutes)
     */
    public function trackShareClick($productId, $source, $ipAddress, $userAgent = '', $recipientEmail = null) {
        require_once __DIR__ . '/Setting.php';
        $settingModel = new Setting();
        $dedupMinutes = (int)$settingModel->get('share_click_dedup_minutes', 60);
        if ($dedupMinutes <= 0) {
            $dedupMinutes = 60;
        }

        $timeThreshold = date('Y-m-d H:i:s', time() - ($dedupMinutes * 60));

        // Check if click exists from this IP address for this product & source within the deduplication duration
        $existing = $this->fetchOne(
            "SELECT id FROM product_share_clicks WHERE product_id = ? AND source = ? AND ip_address = ? AND clicked_at >= ? LIMIT 1",
            [(int)$productId, strtolower($source), $ipAddress, $timeThreshold]
        );

        if (!$existing) {
            $location = $this->resolveGeoLocation($ipAddress);
            $now = date('Y-m-d H:i:s');
            $this->query(
                "INSERT INTO product_share_clicks (product_id, source, ip_address, user_agent, country, country_code, city, recipient_email, clicked_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [(int)$productId, strtolower($source), $ipAddress, substr($userAgent, 0, 500), $location['country'], $location['country_code'], $location['city'], $recipientEmail, $now]
            );
            return true; // Click counted!
        }

        return false; // Ignored duplicate click within window
    }

    /**
     * Get share click statistics per product or for all products including locations
     */
    public function getShareStats($productId = null) {
        if ($productId) {
            $rows = $this->fetchAll(
                "SELECT source, COUNT(*) as click_count FROM product_share_clicks WHERE product_id = ? GROUP BY source",
                [(int)$productId]
            );
            $locRows = $this->fetchAll(
                "SELECT country, country_code, city, COUNT(*) as click_count FROM product_share_clicks WHERE product_id = ? GROUP BY country, country_code, city ORDER BY click_count DESC LIMIT 10",
                [(int)$productId]
            );
            $total = 0;
            $bySource = [
                'instagram' => 0,
                'facebook' => 0,
                'whatsapp' => 0,
                'tiktok' => 0,
                'youtube' => 0
            ];
            foreach ($rows as $r) {
                $src = strtolower($r['source']);
                $count = (int)$r['click_count'];
                $bySource[$src] = $count;
                $total += $count;
            }

            $locations = [];
            foreach ($locRows as $lr) {
                $locations[] = [
                    'country' => $lr['country'] ?: 'Unknown',
                    'country_code' => $lr['country_code'] ?: 'UN',
                    'city' => $lr['city'] ?: 'Unknown',
                    'count' => (int)$lr['click_count']
                ];
            }

            return ['total' => $total, 'by_source' => $bySource, 'locations' => $locations];
        } else {
            $rows = $this->fetchAll(
                "SELECT product_id, source, COUNT(*) as click_count FROM product_share_clicks GROUP BY product_id, source"
            );
            $locRows = $this->fetchAll(
                "SELECT product_id, country, country_code, city, COUNT(*) as click_count FROM product_share_clicks GROUP BY product_id, country, country_code, city ORDER BY click_count DESC"
            );

            $stats = [];
            foreach ($rows as $r) {
                $pid = (int)$r['product_id'];
                if (!isset($stats[$pid])) {
                    $stats[$pid] = [
                        'total' => 0,
                        'by_source' => [
                            'instagram' => 0,
                            'facebook' => 0,
                            'whatsapp' => 0,
                            'tiktok' => 0,
                            'youtube' => 0
                        ],
                        'locations' => []
                    ];
                }
                $src = strtolower($r['source']);
                $count = (int)$r['click_count'];
                $stats[$pid]['by_source'][$src] = $count;
                $stats[$pid]['total'] += $count;
            }

            foreach ($locRows as $lr) {
                $pid = (int)$lr['product_id'];
                if (isset($stats[$pid])) {
                    if (count($stats[$pid]['locations']) < 10) {
                        $stats[$pid]['locations'][] = [
                            'country' => $lr['country'] ?: 'Unknown',
                            'country_code' => $lr['country_code'] ?: 'UN',
                            'city' => $lr['city'] ?: 'Unknown',
                            'count' => (int)$lr['click_count']
                        ];
                    }
                }
            }

            return $stats;
        }
    }

    /**
     * Get comprehensive click insights data for admin reporting dashboard
     */
    public function getDetailedClickInsights() {
        $totalRow = $this->fetchOne("SELECT COUNT(*) as total FROM product_share_clicks");
        $totalClicks = (int)($totalRow['total'] ?? 0);

        $allProducts = $this->getAll(null, 0, 'featured', null, null, false);
        $shareStats = $this->getShareStats();

        $topLocationsByProduct = [];
        $locQuery = $this->fetchAll("
            SELECT product_id, country, city, country_code, COUNT(*) as cnt 
            FROM product_share_clicks 
            GROUP BY product_id, country, city, country_code 
            ORDER BY cnt DESC
        ");
        foreach ($locQuery as $lq) {
            $pid = (int)$lq['product_id'];
            if (!isset($topLocationsByProduct[$pid])) {
                $topLocationsByProduct[$pid] = ($lq['country'] ?: 'Unknown') . ' (' . ($lq['city'] ?: 'Unknown') . ')';
            }
        }

        $latestClicksByProduct = [];
        $latestQuery = $this->fetchAll("
            SELECT product_id, MAX(clicked_at) as max_clicked 
            FROM product_share_clicks 
            GROUP BY product_id
        ");
        foreach ($latestQuery as $lq) {
            $latestClicksByProduct[(int)$lq['product_id']] = $lq['max_clicked'];
        }

        $productPerformance = [];
        foreach ($allProducts as $p) {
            $pid = (int)$p['id'];
            $pStats = $shareStats[$pid] ?? [
                'total' => 0,
                'by_source' => ['instagram' => 0, 'facebook' => 0, 'whatsapp' => 0, 'tiktok' => 0, 'youtube' => 0]
            ];

            $productPerformance[] = [
                'id' => $pid,
                'product_code' => $p['product_code'],
                'name' => $p['name'],
                'image' => $p['image'],
                'price' => $p['price'],
                'category_name' => $p['category_name'] ?? 'General',
                'total_clicks' => $pStats['total'],
                'by_source' => $pStats['by_source'],
                'top_location' => $topLocationsByProduct[$pid] ?? 'No clicks yet',
                'last_clicked_at' => $latestClicksByProduct[$pid] ?? null
            ];
        }

        usort($productPerformance, fn($a, $b) => $b['total_clicks'] <=> $a['total_clicks']);

        $platformsConfig = [
            'whatsapp' => ['name' => 'WhatsApp', 'icon' => '💬', 'color' => '#38a169'],
            'instagram' => ['name' => 'Instagram', 'icon' => '📸', 'color' => '#d69e2e'],
            'facebook' => ['name' => 'Facebook', 'icon' => '📘', 'color' => '#3182ce'],
            'tiktok' => ['name' => 'TikTok', 'icon' => '🎵', 'color' => '#805ad5'],
            'youtube' => ['name' => 'YouTube', 'icon' => '📺', 'color' => '#e53e3e'],
            'email' => ['name' => 'Email Campaign', 'icon' => '✉️', 'color' => '#8b5cf6'],
            'email_campaign' => ['name' => 'Email Campaign', 'icon' => '✉️', 'color' => '#8b5cf6']
        ];

        $platformCounts = $this->fetchAll("
            SELECT source, COUNT(*) as cnt 
            FROM product_share_clicks 
            GROUP BY source
        ");
        $platformTotals = [];
        foreach ($platformCounts as $pc) {
            $platformTotals[strtolower($pc['source'])] = (int)$pc['cnt'];
        }

        $topProductByPlatform = [];
        $topProdQuery = $this->fetchAll("
            SELECT psc.source, p.name as product_name, COUNT(*) as cnt
            FROM product_share_clicks psc
            JOIN products p ON p.id = psc.product_id
            GROUP BY psc.source, psc.product_id
            ORDER BY cnt DESC
        ");
        foreach ($topProdQuery as $tpq) {
            $src = strtolower($tpq['source']);
            if (!isset($topProductByPlatform[$src])) {
                $topProductByPlatform[$src] = $tpq['product_name'];
            }
        }

        $topCountryByPlatform = [];
        $topCountryQuery = $this->fetchAll("
            SELECT source, country, COUNT(*) as cnt
            FROM product_share_clicks
            GROUP BY source, country
            ORDER BY cnt DESC
        ");
        foreach ($topCountryQuery as $tcq) {
            $src = strtolower($tcq['source']);
            if (!isset($topCountryByPlatform[$src])) {
                $topCountryByPlatform[$src] = $tcq['country'] ?: 'Unknown';
            }
        }

        $platformPerformance = [];
        foreach ($platformsConfig as $key => $cfg) {
            $clicks = $platformTotals[$key] ?? 0;
            $sharePct = $totalClicks > 0 ? round(($clicks / $totalClicks) * 100, 1) : 0;
            $platformPerformance[] = [
                'key' => $key,
                'name' => $cfg['name'],
                'icon' => $cfg['icon'],
                'color' => $cfg['color'],
                'total_clicks' => $clicks,
                'share_percent' => $sharePct,
                'top_product' => $topProductByPlatform[$key] ?? 'N/A',
                'top_country' => $topCountryByPlatform[$key] ?? 'N/A'
            ];
        }

        usort($platformPerformance, fn($a, $b) => $b['total_clicks'] <=> $a['total_clicks']);

        $locationPerformance = $this->fetchAll("
            SELECT country, country_code, city, COUNT(*) as total_clicks
            FROM product_share_clicks
            GROUP BY country, country_code, city
            ORDER BY total_clicks DESC
            LIMIT 20
        ");

        $recentClicks = $this->fetchAll("
            SELECT psc.*, p.name as product_name, p.product_code, p.image
            FROM product_share_clicks psc
            JOIN products p ON p.id = psc.product_id
            ORDER BY psc.clicked_at DESC
            LIMIT 25
        ");

        $topProduct = count($productPerformance) > 0 && $productPerformance[0]['total_clicks'] > 0 ? $productPerformance[0]['name'] : 'None';
        $topPlatform = count($platformPerformance) > 0 && $platformPerformance[0]['total_clicks'] > 0 ? $platformPerformance[0]['name'] : 'None';
        $topCountryRow = $this->fetchOne("SELECT country, COUNT(*) as cnt FROM product_share_clicks GROUP BY country ORDER BY cnt DESC LIMIT 1");
        $topCountry = $topCountryRow ? ($topCountryRow['country'] ?: 'Unknown') : 'None';

        return [
            'summary' => [
                'total_clicks' => $totalClicks,
                'top_product' => $topProduct,
                'top_platform' => $topPlatform,
                'top_country' => $topCountry
            ],
            'product_performance' => $productPerformance,
            'platform_performance' => $platformPerformance,
            'location_performance' => $locationPerformance,
            'recent_clicks' => $recentClicks
        ];
    }

    /**
     * Track a product detail page view with 2-minute cooldown per IP
     */
    public function trackProductView($productId, $ipAddress, $userAgent = '') {
        if (!$productId || $productId <= 0) return false;

        $timeThreshold = date('Y-m-d H:i:s', time() - 120);
        $existing = $this->fetchOne(
            "SELECT id FROM product_views WHERE product_id = ? AND ip_address = ? AND viewed_at >= ? LIMIT 1",
            [(int)$productId, $ipAddress, $timeThreshold]
        );

        if (!$existing) {
            $location = $this->resolveGeoLocation($ipAddress);
            $now = date('Y-m-d H:i:s');
            $this->query(
                "INSERT INTO product_views (product_id, ip_address, user_agent, country, country_code, city, viewed_at) VALUES (?, ?, ?, ?, ?, ?, ?)",
                [(int)$productId, $ipAddress, substr($userAgent, 0, 500), $location['country'], $location['country_code'], $location['city'], $now]
            );
            return true;
        }

        return false;
    }

    /**
     * Get Total Product Views Count (supports optional date filtering)
     */
    public function getTotalProductViewsCount($startDate = null, $endDate = null) {
        $sql = "SELECT COUNT(*) as total FROM product_views WHERE 1=1";
        $params = [];
        if ($startDate) {
            $sql .= " AND DATE(viewed_at) >= ?";
            $params[] = $startDate;
        }
        if ($endDate) {
            $sql .= " AND DATE(viewed_at) <= ?";
            $params[] = $endDate;
        }
        $row = $this->fetchOne($sql, $params);
        return $row ? (int)$row['total'] : 0;
    }

    /**
     * Get Top Viewed Products List (for Dashboard widget and ranking)
     */
    public function getTopViewedProducts($limit = 10, $startDate = null, $endDate = null) {
        $sql = "SELECT p.id, p.product_code, p.name, p.slug, p.image, p.price, p.sale_price, 
                       COUNT(pv.id) as total_views, 
                       COUNT(DISTINCT pv.ip_address) as unique_visitors
                FROM product_views pv
                JOIN products p ON pv.product_id = p.id
                WHERE 1=1";
        $params = [];
        if ($startDate) {
            $sql .= " AND DATE(pv.viewed_at) >= ?";
            $params[] = $startDate;
        }
        if ($endDate) {
            $sql .= " AND DATE(pv.viewed_at) <= ?";
            $params[] = $endDate;
        }
        $sql .= " GROUP BY p.id ORDER BY total_views DESC LIMIT " . (int)$limit;
        return $this->fetchAll($sql, $params);
    }

    /**
     * Get Comprehensive Detailed Product View Insights & Repeat IP Report (with date range filtering)
     */
    public function getDetailedProductViewInsights($startDate = null, $endDate = null) {
        $whereSql = " WHERE 1=1";
        $pvWhereSql = " WHERE 1=1";
        $params = [];

        if ($startDate) {
            $whereSql .= " AND DATE(viewed_at) >= ?";
            $pvWhereSql .= " AND DATE(pv.viewed_at) >= ?";
            $params[] = $startDate;
        }
        if ($endDate) {
            $whereSql .= " AND DATE(viewed_at) <= ?";
            $pvWhereSql .= " AND DATE(pv.viewed_at) <= ?";
            $params[] = $endDate;
        }

        $totalViewsRow = $this->fetchOne("SELECT COUNT(*) as total, COUNT(DISTINCT ip_address) as unique_ips FROM product_views" . $whereSql, $params);
        $totalViews = $totalViewsRow ? (int)$totalViewsRow['total'] : 0;
        $uniqueIps = $totalViewsRow ? (int)$totalViewsRow['unique_ips'] : 0;

        $topProductRow = $this->fetchOne("
            SELECT p.name, COUNT(pv.id) as cnt 
            FROM product_views pv 
            JOIN products p ON p.id = pv.product_id 
            " . $pvWhereSql . "
            GROUP BY p.id ORDER BY cnt DESC LIMIT 1
        ", $params);
        $topProduct = $topProductRow ? $topProductRow['name'] : 'None';

        $topCountryRow = $this->fetchOne("
            SELECT country, COUNT(*) as cnt 
            FROM product_views 
            " . $whereSql . "
            GROUP BY country ORDER BY cnt DESC LIMIT 1
        ", $params);
        $topCountry = $topCountryRow ? ($topCountryRow['country'] ?: 'Unknown') : 'None';

        // Product Ranking Performance
        $productPerformance = $this->fetchAll("
            SELECT p.id, p.product_code, p.name, p.slug, p.image, p.price, p.sale_price, 
                   COUNT(pv.id) as total_views, 
                   COUNT(DISTINCT pv.ip_address) as unique_visitors
            FROM product_views pv
            JOIN products p ON p.id = pv.product_id
            " . $pvWhereSql . "
            GROUP BY p.id
            HAVING total_views > 0
            ORDER BY total_views DESC
        ", $params);

        // Geolocation Performance
        $locationPerformance = $this->fetchAll("
            SELECT country, country_code, city, COUNT(*) as total_views
            FROM product_views
            " . $whereSql . "
            GROUP BY country, country_code, city
            ORDER BY total_views DESC
            LIMIT 50
        ", $params);

        // Repeat IP Visitor Report (How many times the same user/IP viewed the same item)
        $repeatIpReport = $this->fetchAll("
            SELECT p.id as product_id, p.product_code, p.name as product_name, p.image, 
                   pv.ip_address, pv.country, pv.country_code, pv.city, 
                   COUNT(pv.id) as ip_view_count, MAX(pv.viewed_at) as last_viewed_at
            FROM product_views pv
            JOIN products p ON p.id = pv.product_id
            " . $pvWhereSql . "
            GROUP BY p.id, pv.ip_address, pv.country, pv.country_code, pv.city
            ORDER BY ip_view_count DESC, last_viewed_at DESC
            LIMIT 100
        ", $params);

        // Recent Individual View Logs
        $recentViews = $this->fetchAll("
            SELECT pv.*, p.name as product_name, p.product_code, p.image
            FROM product_views pv
            JOIN products p ON p.id = pv.product_id
            " . $pvWhereSql . "
            ORDER BY pv.viewed_at DESC
            LIMIT 100
        ", $params);

        return [
            'summary' => [
                'total_views' => $totalViews,
                'unique_ips' => $uniqueIps,
                'top_product' => $topProduct,
                'top_country' => $topCountry
            ],
            'product_performance' => $productPerformance,
            'location_performance' => $locationPerformance,
            'repeat_ip_report' => $repeatIpReport,
            'recent_views' => $recentViews
        ];
    }

    /**
     * Get Email Campaign Click Performance (clicks resulting from promotional emails)
     */
    public function getEmailCampaignClickStats() {
        $totalEmailClicksRow = $this->fetchOne("
            SELECT COUNT(*) as total, COUNT(DISTINCT ip_address) as unique_ips 
            FROM product_share_clicks 
            WHERE source IN ('email', 'email_campaign')
        ");

        $recentEmailClicks = $this->fetchAll("
            SELECT psc.*, p.name as product_name, p.product_code, p.image, p.slug
            FROM product_share_clicks psc
            JOIN products p ON p.id = psc.product_id
            WHERE psc.source IN ('email', 'email_campaign')
            ORDER BY psc.clicked_at DESC
            LIMIT 50
        ");

        $topEmailProducts = $this->fetchAll("
            SELECT p.id, p.product_code, p.name, p.image, p.price, COUNT(psc.id) as total_clicks
            FROM product_share_clicks psc
            JOIN products p ON p.id = psc.product_id
            WHERE psc.source IN ('email', 'email_campaign')
            GROUP BY p.id
            ORDER BY total_clicks DESC
            LIMIT 10
        ");

        return [
            'total_clicks' => $totalEmailClicksRow ? (int)$totalEmailClicksRow['total'] : 0,
            'unique_ips' => $totalEmailClicksRow ? (int)$totalEmailClicksRow['unique_ips'] : 0,
            'top_products' => $topEmailProducts,
            'recent_clicks' => $recentEmailClicks
        ];
    }
}
