<?php
// Front Controller for Dar Jana Fashion

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Router.php';

// Initialize Cart Session if empty
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Router Initialization
$router = new Router();

// Define Routes
$router->add('GET', '/', 'HomeController@index');
$router->add('GET', '/collections/{slug}', 'ProductController@index');
$router->add('GET', '/product/{slug}', 'ProductController@detail');
$router->add('GET', '/search', 'ProductController@search');

$router->add('GET', '/cart', 'CartController@index');
$router->add('POST', '/cart/add', 'CartController@add');
$router->add('POST', '/cart/update', 'CartController@update');
$router->add('POST', '/cart/remove', 'CartController@remove');
$router->add('GET', '/cart/get-json', 'CartController@getJson');

$router->add('GET', '/checkout', 'CheckoutController@index');
$router->add('POST', '/checkout/process', 'CheckoutController@process');

$router->add('POST', '/newsletter/subscribe', 'HomeController@subscribe');

$router->add('GET', '/admin/login', 'AdminController@login');
$router->add('POST', '/admin/login', 'AdminController@processLogin');
$router->add('GET', '/admin/logout', 'AdminController@logout');

$router->add('GET', '/admin', 'AdminController@index');
$router->add('GET', '/admin/users', 'AdminController@users');
$router->add('POST', '/admin/users/add', 'AdminController@addUser');
$router->add('GET', '/admin/history', 'AdminController@history');
$router->add('GET', '/admin/orders', 'AdminController@orders');
$router->add('GET', '/admin/products/ajax', 'AdminController@ajaxProducts');
$router->add('POST', '/admin/product/add', 'AdminController@addProduct');
$router->add('GET', '/admin/product/edit/{id}', 'AdminController@editProduct');
$router->add('POST', '/admin/product/edit/{id}', 'AdminController@updateProduct');
$router->add('GET', '/admin/product/delete/{id}', 'AdminController@deleteProduct');
$router->add('GET', '/admin/order/delete/{id}', 'AdminController@deleteOrder');

// Dispatch Request
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$router->dispatch($uri, $requestMethod);
