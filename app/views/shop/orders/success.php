<?php
declare(strict_types=1);

$statusLabel = $order['status'] === 'pending' ? 'Pending admin approval' : (string)$order['status'];
?>
<section class="page">
    <div class="page-header">
        <h1>Order Confirmed</h1>
        <p class="muted">Your order is pending admin approval.</p>
    </div>

    <div class="summary-card">
        <div class="summary-row">
            <span>Order ID</span>
            <span>#<?= (int)$order['id'] ?></span>
        </div>
        <div class="summary-row">
            <span>Status</span>
            <span class="badge"><?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <div class="summary-row total">
            <span>Total</span>
            <span>BDT <?= number_format((float)$order['total_amount'], 2) ?></span>
        </div>
    </div>

    <div class="table-wrap">
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Medicine</th>
                    <th>Vendor</th>
                    <th>Unit Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <?php
                    $price = (float)$item['unit_price'];
                    $quantity = (int)$item['quantity'];
                    $subtotal = $price * $quantity;
                ?>
                <tr>
                    <td><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($item['vendor_name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>BDT <?= number_format($price, 2) ?></td>
                    <td><?= $quantity ?></td>
                    <td>BDT <?= number_format($subtotal, 2) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="form-actions">
        <a class="btn" href="<?= $baseUrl ?>/cart">Back to Cart</a>
    </div>
</section>
