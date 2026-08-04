<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Models/Order.php';
require_once __DIR__ . '/../Models/Product.php';
require_once __DIR__ . '/../Models/Category.php';

class AdminController extends Controller {

    private function requireAuth() {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            $this->redirect(BASE_URL . '/admin/login');
        }
    }

    private function logActivity($actionType, $description) {
        if (isset($_SESSION['user_id'])) {
            $db = Database::getInstance();
            $stmt = $db->prepare("INSERT INTO activity_logs (user_id, action_type, description) VALUES (?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $actionType, $description]);
        }
    }

    public function login() {
        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
            $this->redirect(BASE_URL . '/admin');
        }
        $this->render('admin/login', ['pageTitle' => 'Admin Login'], 'admin');
    }

    public function processLogin() {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, username, password_hash FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $this->logActivity('LOGIN', "Admin {$user['username']} logged in.");
            $this->redirect(BASE_URL . '/admin');
        } else {
            $this->redirect(BASE_URL . '/admin/login?error=1');
        }
    }

    public function logout() {
        if (isset($_SESSION['username'])) {
            $this->logActivity('LOGOUT', "Admin {$_SESSION['username']} logged out.");
        }
        unset($_SESSION['admin_logged_in']);
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        $this->redirect(BASE_URL . '/admin/login');
    }

    public function users() {
        $this->requireAuth();
        $db = Database::getInstance();
        $users = $db->query("SELECT id, username, created_at FROM users ORDER BY created_at DESC")->fetchAll();
        $this->render('admin/users', ['pageTitle' => 'Manage Admins', 'users' => $users], 'admin');
    }

    public function addUser() {
        $this->requireAuth();
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (!empty($username) && !empty($password)) {
            $db = Database::getInstance();
            // Check if exists
            $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetchColumn() == 0) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
                $stmt->execute([$username, $hash]);
                $this->logActivity('ADD_USER', "Added new admin user: {$username}");
            }
        }
        $this->redirect(BASE_URL . '/admin/users');
    }

    public function history() {
        $this->requireAuth();
        $db = Database::getInstance();
        $logs = $db->query("
            SELECT a.*, u.username 
            FROM activity_logs a 
            LEFT JOIN users u ON a.user_id = u.id 
            ORDER BY a.created_at DESC 
            LIMIT 100
        ")->fetchAll();
        $this->render('admin/history', ['pageTitle' => 'Activity History', 'logs' => $logs], 'admin');
    }

    public function index() {
        $this->requireAuth();
        $orderModel = new Order();
        $productModel = new Product();
        $categoryModel = new Category();

        $orders = $orderModel->getAllOrders();
        $products = $productModel->getAll();
        $categories = $categoryModel->getAll();

        // Calculate sales metrics
        $totalRevenue = 0;
        foreach ($orders as $order) {
            $totalRevenue += (float)$order['total_amount'];
        }

        $data = [
            'pageTitle' => 'Admin Dashboard | Dar Jana Fashion',
            'orders' => $orders,
            'products' => $products,
            'categories' => $categories,
            'totalRevenue' => number_format($totalRevenue, 2, '.', ''),
            'totalOrdersCount' => count($orders),
            'totalProductsCount' => count($products)
        ];

        $this->render('admin/dashboard', $data, 'admin');
    }

    public function orders() {
        $this->requireAuth();
        $orderModel = new Order();
        
        $orders = $orderModel->getAllOrders();

        $data = [
            'pageTitle' => 'Customer Orders | Dar Jana Fashion',
            'orders' => $orders
        ];

        $this->render('admin/orders', $data, 'admin');
    }


    public function editProduct($id) {
        $this->requireAuth();
        $productModel = new Product();
        $categoryModel = new Category();

        $product = $productModel->getById($id);
        if (!$product) {
            $this->redirect(BASE_URL . '/admin');
        }

        $categories = $categoryModel->getAll();

        $data = [
            'pageTitle' => 'Edit Product | Dar Jana Fashion',
            'product' => $product,
            'categories' => $categories
        ];

        $this->render('admin/product_edit', $data, 'admin');
    }

    public function updateProduct($id) {
        $this->requireAuth();
        
        $productModel = new Product();
        $existingProduct = $productModel->getById($id);
        if (!$existingProduct) {
            if (isset($_POST['ajax'])) { header('Content-Type: application/json'); echo json_encode(['success' => false, 'error' => 'Product not found']); exit; }
            $this->redirect(BASE_URL . '/admin?error=notfound');
        }

        $name = trim($_POST['name'] ?? '');
        $code = trim($_POST['product_code'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 1);
        $price = (float)($_POST['price'] ?? 0);
        $salePrice = !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null;
        $offerTagType = $_POST['offer_tag_type'] ?? 'percentage';
        $colors = trim($_POST['colors'] ?? 'Black, Red, Green & Red, Blue & Gray');
        $sizes = trim($_POST['sizes'] ?? 'S, M, L, XL, XXL');
        $lengths = trim($_POST['lengths'] ?? '52, 54, 55, 56, 57, 58, 60');
        $image = trim($_POST['image'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $descriptionAr = trim($_POST['description_ar'] ?? '');

        // Handle Primary Image Upload
        if (isset($_FILES['primary_image_file']) && $_FILES['primary_image_file']['error'] === UPLOAD_ERR_OK) {
            $processedImage = $this->processImageUpload($_FILES['primary_image_file']['tmp_name'], $_FILES['primary_image_file']['name']);
            if ($processedImage) {
                $image = $processedImage['high'];
            }
        } else if (empty($image) && !empty($existingProduct['image'])) {
            $image = $existingProduct['image'];
        }

        if (empty($name) || empty($code) || $price <= 0 || empty($image)) {
            if (isset($_POST['ajax'])) { header('Content-Type: application/json'); echo json_encode(['success' => false, 'error' => 'Please fill out all required fields.']); exit; }
            $this->redirect(BASE_URL . '/admin/product/edit/' . $id . '?error=1');
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-')) . '-' . rand(100, 999);
        
        $mediaArray = json_decode($existingProduct['media'], true) ?: [];

        // 1. Process Video Upload (Max 5.5MB limit enforced)
        if (isset($_FILES['product_video']) && $_FILES['product_video']['error'] === UPLOAD_ERR_OK) {
            $videoSize = $_FILES['product_video']['size'];
            if ($videoSize <= 5767168) {
                $ext = strtolower(pathinfo($_FILES['product_video']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['mp4', 'webm'])) {
                    $videoName = uniqid() . '-' . time() . '.' . $ext;
                    $videoPath = __DIR__ . '/../../public/uploads/products/video/' . $videoName;
                    if (move_uploaded_file($_FILES['product_video']['tmp_name'], $videoPath)) {
                        $mediaArray[] = [
                            'type' => 'video',
                            'url' => BASE_URL . '/uploads/products/video/' . $videoName
                        ];
                    }
                }
            }
        }

        // 2. Process Gallery Images Upload
        if (isset($_FILES['gallery_images']) && is_array($_FILES['gallery_images']['tmp_name'])) {
            foreach ($_FILES['gallery_images']['tmp_name'] as $index => $tmpName) {
                if ($_FILES['gallery_images']['error'][$index] === UPLOAD_ERR_OK) {
                    $fileName = $_FILES['gallery_images']['name'][$index];
                    $processedImage = $this->processImageUpload($tmpName, $fileName);
                    if ($processedImage) {
                        $mediaArray[] = $processedImage;
                    }
                }
            }
        }

        $productModel->update($id, [
            'category_id' => $categoryId,
            'product_code' => $code,
            'name' => $name,
            'slug' => $slug,
            'price' => $price,
            'sale_price' => $salePrice,
            'image' => $image,
            'secondary_image' => $image,
            'description' => $description,
            'description_ar' => $descriptionAr,
            'offer_tag_type' => $offerTagType,
            'colors' => $colors,
            'sizes' => $sizes,
            'lengths' => $lengths,
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'stock' => (int)($_POST['stock'] ?? 50),
            'media' => json_encode($mediaArray)
        ]);

        $this->logActivity('UPDATE_PRODUCT', "Updated product: {$name} ({$code})");

        if (isset($_POST['ajax'])) { header('Content-Type: application/json'); echo json_encode(['success' => true, 'message' => 'Product updated successfully!']); exit; }
        $this->redirect(BASE_URL . '/admin?success=updated');
    }

    public function deleteProduct($id) {
        $this->requireAuth();
        $productModel = new Product();
        $product = $productModel->getById($id);
        if ($product) {
            $productModel->delete($id);
            $this->logActivity('DELETE_PRODUCT', "Deleted product: {$product['name']} ({$product['product_code']})");
        }
        $this->redirect(BASE_URL . '/admin?success=deleted');
    }

    public function addProduct() {
        $this->requireAuth();
        $name = trim($_POST['name'] ?? '');
        $code = trim($_POST['product_code'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 1);
        $price = (float)($_POST['price'] ?? 0);
        $salePrice = !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null;
        $offerTagType = $_POST['offer_tag_type'] ?? 'percentage';
        $colors = trim($_POST['colors'] ?? 'Black, Red, Green & Red, Blue & Gray');
        $sizes = trim($_POST['sizes'] ?? 'S, M, L, XL, XXL');
        $lengths = trim($_POST['lengths'] ?? '52, 54, 55, 56, 57, 58, 60');
        $image = trim($_POST['image'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $descriptionAr = trim($_POST['description_ar'] ?? '');

        // Handle Primary Image Upload
        if (isset($_FILES['primary_image_file']) && $_FILES['primary_image_file']['error'] === UPLOAD_ERR_OK) {
            $processedImage = $this->processImageUpload($_FILES['primary_image_file']['tmp_name'], $_FILES['primary_image_file']['name']);
            if ($processedImage) {
                $image = $processedImage['high'];
            }
        }

        if (empty($name) || empty($code) || $price <= 0 || empty($image)) {
            $this->redirect(BASE_URL . '/admin?error=1');
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-')) . '-' . rand(100, 999);
        
        $mediaArray = [];

        // 1. Process Video Upload (Max 5.5MB limit enforced)
        if (isset($_FILES['product_video']) && $_FILES['product_video']['error'] === UPLOAD_ERR_OK) {
            $videoSize = $_FILES['product_video']['size'];
            // 5.5 MB = 5.5 * 1024 * 1024 = 5767168 bytes
            if ($videoSize <= 5767168) {
                $ext = strtolower(pathinfo($_FILES['product_video']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['mp4', 'webm'])) {
                    $videoName = uniqid() . '-' . time() . '.' . $ext;
                    $videoPath = __DIR__ . '/../../public/uploads/products/video/' . $videoName;
                    if (move_uploaded_file($_FILES['product_video']['tmp_name'], $videoPath)) {
                        $mediaArray[] = [
                            'type' => 'video',
                            'url' => BASE_URL . '/uploads/products/video/' . $videoName
                        ];
                    }
                }
            }
        }

        // 2. Process Gallery Images Upload
        if (isset($_FILES['gallery_images']) && is_array($_FILES['gallery_images']['tmp_name'])) {
            foreach ($_FILES['gallery_images']['tmp_name'] as $index => $tmpName) {
                if ($_FILES['gallery_images']['error'][$index] === UPLOAD_ERR_OK) {
                    $fileName = $_FILES['gallery_images']['name'][$index];
                    $processedImage = $this->processImageUpload($tmpName, $fileName);
                    if ($processedImage) {
                        $mediaArray[] = $processedImage;
                    }
                }
            }
        }

        $productModel = new Product();
        $productModel->create([
            'category_id' => $categoryId,
            'product_code' => $code,
            'name' => $name,
            'slug' => $slug,
            'price' => $price,
            'sale_price' => $salePrice,
            'image' => $image,
            'secondary_image' => $image,
            'description' => $description,
            'description_ar' => $descriptionAr,
            'offer_tag_type' => $offerTagType,
            'colors' => $colors,
            'sizes' => $sizes,
            'lengths' => $lengths,
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'stock' => (int)($_POST['stock'] ?? 50),
            'media' => json_encode($mediaArray)
        ]);

        $this->logActivity('ADD_PRODUCT', "Added new product: {$name} ({$code})");

        $this->redirect(BASE_URL . '/admin?success=1');
    }

    private function processImageUpload($tmpName, $fileName) {
        $uploadDir = __DIR__ . '/../../public/uploads/products/';
        $thumbDir = $uploadDir . 'thumb/';
        $highDir = $uploadDir . 'high/';
        
        if (!is_dir($thumbDir)) mkdir($thumbDir, 0755, true);
        if (!is_dir($highDir)) mkdir($highDir, 0755, true);
        
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $newName = uniqid() . '-' . time() . '.jpg';
        
        $thumbPath = $thumbDir . $newName;
        $highPath = $highDir . $newName;

        $source = null;
        if (in_array($ext, ['jpg', 'jpeg'])) {
            $source = @imagecreatefromjpeg($tmpName);
        } elseif ($ext === 'png') {
            $source = @imagecreatefrompng($tmpName);
        } elseif ($ext === 'webp') {
            $source = @imagecreatefromwebp($tmpName);
        }

        if (!$source) return false;

        $width = imagesx($source);
        $height = imagesy($source);

        // Thumb Version (Max 800px width)
        $thumbWidth = min($width, 800);
        $thumbHeight = ($height / $width) * $thumbWidth;
        $thumbImage = imagecreatetruecolor($thumbWidth, $thumbHeight);
        imagefill($thumbImage, 0, 0, imagecolorallocate($thumbImage, 255, 255, 255));
        imagecopyresampled($thumbImage, $source, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);
        imagejpeg($thumbImage, $thumbPath, 80);
        imagedestroy($thumbImage);

        // High-Res Zoom Version (Max 2000px width)
        $highWidth = min($width, 2000);
        $highHeight = ($height / $width) * $highWidth;
        $highImage = imagecreatetruecolor($highWidth, $highHeight);
        imagefill($highImage, 0, 0, imagecolorallocate($highImage, 255, 255, 255));
        imagecopyresampled($highImage, $source, 0, 0, 0, 0, $highWidth, $highHeight, $width, $height);
        imagejpeg($highImage, $highPath, 85);
        imagedestroy($highImage);

        imagedestroy($source);

        return [
            'type' => 'image',
            'thumb' => BASE_URL . '/uploads/products/thumb/' . $newName,
            'high' => BASE_URL . '/uploads/products/high/' . $newName
        ];
    }

    public function deleteOrder($id) {
        $this->requireAuth();
        $orderModel = new Order();
        // Log before deleting to get details
        $this->logActivity('DELETE_ORDER', "Deleted order ID: {$id}");
        $orderModel->deleteOrder((int)$id);
        $this->redirect(BASE_URL . '/admin');
    }
}
