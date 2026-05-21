<?php
declare(strict_types=1);
?>
<section class="page">
    <div class="page-header">
        <h1>Invoice</h1>
        <p class="muted">Review your purchase summary.</p>
    </div>

    <div class="invoice-card">
        <h2>Shipping Address</h2>
        <p><?= nl2br(htmlspecialchars($address, ENT_QUOTES, 'UTF-8')) ?></p>
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
                    $price = (float)$item['price'];
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

    <div class="summary-card">
        <div class="summary-row total">
            <span>Total</span>
            <span>BDT <?= number_format((float)$totals['total_amount'], 2) ?></span>
        </div>
    </div>

    <form method="post" action="<?= $baseUrl ?>/checkout/confirm" class="form-actions">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <button class="btn" type="submit">Confirm Purchase</button>
        <a class="btn-secondary" href="<?= $baseUrl ?>/cart">Cancel</a>
    </form>
</section>
