<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Models/Order.php';
require_once __DIR__ . '/../Models/Product.php';
require_once __DIR__ . '/../Models/Category.php';
require_once __DIR__ . '/../Models/Setting.php';

class AdminController extends Controller {

    public function settings() {
        $this->requireAuth();
        $settingModel = new Setting();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $vatPercentage = $_POST['vat_percentage'] ?? '0';
            $vatType = $_POST['vat_type'] ?? 'exclusive';
            $timezone = $_POST['timezone'] ?? 'Asia/Bahrain';
            
            $settingModel->set('vat_percentage', $vatPercentage);
            $settingModel->set('vat_type', $vatType);
            $settingModel->set('timezone', $timezone);
            
            if (isset($_POST['size_guide_chest'])) {
                $settingModel->set('size_guide_chest', $_POST['size_guide_chest']);
            }
            if (isset($_POST['size_guide_length'])) {
                $settingModel->set('size_guide_length', $_POST['size_guide_length']);
            }
            if (isset($_POST['size_guide_desc_en'])) {
                $settingModel->set('size_guide_desc_en', $_POST['size_guide_desc_en']);
            }
            if (isset($_POST['size_guide_desc_ar'])) {
                $settingModel->set('size_guide_desc_ar', $_POST['size_guide_desc_ar']);
            }
            
            // Payment Gateway Settings
            $afsEnabled = isset($_POST['afs_gateway_enabled']) ? '1' : '0';
            $settingModel->set('afs_gateway_enabled', $afsEnabled);
            
            if (isset($_POST['afs_gateway_name'])) {
                $settingModel->set('afs_gateway_name', $_POST['afs_gateway_name']);
            }
            if (isset($_POST['afs_api_endpoint'])) {
                $settingModel->set('afs_api_endpoint', rtrim($_POST['afs_api_endpoint'], '/'));
            }
            if (isset($_POST['afs_entity_id'])) {
                $settingModel->set('afs_entity_id', trim($_POST['afs_entity_id']));
            }
            if (isset($_POST['afs_access_token'])) {
                $settingModel->set('afs_access_token', trim($_POST['afs_access_token']));
            }
            if (isset($_POST['afs_currency'])) {
                $settingModel->set('afs_currency', strtoupper(trim($_POST['afs_currency'])));
            }
            
            $this->logActivity('UPDATE_SETTINGS', "Updated store settings (VAT: $vatPercentage%, Type: $vatType, TZ: $timezone)");
            $_SESSION['admin_success'] = 'Settings updated successfully.';
            $this->redirect(BASE_URL . '/admin/settings');
        }
        
        $settings = $settingModel->getAll();
        
        $data = [
            'pageTitle' => 'Store Settings | Admin',
            'settings' => $settings
        ];
        
