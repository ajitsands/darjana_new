<?php
require_once __DIR__ . '/../../core/Model.php';

class Order extends Model {
    public function createOrder($customerData, $items, $totalAmount) {
        $orderNumber = 'DJF-' . strtoupper(substr(uniqid(), -6));
        
        $sql = "INSERT INTO orders (order_number, customer_name, email, phone, address, city, country, total_amount, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')";
        
        $this->query($sql, [
            $orderNumber,
            $customerData['name'],
            $customerData['email'],
            $customerData['phone'],
            $customerData['address'],
            $customerData['city'] ?? 'Kuwait City',
            $customerData['country'] ?? 'Kuwait',
            $totalAmount
        ]);

        $orderId = $this->db->lastInsertId();

        $itemSql = "INSERT INTO order_items (order_id, product_id, product_name, size, price, quantity) VALUES (?, ?, ?, ?, ?, ?)";
        foreach ($items as $item) {
            $this->query($itemSql, [
                $orderId,
                $item['id'],
                $item['name'],
                $item['size'] ?? '54',
                $item['price'],
                $item['quantity']
            ]);
        }

        return [
            'id' => $orderId,
            'order_number' => $orderNumber
        ];
    }

    public function getAllOrders() {
        return $this->fetchAll("SELECT * FROM orders ORDER BY id DESC");
    }

    public function getOrderItems($orderId) {
        return $this->fetchAll("SELECT * FROM order_items WHERE order_id = ?", [$orderId]);
    }

    public function deleteOrder($orderId) {
        $this->query("DELETE FROM order_items WHERE order_id = ?", [$orderId]);
        return $this->query("DELETE FROM orders WHERE id = ?", [$orderId]);
    }
}
