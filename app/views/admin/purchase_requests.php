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
    <title>Purchase Requests - Medicine Shop</title>
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
                    <li><a href="<?= BASE_PATH ?>/index.php?action=purchase_requests" class="nav-item active">Purchase Requests</a></li>
                    <li><a href="<?= BASE_PATH ?>/index.php?action=purchase_history" class="nav-item">Purchase History</a></li>
                    <li><a href="<?= BASE_PATH ?>/index.php?action=logout" class="nav-item logout">Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-content">
            <div class="content-header">
                <h1>Purchase Requests</h1>
            </div>

            <!-- Error Messages -->
            <?php if (isset($data['error'])): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($data['error']); ?></div>
            <?php endif; ?>

            <!-- Filter Tabs -->
            <div class="filter-tabs">
                <a href="<?= BASE_PATH ?>/index.php?action=purchase_requests&status=all" class="tab-btn <?php echo (!isset($_GET['status']) || $_GET['status'] === 'all') ? 'active' : ''; ?>">All Orders</a>
                <a href="<?= BASE_PATH ?>/index.php?action=purchase_requests&status=pending" class="tab-btn <?php echo (isset($_GET['status']) && $_GET['status'] === 'pending') ? 'active' : ''; ?>">Pending</a>
                <a href="<?= BASE_PATH ?>/index.php?action=purchase_requests&status=accepted" class="tab-btn <?php echo (isset($_GET['status']) && $_GET['status'] === 'accepted') ? 'active' : ''; ?>">Accepted</a>
                <a href="<?= BASE_PATH ?>/index.php?action=purchase_requests&status=rejected" class="tab-btn <?php echo (isset($_GET['status']) && $_GET['status'] === 'rejected') ? 'active' : ''; ?>">Rejected</a>
            </div>

            <!-- Orders Table -->
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer Name</th>
                            <th>Email</th>
                            <th>Total Amount</th>
                            <th>Shipping Address</th>
                            <th>Order Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (is_array($data) && count($data) > 0): ?>
                            <?php foreach ($data as $order): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['id']); ?></td>
                                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($order['customer_email']); ?></td>
                                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td><?php echo htmlspecialchars(substr($order['shipping_address'], 0, 50)); ?></td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($order['order_date'])); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $order['status']; ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td class="action-buttons">
                                        <button class="btn btn-sm btn-info" onclick="viewOrderDetails(<?php echo $order['id']; ?>)">Details</button>
                                        <?php if ($order['status'] === 'pending'): ?>
                                            <button class="btn btn-sm btn-success" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'accepted')">Accept</button>
                                            <button class="btn btn-sm btn-danger" onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'rejected')">Reject</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No orders found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Order Details Modal -->
    <div id="orderModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close-modal" onclick="closeOrderModal()">&times;</span>
            <h2>Order Details</h2>
            <div id="orderDetails"></div>
        </div>
    </div>

    <script src="<?= BASE_PATH ?>/public/admin/js/admin.js"></script>
    <script>
        function updateOrderStatus(orderId, status) {
            if (!confirm(`Are you sure you want to ${status} this order?`)) {
                return;
            }

            const formData = new FormData();
            formData.append('action', 'update_order_status');
            formData.append('order_id', orderId);
            formData.append('status', status);

            fetch('<?= BASE_PATH ?>/index.php?action=update_order_status', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Order status updated successfully');
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred: ' + error.message);
            });
        }

        function viewOrderDetails(orderId) {
            const formData = new FormData();
            formData.append('action', 'get_order_details');
            formData.append('order_id', orderId);

            fetch('<?= BASE_PATH ?>/index.php?action=get_order_details', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let html = `
                        <div class="order-details-content">
                            <p><strong>Order ID:</strong> ${data.order.id}</p>
                            <p><strong>Customer:</strong> ${data.order.customer_name}</p>
                            <p><strong>Email:</strong> ${data.order.customer_email}</p>
                            <p><strong>Total Amount:</strong> $${parseFloat(data.order.total_amount).toFixed(2)}</p>
                            <p><strong>Shipping Address:</strong> ${data.order.shipping_address}</p>
                            <p><strong>Order Date:</strong> ${new Date(data.order.order_date).toLocaleString()}</p>
                            <p><strong>Status:</strong> ${data.order.status}</p>
                            <h3>Items:</h3>
                            <table class="order-items-table">
                                <thead>
                                    <tr>
                                        <th>Medicine</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    
                    data.order.items.forEach(item => {
                        html += `
                            <tr>
                                <td>${item.medicine_name}</td>
                                <td>${item.quantity}</td>
                                <td>$${parseFloat(item.unit_price).toFixed(2)}</td>
                                <td>$${(item.quantity * item.unit_price).toFixed(2)}</td>
                            </tr>
                        `;
                    });

                    html += `
                                </tbody>
                            </table>
                        </div>
                    `;

                    document.getElementById('orderDetails').innerHTML = html;
                    document.getElementById('orderModal').style.display = 'block';
                } else {
                    alert('Error: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred: ' + error.message);
            });
        }

        function closeOrderModal() {
            document.getElementById('orderModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('orderModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
