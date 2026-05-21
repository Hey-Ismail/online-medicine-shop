<?php
require_once dirname(__DIR__, 3) . '/config/database.php';
require_once dirname(__DIR__, 2) . '/models/admin/Category.php';
require_once dirname(__DIR__, 2) . '/models/admin/Medicine.php';
require_once dirname(__DIR__, 2) . '/models/admin/User.php';
require_once dirname(__DIR__, 2) . '/models/admin/Order.php';

class AdminController {
    private $pdo;
    private $categoryModel;
    private $medicineModel;
    private $userModel;
    private $orderModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->categoryModel = new Category($pdo);
        $this->medicineModel = new Medicine($pdo);
        $this->userModel = new User($pdo);
        $this->orderModel = new Order($pdo);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    /**
     * Redirect to admin login if not admin
     */
    public function requireAdmin() {
        if (!$this->isAdmin()) {
            header('Location: ' . BASE_PATH . '/login');
            exit;
        }
    }

    // ==================== DASHBOARD ====================

    public function dashboard() {
        $this->requireAdmin();

        $totalMedicines = $this->medicineModel->getCount();
        $totalCategories = count($this->categoryModel->getAll());
        $totalCustomers = $this->userModel->getCustomerCount();
        $pendingOrders = $this->orderModel->getPendingCount();

        return [
            'totalMedicines' => $totalMedicines,
            'totalCategories' => $totalCategories,
            'totalCustomers' => $totalCustomers,
            'pendingOrders' => $pendingOrders
        ];
    }

    // ==================== CATEGORY MANAGEMENT ====================

    public function getCategories() {
        $this->requireAdmin();
        return $this->categoryModel->getAll();
    }

    public function addCategory() {
        $this->requireAdmin();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $category_type = $_POST['category_type'] ?? '';

            // Validation
            if (empty($name)) {
                $errors[] = "Category name is required";
            } elseif (strlen($name) < 2) {
                $errors[] = "Category name must be at least 2 characters";
            } elseif ($this->categoryModel->exists($name)) {
                $errors[] = "Category already exists";
            }

            if (!in_array($category_type, ['liquid', 'solid'])) {
                $errors[] = "Invalid category type";
            }

            if (empty($errors)) {
                try {
                    $this->categoryModel->create($name, $category_type);
                    return ['success' => true, 'message' => 'Category added successfully'];
                } catch (Exception $e) {
                    $errors[] = "Error adding category: " . $e->getMessage();
                }
            }
        }

