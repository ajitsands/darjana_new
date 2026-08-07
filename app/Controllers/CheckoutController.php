<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Models/Order.php';
require_once __DIR__ . '/../Models/Product.php';
require_once __DIR__ . '/../Models/Coupon.php';
require_once __DIR__ . '/../Models/Setting.php';

class CheckoutController extends Controller {
    public function index() {
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            $this->redirect(BASE_URL . '/collections/all-abaya');
        }

        $cartTotal = 0;
        foreach ($cart as $item) {
            $cartTotal += $item['price'] * $item['quantity'];
        }

        $settingModel = new Setting();
        $vatPercentage = (float)$settingModel->get('vat_percentage', 5);
        $vatType = $settingModel->get('vat_type', 'exclusive');

        $subtotal = 0;
        $vatAmount = 0;
        $finalTotal = 0;

        if ($vatType === 'none') {
            $subtotal = $cartTotal;
            $vatAmount = 0;
            $finalTotal = $cartTotal;
        } else if ($vatType === 'inclusive') {
            $finalTotal = $cartTotal;
            $subtotal = $cartTotal / (1 + ($vatPercentage / 100));
            $vatAmount = $cartTotal - $subtotal;
        } else {
            $subtotal = $cartTotal;
            $vatAmount = $cartTotal * ($vatPercentage / 100);
            $finalTotal = $cartTotal + $vatAmount;
        }

