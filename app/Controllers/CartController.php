<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Models/Product.php';

class CartController extends Controller {
    public function index() {
        $cart = $_SESSION['cart'] ?? [];
        $total = $this->calculateTotal($cart);

        $data = [
            'pageTitle' => 'Shopping Cart | Dar Jana Fashion',
            'cart' => $cart,
            'total' => $total
        ];

        $this->render('cart/index', $data);
    }

    public function add() {
        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);
        $color = trim($_POST['color'] ?? 'Black');
        $size = trim($_POST['size'] ?? 'M');
        $length = trim($_POST['length'] ?? '56');
        $note = trim($_POST['note'] ?? '');
        $buyNow = isset($_POST['buy_now']) && $_POST['buy_now'] == '1';

        if ($productId <= 0) {
            $this->json(['success' => false, 'message' => 'Invalid product.']);
        }

        $productModel = new Product();
        $product = $productModel->getById($productId);

        if (!$product) {
            $this->json(['success' => false, 'message' => 'Product not found.']);
        }

        $cartKey = $productId . '_' . md5($color . '_' . $size . '_' . $length . '_' . $note);
        $price = $product['sale_price'] ? (float)$product['sale_price'] : (float)$product['price'];
        $isDiscounted = ($product['sale_price'] > 0 && $product['sale_price'] < $product['price']) ? true : false;

        if (isset($_SESSION['cart'][$cartKey])) {
            $_SESSION['cart'][$cartKey]['quantity'] += $quantity;
            if (!empty($note)) {
                $_SESSION['cart'][$cartKey]['note'] = $note;
            }
        } else {
            $_SESSION['cart'][$cartKey] = [
                'key' => $cartKey,
                'id' => $product['id'],
                'product_code' => $product['product_code'],
                'name' => $product['name'],
                'slug' => $product['slug'],
                'price' => $price,
                'is_discounted' => $isDiscounted,
                'image' => $product['image'],
                'color' => $color,
                'size' => $size,
                'length' => $length,
                'note' => $note,
                'quantity' => $quantity
            ];
        }

        if ($buyNow) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                $this->json(['success' => true, 'redirect' => BASE_URL . '/checkout']);
            } else {
                $this->redirect(BASE_URL . '/checkout');
            }
        }

        $this->json([
            'success' => true,
            'message' => 'Item added to cart successfully!',
            'cart' => $_SESSION['cart'],
            'count' => $this->calculateCount($_SESSION['cart']),
            'total' => $this->calculateTotal($_SESSION['cart'])
        ]);
    }

    public function update() {
        $cartKey = trim($_POST['cart_key'] ?? '');
        $quantity = (int)($_POST['quantity'] ?? 1);

        if (isset($_SESSION['cart'][$cartKey])) {
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$cartKey]);
            } else {
                $_SESSION['cart'][$cartKey]['quantity'] = $quantity;
            }
        }

        $this->json([
            'success' => true,
            'cart' => $_SESSION['cart'],
            'count' => $this->calculateCount($_SESSION['cart']),
            'total' => $this->calculateTotal($_SESSION['cart'])
        ]);
    }

    public function remove() {
        $cartKey = trim($_POST['cart_key'] ?? '');

        if (isset($_SESSION['cart'][$cartKey])) {
            unset($_SESSION['cart'][$cartKey]);
        }

        $this->json([
            'success' => true,
            'cart' => $_SESSION['cart'],
            'count' => $this->calculateCount($_SESSION['cart']),
            'total' => $this->calculateTotal($_SESSION['cart'])
        ]);
    }

    public function getJson() {
        $cart = $_SESSION['cart'] ?? [];
        $this->json([
            'cart' => array_values($cart),
            'count' => $this->calculateCount($cart),
            'total' => $this->calculateTotal($cart)
        ]);
    }

    private function calculateTotal($cart) {
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return number_format($total, 2, '.', '');
    }

    private function calculateCount($cart) {
        $count = 0;
        foreach ($cart as $item) {
            $count += $item['quantity'];
        }
        return $count;
    }
}
