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
    <title>Admin Dashboard - Medicine Shop</title>
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
                    <li><a href="<?= BASE_PATH ?>/index.php?action=admin_dashboard" class="nav-item active">Dashboard</a></li>
                    <li><a href="<?= BASE_PATH ?>/index.php?action=manage_categories" class="nav-item">Categories</a></li>
                    <li><a href="<?= BASE_PATH ?>/index.php?action=manage_medicines" class="nav-item">Medicines</a></li>
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
                <h1>Admin Dashboard</h1>
                <p>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></p>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon medicines">📦</div>
                    <div class="stat-info">
                        <h3><?php echo $data['totalMedicines']; ?></h3>
                        <p>Total Medicines</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon categories">🏷️</div>
                    <div class="stat-info">
                        <h3><?php echo $data['totalCategories']; ?></h3>
                        <p>Categories</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon customers">👥</div>
                    <div class="stat-info">
                        <h3><?php echo $data['totalCustomers']; ?></h3>
                        <p>Total Customers</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon pending">⏳</div>
                    <div class="stat-info">
                        <h3><?php echo $data['pendingOrders']; ?></h3>
                        <p>Pending Orders</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h2>Quick Actions</h2>
                <div class="action-buttons">
                    <a href="<?= BASE_PATH ?>/index.php?action=add_category" class="btn btn-primary">+ Add Category</a>
                    <a href="<?= BASE_PATH ?>/index.php?action=add_medicine" class="btn btn-primary">+ Add Medicine</a>
                    <a href="<?= BASE_PATH ?>/index.php?action=purchase_requests" class="btn btn-warning">View Requests</a>
                    <a href="<?= BASE_PATH ?>/index.php?action=manage_customers" class="btn btn-secondary">Manage Customers</a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
