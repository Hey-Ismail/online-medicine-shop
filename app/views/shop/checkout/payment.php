<?php
declare(strict_types=1);
?>
<section class="page">
    <div class="page-header">
        <h1>Payment Method</h1>
        <p class="muted">Select how you want to pay.</p>
    </div>

    <div class="summary-card">
        <div class="summary-row total">
            <span>Total</span>
            <span>BDT <?= number_format((float)$totals['total_amount'], 2) ?></span>
        </div>
    </div>

    <form id="payment-form" class="form-card" method="post" action="<?= $baseUrl ?>/checkout/payment">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <div class="radio-group">
            <label><input type="radio" name="payment_method" value="Credit Card">Credit Card</label>
            <label><input type="radio" name="payment_method" value="bKash">bKash</label>
            <label><input type="radio" name="payment_method" value="Nagad">Nagad</label>
            <label><input type="radio" name="payment_method" value="Bank Transfer">Bank Transfer</label>
            <label><input type="radio" name="payment_method" value="Cash on Delivery">Cash on Delivery</label>
        </div>
        <?php if (!empty($errors['payment'])): ?>
            <p class="form-error"><?= htmlspecialchars($errors['payment'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <div class="form-actions">
            <button class="btn" type="submit">Place Order</button>
            <a class="btn-secondary" href="<?= $baseUrl ?>/checkout/invoice">Back to Invoice</a>
        </div>
    </form>
</section>
