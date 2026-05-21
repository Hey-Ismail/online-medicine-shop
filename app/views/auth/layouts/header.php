<?php
/**
 * Layout: Header
 * Online Medicine Shop – Task 1 (23-50009-1)
 *
 * Included at the top of every view.
 *
 * Expected variable:
 *   $pageTitle (string) – used in <title> tag
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="MediShop – Your trusted online pharmacy for all your medicine needs.">
    <title><?= isset($pageTitle) ? e($pageTitle) : 'MediShop' ?></title>

    <!-- ── Bootstrap 5 CSS ──────────────────────────────────────────────────── -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <!-- ── Font Awesome 6 ───────────────────────────────────────────────────── -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
          referrerpolicy="no-referrer">

    <!-- ── Custom Stylesheet ────────────────────────────────────────────────── -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/auth/css/style.css">

    <style>
        /* Inline base tokens so the page looks correct even before style.css loads */
        :root {
            --ms-primary : #2563eb;
            --ms-green   : #16a34a;
            --ms-bg      : #f0f4f8;
            --ms-dark    : #1e293b;
        }
        body { background-color: var(--ms-bg); }

        /* Navbar link hover brightening */
        .nav-link.text-white-50:hover,
        .nav-link.text-white-50:focus { color: #fff !important; }

        /* Footer link hover */
        .footer-link:hover { color: var(--ms-green) !important; }
    </style>
</head>
<body data-base-path="<?= BASE_PATH ?>" data-base-url="<?= BASE_URL ?>">

<!-- ═══════════════════════════════════ NAVBAR ═══════════════════════════════ -->
<nav class="navbar navbar-expand-lg sticky-top shadow-sm" style="background-color: #1e293b;">
    <div class="container">

        <!-- ── Brand ── -->
        <a class="navbar-brand fw-bold fs-4 text-white text-decoration-none"
           href="<?= BASE_PATH ?>/">
            <i class="fa-solid fa-pills me-2" style="color: #16a34a;"></i>MediShop
        </a>

        <!-- ── Hamburger toggler ── -->
        <button class="navbar-toggler py-1"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainNavbar"
                aria-controls="mainNavbar"
                aria-expanded="false"
                aria-label="Toggle navigation"
                style="border-color: rgba(255,255,255,.25);">
            <i class="fa-solid fa-bars text-white"></i>
        </button>

        <!-- ── Collapsible menu ── -->
        <div class="collapse navbar-collapse" id="mainNavbar">

            <!-- Left side – always visible -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link text-white-50 px-3" href="<?= BASE_PATH ?>/">
                        <i class="fa-solid fa-house me-1"></i>Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white-50 px-3" href="<?= BASE_PATH ?>/medicines">
                        <i class="fa-solid fa-capsules me-1"></i>Medicines
                    </a>
                </li>
            </ul>

            <!-- Right side – auth-aware -->
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-lg-1">

                <?php if (!is_logged_in()): ?>
                    <!-- ── Guest ─────────────────────────────────────────── -->
                    <li class="nav-item">
                        <a class="nav-link text-white-50" href="<?= BASE_PATH ?>/login">
                            <i class="fa-solid fa-right-to-bracket me-1"></i>Login
                        </a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-sm fw-semibold text-white px-3"
                           href="<?= BASE_PATH ?>/register"
                           style="background-color: #16a34a; border-color: #16a34a;">
                            <i class="fa-solid fa-user-plus me-1"></i>Register
                        </a>
                    </li>

                <?php elseif (($_SESSION['role'] ?? '') === 'admin'): ?>
                    <!-- ── Admin ──────────────────────────────────────────── -->
                    <li class="nav-item d-flex align-items-center me-lg-2">
                        <span class="badge text-bg-warning rounded-pill px-3 py-2">
                            <i class="fa-solid fa-shield-halved me-1"></i>Admin Panel
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white-50" href="<?= BASE_PATH ?>/profile">
                            <i class="fa-solid fa-circle-user me-1"></i>
                            <?= e($_SESSION['name'] ?? 'Profile') ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_PATH ?>/logout"
                           style="color: #f87171;">
                            <i class="fa-solid fa-right-from-bracket me-1"></i>Logout
                        </a>
                    </li>

                <?php else: ?>
                    <!-- ── Customer ───────────────────────────────────────── -->
                    <li class="nav-item">
                        <a class="nav-link text-white-50" href="<?= BASE_PATH ?>/profile">
                            <i class="fa-solid fa-circle-user me-1"></i>
                            <?= e($_SESSION['name'] ?? 'Profile') ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white-50 position-relative"
                           href="<?= BASE_PATH ?>/cart"
                           title="My Cart">
                            <i class="fa-solid fa-cart-shopping fs-5"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_PATH ?>/logout"
                           style="color: #f87171;">
                            <i class="fa-solid fa-right-from-bracket me-1"></i>Logout
                        </a>
                    </li>

                <?php endif; ?>

            </ul><!-- /.navbar-nav right -->
        </div><!-- /.collapse -->
    </div><!-- /.container -->
</nav>

<!-- ═════════════════════════════ MAIN CONTENT ═══════════════════════════════ -->
<main class="py-4" style="background-color: #f0f4f8; min-height: 80vh;">

    <?php
    // ── Flash message (consumed once) ──────────────────────────────────────
    $flash = get_flash();
    if ($flash):
        $fType = e($flash['type']);   // e.g. success | danger | warning | info
        $fMsg  = e($flash['msg']);
    ?>
    <div class="container mb-2">
        <div class="alert alert-<?= $fType ?> alert-dismissible fade show shadow-sm"
             role="alert"
             id="flash-alert">
            <?php if ($fType === 'success'): ?>
                <i class="fa-solid fa-circle-check me-2"></i>
            <?php elseif ($fType === 'danger'): ?>
                <i class="fa-solid fa-circle-xmark me-2"></i>
            <?php elseif ($fType === 'warning'): ?>
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
            <?php else: ?>
                <i class="fa-solid fa-circle-info me-2"></i>
            <?php endif; ?>
            <?= $fMsg ?>
            <button type="button" class="btn-close"
                    data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Individual views render their content below this line -->
