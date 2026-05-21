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
    <title><?php echo isset($data['category']) ? 'Edit' : 'Add'; ?> Category - Medicine Shop</title>
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
                    <li><a href="<?= BASE_PATH ?>/index.php?action=manage_categories" class="nav-item active">Categories</a></li>
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
                <h1><?php echo isset($data['category']) ? 'Edit' : 'Add'; ?> Category</h1>
                <a href="<?= BASE_PATH ?>/index.php?action=manage_categories" class="btn btn-secondary">Back to Categories</a>
            </div>

            <!-- Error Messages -->
            <?php if (isset($data['errors']) && count($data['errors']) > 0): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($data['errors'] as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Success Message -->
            <?php if (isset($data['success'])): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($data['success']); ?></div>
            <?php endif; ?>

            <!-- Form -->
            <div class="form-container">
                <form method="POST" class="admin-form">
                    <div class="form-group">
                        <label for="name">Category Name *</label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            placeholder="e.g., Aspirin Genre"
                            value="<?php echo isset($data['category']) ? htmlspecialchars($data['category']['name']) : ''; ?>"
                            required
                            minlength="2"
                            maxlength="100"
                        >
                        <span class="error-message" id="name-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="category_type">Category Type *</label>
                        <select id="category_type" name="category_type" required>
                            <option value="">-- Select Type --</option>
                            <option value="liquid" <?php echo (isset($data['category']) && $data['category']['category_type'] === 'liquid') ? 'selected' : ''; ?>>Liquid</option>
                            <option value="solid" <?php echo (isset($data['category']) && $data['category']['category_type'] === 'solid') ? 'selected' : ''; ?>>Solid</option>
                        </select>
                        <span class="error-message" id="type-error"></span>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">
                            <?php echo isset($data['category']) ? 'Update Category' : 'Add Category'; ?>
                        </button>
                        <a href="<?= BASE_PATH ?>/index.php?action=manage_categories" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script src="<?= BASE_PATH ?>/public/admin/js/validation.js"></script>
    <script>
        // Client-side validation for category form
        document.querySelector('.admin-form').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const categoryType = document.getElementById('category_type').value;
            let isValid = true;

            // Validate name
            if (!name || name.length < 2) {
                document.getElementById('name-error').textContent = 'Category name must be at least 2 characters';
                isValid = false;
            } else {
                document.getElementById('name-error').textContent = '';
            }

            // Validate type
            if (!categoryType) {
                document.getElementById('type-error').textContent = 'Please select a category type';
                isValid = false;
            } else {
                document.getElementById('type-error').textContent = '';
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
