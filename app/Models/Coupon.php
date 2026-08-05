<?php
require_once __DIR__ . '/../../core/Model.php';

class Coupon extends Model {
    public function __construct() {
        parent::__construct();
        $this->ensureAudienceColumns();
    }

    private function ensureAudienceColumns() {
        try {
            $this->db->query("SELECT audience_type FROM coupons LIMIT 1");
        } catch (Exception $e) {
            try { $this->db->exec("ALTER TABLE coupons ADD COLUMN audience_type VARCHAR(50) DEFAULT 'all'"); } catch (Exception $ex) {}
        }

        try {
            $this->db->query("SELECT targeted_customers FROM coupons LIMIT 1");
        } catch (Exception $e) {
            try { $this->db->exec("ALTER TABLE coupons ADD COLUMN targeted_customers TEXT DEFAULT NULL"); } catch (Exception $ex) {}
        }
    }

    public function getAllCoupons() {
        return $this->query("SELECT * FROM coupons ORDER BY created_at DESC")->fetchAll();
    }

    public function getAvailableCustomers() {
        $sql = "SELECT 
                    customer_name, 
                    email, 
                    phone, 
                    city, 
                    country, 
                    COUNT(id) as total_orders, 
                    SUM(total_amount) as total_spent, 
                    MAX(created_at) as last_order_date 
                FROM orders 
                WHERE (email IS NOT NULL AND email != '') OR (phone IS NOT NULL AND phone != '')
                GROUP BY email, phone, customer_name, city, country 
                ORDER BY last_order_date DESC";
        $orderCustomers = $this->query($sql)->fetchAll();
        
        try {
            $subscribers = $this->query("SELECT email, subscribed_at as created_at FROM subscribers")->fetchAll();
            $existingEmails = array_map(function($c) { return strtolower(trim($c['email'])); }, $orderCustomers);
            
            foreach ($subscribers as $sub) {
                if (!empty($sub['email']) && !in_array(strtolower(trim($sub['email'])), $existingEmails)) {
                    $orderCustomers[] = [
                        'customer_name' => 'Newsletter Subscriber',
                        'email' => $sub['email'],
                        'phone' => '',
                        'city' => '-',
                        'country' => '-',
                        'total_orders' => 0,
                        'total_spent' => 0.00,
                        'last_order_date' => $sub['created_at']
                    ];
                }
            }
        } catch (Exception $e) {}

        return $orderCustomers;
    }

    public function getCouponById($id) {
        return $this->query("SELECT * FROM coupons WHERE id = ?", [$id])->fetch();
    }

    public function getCouponByCode($code) {
        return $this->query("SELECT * FROM coupons WHERE code = ?", [$code])->fetch();
    }

    public function createCoupon($data) {
        $sql = "INSERT INTO coupons (code, discount_type, discount_value, start_date, end_date, usage_limit_per_user, audience_type, targeted_customers) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        return $this->query($sql, [
            $data['code'],
            $data['discount_type'],
            $data['discount_value'],
            $data['start_date'],
            $data['end_date'],
            $data['usage_limit_per_user'],
            $data['audience_type'] ?? 'all',
            $data['targeted_customers'] ?? null
        ]);
    }

    public function updateCoupon($id, $data) {
        $sql = "UPDATE coupons SET 
                code = ?, 
                discount_type = ?, 
                discount_value = ?, 
                start_date = ?, 
                end_date = ?, 
                usage_limit_per_user = ?, 
                audience_type = ?, 
                targeted_customers = ? 
                WHERE id = ?";
        return $this->query($sql, [
            $data['code'],
            $data['discount_type'],
            $data['discount_value'],
            $data['start_date'],
            $data['end_date'],
            $data['usage_limit_per_user'],
            $data['audience_type'] ?? 'all',
            $data['targeted_customers'] ?? null,
            $id
        ]);
    }

    public function deleteCoupon($id) {
        return $this->query("DELETE FROM coupons WHERE id = ?", [$id]);
    }

    public function generateRandomCode($length = 6) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function validateCoupon($code, $email = '', $phone = '') {
        $coupon = $this->getCouponByCode($code);
        
        if (!$coupon) {
            return ['success' => false, 'message' => 'Invalid coupon code.'];
        }
        
        $currentDate = date('Y-m-d');
        if ($currentDate < $coupon['start_date']) {
            return ['success' => false, 'message' => 'This coupon is not active yet (Starts on ' . $coupon['start_date'] . ').'];
        }
        if ($currentDate > $coupon['end_date']) {
            return ['success' => false, 'message' => 'This coupon has expired on ' . $coupon['end_date'] . '.'];
        }

        // Check Targeted Audience restriction
        $audienceType = $coupon['audience_type'] ?? 'all';
        if ($audienceType === 'targeted') {
            $targetedRaw = $coupon['targeted_customers'] ?? '';
            $entries = preg_split('/[,\r\n;]+/', $targetedRaw, -1, PREG_SPLIT_NO_EMPTY);
            
            $isMatched = false;
            $customerEmail = strtolower(trim($email));
            $customerPhoneDigits = preg_replace('/[^0-9]/', '', $phone);

            foreach ($entries as $entry) {
                $entry = trim($entry);
                if (empty($entry)) continue;

                // Check Email Match
                if (strpos($entry, '@') !== false) {
                    if (!empty($customerEmail) && strtolower($entry) === $customerEmail) {
                        $isMatched = true;
                        break;
                    }
                } else {
                    // Check Phone Match
                    $entryDigits = preg_replace('/[^0-9]/', '', $entry);
                    if (!empty($customerPhoneDigits) && !empty($entryDigits)) {
                        // Check if exact or suffix matches (e.g. 33112233 matches +973 33112233)
                        $lenMin = min(strlen($customerPhoneDigits), strlen($entryDigits));
                        if ($customerPhoneDigits === $entryDigits || 
                            ($lenMin >= 7 && (substr($customerPhoneDigits, -$lenMin) === substr($entryDigits, -$lenMin)))) {
                            $isMatched = true;
                            break;
                        }
                    }
                }
            }

            if (!$isMatched) {
                return [
                    'success' => false, 
                    'message' => 'This offer code is an exclusive coupon valid only for designated targeted customers.'
                ];
            }
        }

        // Check usage limit
        $email = trim($email);
        $phone = trim($phone);
        if (!empty($email) || !empty($phone)) {
            $conditions = [];
            $params = [$code];
            if (!empty($email)) {
                $conditions[] = "email = ?";
                $params[] = $email;
            }
            if (!empty($phone)) {
                $conditions[] = "phone = ? OR phone LIKE ?";
                $params[] = $phone;
                $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                $params[] = '%' . $cleanPhone . '%';
            }
            $sql = "SELECT COUNT(*) as usage_count FROM orders 
                    WHERE coupon_code = ? AND (" . implode(' OR ', $conditions) . ")";
            $stmt = $this->query($sql, $params);
            $result = $stmt->fetch();
            $usageCount = (int)($result['usage_count'] ?? 0);

            if ($usageCount >= (int)$coupon['usage_limit_per_user']) {
                return ['success' => false, 'message' => 'You have reached the maximum usage limit (' . $coupon['usage_limit_per_user'] . ' time(s)) for this coupon.'];
            }
        }

        return ['success' => true, 'coupon' => $coupon];
    }
}
