<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Models/Order.php';
require_once __DIR__ . '/../Models/Category.php';

class TrackController extends Controller {
    public function index() {
        $categoryModel = new Category();
        $categories = $categoryModel->getAllActive();

        $orderNumber = $_GET['order_number'] ?? '';
        $order = null;
        $orderItems = [];
        $error = '';

        if (!empty($orderNumber)) {
            $orderModel = new Order();
            // Since we don't have a getOrderByNumber in Order.php, we'll write it inline or modify the model.
            // But let's just do a direct query for simplicity and speed.
            require_once __DIR__ . '/../../core/Database.php';
            $db = Database::getInstance();
            $stmt = $db->prepare("SELECT * FROM orders WHERE order_number = ?");
            $stmt->execute([$orderNumber]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($order) {
                $stmtItems = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
                $stmtItems->execute([$order['id']]);
                $orderItems = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $error = 'Order not found. Please check your order number and try again.';
            }
        }

        $data = [
            'pageTitle' => 'Track Your Order | Dar Jana Fashion',
            'categories' => $categories,
            'order' => $order,
            'orderItems' => $orderItems,
            'orderNumber' => $orderNumber,
            'error' => $error
        ];

        $this->render('track/index', $data);
    }
}
