<?php
// Front Controller for Dar Jana Fashion

session_start();

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Router.php';
require_once __DIR__ . '/../core/Mail.php';

// Initialize Cart Session if empty
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

require_once __DIR__ . '/../app/Models/Setting.php';
$settingModel = new Setting();
$timezone = $settingModel->get('timezone', 'Asia/Bahrain');
date_default_timezone_set($timezone);

// Router Initialization
$router = new Router();

// Define Routes
$router->add('GET', '/', 'HomeController@index');
$router->add('GET', '/collections/{slug}', 'ProductController@index');
$router->add('GET', '/product/{slug}', 'ProductController@detail');
$router->add('GET', '/search', 'ProductController@search');
$router->add('GET', '/track-order', 'TrackController@index');

$router->add('GET', '/cart', 'CartController@index');
$router->add('POST', '/cart/add', 'CartController@add');
$router->add('POST', '/cart/update', 'CartController@update');
$router->add('POST', '/cart/remove', 'CartController@remove');
$router->add('GET', '/cart/get-json', 'CartController@getJson');

$router->add('GET', '/checkout', 'CheckoutController@index');
$router->add('POST', '/checkout/process', 'CheckoutController@process');
$router->add('GET', '/checkout/payment-result', 'CheckoutController@paymentResult');

$router->add('POST', '/newsletter/subscribe', 'HomeController@subscribe');

$router->add('GET', '/lang/{lang}', 'LanguageController@switchLang');

$router->add('GET', '/admin/login', 'AdminController@login');
$router->add('POST', '/admin/login', 'AdminController@processLogin');
$router->add('GET', '/admin/logout', 'AdminController@logout');

$router->add('GET', '/admin', 'AdminController@index');
$router->add('GET', '/admin/products', 'AdminController@products');
$router->add('GET', '/admin/generate-tiny', 'AdminController@generateTinyThumbnails');
$router->add('GET', '/admin/optimize-images', 'AdminController@optimizeImages');
$router->add('GET', '/admin/categories', 'AdminController@categories');
$router->add('POST', '/admin/categories/add', 'AdminController@addCategory');
$router->add('GET', '/admin/category/edit/{id}', 'AdminController@editCategory');
$router->add('POST', '/admin/category/edit/{id}', 'AdminController@updateCategory');
$router->add('POST', '/admin/category/toggle/{id}', 'AdminController@toggleCategoryStatus');
$router->add('GET', '/admin/users', 'AdminController@users');
$router->add('POST', '/admin/users/add', 'AdminController@addUser');
$router->add('POST', '/admin/users/update/{id}', 'AdminController@updateUser');
$router->add('POST', '/admin/users/reset-password/{id}', 'AdminController@resetPassword');
$router->add('GET', '/admin/users/delete/{id}', 'AdminController@deleteUser');
$router->add('GET', '/admin/history', 'AdminController@history');
$router->add('GET', '/admin/orders', 'AdminController@orders');
$router->add('GET', '/admin/products/ajax', 'AdminController@ajaxProducts');
$router->add('POST', '/admin/product/add', 'AdminController@addProduct');
$router->add('GET', '/admin/product/edit/{id}', 'AdminController@editProduct');
$router->add('POST', '/admin/product/edit/{id}', 'AdminController@updateProduct');
$router->add('GET', '/admin/product/delete/{id}', 'AdminController@deleteProduct');
$router->add('GET', '/admin/order/{id}', 'AdminController@orderDetail');
$router->add('POST', '/admin/order/assign-item/{id}', 'AdminController@assignItem');
$router->add('GET', '/admin/order/remove-assignment/{id}', 'AdminController@removeAssignment');
$router->add('GET', '/admin/order/print-process-requests/{id}', 'AdminController@printProcessRequests');
$router->add('GET', '/admin/order/print-assignment-summary/{id}', 'AdminController@printAssignmentSummary');
$router->add('POST', '/admin/order/update-status/{id}', 'AdminController@updateStatus');
$router->add('GET', '/admin/order/delete/{id}', 'AdminController@deleteOrder');
$router->add('GET', '/admin/settings', 'AdminController@settings');
$router->add('POST', '/admin/settings', 'AdminController@settings');

$router->add('GET', '/admin/coupons', 'AdminCouponController@index');
$router->add('GET', '/admin/coupons/create', 'AdminCouponController@create');
$router->add('POST', '/admin/coupons/store', 'AdminCouponController@store');
$router->add('GET', '/admin/coupons/edit/{id}', 'AdminCouponController@edit');
$router->add('POST', '/admin/coupons/update/{id}', 'AdminCouponController@update');
$router->add('GET', '/admin/coupons/delete/{id}', 'AdminCouponController@delete');

$router->add('GET', '/admin/tailoring-units', 'AdminTailoringUnitController@index');
$router->add('GET', '/admin/tailoring-units/create', 'AdminTailoringUnitController@create');
$router->add('POST', '/admin/tailoring-units/store', 'AdminTailoringUnitController@store');
$router->add('GET', '/admin/tailoring-units/edit/{id}', 'AdminTailoringUnitController@edit');
$router->add('POST', '/admin/tailoring-units/update/{id}', 'AdminTailoringUnitController@update');
$router->add('GET', '/admin/tailoring-units/delete/{id}', 'AdminTailoringUnitController@delete');
$router->add('GET', '/admin/tailoring-units/check-code', 'AdminTailoringUnitController@checkCode');

$router->add('POST', '/checkout/apply-coupon', 'CheckoutController@applyCoupon');

// Dispatch Request
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$router->dispatch($uri, $requestMethod);
