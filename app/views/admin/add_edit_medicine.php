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
    <title><?php echo isset($data['medicine']) ? 'Edit' : 'Add'; ?> Medicine - Medicine Shop</title>
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
                <h1><?php echo isset($data['medicine']) ? 'Edit' : 'Add'; ?> Medicine</h1>
                <a href="<?= BASE_PATH ?>/index.php?action=manage_medicines" class="btn btn-secondary">Back to Medicines</a>
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
                <form method="POST" enctype="multipart/form-data" class="admin-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Medicine Name *</label>
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                placeholder="e.g., Aspirin 500mg"
                                value="<?php echo isset($data['medicine']) ? htmlspecialchars($data['medicine']['name']) : ''; ?>"
                                required
                                minlength="2"
                                maxlength="150"
                            >
                            <span class="error-message" id="name-error"></span>
                        </div>

                        <div class="form-group">
                            <label for="category_id">Category *</label>
                            <select id="category_id" name="category_id" required>
                                <option value="">-- Select Category --</option>
                                <?php foreach ($data['categories'] as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" 
                                        <?php echo (isset($data['medicine']) && $data['medicine']['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="error-message" id="category-error"></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="vendor_name">Vendor Name *</label>
                            <input 
                                type="text" 
                                id="vendor_name" 
                                name="vendor_name" 
                                placeholder="e.g., Pharma Inc."
                                value="<?php echo isset($data['medicine']) ? htmlspecialchars($data['medicine']['vendor_name']) : ''; ?>"
                                required
                                maxlength="100"
                            >
                            <span class="error-message" id="vendor-error"></span>
                        </div>

                        <div class="form-group">
                            <label for="price">Price (USD) *</label>
                            <input 
                                type="number" 
                                id="price" 
                                name="price" 
                                placeholder="0.00"
                                step="0.01"
                                min="0.01"
                                value="<?php echo isset($data['medicine']) ? htmlspecialchars($data['medicine']['price']) : ''; ?>"
                                required
                            >
                            <span class="error-message" id="price-error"></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="availability">Stock Availability *</label>
                            <input 
                                type="number" 
                                id="availability" 
                                name="availability" 
                                placeholder="0"
                                min="0"
                                value="<?php echo isset($data['medicine']) ? htmlspecialchars($data['medicine']['availability']) : ''; ?>"
                                required
                            >
                            <span class="error-message" id="stock-error"></span>
                        </div>

                        <div class="form-group">
                            <label for="image">Medicine Image (JPEG/PNG, Max 2MB)</label>
                            <input 
                                type="file" 
                                id="image" 
                                name="image" 
                                accept=".jpeg,.jpg,.png"
                            >
                            <span class="error-message" id="image-error"></span>
                            <?php if (isset($data['medicine']) && $data['medicine']['image_path']): ?>
                                <div class="current-image">
                                    <p>Current Image:</p>
                                    <img src="/<?php echo htmlspecialchars($data['medicine']['image_path']); ?>" alt="Medicine">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea 
                            id="description" 
                            name="description" 
                            placeholder="Enter medicine description"
                            rows="5"
                            maxlength="1000"
                        ><?php echo isset($data['medicine']) ? htmlspecialchars($data['medicine']['description']) : ''; ?></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">
                            <?php echo isset($data['medicine']) ? 'Update Medicine' : 'Add Medicine'; ?>
                        </button>
                        <a href="<?= BASE_PATH ?>/index.php?action=manage_medicines" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script src="<?= BASE_PATH ?>/public/admin/js/validation.js"></script>
    <script>
        // Client-side validation for medicine form
        document.querySelector('.admin-form').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const categoryId = document.getElementById('category_id').value;
            const vendorName = document.getElementById('vendor_name').value.trim();
            const price = parseFloat(document.getElementById('price').value);
            const availability = parseInt(document.getElementById('availability').value);
            const image = document.getElementById('image');
            let isValid = true;

            // Validate name
            if (!name || name.length < 2) {
                document.getElementById('name-error').textContent = 'Medicine name must be at least 2 characters';
                isValid = false;
            } else {
                document.getElementById('name-error').textContent = '';
            }

            // Validate category
            if (!categoryId) {
                document.getElementById('category-error').textContent = 'Please select a category';
                isValid = false;
            } else {
                document.getElementById('category-error').textContent = '';
            }

            // Validate vendor
            if (!vendorName) {
                document.getElementById('vendor-error').textContent = 'Vendor name is required';
                isValid = false;
            } else {
                document.getElementById('vendor-error').textContent = '';
            }

            // Validate price
            if (isNaN(price) || price <= 0) {
                document.getElementById('price-error').textContent = 'Price must be greater than 0';
                isValid = false;
            } else {
                document.getElementById('price-error').textContent = '';
            }

            // Validate stock
            if (isNaN(availability) || availability < 0) {
                document.getElementById('stock-error').textContent = 'Stock cannot be negative';
                isValid = false;
            } else {
                document.getElementById('stock-error').textContent = '';
            }

            // Validate image if uploaded
            if (image.files.length > 0) {
                const file = image.files[0];
                const allowedTypes = ['image/jpeg', 'image/png'];
                const maxSize = 2 * 1024 * 1024;

                if (!allowedTypes.includes(file.type)) {
                    document.getElementById('image-error').textContent = 'Only JPEG and PNG images are allowed';
                    isValid = false;
                } else if (file.size > maxSize) {
                    document.getElementById('image-error').textContent = 'Image size must not exceed 2MB';
                    isValid = false;
                } else {
                    document.getElementById('image-error').textContent = '';
                }
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
