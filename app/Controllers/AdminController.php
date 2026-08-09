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
            
            if (isset($_POST['share_click_dedup_minutes'])) {
                $settingModel->set('share_click_dedup_minutes', (int)$_POST['share_click_dedup_minutes']);
            }
            
            // Store Legal & Policy Contents
            if (isset($_POST['shipping_policy'])) {
                $settingModel->set('shipping_policy', $_POST['shipping_policy']);
            }
            if (isset($_POST['return_policy'])) {
                $settingModel->set('return_policy', $_POST['return_policy']);
            }
            if (isset($_POST['terms_conditions'])) {
                $settingModel->set('terms_conditions', $_POST['terms_conditions']);
            }
            if (isset($_POST['privacy_policy'])) {
                $settingModel->set('privacy_policy', $_POST['privacy_policy']);
            }

            // Top Announcement Bar & Homepage Promo Banner Settings
            if (isset($_POST['top_announcement_bar'])) {
                $settingModel->set('top_announcement_bar', trim($_POST['top_announcement_bar']));
            }
            if (isset($_POST['promo_tagline'])) {
                $settingModel->set('promo_tagline', trim($_POST['promo_tagline']));
            }
            if (isset($_POST['promo_title'])) {
                $settingModel->set('promo_title', trim($_POST['promo_title']));
            }
            if (isset($_POST['promo_desc'])) {
                $settingModel->set('promo_desc', trim($_POST['promo_desc']));
            }

            // Currency Exchange Rates (Relative to 1 BHD)
            $rateKeys = ['kwd', 'sar', 'aed', 'qar', 'omr', 'usd', 'eur'];
            foreach ($rateKeys as $rk) {
                if (isset($_POST['currency_rate_' . $rk])) {
                    $settingModel->set('currency_rate_' . $rk, (float)$_POST['currency_rate_' . $rk]);
                }
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
            if (isset($_POST['afs_charge_currency_mode'])) {
                $settingModel->set('afs_charge_currency_mode', trim($_POST['afs_charge_currency_mode']));
            }
            
            // Delete Homepage Hero Video Option
            if (isset($_POST['delete_home_hero_video']) && $_POST['delete_home_hero_video'] === '1') {
                $existingVideo = $settingModel->get('home_hero_video');
                if ($existingVideo) {
                    $oldPath = __DIR__ . '/../../public' . $existingVideo;
                    if (file_exists($oldPath) && is_file($oldPath)) {
                        @unlink($oldPath);
                    }
                    $settingModel->set('home_hero_video', '');
                }
            }

            // Homepage Hero Video Upload (Max 50MB limit)
            if (isset($_FILES['home_hero_video']) && $_FILES['home_hero_video']['error'] !== UPLOAD_ERR_NO_FILE) {
                $fileError = $_FILES['home_hero_video']['error'];
                if ($fileError === UPLOAD_ERR_INI_SIZE || $fileError === UPLOAD_ERR_FORM_SIZE) {
                    $_SESSION['admin_error'] = 'Video file size exceeds server upload limit.';
                } elseif ($fileError === UPLOAD_ERR_OK) {
                    $fileSize = $_FILES['home_hero_video']['size'];
                    $maxSize = 50 * 1024 * 1024; // 50 MB
                    if ($fileSize > $maxSize) {
                        $_SESSION['admin_error'] = 'Video file size must not exceed 50 MB (uploaded size: ' . round($fileSize / 1048576, 2) . ' MB).';
                    } else {
                        $ext = strtolower(pathinfo($_FILES['home_hero_video']['name'], PATHINFO_EXTENSION));
                        $allowedExts = ['mp4', 'webm', 'ogg', 'mov'];
                        if (!in_array($ext, $allowedExts)) {
                            $_SESSION['admin_error'] = 'Invalid video file format. Allowed formats: MP4, WebM, OGG, MOV.';
                        } else {
                            $uploadDir = __DIR__ . '/../../public/uploads/videos/';
                            if (!file_exists($uploadDir)) {
                                mkdir($uploadDir, 0777, true);
                            }

                            // Delete existing video file if present
                            $existingVideo = $settingModel->get('home_hero_video');
                            if (!empty($existingVideo)) {
                                $oldPath = __DIR__ . '/../../public' . $existingVideo;
                                if (file_exists($oldPath) && is_file($oldPath)) {
                                    @unlink($oldPath);
                                }
                            }

                            // Generate unique filename and save
                            $filename = 'home_hero_' . time() . '_' . uniqid() . '.' . $ext;
                            $targetPath = $uploadDir . $filename;
                            if (move_uploaded_file($_FILES['home_hero_video']['tmp_name'], $targetPath)) {
                                $relativeUrl = '/uploads/videos/' . $filename;
                                $settingModel->set('home_hero_video', $relativeUrl);
                            } else {
                                $_SESSION['admin_error'] = 'Failed to save uploaded video file to server.';
                            }
                        }
                    }
                } else {
                    $_SESSION['admin_error'] = 'An error occurred during video upload (Error Code: ' . $fileError . ').';
                }
            }

            $this->logActivity('UPDATE_SETTINGS', "Updated store settings (VAT: $vatPercentage%, Type: $vatType, TZ: $timezone)");
            if (!isset($_SESSION['admin_error'])) {
                $_SESSION['admin_success'] = 'Settings updated successfully.';
            }
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
        $orderModel  = new Order();
        $productModel = new Product();
        $db = Database::getInstance();

        // --- Date filter ---
        $startDate = $_GET['start_date'] ?? '';
        $endDate   = $_GET['end_date']   ?? '';

        // Fetch orders filtered by date if provided
        $orders = $orderModel->getAllOrders(
            $startDate ?: null,
            $endDate   ?: null,
            0   // 0 = no limit
        );

        // --- KPI Stats ---
        $totalRevenue = 0;
        $paidRevenue  = 0;
        $pendingCount = 0;
        $paidCount    = 0;
        $failedCount  = 0;
        foreach ($orders as $order) {
            $totalRevenue += (float)$order['total_amount'];
            $ps = strtolower($order['payment_status'] ?? 'pending');
            if ($ps === 'paid') {
                $paidRevenue += (float)$order['total_amount'];
                $paidCount++;
            } elseif ($ps === 'failed') {
                $failedCount++;
            } else {
                $pendingCount++;
            }
        }

        // --- Revenue chart: group by day if ≤31 days, else by month ---
        $chartData = [];
        if ($startDate && $endDate) {
            $diffDays = (int)((strtotime($endDate) - strtotime($startDate)) / 86400) + 1;
            if ($diffDays <= 31) {
                // Daily grouping within the range
                $d = new DateTime($startDate);
                $end = new DateTime($endDate);
                while ($d <= $end) {
                    $chartData[$d->format('Y-m-d')] = 0;
                    $d->modify('+1 day');
                }
                foreach ($orders as $o) {
                    $day = substr($o['created_at'] ?? '', 0, 10);
                    if (isset($chartData[$day])) $chartData[$day] += (float)$o['total_amount'];
                }
            } else {
                // Monthly grouping
                $d = new DateTime($startDate);
                $end = new DateTime($endDate);
                $d->modify('first day of this month');
                $end->modify('first day of next month');
                while ($d < $end) {
                    $chartData[$d->format('Y-m')] = 0;
                    $d->modify('+1 month');
                }
                foreach ($orders as $o) {
                    $month = substr($o['created_at'] ?? '', 0, 7);
                    if (isset($chartData[$month])) $chartData[$month] += (float)$o['total_amount'];
                }
            }
        } else {
            // Default: last 6 months
            for ($i = 5; $i >= 0; $i--) {
                $chartData[date('Y-m', strtotime("-$i months"))] = 0;
            }
            foreach ($orders as $o) {
                $month = substr($o['created_at'] ?? '', 0, 7);
                if (isset($chartData[$month])) $chartData[$month] += (float)$o['total_amount'];
            }
        }

        // --- Chart labels: format nicely ---
        $chartLabels = array_map(function($k) {
            if (strlen($k) === 10) return date('d M', strtotime($k));     // daily
            return date('M Y', strtotime($k . '-01'));                      // monthly
        }, array_keys($chartData));

        // --- Top 5 products filtered by date ---
        $topProducts = [];
        try {
            $tpSql = "SELECT oi.product_name, SUM(oi.quantity) as total_qty, SUM(oi.price * oi.quantity) as total_revenue
                      FROM order_items oi
                      JOIN orders o ON oi.order_id = o.id";
            $tpParams = [];
            if ($startDate && $endDate) {
                $tpSql .= " WHERE DATE(o.created_at) >= ? AND DATE(o.created_at) <= ?";
                $tpParams = [$startDate, $endDate];
            }
            $tpSql .= " GROUP BY oi.product_name ORDER BY total_qty DESC LIMIT 5";
            $stmt = $db->prepare($tpSql);
            $stmt->execute($tpParams);
            $topProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {}

        // --- Recent 5 orders (from filtered set) ---
        $recentOrders = array_slice($orders, 0, 5);

        // --- Total products (always all) ---
        $totalProductsCount = count($productModel->getAll());

        // --- Product View Analytics ---
        $totalProductViewsCount = $productModel->getTotalProductViewsCount($startDate ?: null, $endDate ?: null);
        $topViewedProducts = $productModel->getTopViewedProducts(5, $startDate ?: null, $endDate ?: null);

        $data = [
            'pageTitle'              => 'Admin Dashboard | Dar Jana Fashion',
            'totalRevenue'           => number_format($totalRevenue, 3, '.', ','),
            'paidRevenue'            => number_format($paidRevenue,  3, '.', ','),
            'totalOrdersCount'       => count($orders),
            'totalProductsCount'     => $totalProductsCount,
            'totalProductViewsCount' => $totalProductViewsCount,
            'topViewedProducts'      => $topViewedProducts,
            'pendingCount'           => $pendingCount,
            'paidCount'              => $paidCount,
            'failedCount'            => $failedCount,
            'chartLabels'            => $chartLabels,
            'chartData'              => array_values($chartData),
            'topProducts'            => $topProducts,
            'recentOrders'           => $recentOrders,
            'startDate'              => $startDate,
            'endDate'                => $endDate,
        ];

        $this->render('admin/dashboard', $data, 'admin');
    }

    public function productViewInsights() {
        $this->requireAuth();
        
        if (!isset($_GET['start_date']) && !isset($_GET['end_date']) && empty($_GET['all_time'])) {
            // Default to "This Week"
            $startDate = date('Y-m-d', strtotime('monday this week'));
            $endDate   = date('Y-m-d');
        } else {
            $startDate = $_GET['start_date'] ?? '';
            $endDate   = $_GET['end_date']   ?? '';
        }

        $productModel = new Product();
        $insights = $productModel->getDetailedProductViewInsights($startDate ?: null, $endDate ?: null);

        $data = [
            'pageTitle' => 'Product Detail View Insights & Analytics | Admin',
            'summary' => $insights['summary'],
            'productPerformance' => $insights['product_performance'],
            'locationPerformance' => $insights['location_performance'],
            'repeatIpReport' => $insights['repeat_ip_report'],
            'recentViews' => $insights['recent_views'],
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        $this->render('admin/product_view_insights', $data, 'admin');
    }

    public function products() {
        $this->requireAuth();
        $productModel = new Product();
        $categoryModel = new Category();

        $products = $productModel->getAll();
        $categories = $categoryModel->getAll();

        $data = [
            'pageTitle'  => 'Products | Admin Dashboard',
            'products'   => $products,
            'categories' => $categories,
            'totalProductsCount' => count($products),
        ];

        $this->render('admin/products', $data, 'admin');
    }


    public function orders() {
        $this->requireAuth();
        $orderModel = new Order();
        
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-20 days'));
        
        $orderStatus = $_GET['order_status'] ?? null;
        $paymentStatus = $_GET['payment_status'] ?? null;

        $orders = $orderModel->getAllOrders($startDate, $endDate, $orderStatus, $paymentStatus);

        $data = [
            'pageTitle' => 'Customer Orders | Dar Jana Fashion',
            'orders' => $orders,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'orderStatusFilter' => $orderStatus,
            'paymentStatusFilter' => $paymentStatus
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
        $assignments = $orderModel->getItemAssignments($id);

        require_once __DIR__ . '/../Models/TailoringUnit.php';
        $unitModel = new TailoringUnit();
        $activeUnits = $unitModel->getActiveUnits();

        $data = [
            'pageTitle' => 'Order Details | Dar Jana Fashion',
            'order' => $order,
            'items' => $items,
            'assignments' => $assignments,
            'activeUnits' => $activeUnits
        ];

        $this->render('admin/order_detail', $data, 'admin');
    }

    public function assignItem($id) {
        $this->requireAuth();
        $orderModel = new Order();
        $order = $orderModel->getOrderById($id);
        
        if (!$order) {
            $_SESSION['admin_error'] = 'Order not found.';
            $this->redirect(BASE_URL . '/admin/orders');
            return;
        }

        $orderItemId = (int)$_POST['order_item_id'];
        $unitId = (int)$_POST['tailoring_unit_id'];
        $quantity = (int)$_POST['quantity'];

        if ($quantity <= 0) {
            $_SESSION['admin_error'] = 'Quantity must be at least 1.';
            $this->redirect(BASE_URL . '/admin/order/' . $id);
            return;
        }

        require_once __DIR__ . '/../Models/TailoringUnit.php';
        $unitModel = new TailoringUnit();
        $unit = $unitModel->getUnitById($unitId);
        $unitCode = $unit ? $unit['unique_unit_code'] : 'U' . $unitId;

        // Generate process number: PR-{ORDER_ID}-{UNIT_CODE}-{RANDOM}
        $processNumber = 'PR-' . $order['order_number'] . '-' . strtoupper($unitCode) . '-' . rand(100, 999);

        $orderModel->addAssignment($id, $orderItemId, $unitId, $quantity, $processNumber);
        
        $_SESSION['admin_success'] = "Item successfully assigned.";
        $this->redirect(BASE_URL . '/admin/order/' . $id);
    }

    public function cancelOrderItem($id) {
        $this->requireAuth();
        $orderId = (int)($_GET['order_id'] ?? 0);
        $orderModel = new Order();
        
        if ($orderId > 0) {
            $assignments = $orderModel->getItemAssignments($orderId);
            $hasAssignments = false;
            foreach ($assignments as $a) {
                if ((int)$a['order_item_id'] === (int)$id) {
                    $hasAssignments = true;
                    break;
                }
            }
            
            if ($hasAssignments) {
                $_SESSION['admin_error'] = "Cannot cancel item. Remove assignments first.";
            } else {
                if ($orderModel->cancelOrderItem($id)) {
                    $_SESSION['admin_success'] = "Item cancelled successfully.";
                } else {
                    $_SESSION['admin_error'] = "Item could not be cancelled or is already cancelled.";
                }
            }
            $this->redirect(BASE_URL . '/admin/order/' . $orderId);
        } else {
            $this->redirect(BASE_URL . '/admin/orders');
        }
    }

    public function removeAssignment($id) {
        $this->requireAuth();
        $orderId = (int)($_GET['order_id'] ?? 0);
        $orderModel = new Order();
        
        if ($orderId > 0) {
            $orderModel->removeAssignment($id);
            $_SESSION['admin_success'] = "Assignment removed.";
            $this->redirect(BASE_URL . '/admin/order/' . $orderId);
        } else {
            $this->redirect(BASE_URL . '/admin/orders');
        }
    }

    public function printProcessRequests($id) {
        $this->requireAuth();
        $orderModel = new Order();
        $order = $orderModel->getOrderById($id);
        
        if (!$order) {
            die('Order not found');
        }

        $items = $orderModel->getOrderItems($id);
        $assignments = $orderModel->getItemAssignments($id);

        if (empty($assignments)) {
            die('No assignments found for this order.');
        }

        // Group assignments by Process Number (which is essentially by Unit and batch)
        $groupedRequests = [];
        foreach ($assignments as $assignment) {
            $prNumber = $assignment['process_number'];
            if (!isset($groupedRequests[$prNumber])) {
                $groupedRequests[$prNumber] = [
                    'process_number' => $prNumber,
                    'unit_name' => $assignment['unit_name'],
                    'items' => []
                ];
            }

            // Find item details
            $itemDetails = null;
            foreach ($items as $item) {
                if ($item['id'] == $assignment['order_item_id']) {
                    $itemDetails = $item;
                    break;
                }
            }

            if ($itemDetails) {
                $groupedRequests[$prNumber]['items'][] = [
                    'product_code' => $itemDetails['product_code'],
                    'product_name' => $itemDetails['product_name'],
                    'size' => $itemDetails['size'],
                    'color' => $itemDetails['color'],
                    'length' => $itemDetails['length'],
                    'note' => $itemDetails['note'],
                    'assigned_quantity' => $assignment['quantity']
                ];
            }
        }

        require_once __DIR__ . '/../Views/admin/print_process_requests.php';
    }

    public function printAssignmentSummary($id) {
        $this->requireAuth();
        $orderModel = new Order();
        $order = $orderModel->getOrderById($id);
        
        if (!$order) {
            die('Order not found');
        }

        $items = $orderModel->getOrderItems($id);
        $assignments = $orderModel->getItemAssignments($id);

        if (empty($assignments)) {
            die('No assignments found for this order.');
        }

        require_once __DIR__ . '/../Views/admin/print_assignment_summary.php';
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
        
        $trackingNumber = $_POST['tracking_number'] ?? '';
        $shippingProvider = $_POST['shipping_provider'] ?? '';
        $shippingAttachment = $order['shipping_attachment'] ?? '';

        if ($orderStatus === 'Shipped') {
            // Handle file upload
            if (isset($_FILES['shipping_attachment']) && $_FILES['shipping_attachment']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/shipping/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $ext = strtolower(pathinfo($_FILES['shipping_attachment']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'pdf', 'webp'];
                if (in_array($ext, $allowed)) {
                    $fileName = 'shipping_' . $order['order_number'] . '_' . time() . '.' . $ext;
                    if (move_uploaded_file($_FILES['shipping_attachment']['tmp_name'], $uploadDir . $fileName)) {
                        $shippingAttachment = $fileName;
                    }
                }
            }
        }

        $orderModel->updateOrderStatuses($id, $orderStatus, $paymentStatus, $trackingNumber, $shippingProvider, $shippingAttachment);
        
        $orderStatusChanged = ($order['status'] !== $orderStatus);
        $paymentStatusChanged = (($order['payment_status'] ?? 'Pending') !== $paymentStatus);
        
        $messages = [];
        if ($orderStatusChanged) {
            $messages[] = 'Order Status updated';
        }
        if ($paymentStatusChanged) {
            $messages[] = 'Payment Status updated';
        }
        
        if (empty($messages)) {
            $_SESSION['admin_success'] = 'Order details saved.';
        } else {
            $_SESSION['admin_success'] = implode(' and ', $messages) . ' successfully.';
        }
        $this->redirect(BASE_URL . '/admin/order/' . $id);
    }

    public function ajaxProducts() {
        $this->requireAuth();
        $productModel = new Product();
        $products = $productModel->getAll(null, 0, 'date_asc', null, null, false);
        $shareStats = $productModel->getShareStats();
        
        $data = [];
        foreach ($products as $p) {
            $tinyImg = str_replace('/uploads/products/high/', '/uploads/products/tiny/', $p['image']);
            $imageHtml = '<img src="' . htmlspecialchars($tinyImg) . '" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">';
            $activeBadge = (isset($p['is_active']) && $p['is_active'] == 0) ? ' <span style="color: #e53e3e; font-size: 10px; font-weight: 700;">(Inactive)</span>' : '';
            $isVerified = isset($p['is_verified']) ? (int)$p['is_verified'] : 1;
            $verifyBadge = ($isVerified === 1) 
                ? ' <span style="background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 4px; display: inline-flex; align-items: center; margin-left: 6px;">🟢 Published</span>'
                : ' <span style="background: #fefce8; color: #ca8a04; border: 1px solid #fef08a; font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 4px; display: inline-flex; align-items: center; margin-left: 6px;">🟡 Unverified Draft</span>';

            $infoHtml = '<div style="font-size: 11px; color: #c5a059; font-weight: 700;">' . htmlspecialchars($p['product_code']) . '</div>' .
                        '<div style="font-weight: 600; font-size: 13px;">' . htmlspecialchars($p['name']) . $activeBadge . $verifyBadge . '</div>';
            $categoryHtml = '<span style="font-size: 12px;">' . htmlspecialchars($p['category_name']) . '</span>';
            $tagHtml = '<span style="font-size: 11px; font-weight: 700; background: #e2e8f0; padding: 2px 6px; border-radius: 3px; text-transform: uppercase;">' . htmlspecialchars($p['offer_tag_type']) . '</span>';
            $priceHtml = '<span style="font-weight: 700; font-size: 13px;">' . number_format($p['price'], 2) . ' BHD</span>';

            $pStats = $shareStats[$p['id']] ?? [
                'total' => 0,
                'by_source' => [
                    'instagram' => 0,
                    'facebook' => 0,
                    'whatsapp' => 0,
                    'tiktok' => 0,
                    'youtube' => 0
                ],
                'locations' => []
            ];
            $totalShares = $pStats['total'];

            $productShareData = json_encode([
                'id' => $p['id'],
                'code' => $p['product_code'],
                'name' => $p['name'],
                'slug' => $p['slug'],
                'url' => BASE_URL . '/product/' . $p['slug'],
                'total' => $totalShares,
                'stats' => $pStats['by_source'],
                'locations' => $pStats['locations'] ?? []
            ], JSON_HEX_APOS | JSON_HEX_QUOT);

            $productOptionData = json_encode([
                'id' => $p['id'],
                'code' => $p['product_code'],
                'name' => $p['name'],
                'name_ar' => $p['name_ar'] ?? '',
                'slug' => $p['slug'],
                'price' => (float)$p['price'],
                'sale_price' => $p['sale_price'] ? (float)$p['sale_price'] : null,
                'image' => $p['image'],
                'secondary_image' => $p['secondary_image'] ?? '',
                'media' => json_decode($p['media'] ?? '[]', true) ?: [],
                'description' => $p['description'] ?? '',
                'description_ar' => $p['description_ar'] ?? '',
                'colors' => $p['colors'] ?? '',
                'sizes' => $p['sizes'] ?? '',
                'lengths' => $p['lengths'] ?? '',
                'is_verified' => $isVerified,
                'url' => BASE_URL . '/product/' . $p['slug']
            ], JSON_HEX_APOS | JSON_HEX_QUOT);

            $shareCountBadge = ($totalShares > 0) ? ' <span style="background: #ebf8ff; color: #2b6cb0; padding: 1px 6px; border-radius: 10px; font-size: 10px; font-weight: 700; border: 1px solid #bee3f8; margin-left: auto;">' . $totalShares . '</span>' : '';

            $actionsHtml = '<div class="action-dropdown" style="position: relative; display: inline-block;">' .
                           '<button type="button" onclick="toggleActionMenu(event, this)" style="background: #f1f5f9; border: 1px solid #cbd5e0; color: #1e293b; padding: 6px 12px; border-radius: 6px; font-weight: 700; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.15s ease;" onmouseover="this.style.background=\'#e2e8f0\'" onmouseout="this.style.background=\'#f1f5f9\'" title="Product Options & Actions">' .
                           '⚙️ <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>' .
                           '</button>' .
                           '<div class="action-menu-dropdown" style="display: none; position: absolute; right: 0; top: 100%; margin-top: 4px; background: #ffffff; border: 1px solid #cbd5e0; border-radius: 6px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); min-width: 170px; z-index: 99999; text-align: left; overflow: hidden;">' .
                           '<a href="javascript:void(0)" onclick=\'openProductOptionsModal(' . $productOptionData . '); closeAllActionMenus();\' style="display: flex; align-items: center; gap: 8px; padding: 10px 14px; color: #1e293b; font-size: 12px; font-weight: 600; text-decoration: none; border-bottom: 1px solid #f1f5f9;" onmouseover="this.style.background=\'#f8fafc\'" onmouseout="this.style.background=\'transparent\'">' .
                           '<span>⚙️</span> Verify &amp; Status' .
                           '</a>' .
                           '<a href="' . BASE_URL . '/admin/product/edit/' . $p['id'] . '" style="display: flex; align-items: center; gap: 8px; padding: 10px 14px; color: #2b6cb0; font-size: 12px; font-weight: 600; text-decoration: none; border-bottom: 1px solid #f1f5f9;" onmouseover="this.style.background=\'#ebf8ff\'" onmouseout="this.style.background=\'transparent\'">' .
                           '<span>✏️</span> Edit Product' .
                           '</a>' .
                           '<a href="javascript:void(0)" onclick=\'openShareModal(' . $productShareData . '); closeAllActionMenus();\' style="display: flex; align-items: center; gap: 8px; padding: 10px 14px; color: #319795; font-size: 12px; font-weight: 600; text-decoration: none; border-bottom: 1px solid #f1f5f9;" onmouseover="this.style.background=\'#e6fffa\'" onmouseout="this.style.background=\'transparent\'">' .
                           '<span>📤</span> Share Link' . $shareCountBadge .
                           '</a>' .
                           '<a href="' . BASE_URL . '/admin/product/delete/' . $p['id'] . '" onclick="confirmDelete(event, this.href, \'Delete this product?\')" style="display: flex; align-items: center; gap: 8px; padding: 10px 14px; color: #e53e3e; font-size: 12px; font-weight: 600; text-decoration: none;" onmouseover="this.style.background=\'#fff5f5\'" onmouseout="this.style.background=\'transparent\'">' .
                           '<span>🗑️</span> Delete Product' .
                           '</a>' .
                           '</div>' .
                           '</div>';
            
            $data[] = [
                'DT_RowId' => 'product-row-' . $p['id'],
                'DT_RowClass' => 'product-table-row',
                0 => $imageHtml,
                1 => $infoHtml,
                2 => $categoryHtml,
                3 => $tagHtml,
                4 => $priceHtml,
                5 => $actionsHtml
            ];
        }
        
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');
        echo json_encode(['data' => $data]);
        exit;
    }

    public function ajaxTogglePublishProduct() {
        $this->requireAuth();
        $id = (int)($_POST['id'] ?? 0);
        $status = (int)($_POST['status'] ?? 0);

        if ($id <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid Product ID']);
            exit;
        }

        $productModel = new Product();
        $productModel->togglePublishStatus($id, $status);

        $this->logActivity('TOGGLE_PUBLISH_PRODUCT', "Updated product #{$id} publish verification status to: " . ($status ? 'Published' : 'Unverified Draft'));

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'is_verified' => $status,
            'message' => $status ? 'Product verified and published to storefront portal!' : 'Product unpublished and hidden from storefront portal.'
        ]);
        exit;
    }

    public function clickInsights() {
        $this->requireAuth();
        $productModel = new Product();
        $insights = $productModel->getDetailedClickInsights();

        $data = [
            'pageTitle' => 'Click Insights & Performance | Admin',
            'insights' => $insights
        ];

        $this->render('admin/click_insights', $data, 'admin');
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
            'is_verified' => isset($_POST['is_verified']) ? 1 : 0,
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

    public function deleteProductMedia($id) {
        $this->requireAuth();
        $index = isset($_GET['index']) ? (int)$_GET['index'] : -1;
        
        require_once __DIR__ . '/../Models/Product.php';
        $productModel = new Product();
        $product = $productModel->getById($id);
        
        if ($product && $index >= 0) {
            $media = json_decode($product['media'] ?? '[]', true) ?: [];
            if (isset($media[$index])) {
                $item = $media[$index];
                
                // Physically delete the files
                if (!empty($item['url'])) {
                    // Extract path relative to the domain
                    $parsedUrl = parse_url($item['url']);
                    if (isset($parsedUrl['path'])) {
                        // Assuming the path starts with something like /uploads/...
                        // For localhost like /darjanafashon_new/public/uploads/... we need to map to public dir
                        $path = ltrim($parsedUrl['path'], '/');
                        $parts = explode('public/', $path);
                        if (count($parts) > 1) {
                            $physicalPath = __DIR__ . '/../../public/' . $parts[1];
                        } else {
                            // Fallback if public isn't in URL path (e.g. on production server)
                            // We look for 'uploads/' directly
                            $uploadParts = explode('uploads/', $path);
                            if (count($uploadParts) > 1) {
                                $physicalPath = __DIR__ . '/../../public/uploads/' . $uploadParts[1];
                            } else {
                                $physicalPath = false;
                            }
                        }
                        
                        if ($physicalPath && file_exists($physicalPath) && is_file($physicalPath)) {
                            unlink($physicalPath);
                        }
                    }
                }
                if (!empty($item['thumb'])) {
                    $parsedUrl = parse_url($item['thumb']);
                    if (isset($parsedUrl['path'])) {
                        $path = ltrim($parsedUrl['path'], '/');
                        $parts = explode('public/', $path);
                        if (count($parts) > 1) {
                            $physicalPath = __DIR__ . '/../../public/' . $parts[1];
                        } else {
                            $uploadParts = explode('uploads/', $path);
                            if (count($uploadParts) > 1) {
                                $physicalPath = __DIR__ . '/../../public/uploads/' . $uploadParts[1];
                            } else {
                                $physicalPath = false;
                            }
                        }
                        if ($physicalPath && file_exists($physicalPath) && is_file($physicalPath)) {
                            unlink($physicalPath);
                        }
                    }
                }
                
                // Remove from array and re-index
                array_splice($media, $index, 1);
                
                // Update Database
                $db = Database::getInstance();
                $stmt = $db->prepare("UPDATE products SET media = ? WHERE id = ?");
                $stmt->execute([json_encode($media), $id]);
                
                $this->logActivity('DELETE_PRODUCT_MEDIA', "Deleted media at index {$index} for product: {$product['product_code']}");
                $_SESSION['admin_success'] = "Media file deleted successfully.";
            }
        }
        
        $this->redirect(BASE_URL . "/admin/product/edit/{$id}");
    }

    public function addProduct() {
        $this->requireAuth();
        // Check if POST data is empty but content length > 0 (usually means post_max_size was exceeded)
        if (empty($_POST) && empty($_FILES) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > 0) {
            $this->redirect(BASE_URL . '/admin/products?error=file_too_large');
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
        }

        if (empty($name) || empty($code) || $price <= 0 || empty($image)) {
            if (isset($_POST['ajax'])) { header('Content-Type: application/json'); echo json_encode(['success' => false, 'error' => 'Please fill out all required fields.']); exit; }
            $this->redirect(BASE_URL . '/admin/products?error=missing_fields');
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
            'is_verified' => isset($_POST['is_verified']) ? 1 : 0,
            'stock' => (int)($_POST['stock'] ?? 50),
            'media' => json_encode($mediaArray)
        ]);

        $this->logActivity('ADD_PRODUCT', "Added new product: {$name} ({$code})");

        $this->redirect(BASE_URL . '/admin/products?success=1');
    }

    public function generateTinyThumbnails() {
        $this->requireAuth();
        
        // Prevent timeout and memory exhaustion on server
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '512M');
        
        try {
            $db = Database::getInstance();
            $products = $db->query("SELECT id, name, media FROM products")->fetchAll(PDO::FETCH_ASSOC);

            $uploadDir = __DIR__ . '/../../public/uploads/products/';
            $thumbDir = $uploadDir . 'thumb/';
            $highDir = $uploadDir . 'high/';
            $tinyDir = $uploadDir . 'tiny/';

            if (!is_dir($tinyDir)) {
                @mkdir($tinyDir, 0755, true);
            }

        $updatedCount = 0;
        $missingFilesCount = 0;

        foreach ($products as $product) {
            if (!$product['media']) continue;
            
            $media = json_decode($product['media'], true);
            if (!is_array($media)) continue;
            
            $changed = false;
            
            foreach ($media as &$item) {
                if ($item['type'] === 'image') {
                    if (!isset($item['tiny'])) {
                        // Extract filename from thumb URL
                        $thumbUrl = $item['thumb'];
                        $fileName = basename(parse_url($thumbUrl, PHP_URL_PATH));
                        
                        $sourcePath = $highDir . $fileName;
                        if (!file_exists($sourcePath)) {
                            $sourcePath = $thumbDir . $fileName;
                        }
                        
                        $tinyPath = $tinyDir . $fileName;
                        
                        if (file_exists($sourcePath) && !file_exists($tinyPath)) {
                            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                            $source = null;
                            if (in_array($ext, ['jpg', 'jpeg'])) {
                                $source = @imagecreatefromjpeg($sourcePath);
                            } elseif ($ext === 'png') {
                                $source = @imagecreatefrompng($sourcePath);
                            } elseif ($ext === 'webp') {
                                $source = @imagecreatefromwebp($sourcePath);
                            }
                            
                            if ($source) {
                                $width = imagesx($source);
                                $height = imagesy($source);
                                $tinyWidth = min($width, 150);
                                $tinyHeight = ($height / $width) * $tinyWidth;
                                $tinyImage = imagecreatetruecolor($tinyWidth, $tinyHeight);
                                imagefill($tinyImage, 0, 0, imagecolorallocate($tinyImage, 255, 255, 255));
                                imagecopyresampled($tinyImage, $source, 0, 0, 0, 0, $tinyWidth, $tinyHeight, $width, $height);
                                imagejpeg($tinyImage, $tinyPath, 75);
                                imagedestroy($tinyImage);
                                imagedestroy($source);
                                
                                $baseUrlStr = substr($thumbUrl, 0, strpos($thumbUrl, '/uploads/products/thumb/'));
                                $item['tiny'] = $baseUrlStr . '/uploads/products/tiny/' . $fileName;
                                $changed = true;
                            }
                        } elseif (file_exists($tinyPath) && !isset($item['tiny'])) {
                             $baseUrlStr = substr($thumbUrl, 0, strpos($thumbUrl, '/uploads/products/thumb/'));
                             $item['tiny'] = $baseUrlStr . '/uploads/products/tiny/' . $fileName;
                             $changed = true;
                        } else {
                            if (!file_exists($sourcePath)) {
                                $missingFilesCount++;
                            }
                        }
                    }
                }
            }
            
            if ($changed) {
                $newMediaJson = json_encode($media);
                $stmt = $db->prepare("UPDATE products SET media = ? WHERE id = ?");
                $stmt->execute([$newMediaJson, $product['id']]);
                $updatedCount++;
            }
        }

        echo "<h2>Optimization Complete</h2>";
        echo "Done! Generated and updated tiny thumbnails for <b>{$updatedCount}</b> products.<br><br>";
        if ($missingFilesCount > 0) {
            echo "<p style='color:red;'>Note: Skipped {$missingFilesCount} images because the original physical files were missing from the server (uploads/products/high/ or thumb/ directory).</p>";
        }
        echo "<br>You can safely close this page and return to the dashboard.";
        
        } catch (Exception $e) {
            echo "<h2>Error Occurred</h2>";
            echo "<p style='color:red;'>An error occurred during thumbnail generation: " . $e->getMessage() . "</p>";
            echo "<p>Please ensure the server has proper write permissions to the 'public/uploads/products/tiny' directory.</p>";
        }
    }

    public function optimizeImages() {
        $this->requireAuth();
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '512M');
        
        $uploadDir = __DIR__ . '/../../public/uploads/products/';
        $thumbDir = $uploadDir . 'thumb/';
        
        echo "<h2>Optimizing Thumb Images (Max 800px)</h2>";
        
        $optimizedCount = 0;
        
        if (is_dir($thumbDir)) {
            $files = scandir($thumbDir);
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') continue;
                $filePath = $thumbDir . $file;
                
                // Only process files larger than 500KB
                if (is_file($filePath) && filesize($filePath) > 500 * 1024) {
                    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                    $source = null;
                    
                    if (in_array($ext, ['jpg', 'jpeg'])) $source = @imagecreatefromjpeg($filePath);
                    elseif ($ext === 'png') $source = @imagecreatefrompng($filePath);
                    elseif ($ext === 'webp') $source = @imagecreatefromwebp($filePath);
                    
                    if ($source) {
                        $width = imagesx($source);
                        $height = imagesy($source);
                        
                        // Resize to max 800px
                        $newWidth = min($width, 800);
                        $newHeight = ($height / $width) * $newWidth;
                        
                        $newImage = imagecreatetruecolor($newWidth, $newHeight);
                        imagefill($newImage, 0, 0, imagecolorallocate($newImage, 255, 255, 255));
                        imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                        
                        // Overwrite original file
                        imagejpeg($newImage, $filePath, 80);
                        imagedestroy($newImage);
                        imagedestroy($source);
                        
                        $optimizedCount++;
                        echo "Optimized: {$file}<br>";
                    }
                }
            }
        }
        
        echo "<h3>Done! Optimized {$optimizedCount} large thumb images.</h3>";
        echo "<p>You can safely close this page.</p>";
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

        // Tiny Version (Max 150px width for small gallery icons)
        $tinyDir = $uploadDir . 'tiny/';
        if (!is_dir($tinyDir)) mkdir($tinyDir, 0755, true);
        $tinyPath = $tinyDir . $newName;
        $tinyWidth = min($width, 150);
        $tinyHeight = ($height / $width) * $tinyWidth;
        $tinyImage = imagecreatetruecolor($tinyWidth, $tinyHeight);
        imagefill($tinyImage, 0, 0, imagecolorallocate($tinyImage, 255, 255, 255));
        imagecopyresampled($tinyImage, $source, 0, 0, 0, 0, $tinyWidth, $tinyHeight, $width, $height);
        imagejpeg($tinyImage, $tinyPath, 75);
        imagedestroy($tinyImage);

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
            'tiny' => BASE_URL . '/uploads/products/tiny/' . $newName,
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

    public function toggleCategoryStatus($id) {
        $this->requireAuth();
        $db = Database::getInstance();
        // Get current status
        $stmt = $db->prepare("SELECT is_active FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $cat = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($cat) {
            $newStatus = ($cat['is_active'] ?? 1) ? 0 : 1;
            $stmt = $db->prepare("UPDATE categories SET is_active = ? WHERE id = ?");
            $stmt->execute([$newStatus, $id]);
            $this->logActivity('CATEGORY_TOGGLE', "Set category ID $id is_active = $newStatus");
        }
        $this->redirect(BASE_URL . '/admin/categories');
    }

    public function subscribers() {
        $this->requireAuth();
        require_once __DIR__ . '/../Models/Subscriber.php';
        require_once __DIR__ . '/../Models/Product.php';

        $subscriberModel = new Subscriber();
        $productModel = new Product();

        $subscribers = $subscriberModel->getAllSubscribers();
        $products = $productModel->getAll();
        $emailCampaignStats = $productModel->getEmailCampaignClickStats();

        $data = [
            'pageTitle' => 'Newsletter Subscribers & Promotional Campaigns | Admin',
            'subscribers' => $subscribers,
            'products' => $products,
            'emailCampaignStats' => $emailCampaignStats
        ];

        $this->render('admin/subscribers', $data, 'admin');
    }

    public function deleteSubscriber($id) {
        $this->requireAuth();
        require_once __DIR__ . '/../Models/Subscriber.php';
        $subscriberModel = new Subscriber();
        $subscriberModel->deleteSubscriber($id);
        $this->logActivity('SUBSCRIBER_DELETE', "Deleted subscriber ID: $id");
        $this->redirect(BASE_URL . '/admin/subscribers');
    }

    public function ajaxSendPromotionalEmail() {
        $this->requireAuth();
        require_once __DIR__ . '/../Models/Subscriber.php';
        require_once __DIR__ . '/../Models/Product.php';
        require_once __DIR__ . '/../../core/Mail.php';

        $productId = (int)($_POST['product_id'] ?? 0);
        $subject = trim($_POST['subject'] ?? '');
        $customMessage = trim($_POST['custom_message'] ?? '');
        $targetAudience = $_POST['target_audience'] ?? 'all';
        $selectedEmails = $_POST['selected_emails'] ?? [];

        if ($productId <= 0) {
            $this->json(['success' => false, 'message' => 'Please select a valid product for the promotional email.']);
            return;
        }

        $productModel = new Product();
        $product = $productModel->getById($productId);

        if (!$product) {
            $this->json(['success' => false, 'message' => 'Selected product not found in catalog.']);
            return;
        }

        $subscriberModel = new Subscriber();
        $targetEmails = [];

        if ($targetAudience === 'selected' && !empty($selectedEmails) && is_array($selectedEmails)) {
            $targetEmails = array_unique(array_filter(array_map('trim', $selectedEmails)));
        } else {
            $allSubs = $subscriberModel->getActiveSubscribers();
            $targetEmails = array_column($allSubs, 'email');
        }

        if (empty($targetEmails)) {
            $this->json(['success' => false, 'message' => 'No active subscribers selected for this campaign.']);
            return;
        }

        $sentCount = 0;
        $failedCount = 0;

        foreach ($targetEmails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $sent = Mail::sendPromotionalEmail($email, $subject, $product, $customMessage);
                if ($sent) {
                    $sentCount++;
                } else {
                    $failedCount++;
                }
            }
        }

        $this->logActivity('PROMO_EMAIL_CAMPAIGN', "Sent promo email for product '{$product['name']}' to {$sentCount} subscribers.");

        $this->json([
            'success' => true,
            'message' => "Campaign sent successfully! Total Delivered: {$sentCount} email(s)." . ($failedCount > 0 ? " ({$failedCount} failed)" : ""),
            'sent_count' => $sentCount,
            'failed_count' => $failedCount
        ]);
    }
}
