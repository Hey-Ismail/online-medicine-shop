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
    <title>Purchase History - Medicine Shop</title>
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
                    <li><a href="<?= BASE_PATH ?>/index.php?action=manage_medicines" class="nav-item">Medicines</a></li>
                    <li><a href="<?= BASE_PATH ?>/index.php?action=manage_customers" class="nav-item">Customers</a></li>
                    <li><a href="<?= BASE_PATH ?>/index.php?action=purchase_requests" class="nav-item">Purchase Requests</a></li>
                    <li><a href="<?= BASE_PATH ?>/index.php?action=purchase_history" class="nav-item active">Purchase History</a></li>
                    <li><a href="<?= BASE_PATH ?>/index.php?action=logout" class="nav-item logout">Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-content">
            <div class="content-header">
                <h1>Purchase History (All Accepted Orders)</h1>
            </div>

            <!-- Error Messages -->
            <?php if (isset($data['error'])): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($data['error']); ?></div>
            <?php endif; ?>

            <!-- Purchase History -->
            <?php if (is_array($data) && count($data) > 0): ?>
                <?php foreach ($data as $order): ?>
                    <div class="purchase-history-card">
                        <div class="card-header">
                            <div class="order-info">
                                <h3>Order #<?php echo htmlspecialchars($order['id']); ?></h3>
                                <p class="customer-info">
                                    <strong>Customer:</strong> <?php echo htmlspecialchars($order['customer_name']); ?> 
                                    (<?php echo htmlspecialchars($order['customer_email']); ?>)
                                </p>
                            </div>
                            <div class="order-summary">
                                <p class="total-amount">
                                    <strong>Total: $<?php echo number_format($order['total_amount'], 2); ?></strong>
                                </p>
                                <p class="order-date">
                                    <?php echo date('Y-m-d H:i', strtotime($order['order_date'])); ?>
                                </p>
                            </div>
                        </div>

                        <div class="card-body">
                            <p><strong>Shipping Address:</strong> <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                            
                            <h4>Items Purchased:</h4>
                            <table class="items-table">
                                <thead>
                                    <tr>
                                        <th>Medicine Name</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['medicine_name']); ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td>$<?php echo number_format($item['unit_price'], 2); ?></td>
                                            <td>$<?php echo number_format($item['quantity'] * $item['unit_price'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info">No purchase history found</div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
