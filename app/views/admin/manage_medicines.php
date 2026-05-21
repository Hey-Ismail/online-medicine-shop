<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if ($_SESSION['role'] !== 'admin') {
    header('Location: ' . BASE_PATH . '/login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Medicines - Medicine Shop</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/admin/css/admin.css">
    <link rel="stylesheet" href="<?= BASE_PATH ?>/public/admin/css/responsive.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar Navigation -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="<?= BASE_PATH ?>/index.php?action=admin_dashboard" class="nav-item">Dashboard</a></li>
                    <li><a href="<?= BASE_PATH ?>/index.php?action=manage_categories" class="nav-item">Categories</a></li>
                    <li><a href="<?= BASE_PATH ?>/index.php?action=manage_medicines" class="nav-item active">Medicines</a></li>
                    <li><a href="<?= BASE_PATH ?>/index.php?action=manage_customers" class="nav-item">Customers</a></li>
                    <li><a href="<?= BASE_PATH ?>/index.php?action=purchase_requests" class="nav-item">Purchase Requests</a></li>
                    <li><a href="<?= BASE_PATH ?>/index.php?action=purchase_history" class="nav-item">Purchase History</a></li>
                    <li><a href="<?= BASE_PATH ?>/index.php?action=logout" class="nav-item logout">Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-content">
            <div class="content-header">
                <h1>Medicine Management</h1>
                <a href="<?= BASE_PATH ?>/index.php?action=add_medicine" class="btn btn-primary">+ Add New Medicine</a>
            </div>

            <!-- Error Messages -->
            <?php if (isset($data['error'])): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($data['error']); ?></div>
            <?php endif; ?>

            <!-- Success Message -->
            <?php if (isset($data['success'])): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($data['success']); ?></div>
            <?php endif; ?>

            <!-- Medicines Table -->
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Vendor</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (is_array($data) && count($data) > 0): ?>
                            <?php foreach ($data as $medicine): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($medicine['id']); ?></td>
                                    <td><?php echo htmlspecialchars($medicine['name']); ?></td>
                                    <td><?php echo htmlspecialchars($medicine['category_name']); ?></td>
                                    <td><?php echo htmlspecialchars($medicine['vendor_name']); ?></td>
                                    <td>$<?php echo number_format($medicine['price'], 2); ?></td>
                                    <td>
                                        <span class="stock-badge <?php echo $medicine['availability'] > 0 ? 'in-stock' : 'out-stock'; ?>">
                                            <?php echo $medicine['availability']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($medicine['image_path']): ?>
                                            <img src="/<?php echo htmlspecialchars($medicine['image_path']); ?>" alt="Medicine" class="thumb">
                                        <?php else: ?>
                                            <span class="text-muted">No image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="action-buttons">
                                        <a href="<?= BASE_PATH ?>/index.php?action=edit_medicine&id=<?php echo $medicine['id']; ?>" class="btn btn-sm btn-info">Edit</a>
                                        <button class="btn btn-sm btn-danger" onclick="deleteMedicine(<?php echo $medicine['id']; ?>)">Delete</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No medicines found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script src="<?= BASE_PATH ?>/public/admin/js/admin.js"></script>
</body>
</html>