        $data = [
            'pageTitle' => 'Checkout | Dar Jana Fashion',
            'cart' => $cart,
            'subtotal' => number_format($subtotal, 2, '.', ''),
            'vatAmount' => number_format($vatAmount, 2, '.', ''),
            'vatPercentage' => $vatPercentage,
            'vatType' => $vatType,
            'total' => number_format($finalTotal, 2, '.', ''),
            'afs_gateway_enabled' => $settingModel->get('afs_gateway_enabled', '0') === '1'
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
        
        $phoneCode = trim($_POST['phone_code'] ?? '+973');
        $phoneNum = trim($_POST['phone'] ?? '');
        $phone = $phoneCode . ' ' . $phoneNum;

        $address = trim($_POST['address'] ?? '');
        $city = trim($_POST['city'] ?? 'Kuwait City');
        $country = trim($_POST['country'] ?? 'Kuwait');

        $useDifferentShipping = isset($_POST['different_shipping']) && $_POST['different_shipping'] === '1';
        $shippingAddress = $useDifferentShipping ? trim($_POST['shipping_address'] ?? '') : $address;
        $shippingCity = $useDifferentShipping ? trim($_POST['shipping_city'] ?? '') : $city;
        $shippingCountry = $useDifferentShipping ? trim($_POST['shipping_country'] ?? '') : $country;

        $orderNote = trim($_POST['order_note'] ?? '');

        if (empty($name) || empty($email) || empty($phone) || empty($address)) {
            $this->json(['success' => false, 'message' => 'Please fill in all required customer fields.']);
        }

        $cartTotal = 0;
        $eligibleTotal = 0;
        $productModel = new Product();

        foreach ($cart as $item) {
            $lineTotal = $item['price'] * $item['quantity'];
            $cartTotal += $lineTotal;
            
            // Check if product is on sale/offer in database
            $prod = $productModel->getById($item['id']);
            $isDiscounted = false;
            if ($prod && !empty($prod['sale_price']) && (float)$prod['sale_price'] > 0 && (float)$prod['sale_price'] != (float)$prod['price']) {
                $isDiscounted = true;
            } elseif (!empty($item['is_discounted'])) {
                $isDiscounted = true;
            }

            if (!$isDiscounted) {
                $eligibleTotal += $lineTotal;
            }
        }

        $couponCode = trim($_POST['coupon_code'] ?? '');
        $discountAmount = 0;

        if (!empty($couponCode)) {
            require_once __DIR__ . '/../Models/Coupon.php';
            $couponModel = new Coupon();
            $validation = $couponModel->validateCoupon($couponCode, $email, $phone);
            if ($validation['success']) {
                if ($eligibleTotal <= 0) {
                    $this->json(['success' => false, 'message' => 'This coupon cannot be applied because all items in your cart are already on sale / offer.']);
                }
                $coupon = $validation['coupon'];
                if ($coupon['discount_type'] === 'percentage') {
                    $discountAmount = $eligibleTotal * ($coupon['discount_value'] / 100);
                } else {
                    $discountAmount = $coupon['discount_value'];
                    if ($discountAmount > $eligibleTotal) {
                        $discountAmount = $eligibleTotal;
                    }
                }
            } else {
                $this->json(['success' => false, 'message' => $validation['message']]);
            }
        }

        $discountedCartTotal = $cartTotal - $discountAmount;
        if ($discountedCartTotal < 0) $discountedCartTotal = 0;

        $settingModel = new Setting();
        $vatPercentage = (float)$settingModel->get('vat_percentage', 5);
        $vatType = $settingModel->get('vat_type', 'exclusive');

        $vatAmount = 0;
        $finalTotal = 0;

        if ($vatType === 'none') {
            $vatAmount = 0;
            $finalTotal = $discountedCartTotal;
        } else if ($vatType === 'inclusive') {
            $finalTotal = $discountedCartTotal;
            $subtotal = $discountedCartTotal / (1 + ($vatPercentage / 100));
            $vatAmount = $discountedCartTotal - $subtotal;
        } else {
            $vatAmount = $discountedCartTotal * ($vatPercentage / 100);
            $finalTotal = $discountedCartTotal + $vatAmount;
        }

        $orderModel = new Order();
        $order = $orderModel->createOrder([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'city' => $city,
            'country' => $country,
            'shipping_address' => $shippingAddress,
            'shipping_city' => $shippingCity,
            'shipping_country' => $shippingCountry,
            'order_note' => $orderNote
        ], $cart, $finalTotal, $vatAmount, $vatType, $couponCode, $discountAmount);

        // Clear cart session after successful order
        $_SESSION['cart'] = [];

        $isAfsEnabled = $settingModel->get('afs_gateway_enabled', '0') === '1';
        if ($isAfsEnabled && $finalTotal > 0) {
            $apiEndpoint = rtrim($settingModel->get('afs_api_endpoint', 'https://test.oppwa.com'), '/');
            $entityId = $settingModel->get('afs_entity_id', '');
            $accessToken = $settingModel->get('afs_access_token', '');

            $chargeMode = $settingModel->get('afs_charge_currency_mode', 'base');
            $baseCurrency = strtoupper($settingModel->get('afs_currency', 'BHD'));

            if ($chargeMode === 'customer_currency') {
                // Determine active customer currency & exchange rate configured by admin
                $userCurrency = strtoupper(trim($_POST['user_currency'] ?? $_COOKIE['user_currency'] ?? ''));
                $supportedRates = [
                    'BHD' => 1.00,
                    'KWD' => (float)($settingModel->get('currency_rate_kwd', 0.81)),
                    'SAR' => (float)($settingModel->get('currency_rate_sar', 9.95)),
                    'AED' => (float)($settingModel->get('currency_rate_aed', 9.76)),
                    'QAR' => (float)($settingModel->get('currency_rate_qar', 9.67)),
                    'OMR' => (float)($settingModel->get('currency_rate_omr', 1.02)),
                    'USD' => (float)($settingModel->get('currency_rate_usd', 2.65)),
                    'EUR' => (float)($settingModel->get('currency_rate_eur', 2.44))
                ];

                if (!isset($supportedRates[$userCurrency])) {
                    $userCurrency = $baseCurrency;
                }

                $rate = $supportedRates[$userCurrency] ?? 1.00;
                $gatewayAmount = number_format($finalTotal * $rate, 2, '.', '');
                $gatewayCurrency = $userCurrency;
            } else {
                // Charge in Store Base Currency (e.g. BHD) to prevent AFS single-currency MID authorization rejection
                $gatewayAmount = number_format($finalTotal, 2, '.', '');
                $gatewayCurrency = $baseCurrency;
            }

            $url = $apiEndpoint . "/v1/checkouts";
            $data = http_build_query([
                'entityId' => $entityId,
                'amount' => $gatewayAmount,
                'currency' => $gatewayCurrency,
                'paymentType' => 'DB',
                'merchantTransactionId' => $order['order_number']
            ]);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $accessToken
            ]);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $responseData = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($responseData, true);
            if (isset($result['id'])) {
                $this->json([
                    'success' => true,
                    'is_payment' => true,
                    'checkout_id' => $result['id'],
                    'order_id' => $order['id'],
                    'order_number' => $order['order_number'],
                    'payment_script' => $apiEndpoint . '/v1/paymentWidgets.js?checkoutId=' . $result['id']
                ]);
            }
        }

        if (!$isAfsEnabled) {
            Mail::sendOrderConfirmation($order, $cart, $email);
        }

        $this->json([
            'success' => true,
            'is_payment' => false,
            'message' => 'Thank you! Your order has been placed successfully.',
            'order_number' => $order['order_number']
        ]);
    }

    public function applyCoupon() {
        $code = trim($_POST['coupon_code'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phoneNum = trim($_POST['phone'] ?? '');
        $phoneCode = trim($_POST['phone_code'] ?? '');
        $phone = trim($phoneCode . ' ' . $phoneNum);
        
        if (empty($code)) {
            $this->json(['success' => false, 'message' => 'Please enter a coupon code.']);
        }
        
        if (empty($email) && empty($phoneNum)) {
            $this->json(['success' => false, 'message' => 'Please enter your phone number or email address first to validate this offer code.']);
        }

        require_once __DIR__ . '/../Models/Coupon.php';
        $couponModel = new Coupon();
        $validation = $couponModel->validateCoupon($code, $email, $phone);

        if (!$validation['success']) {
            $this->json($validation);
        }

        $coupon = $validation['coupon'];

        // Calculate eligible total (only items where regular_price == sale_price or no sale_price)
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            $this->json(['success' => false, 'message' => 'Your cart is empty.']);
        }

        $eligibleTotal = 0;
        $productModel = new Product();
        foreach ($cart as $item) {
            $prod = $productModel->getById($item['id']);
            $isDiscounted = false;
            if ($prod && !empty($prod['sale_price']) && (float)$prod['sale_price'] > 0 && (float)$prod['sale_price'] != (float)$prod['price']) {
                $isDiscounted = true;
            } elseif (!empty($item['is_discounted'])) {
                $isDiscounted = true;
            }

            if (!$isDiscounted) {
                $eligibleTotal += $item['price'] * $item['quantity'];
            }
        }

        if ($eligibleTotal <= 0) {
            $this->json(['success' => false, 'message' => 'This coupon cannot be applied because all items in your cart are already on special offer/discount.']);
        }

        $discountAmount = 0;
        if ($coupon['discount_type'] === 'percentage') {
            $discountAmount = $eligibleTotal * ($coupon['discount_value'] / 100);
        } else {
            $discountAmount = $coupon['discount_value'];
            if ($discountAmount > $eligibleTotal) {
                $discountAmount = $eligibleTotal; // Cap discount at eligible total
            }
        }

        $this->json([
            'success' => true,
            'discount_amount' => number_format($discountAmount, 2, '.', ''),
            'message' => 'Coupon applied successfully!'
        ]);
    }

    public function paymentResult() {
        $resourcePath = $_GET['resourcePath'] ?? '';
        $orderId = $_GET['order_id'] ?? '';

        if (empty($resourcePath) || empty($orderId)) {
            die("Invalid payment callback parameters.");
        }

        $settingModel = new Setting();
        $apiEndpoint = rtrim($settingModel->get('afs_api_endpoint', 'https://test.oppwa.com'), '/');
        $entityId = $settingModel->get('afs_entity_id', '');
        $accessToken = $settingModel->get('afs_access_token', '');

        $url = $apiEndpoint . $resourcePath . "?entityId=" . $entityId;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken
        ]);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($responseData, true);
        $paymentStatus = 'Failed';
        $success = false;

        if (isset($result['result']['code'])) {
            $code = $result['result']['code'];
            // Regular expression for success codes as per OPPWA documentation
            if (preg_match('/^(000\.000\.|000\.100\.1|000\.[36])/', $code) || preg_match('/^(000\.400\.0[^3]|000\.400\.100)/', $code)) {
                $paymentStatus = 'Paid';
                $success = true;
            }
        }

        $orderModel = new Order();
        // Custom query to update payment status and api_response
        $orderModel->updatePaymentStatus($orderId, $paymentStatus, $responseData);
        
        $orderData = $orderModel->getOrderById($orderId);
        
        if ($success) {
            $orderItems = $orderModel->getOrderItems($orderId);
            Mail::sendOrderConfirmation($orderData, $orderItems, $orderData['email']);
        }

        $data = [
            'pageTitle' => 'Payment Result | Dar Jana Fashion',
            'success' => $success,
            'paymentStatus' => $paymentStatus,
            'order' => $orderData,
            'resultMessage' => $result['result']['description'] ?? 'Transaction Failed.'
        ];

        $this->render('checkout/payment_result', $data);
    }
}