        return ['errors' => $errors];
    }

    public function editCategory($id) {
        $this->requireAdmin();
        $errors = [];
        $category = $this->categoryModel->getById($id);

        if (!$category) {
            return ['error' => 'Category not found'];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $category_type = $_POST['category_type'] ?? '';

            // Validation
            if (empty($name)) {
                $errors[] = "Category name is required";
            } elseif (strlen($name) < 2) {
                $errors[] = "Category name must be at least 2 characters";
            } elseif ($this->categoryModel->exists($name, $id)) {
                $errors[] = "Category already exists";
            }

            if (!in_array($category_type, ['liquid', 'solid'])) {
                $errors[] = "Invalid category type";
            }

            if (empty($errors)) {
                try {
                    $this->categoryModel->update($id, $name, $category_type);
                    return ['success' => true, 'message' => 'Category updated successfully'];
                } catch (Exception $e) {
                    $errors[] = "Error updating category: " . $e->getMessage();
                }
            }
        }

        return ['category' => $category, 'errors' => $errors];
    }

    public function deleteCategory($id) {
        $this->requireAdmin();

        try {
            $this->categoryModel->delete($id);
            return ['success' => true, 'message' => 'Category deleted successfully'];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // ==================== MEDICINE MANAGEMENT ====================

    public function getMedicines() {
        $this->requireAdmin();
        return $this->medicineModel->getAll();
    }

    public function addMedicine() {
        $this->requireAdmin();
        $errors = [];
        $categories = $this->categoryModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $category_id = (int)($_POST['category_id'] ?? 0);
            $vendor_name = trim($_POST['vendor_name'] ?? '');
            $price = (float)($_POST['price'] ?? 0);
            $availability = (int)($_POST['availability'] ?? 0);
            $description = trim($_POST['description'] ?? '');
            $image_path = '';

            // Validation
            if (empty($name)) {
                $errors[] = "Medicine name is required";
            } elseif (strlen($name) < 2) {
                $errors[] = "Medicine name must be at least 2 characters";
            }

            if ($category_id <= 0) {
                $errors[] = "Invalid category selected";
            }

            if (empty($vendor_name)) {
                $errors[] = "Vendor name is required";
            }

            if ($price <= 0) {
                $errors[] = "Price must be greater than 0";
            }

            if ($availability < 0) {
                $errors[] = "Stock availability cannot be negative";
            }

            // File upload validation
            if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
                $image = $_FILES['image'];
                $allowed_types = ['image/jpeg', 'image/png'];
                $max_size = 2 * 1024 * 1024; // 2MB

                if (!in_array($image['type'], $allowed_types)) {
                    $errors[] = "Only JPEG and PNG images are allowed";
                }

                if ($image['size'] > $max_size) {
                    $errors[] = "Image size must not exceed 2MB";
                }

                if (empty($errors)) {
                    $upload_dir = ROOT_DIR . '/public/uploads/medicines/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }

                    $filename = uniqid() . '_' . basename($image['name']);
                    $filepath = $upload_dir . $filename;

                    if (move_uploaded_file($image['tmp_name'], $filepath)) {
                        $image_path = 'uploads/medicines/' . $filename;
                    } else {
                        $errors[] = "Failed to upload image";
                    }
                }
            }

            if (empty($errors)) {
                try {
                    $this->medicineModel->create($name, $category_id, $vendor_name, $price, $availability, $description, $image_path);
                    return ['success' => true, 'message' => 'Medicine added successfully'];
                } catch (Exception $e) {
                    $errors[] = "Error adding medicine: " . $e->getMessage();
                }
            }
        }

        return ['categories' => $categories, 'errors' => $errors];
    }

    public function editMedicine($id) {
        $this->requireAdmin();
        $errors = [];
        $medicine = $this->medicineModel->getById($id);
        $categories = $this->categoryModel->getAll();

        if (!$medicine) {
            return ['error' => 'Medicine not found'];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $category_id = (int)($_POST['category_id'] ?? 0);
            $vendor_name = trim($_POST['vendor_name'] ?? '');
            $price = (float)($_POST['price'] ?? 0);
            $availability = (int)($_POST['availability'] ?? 0);
            $description = trim($_POST['description'] ?? '');
            $image_path = $medicine['image_path'];

            // Validation
            if (empty($name)) {
                $errors[] = "Medicine name is required";
            } elseif (strlen($name) < 2) {
                $errors[] = "Medicine name must be at least 2 characters";
            }

            if ($category_id <= 0) {
                $errors[] = "Invalid category selected";
            }

            if (empty($vendor_name)) {
                $errors[] = "Vendor name is required";
            }

            if ($price <= 0) {
                $errors[] = "Price must be greater than 0";
            }

            if ($availability < 0) {
                $errors[] = "Stock availability cannot be negative";
            }

            // File upload validation
            if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
                $image = $_FILES['image'];
                $allowed_types = ['image/jpeg', 'image/png'];
                $max_size = 2 * 1024 * 1024;

                if (!in_array($image['type'], $allowed_types)) {
                    $errors[] = "Only JPEG and PNG images are allowed";
                }

                if ($image['size'] > $max_size) {
                    $errors[] = "Image size must not exceed 2MB";
                }

                if (empty($errors)) {
                    // Delete old image
                    if ($medicine['image_path']) {
                        $old_file = ROOT_DIR . '/public/' . $medicine['image_path'];
                        if (file_exists($old_file)) {
                            unlink($old_file);
                        }
                    }

                    $upload_dir = ROOT_DIR . '/public/uploads/medicines/';
                    $filename = uniqid() . '_' . basename($image['name']);
                    $filepath = $upload_dir . $filename;

                    if (move_uploaded_file($image['tmp_name'], $filepath)) {
                        $image_path = 'uploads/medicines/' . $filename;
                    } else {
                        $errors[] = "Failed to upload image";
                    }
                }
            }

            if (empty($errors)) {
                try {
                    $this->medicineModel->update($id, $name, $category_id, $vendor_name, $price, $availability, $description, $image_path);
                    return ['success' => true, 'message' => 'Medicine updated successfully'];
                } catch (Exception $e) {
                    $errors[] = "Error updating medicine: " . $e->getMessage();
                }
            }
        }

        return ['medicine' => $medicine, 'categories' => $categories, 'errors' => $errors];
    }

    public function deleteMedicine($id) {
        $this->requireAdmin();

        try {
            $this->medicineModel->delete($id);
            return ['success' => true, 'message' => 'Medicine deleted successfully'];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // ==================== CUSTOMER MANAGEMENT ====================

    public function getCustomers() {
        $this->requireAdmin();
        return $this->userModel->getAllCustomers();
    }

    public function deleteCustomer($id) {
        $this->requireAdmin();

        try {
            $this->userModel->deleteCustomer($id);
            return ['success' => true, 'message' => 'Customer deleted successfully'];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // ==================== ORDER MANAGEMENT ====================

    public function getOrders($status = null) {
        $this->requireAdmin();
        return $this->orderModel->getAll($status);
    }

    public function getOrderDetails($id) {
        $this->requireAdmin();
        $order = $this->orderModel->getById($id);
        if (!$order) {
            return ['error' => 'Order not found'];
        }

        $items = $this->orderModel->getOrderItems($id);
        $order['items'] = $items;

        return $order;
    }

    public function updateOrderStatus($id, $status) {
        $this->requireAdmin();

        if (!in_array($status, ['pending', 'accepted', 'rejected'])) {
            return ['error' => 'Invalid status'];
        }

        try {
            $this->orderModel->updateStatus($id, $status);
            return ['success' => true, 'message' => 'Order status updated successfully', 'status' => $status];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getPurchaseHistory() {
        $this->requireAdmin();
        $orders = $this->orderModel->getAcceptedOrders();

        foreach ($orders as &$order) {
            $order['items'] = $this->orderModel->getOrderItems($order['id']);
        }

        return $orders;
    }
}
?>
