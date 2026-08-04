<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Models/Order.php';

class CheckoutController extends Controller {
    public function index() {
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            $this->redirect(BASE_URL . '/collections/all-abaya');
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        $data = [
            'pageTitle' => 'Checkout | Dar Jana Fashion',
            'cart' => $cart,
            'total' => number_format($total, 2, '.', '')
        ];

        $this->render('checkout/index', $data);
    }

    public function process() {
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            $this->json(['success' => false, 'message' => 'Your cart is empty.']);
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $city = trim($_POST['city'] ?? 'Kuwait City');
        $country = trim($_POST['country'] ?? 'Kuwait');

        if (empty($name) || empty($email) || empty($phone) || empty($address)) {
            $this->json(['success' => false, 'message' => 'Please fill in all required customer fields.']);
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        $orderModel = new Order();
        $order = $orderModel->createOrder([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'city' => $city,
            'country' => $country
        ], $cart, $total);

        // Clear cart session after successful order
        $_SESSION['cart'] = [];

        $this->json([
            'success' => true,
            'message' => 'Thank you! Your order has been placed successfully.',
            'order_number' => $order['order_number']
        ]);
    }
}
