<?php
declare(strict_types=1);
?>
<section class="page">
    <div class="page-header">
        <h1>My Cart</h1>
        <p class="muted">Review your items and adjust quantities.</p>
    </div>

    <div class="alert error js-cart-error" hidden></div>

    <?php if (empty($items)): ?>
        <div class="empty-state">
            <p>Your cart is empty.</p>
            <a class="btn" href="<?= $baseUrl ?>/">Browse medicines</a>
        </div>
    <?php else: ?>
        <div class="table-wrap">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Medicine</th>
                        <th>Vendor</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $item): ?>
                    <?php
                        $price = (float)$item['price'];
                        $quantity = (int)$item['quantity'];
                        $subtotal = $price * $quantity;
                    ?>
                    <tr class="js-cart-row"
                        data-medicine-id="<?= (int)$item['medicine_id'] ?>"
                        data-max="<?= (int)$item['availability'] ?>">
                        <td><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($item['vendor_name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>BDT <?= number_format($price, 2) ?></td>
                        <td>
                            <div class="qty-controls">
                                <button class="btn-ghost js-qty-minus" type="button" aria-label="Decrease quantity">-</button>
                                <input class="qty-input js-qty-input" type="number" min="1"
                                       max="<?= (int)$item['availability'] ?>" value="<?= $quantity ?>">
                                <button class="btn-ghost js-qty-plus" type="button" aria-label="Increase quantity">+</button>
                            </div>
                            <small class="muted">Stock: <?= (int)$item['availability'] ?></small>
                        </td>
                        <td>BDT <span class="js-subtotal"><?= number_format($subtotal, 2) ?></span></td>
                        <td>
                            <button class="link-danger js-remove-item" type="button">Remove</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="summary-card">
            <div class="summary-row">
                <span>Total items</span>
                <span class="js-cart-count-inline"><?= (int)$totals['item_count'] ?></span>
            </div>
            <div class="summary-row total">
                <span>Total</span>
                <span>BDT <span class="js-cart-total"><?= number_format((float)$totals['total_amount'], 2) ?></span></span>
            </div>
            <div class="summary-actions">
                <a class="btn" href="<?= $baseUrl ?>/checkout">Proceed to Checkout</a>
            </div>
        </div>
    <?php endif; ?>
</section>