        $this->render('admin/settings', $data, 'admin');
    }

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
        
        if (empty($username) || empty($password)) {
            $_SESSION['admin_error'] = "Please provide both username and password.";
            $this->redirect(BASE_URL . '/admin/users');
            return;
        }

        if (strlen($username) < 3) {
            $_SESSION['admin_error'] = "Username must be at least 3 characters long.";
            $this->redirect(BASE_URL . '/admin/users');
            return;
        }

        if (strlen($password) < 6) {
            $_SESSION['admin_error'] = "Password must be at least 6 characters long.";
            $this->redirect(BASE_URL . '/admin/users');
            return;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetchColumn() > 0) {
            $_SESSION['admin_error'] = "Username '{$username}' already exists. Please choose a different username.";
            $this->redirect(BASE_URL . '/admin/users');
            return;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
        $stmt->execute([$username, $hash]);
        $this->logActivity('ADD_USER', "Added new admin user: {$username}");

        $_SESSION['admin_success'] = "Administrator '{$username}' created successfully.";
        $this->redirect(BASE_URL . '/admin/users');
    }

    public function updateUser($id) {
        $this->requireAuth();
        $id = (int)$id;
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username)) {
            $_SESSION['admin_error'] = "Username cannot be empty.";
            $this->redirect(BASE_URL . '/admin/users');
            return;
        }

        if (strlen($username) < 3) {
            $_SESSION['admin_error'] = "Username must be at least 3 characters long.";
            $this->redirect(BASE_URL . '/admin/users');
            return;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        if (!$user) {
            $_SESSION['admin_error'] = "Administrator account not found.";
            $this->redirect(BASE_URL . '/admin/users');
            return;
        }

        // Check if new username conflicts with another user
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = ? AND id != ?");
        $stmt->execute([$username, $id]);
        if ($stmt->fetchColumn() > 0) {
            $_SESSION['admin_error'] = "Username '{$username}' is already in use by another administrator.";
            $this->redirect(BASE_URL . '/admin/users');
            return;
        }

        if (!empty($password)) {
            if (strlen($password) < 6) {
                $_SESSION['admin_error'] = "New password must be at least 6 characters long.";
                $this->redirect(BASE_URL . '/admin/users');
                return;
            }
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET username = ?, password_hash = ? WHERE id = ?");
            $stmt->execute([$username, $hash, $id]);
            $this->logActivity('UPDATE_USER', "Updated admin username and password for: {$user['username']} -> {$username}");
        } else {
            $stmt = $db->prepare("UPDATE users SET username = ? WHERE id = ?");
            $stmt->execute([$username, $id]);
            $this->logActivity('UPDATE_USER', "Updated admin username for: {$user['username']} -> {$username}");
        }

        // If editing self, update current session username
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $id) {
            $_SESSION['username'] = $username;
        }

        $_SESSION['admin_success'] = "Administrator '{$username}' updated successfully.";
        $this->redirect(BASE_URL . '/admin/users');
    }

    public function resetPassword($id) {
        $this->requireAuth();
        $id = (int)$id;
        $password = $_POST['new_password'] ?? '';

        if (empty($password)) {
            $_SESSION['admin_error'] = "Please enter a new password.";
            $this->redirect(BASE_URL . '/admin/users');
            return;
        }

        if (strlen($password) < 6) {
            $_SESSION['admin_error'] = "Password must be at least 6 characters long.";
            $this->redirect(BASE_URL . '/admin/users');
            return;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        if (!$user) {
            $_SESSION['admin_error'] = "Administrator account not found.";
            $this->redirect(BASE_URL . '/admin/users');
            return;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmt->execute([$hash, $id]);
        $this->logActivity('RESET_PASSWORD', "Reset password for administrator: {$user['username']}");

        $_SESSION['admin_success'] = "Password reset successfully for '{$user['username']}'.";
        $this->redirect(BASE_URL . '/admin/users');
    }

    public function deleteUser($id) {
        $this->requireAuth();
        $id = (int)$id;

        // Prevent deleting own logged in account
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $id) {
            $_SESSION['admin_error'] = "You cannot delete your own active administrator account.";
            $this->redirect(BASE_URL . '/admin/users');
            return;
        }

        $db = Database::getInstance();
        
        // Count total admins to ensure at least one remains
        $count = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
        if ($count <= 1) {
            $_SESSION['admin_error'] = "Cannot delete the last remaining administrator account.";
            $this->redirect(BASE_URL . '/admin/users');
            return;
        }

        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        if (!$user) {
            $_SESSION['admin_error'] = "Administrator account not found.";
            $this->redirect(BASE_URL . '/admin/users');
            return;
        }

        // Unlink foreign key in activity logs to preserve logs safely
        $stmt = $db->prepare("UPDATE activity_logs SET user_id = NULL WHERE user_id = ?");
        $stmt->execute([$id]);

        // Delete user
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);

        $this->logActivity('DELETE_USER', "Deleted administrator account: {$user['username']}");

        $_SESSION['admin_success'] = "Administrator '{$user['username']}' deleted successfully.";
        $this->redirect(BASE_URL . '/admin/users');
    }

    public function history() {
        $this->requireAuth();
        $db = Database::getInstance();
        
        $startDate = $_GET['start_date'] ?? '';
        $endDate = $_GET['end_date'] ?? '';
        
        $query = "
            SELECT a.*, u.username 
            FROM activity_logs a 
            LEFT JOIN users u ON a.user_id = u.id 
            WHERE 1=1
        ";
        $params = [];
        
        if (!empty($startDate)) {
            $query .= " AND DATE(a.created_at) >= ?";
            $params[] = $startDate;
        }
        
        if (!empty($endDate)) {
            $query .= " AND DATE(a.created_at) <= ?";
            $params[] = $endDate;
        }
        
        $query .= " ORDER BY a.created_at DESC";
        
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $logs = $stmt->fetchAll();
        
        $this->render('admin/history', [
            'pageTitle' => 'Activity History', 
            'logs' => $logs,
            'startDate' => $startDate,
            'endDate' => $endDate
        ], 'admin');
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
        
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;

        $orders = $orderModel->getAllOrders($startDate, $endDate);

        $data = [
            'pageTitle' => 'Customer Orders | Dar Jana Fashion',
            'orders' => $orders,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        $this->render('admin/orders', $data, 'admin');
    }

    public function orderDetail($id) {
        $this->requireAuth();
        $orderModel = new Order();
        
        $order = $orderModel->getOrderById($id);
        if (!$order) {
            $_SESSION['admin_error'] = 'Order not found.';
            $this->redirect(BASE_URL . '/admin/orders');
        }

        $items = $orderModel->getOrderItems($id);

        $data = [
            'pageTitle' => 'Order Details | Dar Jana Fashion',
            'order' => $order,
            'items' => $items
        ];

        $this->render('admin/order_detail', $data, 'admin');
    }

    public function updateStatus($id) {
        $this->requireAuth();
        $orderModel = new Order();
        
        $order = $orderModel->getOrderById($id);
        if (!$order) {
            $_SESSION['admin_error'] = 'Order not found.';
            $this->redirect(BASE_URL . '/admin/orders');
        }

        $orderStatus = $_POST['status'] ?? 'New';
        $paymentStatus = $_POST['payment_status'] ?? 'Pending';

        $orderModel->updateOrderStatuses($id, $orderStatus, $paymentStatus);
        
        $_SESSION['admin_success'] = 'Order statuses updated successfully.';
        $this->redirect(BASE_URL . '/admin/order/' . $id);
    }

    public function ajaxProducts() {
        $this->requireAuth();
        $productModel = new Product();
        $products = $productModel->getAll(null, 0, 'featured', null, null, false);
        
        $data = [];
        foreach ($products as $p) {
            $imageHtml = '<img src="' . htmlspecialchars($p['image']) . '" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">';
            $activeBadge = (isset($p['is_active']) && $p['is_active'] == 0) ? ' <span style="color: #e53e3e; font-size: 10px; font-weight: 700;">(Inactive)</span>' : '';
            $infoHtml = '<div style="font-size: 11px; color: #c5a059; font-weight: 700;">' . htmlspecialchars($p['product_code']) . '</div>' .
                        '<div style="font-weight: 600; font-size: 13px;">' . htmlspecialchars($p['name']) . $activeBadge . '</div>';
            $categoryHtml = '<span style="font-size: 12px;">' . htmlspecialchars($p['category_name']) . '</span>';
            $tagHtml = '<span style="font-size: 11px; font-weight: 700; background: #e2e8f0; padding: 2px 6px; border-radius: 3px; text-transform: uppercase;">' . htmlspecialchars($p['offer_tag_type']) . '</span>';
            $priceHtml = '<span style="font-weight: 700; font-size: 13px;">' . number_format($p['price'], 2) . ' BHD</span>';
            $actionsHtml = '<a href="' . BASE_URL . '/admin/product/edit/' . $p['id'] . '" style="color: #181818; font-size: 12px; font-weight: 600; margin-right: 8px;">Edit</a>' .
                           '<a href="' . BASE_URL . '/admin/product/delete/' . $p['id'] . '" onclick="confirmDelete(event, this.href, \'Delete this product?\')" style="color: #e53e3e; font-size: 12px; font-weight: 600;">Delete</a>';
            
            $data[] = [
                $imageHtml,
                $infoHtml,
                $categoryHtml,
                $tagHtml,
                $priceHtml,
                $actionsHtml
            ];
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            "data" => $data
        ]);
        exit;
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
        $nameAr = trim($_POST['name_ar'] ?? '');
        $code = trim($_POST['product_code'] ?? '');
                $categoryIds = $_POST['category_id'] ?? [1];
        if (!is_array($categoryIds)) $categoryIds = [$categoryIds];
        $categoryId = implode(',', array_map('intval', $categoryIds));
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
            'name_ar' => $nameAr,
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
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
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
        $nameAr = trim($_POST['name_ar'] ?? '');
        $code = trim($_POST['product_code'] ?? '');
                $categoryIds = $_POST['category_id'] ?? [1];
        if (!is_array($categoryIds)) $categoryIds = [$categoryIds];
        $categoryId = implode(',', array_map('intval', $categoryIds));
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
            'name_ar' => $nameAr,
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
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
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

    public function categories() {
        $this->requireAuth();
        $categoryModel = new Category();
        $categories = $categoryModel->getAll();
        
        $data = [
            'pageTitle' => 'Manage Categories | Admin Dashboard',
            'categories' => $categories
        ];
        
        $this->render('admin/categories', $data, 'admin');
    }

    public function addCategory() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            if(empty($slug)) {
                $slug = strtolower(str_replace(' ', '-', $name));
                $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
            }
            $description = trim($_POST['description'] ?? '');
            $imagePath = '';
            
            // Image upload or URL logic
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/assets/images/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                
                $fileName = time() . '_' . basename($_FILES['image_file']['name']);
                $targetFile = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['image_file']['tmp_name'], $targetFile)) {
                    $imagePath = BASE_URL . '/assets/images/' . $fileName;
                }
            } else if (!empty($_POST['image'])) {
                $imagePath = $_POST['image'];
            }
            
            if ($name && $slug && $imagePath) {
                $db = Database::getInstance();
                $stmt = $db->prepare("INSERT INTO categories (name, slug, description, image) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $slug, $description, $imagePath]);
                $this->logActivity('CATEGORY_ADD', "Added category: $name");
            }
        }
        $this->redirect(BASE_URL . '/admin/categories');
    }

    public function editCategory($id) {
        $this->requireAuth();
        $categoryModel = new Category();
        $category = null;
        
        // Find category by ID since there's no getById in Category.php, we'll fetch manually
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$category) {
            $this->redirect(BASE_URL . '/admin/categories');
        }

        $data = [
            'pageTitle' => 'Edit Category - ' . $category['name'],
            'category' => $category
        ];
        
        $this->render('admin/category_edit', $data, 'admin');
    }

    public function updateCategory($id) {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = Database::getInstance();
            $stmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($category) {
                $name = trim($_POST['name'] ?? $category['name']);
                $slug = trim($_POST['slug'] ?? $category['slug']);
                $description = trim($_POST['description'] ?? $category['description']);
                $imagePath = $category['image'];
                
                if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = __DIR__ . '/../../public/assets/images/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                    
                    $fileName = time() . '_' . basename($_FILES['image_file']['name']);
                    $targetFile = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['image_file']['tmp_name'], $targetFile)) {
                        $imagePath = BASE_URL . '/assets/images/' . $fileName;
                    }
                } else if (!empty($_POST['image'])) {
                    $imagePath = $_POST['image'];
                }
                
                $stmt = $db->prepare("UPDATE categories SET name = ?, slug = ?, description = ?, image = ? WHERE id = ?");
                $stmt->execute([$name, $slug, $description, $imagePath, $id]);
                $this->logActivity('CATEGORY_EDIT', "Updated category: $name");
            }
        }
        $this->redirect(BASE_URL . '/admin/categories');
    }
}
