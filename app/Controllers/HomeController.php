<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Models/Product.php';
require_once __DIR__ . '/../Models/Category.php';
require_once __DIR__ . '/../Models/Subscriber.php';

class HomeController extends Controller {
    public function index() {
        $productModel = new Product();
        $categoryModel = new Category();

        $featuredProducts = $productModel->getFeatured();
        $allCategories = $categoryModel->getAll();
        $recentProducts = $productModel->getAll(8);

        $data = [
            'pageTitle' => 'Dar Jana Fashion - Luxury Dresses & Abayas Couture',
            'featuredProducts' => $featuredProducts,
            'categories' => $allCategories,
            'recentProducts' => $recentProducts
        ];

        $this->render('home/index', $data);
    }

    public function subscribe() {
        $email = trim($_POST['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(['success' => false, 'message' => 'Please enter a valid email address.']);
        }

        $subscriberModel = new Subscriber();
        $subscriberModel->subscribe($email);

        $this->json(['success' => true, 'message' => 'Thank you for subscribing to Dar Jana Fashion newsletter!']);
    }
}
