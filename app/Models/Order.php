<?php
require_once __DIR__ . '/../../core/Model.php';

class Order extends Model {
    public function createOrder($customerData, $items, $totalAmount, $vatAmount = 0.00, $vatType = 'exclusive', $couponCode = null, $discountAmount = 0.00) {
        $orderNumber = 'DJF-' . strtoupper(substr(uniqid(), -6));
        $createdAt = date('Y-m-d H:i:s');
        
        $sql = "INSERT INTO orders (order_number, customer_name, email, phone, address, city, country, shipping_address, shipping_city, shipping_country, order_note, coupon_code, discount_amount, total_amount, vat_amount, vat_type, status, payment_status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'New', 'Pending', ?)";
        
        $this->query($sql, [
            $orderNumber,
            $customerData['name'],
            $customerData['email'],
            $customerData['phone'],
            $customerData['address'],
            $customerData['city'] ?? 'Kuwait City',
            $customerData['country'] ?? 'Kuwait',
            $customerData['shipping_address'] ?? $customerData['address'],
            $customerData['shipping_city'] ?? ($customerData['city'] ?? 'Kuwait City'),
            $customerData['shipping_country'] ?? ($customerData['country'] ?? 'Kuwait'),
            $customerData['order_note'] ?? null,
            $couponCode,
            $discountAmount,
            $totalAmount,
            $vatAmount,
            $vatType,
            $createdAt
        ]);

        $orderId = $this->db->lastInsertId();

        $itemSql = "INSERT INTO order_items (order_id, product_id, product_code, product_name, size, color, length, note, price, quantity) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        foreach ($items as $item) {
            $this->query($itemSql, [
                $orderId,
                $item['id'],
                $item['product_code'] ?? '',
                $item['name'],
                $item['size'] ?? '54',
                $item['color'] ?? '',
                $item['length'] ?? '',
                $item['note'] ?? '',
                $item['price'],
                $item['quantity']
            ]);
        }

        return [
            'id' => $orderId,
            'order_number' => $orderNumber
        ];
    }

    public function updatePaymentStatus($orderId, $status, $apiResponse = null) {
        if ($apiResponse !== null) {
            $this->query("UPDATE orders SET payment_status = ?, api_response = ? WHERE id = ?", [$status, $apiResponse, $orderId]);
        } else {
            $this->query("UPDATE orders SET payment_status = ? WHERE id = ?", [$status, $orderId]);
        }
    }

    public function getAllOrders($startDate = null, $endDate = null, $orderStatus = null, $paymentStatus = null, $limit = 30) {
        $sql = "SELECT * FROM orders WHERE 1=1";
        $params = [];
        
        if ($startDate && $endDate) {
            $sql .= " AND DATE(created_at) >= ? AND DATE(created_at) <= ?";
            $params[] = $startDate;
            $params[] = $endDate;
        }

        if ($orderStatus && $orderStatus !== 'All') {
            $sql .= " AND status = ?";
            $params[] = $orderStatus;
        }

        if ($paymentStatus && $paymentStatus !== 'All') {
            $sql .= " AND payment_status = ?";
            $params[] = $paymentStatus;
        }
        
        $sql .= " ORDER BY id DESC";
        
        if (!$startDate && !$endDate && !$orderStatus && !$paymentStatus && $limit > 0) {
            $sql .= " LIMIT " . (int)$limit;
        }

        return $this->fetchAll($sql, $params);
    }

    public function getOrderById($orderId) {
        return $this->fetchOne("SELECT * FROM orders WHERE id = ?", [$orderId]);
    }

    public function getOrderItems($orderId) {
        return $this->fetchAll("SELECT * FROM order_items WHERE order_id = ?", [$orderId]);
    }

    public function getItemAssignments($orderId) {
        $sql = "SELECT a.*, t.unit_name 
                FROM order_item_assignments a 
                JOIN tailoring_units t ON a.tailoring_unit_id = t.id 
                WHERE a.order_id = ? 
                ORDER BY a.created_at ASC";
        return $this->fetchAll($sql, [$orderId]);
    }

    public function addAssignment($orderId, $orderItemId, $unitId, $quantity, $processNumber) {
        $sql = "INSERT INTO order_item_assignments (order_id, order_item_id, tailoring_unit_id, quantity, process_number) VALUES (?, ?, ?, ?, ?)";
        return $this->query($sql, [$orderId, $orderItemId, $unitId, $quantity, $processNumber]);
    }

    public function getAssignmentById($id) {
        return $this->fetchOne("SELECT * FROM order_item_assignments WHERE id = ?", [$id]);
    }

    public function removeAssignment($id) {
        return $this->query("DELETE FROM order_item_assignments WHERE id = ?", [$id]);
    }

    public function deleteOrder($orderId) {
        $this->query("DELETE FROM order_items WHERE order_id = ?", [$orderId]);
        return $this->query("DELETE FROM orders WHERE id = ?", [$orderId]);
    }

    public function updateOrderStatuses($orderId, $orderStatus, $paymentStatus, $trackingNumber = null, $shippingProvider = null, $shippingAttachment = null) {
        return $this->query(
            "UPDATE orders SET status = ?, payment_status = ?, tracking_number = ?, shipping_provider = ?, shipping_attachment = ? WHERE id = ?", 
            [$orderStatus, $paymentStatus, $trackingNumber, $shippingProvider, $shippingAttachment, $orderId]
        );
    }
}
