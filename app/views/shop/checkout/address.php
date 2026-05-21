<?php
declare(strict_types=1);
?>
<section class="page">
    <div class="page-header">
        <h1>Checkout - Address</h1>
        <p class="muted">Confirm your delivery address.</p>
    </div>

    <form id="checkout-address-form" class="form-card" method="post" action="<?= $baseUrl ?>/checkout/address">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <label for="shipping_address">Shipping address</label>
        <textarea id="shipping_address" name="shipping_address" rows="4" required><?= htmlspecialchars((string)$address, ENT_QUOTES, 'UTF-8') ?></textarea>
        <?php if (!empty($errors['shipping_address'])): ?>
            <p class="form-error"><?= htmlspecialchars($errors['shipping_address'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <div class="form-actions">
            <button class="btn" type="submit">Continue to Invoice</button>
            <a class="btn-secondary" href="<?= $baseUrl ?>/cart">Back to Cart</a>
        </div>
    </form>
</section>
