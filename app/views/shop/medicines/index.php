<?php
declare(strict_types=1);
?>
<section class="page">
    <div class="page-header">
        <h1>Browse Medicines</h1>
        <p class="muted">Filter by vendor or category, then add items to your cart.</p>
    </div>

    <div class="alert error js-cart-error" hidden></div>

    <form class="filter-bar" method="get" action="<?= $baseUrl ?>/medicines">
        <div class="filter-group">
            <label for="vendor">Vendor</label>
            <select id="vendor" name="vendor">
                <option value="">All vendors</option>
                <?php foreach ($vendors as $vendorOption): ?>
                    <option value="<?= htmlspecialchars($vendorOption, ENT_QUOTES, 'UTF-8') ?>"
                        <?= ($filters['vendor'] ?? '') === $vendorOption ? 'selected' : '' ?>>
                        <?= htmlspecialchars($vendorOption, ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group">
            <label for="category_id">Category</label>
            <select id="category_id" name="category_id">
                <option value="">All categories</option>
                <?php foreach ($categories as $category): ?>
                    <?php
                        $label = (string)$category['name'];
                        if (!empty($category['category_type'])) {
                            $label .= ' (' . $category['category_type'] . ')';
                        }
                    ?>
                    <option value="<?= (int)$category['id'] ?>"
                        <?= ((int)($filters['category_id'] ?? 0)) === (int)$category['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-actions">
            <button class="btn" type="submit">Apply Filters</button>
            <a class="btn-secondary" href="<?= $baseUrl ?>/medicines">Reset</a>
        </div>
    </form>

    <?php if (empty($medicines)): ?>
        <div class="empty-state">
            <p>No medicines found for the selected filters.</p>
        </div>
    <?php else: ?>
        <div class="medicine-grid">
            <?php foreach ($medicines as $medicine): ?>
                <?php
                    $available = (int)$medicine['availability'];
                    $disabled = $available <= 0;
                    $maxQty = $available > 0 ? $available : 1;
                    $imagePath = trim((string)$medicine['image_path']);
                    $imageSrc = $imagePath !== '' ? $baseUrl . '/' . ltrim($imagePath, '/') : '';
                    $description = trim((string)$medicine['description']);
                    if (strlen($description) > 140) {
                        $description = substr($description, 0, 137) . '...';
                    }
                ?>
                <div class="medicine-card">
                    <div class="medicine-image">
                        <?php if ($imageSrc !== ''): ?>
                            <img src="<?= htmlspecialchars($imageSrc, ENT_QUOTES, 'UTF-8') ?>"
                                 alt="<?= htmlspecialchars($medicine['name'], ENT_QUOTES, 'UTF-8') ?>">
                        <?php else: ?>
                            <span class="muted">No image</span>
                        <?php endif; ?>
                    </div>
                    <div class="medicine-info">
                        <h3><?= htmlspecialchars($medicine['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p class="muted">Vendor: <?= htmlspecialchars($medicine['vendor_name'], ENT_QUOTES, 'UTF-8') ?></p>
                        <div class="medicine-meta">
                            <span class="medicine-price">BDT <?= number_format((float)$medicine['price'], 2) ?></span>
                            <span class="muted">Stock: <?= $available ?></span>
                        </div>
                        <?php if ($description !== ''): ?>
                            <p class="medicine-desc"><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endif; ?>
                    </div>
                    <form class="add-to-cart-form medicine-actions" method="post" action="<?= $baseUrl ?>/api/cart/add">
                        <input type="hidden" name="medicine_id" value="<?= (int)$medicine['id'] ?>">
                        <div class="qty-controls">
                            <input class="qty-input" type="number" name="quantity" min="1" max="<?= $maxQty ?>"
                                   value="1" <?= $disabled ? 'disabled' : '' ?>>
                        </div>
                        <button class="btn" type="submit" <?= $disabled ? 'disabled' : '' ?>>
                            <?= $disabled ? 'Out of Stock' : 'Add to Cart' ?>
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
