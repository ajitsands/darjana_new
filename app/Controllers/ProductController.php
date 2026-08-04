<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Models/Product.php';
require_once __DIR__ . '/../Models/Category.php';

class ProductController extends Controller {
    public function index($slug) {
        $productModel = new Product();
        $categoryModel = new Category();

        $sort = $_GET['sort'] ?? 'featured';
        $minPrice = $_GET['min_price'] ?? null;
        $maxPrice = $_GET['max_price'] ?? null;
        $page = max(1, (int)($_GET['page'] ?? 1));
        
        // 6 Rows per load batch (4 products per row x 6 rows = 24 items)
        $limit = (int)($_GET['limit'] ?? 24);
        $offset = ($page - 1) * $limit;

        $category = $categoryModel->getBySlug($slug);
        $totalProducts = $productModel->countByCategorySlug($slug, $minPrice, $maxPrice);
        $products = $productModel->getByCategorySlug($slug, $limit, $offset, $sort, $minPrice, $maxPrice);
        $categories = $categoryModel->getAll();

        $totalPages = ceil($totalProducts / $limit);
        $hasMore = $page < $totalPages;

        // Return JSON for AJAX Load More requests
        if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
            $this->json([
                'success' => true,
                'products' => $products,
                'page' => $page,
                'limit' => $limit,
                'totalProducts' => $totalProducts,
                'hasMore' => $hasMore,
                'loadedCount' => $offset + count($products)
            ]);
            return;
        }

        $data = [
            'pageTitle' => ($category ? $category['name'] : 'Collections') . ' | Dar Jana Fashion',
            'category' => $category,
            'products' => $products,
            'categories' => $categories,
            'currentSlug' => $slug,
            'currentSort' => $sort,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'productCount' => $totalProducts,
            'currentPage' => $page,
            'limit' => $limit,
            'totalPages' => $totalPages,
            'hasMore' => $hasMore,
            'loadedCount' => count($products)
        ];

        $this->render('products/index', $data);
    }

    public function detail($slug) {
        $productModel = new Product();
        $product = $productModel->getBySlug($slug);

        if (!$product) {
            $this->redirect(BASE_URL . '/collections/all-abaya');
        }

        $relatedProducts = array_filter(
            $productModel->getByCategorySlug($product['category_slug'], 12, 0),
            fn($p) => $p['id'] !== $product['id']
        );

        $data = [
            'pageTitle' => $product['name'] . ' | Dar Jana Fashion',
            'product' => $product,
            'relatedProducts' => $relatedProducts
        ];

        $this->render('products/detail', $data);
    }

    public function search() {
        $query = trim($_GET['q'] ?? '');
        $productModel = new Product();
        $products = [];

        if ($query !== '') {
            $products = $productModel->search($query);
        }

        if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
            $this->json(['products' => $products]);
        }

        $data = [
            'pageTitle' => 'Search Results for "' . htmlspecialchars($query) . '" | Dar Jana Fashion',
            'query' => $query,
            'products' => $products
        ];

        $this->render('products/search', $data);
    }
}
