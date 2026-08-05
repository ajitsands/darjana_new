<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Models/Coupon.php';

class AdminCouponController extends Controller {
    public function index() {
        if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $couponModel = new Coupon();
        $coupons = $couponModel->getAllCoupons();

        $data = [
            'pageTitle' => 'Manage Coupons | Dar Jana Fashion Admin',
            'coupons' => $coupons
        ];

        $this->render('admin/coupons', $data, 'admin');
    }

    public function create() {
        if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $couponModel = new Coupon();
        $generatedCode = $couponModel->generateRandomCode();
        $customers = $couponModel->getAvailableCustomers();

        $data = [
            'pageTitle' => 'Create Coupon | Dar Jana Fashion Admin',
            'generatedCode' => $generatedCode,
            'customers' => $customers
        ];

        $this->render('admin/coupon_form', $data, 'admin');
    }

    public function store() {
        if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = strtoupper(trim($_POST['code'] ?? ''));
            $discountType = $_POST['discount_type'] ?? 'percentage';
            $discountValue = (float)($_POST['discount_value'] ?? 0);
            $startDate = $_POST['start_date'] ?? date('Y-m-d');
            $endDate = $_POST['end_date'] ?? date('Y-m-d', strtotime('+30 days'));
            $limit = (int)($_POST['usage_limit_per_user'] ?? 1);
            $audienceType = $_POST['audience_type'] ?? 'all';
            $targetedCustomers = trim($_POST['targeted_customers'] ?? '');

            if (empty($code)) {
                $_SESSION['error_message'] = "Please provide a coupon code.";
                $this->redirect(BASE_URL . '/admin/coupons/create');
            }

            if ($discountValue <= 0) {
                $_SESSION['error_message'] = "Please provide a valid discount value.";
                $this->redirect(BASE_URL . '/admin/coupons/create');
            }

            if ($audienceType === 'targeted' && empty($targetedCustomers)) {
                $_SESSION['error_message'] = "Please provide at least one phone number or email address for the targeted audience.";
                $this->redirect(BASE_URL . '/admin/coupons/create');
            }

            $couponModel = new Coupon();
            $existing = $couponModel->getCouponByCode($code);
            if ($existing) {
                $_SESSION['error_message'] = "Coupon code '{$code}' already exists. Please choose a different code.";
                $this->redirect(BASE_URL . '/admin/coupons/create');
            }

            $couponModel->createCoupon([
                'code' => $code,
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'usage_limit_per_user' => $limit,
                'audience_type' => $audienceType,
                'targeted_customers' => $targetedCustomers
            ]);

            $_SESSION['success_message'] = "Coupon created successfully.";
            $this->redirect(BASE_URL . '/admin/coupons');
        }
    }

    public function edit($id) {
        if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $couponModel = new Coupon();
        $coupon = $couponModel->getCouponById($id);

        if (!$coupon) {
            $_SESSION['error_message'] = "Coupon not found.";
            $this->redirect(BASE_URL . '/admin/coupons');
        }

        $customers = $couponModel->getAvailableCustomers();

        $data = [
            'pageTitle' => 'Edit Coupon | Dar Jana Fashion Admin',
            'coupon' => $coupon,
            'customers' => $customers,
            'isEdit' => true
        ];

        $this->render('admin/coupon_form', $data, 'admin');
    }

    public function update($id) {
        if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = strtoupper(trim($_POST['code'] ?? ''));
            $type = trim($_POST['discount_type'] ?? 'percentage');
            $value = (float)($_POST['discount_value'] ?? 0);
            $startDate = trim($_POST['start_date'] ?? date('Y-m-d'));
            $endDate = trim($_POST['end_date'] ?? date('Y-m-d'));
            $limit = (int)($_POST['usage_limit_per_user'] ?? 1);
            $audienceType = trim($_POST['audience_type'] ?? 'all');
            $targetedCustomers = trim($_POST['targeted_customers'] ?? '');

            if (empty($code) || $value <= 0) {
                $_SESSION['error_message'] = "Please provide a valid code and discount value.";
                $this->redirect(BASE_URL . '/admin/coupons/edit/' . $id);
            }

            if ($audienceType === 'targeted' && empty($targetedCustomers)) {
                $_SESSION['error_message'] = "Please specify at least one customer phone number or email for targeted audience.";
                $this->redirect(BASE_URL . '/admin/coupons/edit/' . $id);
            }

            $couponModel = new Coupon();
            $existing = $couponModel->getCouponByCode($code);
            if ($existing && $existing['id'] != $id) {
                $_SESSION['error_message'] = "Another coupon with this code already exists.";
                $this->redirect(BASE_URL . '/admin/coupons/edit/' . $id);
            }

            $couponModel->updateCoupon($id, [
                'code' => $code,
                'discount_type' => $type,
                'discount_value' => $value,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'usage_limit_per_user' => $limit,
                'audience_type' => $audienceType,
                'targeted_customers' => $targetedCustomers
            ]);

            $_SESSION['success_message'] = "Coupon updated successfully.";
            $this->redirect(BASE_URL . '/admin/coupons');
        }
    }

    public function delete($id) {
        if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $couponModel = new Coupon();
        $couponModel->deleteCoupon($id);
        
        $_SESSION['success_message'] = "Coupon deleted successfully.";
        $this->redirect(BASE_URL . '/admin/coupons');
    }
}
