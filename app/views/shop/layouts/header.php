<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Online Medicine Shop</title>
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/styles.css">
    <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="base-url" content="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
    <header class="site-header">
        <div class="container nav">
            <a class="brand" href="<?= $baseUrl ?>/">MediQuick</a>
            <nav class="nav-links">
                <a href="<?= $baseUrl ?>/medicines">Medicines</a>
                <?php if (Auth::isCustomer()): ?>
                    <a href="<?= $baseUrl ?>/cart">Cart <span class="cart-count js-cart-count"><?= (int)($cartCount ?? 0) ?></span></a>
                    <a href="<?= $baseUrl ?>/checkout">Checkout</a>
                <?php else: ?>
                    <a href="<?= ROOT_BASE_PATH ?>/login">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main class="container">
