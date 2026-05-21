<?php
declare(strict_types=1);

session_start();

$config = require __DIR__ . '/config/config.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/');
$basePath = BASE_PATH;

if ($basePath !== '' && strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}

$path = rtrim($path, '/') ?: '/';
$action = $_GET['action'] ?? null;

if ($action !== null) {
    require __DIR__ . '/config/database.php';
    require __DIR__ . '/app/controllers/admin/AdminController.php';

    $controller = new AdminController($pdo);

    switch ($action) {
        case 'delete_category':
            header('Content-Type: application/json');
            echo json_encode($controller->deleteCategory((int)($_POST['category_id'] ?? 0)));
            break;

        case 'delete_medicine':
            header('Content-Type: application/json');
            echo json_encode($controller->deleteMedicine((int)($_POST['medicine_id'] ?? 0)));
            break;

        case 'delete_customer':
            header('Content-Type: application/json');
            echo json_encode($controller->deleteCustomer((int)($_POST['customer_id'] ?? 0)));
            break;

        case 'update_order_status':
            header('Content-Type: application/json');
            echo json_encode($controller->updateOrderStatus((int)($_POST['order_id'] ?? 0), (string)($_POST['status'] ?? '')));
            break;

        case 'get_order_details':
            header('Content-Type: application/json');
            $order = $controller->getOrderDetails((int)($_POST['order_id'] ?? 0));
            echo json_encode(isset($order['error']) ? $order : ['success' => true, 'order' => $order]);
            break;

        case 'admin_dashboard':
            $data = $controller->dashboard();
            require __DIR__ . '/app/views/admin/admin_dashboard.php';
            break;

        case 'manage_categories':
            $data = $controller->getCategories();
            require __DIR__ . '/app/views/admin/manage_categories.php';
            break;

        case 'add_category':
            $data = $controller->addCategory();
            require __DIR__ . '/app/views/admin/add_edit_category.php';
            break;

        case 'edit_category':
            $data = $controller->editCategory((int)($_GET['id'] ?? 0));
            require __DIR__ . '/app/views/admin/add_edit_category.php';
            break;

        case 'manage_medicines':
            $data = $controller->getMedicines();
            require __DIR__ . '/app/views/admin/manage_medicines.php';
            break;

        case 'add_medicine':
            $data = $controller->addMedicine();
            require __DIR__ . '/app/views/admin/add_edit_medicine.php';
            break;

        case 'edit_medicine':
            $data = $controller->editMedicine((int)($_GET['id'] ?? 0));
            require __DIR__ . '/app/views/admin/add_edit_medicine.php';
            break;

        case 'manage_customers':
            $data = $controller->getCustomers();
            require __DIR__ . '/app/views/admin/manage_customers.php';
            break;

        case 'purchase_requests':
            $status = $_GET['status'] ?? 'all';
            $data = $controller->getOrders($status === 'all' ? null : (string)$status);
            require __DIR__ . '/app/views/admin/purchase_requests.php';
            break;

        case 'purchase_history':
            $data = $controller->getPurchaseHistory();
            require __DIR__ . '/app/views/admin/purchase_history.php';
            break;

        case 'logout':
            require __DIR__ . '/app/controllers/auth/AuthController.php';
            (new AuthController())->logout();
            break;

        default:
            http_response_code(404);
            require __DIR__ . '/app/views/auth/404.php';
    }

    exit;
}

switch (true) {
    case $path === '/':
        require __DIR__ . '/app/controllers/auth/HomeController.php';
        (new HomeController())->index();
        break;

    case $path === '/login':
        require __DIR__ . '/app/controllers/auth/AuthController.php';
        (new AuthController())->login();
        break;

    case $path === '/register':
        require __DIR__ . '/app/controllers/auth/AuthController.php';
        (new AuthController())->register();
        break;

    case $path === '/logout':
        require __DIR__ . '/app/controllers/auth/AuthController.php';
        (new AuthController())->logout();
        break;

    case $path === '/profile':
        require __DIR__ . '/app/controllers/auth/ProfileController.php';
        (new ProfileController())->index();
        break;

    case $path === '/medicines':
        require __DIR__ . '/app/controllers/auth/HomeController.php';
        (new HomeController())->medicines();
        break;

    case preg_match('#^/medicines/category/(\d+)$#', $path, $matches) === 1:
        require __DIR__ . '/app/controllers/auth/HomeController.php';
        (new HomeController())->category((int)$matches[1]);
        break;

    case $path === '/api/medicines/search':
        require __DIR__ . '/app/controllers/auth/ApiController.php';
        (new ApiController())->search();
        break;

    case preg_match('#^/cart/add/(\d+)$#', $path, $matches) === 1:
        require_login();
        require __DIR__ . '/config/database.php';
        $medicineId = (int)$matches[1];
        $userId = (int)$_SESSION['user_id'];
        $stmt = $pdo->prepare(
            'INSERT INTO cart (user_id, medicine_id, quantity)
             VALUES (?, ?, 1)
             ON DUPLICATE KEY UPDATE quantity = quantity + 1'
        );
        $stmt->execute([$userId, $medicineId]);
        set_flash('success', 'Medicine added to cart.');
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? BASE_PATH . '/medicines'));
        break;

    case $path === '/cart':
    case $path === '/checkout':
        header('Location: ' . BASE_PATH . '/public/shop' . $path);
        break;

    case $path === '/shop':
        header('Location: ' . BASE_PATH . '/public/shop/');
        break;

    default:
        http_response_code(404);
        require __DIR__ . '/app/views/auth/404.php';
}
