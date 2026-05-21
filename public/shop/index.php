<?php
declare(strict_types=1);

$config = require dirname(__DIR__, 2) . '/config/config.php';
require dirname(__DIR__, 2) . '/core/Autoload.php';

session_start();

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/');
$baseUrl = $config['base_url'];

if ($baseUrl !== '' && strpos($path, $baseUrl) === 0) {
    $path = substr($path, strlen($baseUrl));
    if ($path === '') {
        $path = '/';
    }
}

$path = rtrim($path, '/') ?: '/';

if ($method === 'POST' && isset($_POST['_method'])) {
    $method = strtoupper((string)$_POST['_method']);
}

$routes = [
    'GET' => [
        '/' => [MedicinesController::class, 'index'],
        '/medicines' => [MedicinesController::class, 'index'],
        '/cart' => [CartController::class, 'index'],
        '/checkout' => [CheckoutController::class, 'addressForm'],
        '/checkout/invoice' => [CheckoutController::class, 'invoice'],
        '/checkout/payment' => [CheckoutController::class, 'paymentForm'],
        '/orders/success' => [OrderController::class, 'success'],
    ],
    'POST' => [
        '/checkout/address' => [CheckoutController::class, 'saveAddress'],
        '/checkout/confirm' => [CheckoutController::class, 'confirmInvoice'],
        '/checkout/payment' => [CheckoutController::class, 'processPayment'],
        '/api/cart/add' => [CartApiController::class, 'add'],
        '/api/cart/update' => [CartApiController::class, 'update'],
    ],
    'DELETE' => [
        '/api/cart/remove' => [CartApiController::class, 'remove'],
    ],
];

if (!isset($routes[$method][$path])) {
    http_response_code(404);
    echo 'Not Found';
    exit;
}

[$class, $action] = $routes[$method][$path];
$controller = new $class($config);
$controller->$action();
