<?php
/**
 * View: Home – index
 * Online Medicine Shop – Task 1 (23-50009-1)
 *
 * Variables:
 *   $groupedCategories  array ['solid' => [...], 'liquid' => [...]]
 *   $featuredMedicines  array of medicine rows
 *   $allCategories      flat array of all category rows
 *   $vendors            array of vendor name strings
 *   $pageTitle          string
 */
$groupedCategories = $groupedCategories ?? ['solid' => [], 'liquid' => []];
$featuredMedicines = $featuredMedicines ?? [];
$allCategories     = $allCategories     ?? [];
$vendors           = $vendors           ?? [];
$pageTitle         = $pageTitle         ?? 'Home – MediShop';

include __DIR__ . '/../layouts/header.php';

$totalCategories = count($allCategories);
$totalVendors    = count($vendors);
$totalFeatured   = count($featuredMedicines);
$availableCount  = count(array_filter($featuredMedicines, static fn($m) => (bool)($m['availability'] ?? false)));
?>

<!-- ── JS app config ──────────────────────────────────────────────────────── -->
<script>
const APP = {
    basePath:  "<?= BASE_PATH ?>",
    baseUrl:   "<?= BASE_URL ?>",
    isLoggedIn: <?= is_logged_in() ? 'true' : 'false' ?>,
    role:      "<?= e($_SESSION['role'] ?? 'guest') ?>"
};
</script>

<!-- ══════════════════════════════ HERO SECTION ══════════════════════════════ -->
<section class="position-relative overflow-hidden"
         style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 45%, #16a34a 100%);
                min-height: 520px;">

    <!-- Decorative background blobs -->
    <div class="position-absolute rounded-circle"
         style="top:-90px;right:-90px;width:340px;height:340px;
                background:rgba(255,255,255,.05);"></div>
    <div class="position-absolute rounded-circle"
         style="bottom:-70px;left:-70px;width:260px;height:260px;
                background:rgba(255,255,255,.04);"></div>
    <div class="position-absolute rounded-circle"
         style="top:50%;left:40%;width:120px;height:120px;
                background:rgba(255,255,255,.03);"></div>

    <div class="container position-relative py-5">
        <div class="row align-items-center gy-5 py-3">

            <!-- ── Left: copy + search ────────────────────────────────────── -->
            <div class="col-lg-7">
                <span class="badge rounded-pill px-3 py-2 mb-3 d-inline-block"
                      style="background:rgba(255,255,255,.15);color:#fff;
                             font-size:.82rem;letter-spacing:.05em;">
                    <i class="fa-solid fa-star me-1" style="color:#fbbf24;"></i>
                    Trusted by thousands of customers
                </span>

                <h1 class="display-4 fw-bold text-white lh-sm mb-3">
                    Your Health,<br>
                    <span style="color:#86efac;">Our Priority</span>
                </h1>

                <p class="lead mb-4" style="color:rgba(255,255,255,.85);">
                    Browse quality medicines and healthcare products from verified
                    vendors — all in one convenient place.
                </p>

                <!-- Hero search form -->
                <form id="heroSearchForm" autocomplete="off" class="mb-4">
                    <div class="input-group shadow-lg" style="max-width:520px;">
                        <span class="input-group-text bg-white border-0 ps-3">
                            <i class="fa-solid fa-magnifying-glass" style="color:#2563eb;"></i>
                        </span>
                        <input type="text"
                               id="heroSearch"
                               class="form-control form-control-lg border-0 py-3"
                               placeholder="Search medicines, brands&hellip;"
                               autocomplete="off">
                        <button type="submit"
                                class="btn btn-lg fw-semibold text-white border-0 px-4"
                                style="background-color:#16a34a;">
                            <i class="fa-solid fa-search me-1 d-none d-sm-inline"></i>Search
                        </button>
                    </div>
                </form>

                <!-- CTA buttons -->
                <div class="d-flex flex-wrap gap-3">
                    <a href="<?= BASE_PATH ?>/medicines"
                       class="btn btn-light btn-lg fw-semibold rounded-pill px-4 shadow-sm">
                        <i class="fa-solid fa-capsules me-2" style="color:#2563eb;"></i>Browse All
                    </a>
                    <a href="#categories-section"
                       class="btn btn-outline-light btn-lg rounded-pill px-4">
                        <i class="fa-solid fa-th-large me-2"></i>View Categories
                    </a>
                </div>
            </div>

            <!-- ── Right: stat card panel (desktop) ──────────────────────── -->
            <div class="col-lg-5 d-none d-lg-flex justify-content-center">
                <div class="rounded-4 p-4 text-center"
                     style="background:rgba(255,255,255,.1);
                            backdrop-filter:blur(12px);
                            border:1px solid rgba(255,255,255,.2);
                            min-width:280px;">
                    <i class="fa-solid fa-kit-medical mb-3"
                       style="font-size:4.5rem;color:rgba(255,255,255,.9);"></i>
                    <p class="text-white mb-4 small" style="opacity:.8;">
                        Your trusted online pharmacy
                    </p>
                    <div class="row g-2">
                        <div class="col-4">
                            <div class="rounded-3 py-3 px-1"
                                 style="background:rgba(255,255,255,.12);">
                                <div class="fw-bold fs-4 text-white"><?= $totalCategories ?></div>
                                <div class="text-white-50" style="font-size:.72rem;">Categories</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="rounded-3 py-3 px-1"
                                 style="background:rgba(255,255,255,.12);">
                                <div class="fw-bold fs-4 text-white"><?= $availableCount ?>+</div>
                                <div class="text-white-50" style="font-size:.72rem;">In Stock</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="rounded-3 py-3 px-1"
                                 style="background:rgba(255,255,255,.12);">
                                <div class="fw-bold fs-4 text-white"><?= $totalVendors ?>+</div>
                                <div class="text-white-50" style="font-size:.72rem;">Vendors</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stat strip (mobile only) -->
        <div class="row g-3 mt-1 d-lg-none">
            <div class="col-4">
                <div class="rounded-3 text-white text-center py-3"
                     style="background:rgba(255,255,255,.12);">
                    <div class="fw-bold fs-5"><?= $totalCategories ?></div>
                    <div style="font-size:.7rem;opacity:.8;">Categories</div>
                </div>
            </div>
            <div class="col-4">
                <div class="rounded-3 text-white text-center py-3"
                     style="background:rgba(255,255,255,.12);">
                    <div class="fw-bold fs-5"><?= $availableCount ?>+</div>
                    <div style="font-size:.7rem;opacity:.8;">In Stock</div>
                </div>
            </div>
            <div class="col-4">
                <div class="rounded-3 text-white text-center py-3"
                     style="background:rgba(255,255,255,.12);">
                    <div class="fw-bold fs-5"><?= $totalVendors ?>+</div>
                    <div style="font-size:.7rem;opacity:.8;">Vendors</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Smooth SVG wave -->
<div style="margin-top:-2px;line-height:0;background:#f0f4f8;">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 60"
         preserveAspectRatio="none" style="width:100%;height:60px;display:block;">
        <path fill="#fff"
              d="M0,32 C360,64 1080,0 1440,32 L1440,60 L0,60 Z"/>
    </svg>
</div>

<!-- ════════════════════════════ CATEGORIES SECTION ══════════════════════════ -->
<section id="categories-section" class="py-5" style="background:#fff;">
    <div class="container">

        <!-- Section heading -->
        <div class="text-center mb-5">
            <span class="badge rounded-pill px-3 py-2 mb-2"
                  style="background-color:#dbeafe;color:#2563eb;font-size:.8rem;">
                <i class="fa-solid fa-folder-open me-1"></i>CATEGORIES
            </span>
            <h2 class="fw-bold mb-1" style="color:#1e293b;">Browse by Category</h2>
            <p class="text-muted mb-0">Find exactly what you need from our organised medicine library</p>
        </div>

        <div class="row g-4">

            <!-- ── Solid Medicines ────────────────────────────────────────── -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header border-0 rounded-top-4 py-3 px-4"
                         style="background:linear-gradient(135deg,#2563eb 0%,#1d4ed8 100%);">
                        <div class="d-flex align-items-center gap-2">
                            <span class="d-inline-flex align-items-center justify-content-center
                                         rounded-circle bg-white bg-opacity-25"
                                  style="width:38px;height:38px;">
                                <i class="fa-solid fa-pills text-white"></i>
                            </span>
                            <div>
                                <h5 class="mb-0 text-white fw-bold">
                                    Solid Medicines
                                    <span class="badge ms-1 text-primary bg-white"
                                          style="font-size:.65rem;">
                                        <?= count($groupedCategories['solid']) ?>
                                    </span>
                                </h5>
                                <p class="mb-0" style="font-size:.75rem;color:rgba(255,255,255,.75);">
                                    Tablets, capsules, powders &amp; more
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <?php if (empty($groupedCategories['solid'])): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="fa-solid fa-box-open fa-2x mb-2"></i>
                                <p class="mb-0 small">No solid categories available yet.</p>
                            </div>
                        <?php else: ?>
                            <div class="row g-2">
                                <?php foreach ($groupedCategories['solid'] as $cat): ?>
                                    <div class="col-sm-6">
                                        <a href="<?= BASE_PATH ?>/medicines/category/<?= (int)$cat['id'] ?>"
                                           class="text-decoration-none d-block">
                                            <div class="d-flex align-items-center gap-3 p-3
                                                         rounded-3 border category-card bg-white">
                                                <div class="rounded-circle d-flex align-items-center
                                                             justify-content-center flex-shrink-0"
                                                     style="width:40px;height:40px;
                                                            background-color:#dbeafe;">
                                                    <i class="fa-solid fa-pills"
                                                       style="color:#2563eb;"></i>
                                                </div>
                                                <div class="overflow-hidden flex-grow-1">
                                                    <div class="fw-semibold small text-truncate"
                                                         style="color:#1e293b;">
                                                        <?= e($cat['name']) ?>
                                                    </div>
                                                    <span class="badge rounded-pill"
                                                          style="background-color:#dbeafe;
                                                                 color:#2563eb;font-size:.62rem;">
                                                        Solid
                                                    </span>
                                                </div>
                                                <i class="fa-solid fa-chevron-right
                                                           text-muted small flex-shrink-0"></i>
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer border-0 bg-transparent p-3 pt-0">
                        <a href="<?= BASE_PATH ?>/medicines?type=solid"
                           class="btn btn-sm btn-outline-primary rounded-pill w-100">
                            <i class="fa-solid fa-arrow-right me-1"></i>
                            View all solid medicines
                        </a>
                    </div>
                </div>
            </div>

            <!-- ── Liquid Medicines ───────────────────────────────────────── -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header border-0 rounded-top-4 py-3 px-4"
                         style="background:linear-gradient(135deg,#16a34a 0%,#15803d 100%);">
                        <div class="d-flex align-items-center gap-2">
                            <span class="d-inline-flex align-items-center justify-content-center
                                         rounded-circle bg-white bg-opacity-25"
                                  style="width:38px;height:38px;">
                                <i class="fa-solid fa-bottle-droplet text-white"></i>
                            </span>
                            <div>
                                <h5 class="mb-0 text-white fw-bold">
                                    Liquid Medicines
                                    <span class="badge ms-1 bg-white"
                                          style="color:#16a34a;font-size:.65rem;">
                                        <?= count($groupedCategories['liquid']) ?>
                                    </span>
                                </h5>
                                <p class="mb-0"
                                   style="font-size:.75rem;color:rgba(255,255,255,.75);">
                                    Syrups, suspensions, solutions &amp; more
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <?php if (empty($groupedCategories['liquid'])): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="fa-solid fa-box-open fa-2x mb-2"></i>
                                <p class="mb-0 small">No liquid categories available yet.</p>
                            </div>
                        <?php else: ?>
                            <div class="row g-2">
                                <?php foreach ($groupedCategories['liquid'] as $cat): ?>
                                    <div class="col-sm-6">
                                        <a href="<?= BASE_PATH ?>/medicines/category/<?= (int)$cat['id'] ?>"
                                           class="text-decoration-none d-block">
                                            <div class="d-flex align-items-center gap-3 p-3
                                                         rounded-3 border category-card bg-white">
                                                <div class="rounded-circle d-flex align-items-center
                                                             justify-content-center flex-shrink-0"
                                                     style="width:40px;height:40px;
                                                            background-color:#dcfce7;">
                                                    <i class="fa-solid fa-bottle-droplet"
                                                       style="color:#16a34a;"></i>
                                                </div>
                                                <div class="overflow-hidden flex-grow-1">
                                                    <div class="fw-semibold small text-truncate"
                                                         style="color:#1e293b;">
                                                        <?= e($cat['name']) ?>
                                                    </div>
                                                    <span class="badge rounded-pill"
                                                          style="background-color:#dcfce7;
                                                                 color:#16a34a;font-size:.62rem;">
                                                        Liquid
                                                    </span>
                                                </div>
                                                <i class="fa-solid fa-chevron-right
                                                           text-muted small flex-shrink-0"></i>
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer border-0 bg-transparent p-3 pt-0">
                        <a href="<?= BASE_PATH ?>/medicines?type=liquid"
                           class="btn btn-sm rounded-pill w-100 fw-semibold text-white"
                           style="background-color:#16a34a;border-color:#16a34a;">
                            <i class="fa-solid fa-arrow-right me-1"></i>
                            View all liquid medicines
                        </a>
                    </div>
                </div>
            </div>
        </div><!-- /.row categories -->
    </div>
</section>

<!-- ══════════════════════════ FEATURED MEDICINES ════════════════════════════ -->
<section id="medicines-section" class="py-5" style="background:#f0f4f8;">
    <div class="container">

        <!-- Section heading row -->
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div>
                <span class="badge rounded-pill px-3 py-2 mb-2"
                      style="background-color:#dcfce7;color:#16a34a;font-size:.8rem;">
                    <i class="fa-solid fa-star me-1"></i>FEATURED
                </span>
                <h2 class="fw-bold mb-0" style="color:#1e293b;">Featured Medicines</h2>
            </div>
            <a href="<?= BASE_PATH ?>/medicines"
               class="btn btn-outline-primary rounded-pill px-4 fw-semibold mt-1">
                <i class="fa-solid fa-th-large me-2"></i>View All Medicines
            </a>
        </div>

        <!-- ── Filter bar ─────────────────────────────────────────────────── -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3 p-md-4">
                <div class="row g-3 align-items-end">

                    <!-- Name search -->
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-muted mb-1">
                            <i class="fa-solid fa-magnifying-glass me-1"></i>Search
                        </label>
                        <div class="input-group">
                            <input type="text"
                                   id="medicineSearch"
                                   class="form-control"
                                   placeholder="Medicine name&hellip;"
                                   autocomplete="off">
                            <button class="btn btn-outline-secondary border-start-0"
                                    type="button"
                                    id="clearSearch"
                                    title="Clear"
                                    style="display:none;">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Vendor -->
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted mb-1">
                            <i class="fa-solid fa-building me-1"></i>Vendor
                        </label>
                        <select id="vendorFilter" class="form-select">
                            <option value="">All Vendors</option>
                            <?php foreach ($vendors as $v): ?>
                                <option value="<?= e($v) ?>"><?= e($v) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Category / genre -->
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted mb-1">
                            <i class="fa-solid fa-tag me-1"></i>Category
                        </label>
                        <select id="genreFilter" class="form-select">
                            <option value="">All Categories</option>
                            <?php foreach ($allCategories as $cat): ?>
                                <option value="<?= e($cat['name']) ?>"><?= e($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Type radio -->
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted mb-1">
                            <i class="fa-solid fa-filter me-1"></i>Type
                        </label>
                        <div class="d-flex flex-column gap-1">
                            <?php foreach (['all' => 'All', 'solid' => 'Solid', 'liquid' => 'Liquid'] as $val => $label): ?>
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="radio"
                                           name="typeFilter"
                                           id="type_<?= $val ?>"
                                           value="<?= $val === 'all' ? '' : $val ?>"
                                           <?= $val === 'all' ? 'checked' : '' ?>>
                                    <label class="form-check-label small" for="type_<?= $val ?>">
                                        <?= $label ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results meta -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <span class="text-muted small" id="resultsCount">
                Showing <strong><?= $totalFeatured ?></strong>
                medicine<?= $totalFeatured !== 1 ? 's' : '' ?>
            </span>
            <div id="searchSpinner"
                 class="spinner-border spinner-border-sm text-primary d-none"
                 role="status">
                <span class="visually-hidden">Searching&hellip;</span>
            </div>
        </div>

        <!-- ── Medicine grid ──────────────────────────────────────────────── -->
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-4"
             id="medicineGrid">
            <?php foreach ($featuredMedicines as $med): ?>
                <?php
                    $isAvailable = (bool)($med['availability'] ?? false);
                    $isCustomer  = is_logged_in() && ($_SESSION['role'] ?? '') === 'customer';
                    $isLoggedIn  = is_logged_in();
                    $isAdmin     = is_logged_in() && ($_SESSION['role'] ?? '') === 'admin';
                    $imgSrc      = !empty($med['image_path'])
                                   ? BASE_URL . '/public/' . $med['image_path']
                                   : null;
                    $isLiquid    = ($med['category_type'] ?? '') === 'liquid';
                ?>
                <div class="col medicine-item"
                     data-type="<?= e($med['category_type'] ?? '') ?>"
                     data-vendor="<?= e(strtolower($med['vendor_name'] ?? '')) ?>"
                     data-category="<?= e(strtolower($med['category_name'] ?? '')) ?>"
                     data-name="<?= e(strtolower($med['name'] ?? '')) ?>">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden medicine-card">

                        <!-- Image area -->
                        <div class="position-relative"
                             style="height:180px;overflow:hidden;background:#f8fafc;">
                            <?php if ($imgSrc): ?>
                                <img src="<?= e($imgSrc) ?>"
                                     alt="<?= e($med['name']) ?>"
                                     class="w-100 h-100"
                                     style="object-fit:cover;"
                                     loading="lazy"
                                     onerror="this.style.display='none';
                                              this.nextElementSibling.style.display='flex';">
                                <div class="w-100 h-100 d-none align-items-center
                                             justify-content-center"
                                     style="font-size:4rem;position:absolute;
                                            top:0;left:0;background:#f8fafc;">
                                    &#128138;
                                </div>
                            <?php else: ?>
                                <div class="w-100 h-100 d-flex align-items-center
                                             justify-content-center"
                                     style="font-size:4rem;">
                                    &#128138;
                                </div>
                            <?php endif; ?>

                            <!-- Type badge -->
                            <span class="position-absolute top-0 start-0 m-2 badge rounded-pill"
                                  style="<?= $isLiquid
                                      ? 'background-color:#dcfce7;color:#16a34a;'
                                      : 'background-color:#dbeafe;color:#2563eb;' ?>">
                                <i class="fa-solid <?= $isLiquid
                                    ? 'fa-bottle-droplet' : 'fa-pills' ?> me-1"></i>
                                <?= $isLiquid ? 'Liquid' : 'Solid' ?>
                            </span>

                            <!-- Stock badge -->
                            <span class="position-absolute top-0 end-0 m-2 badge rounded-pill"
                                  style="<?= $isAvailable
                                      ? 'background-color:#dcfce7;color:#16a34a;'
                                      : 'background-color:#fee2e2;color:#dc2626;' ?>">
                                <?= $isAvailable ? 'In Stock' : 'Out of Stock' ?>
                            </span>
                        </div>

                        <!-- Card body -->
                        <div class="card-body d-flex flex-column p-3">
                            <h6 class="fw-semibold mb-2 lh-sm"
                                style="color:#1e293b;display:-webkit-box;
                                       -webkit-line-clamp:2;-webkit-box-orient:vertical;
                                       overflow:hidden;">
                                <?= e($med['name']) ?>
                            </h6>

                            <!-- Meta badges -->
                            <div class="d-flex flex-wrap gap-1 mb-2">
                                <?php if (!empty($med['vendor_name'])): ?>
                                    <span class="badge rounded-pill"
                                          style="background:#f1f5f9;color:#475569;
                                                 font-size:.68rem;">
                                        <i class="fa-solid fa-building me-1"></i>
                                        <?= e($med['vendor_name']) ?>
                                    </span>
                                <?php endif; ?>
                                <?php if (!empty($med['category_name'])): ?>
                                    <span class="badge rounded-pill"
                                          style="background:#f3f4f6;color:#6b7280;
                                                 font-size:.68rem;">
                                        <i class="fa-solid fa-tag me-1"></i>
                                        <?= e($med['category_name']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($med['description'])): ?>
                                <p class="text-muted mb-2"
                                   style="font-size:.77rem;display:-webkit-box;
                                          -webkit-line-clamp:2;-webkit-box-orient:vertical;
                                          overflow:hidden;">
                                    <?= e($med['description']) ?>
                                </p>
                            <?php endif; ?>

                            <!-- Price + button -->
                            <div class="mt-auto">
                                <div class="mb-2">
                                    <span class="fw-bold fs-5" style="color:#16a34a;">
                                        $<?= number_format((float)($med['price'] ?? 0), 2) ?>
                                    </span>
                                </div>

                                <?php if ($isCustomer && $isAvailable): ?>
                                    <a href="<?= BASE_PATH ?>/cart/add/<?= (int)$med['id'] ?>"
                                       class="btn btn-sm w-100 fw-semibold text-white"
                                       style="background-color:#16a34a;border-color:#16a34a;">
                                        <i class="fa-solid fa-cart-plus me-1"></i>Add to Cart
                                    </a>
                                <?php elseif ($isCustomer): ?>
                                    <button class="btn btn-sm w-100 btn-outline-secondary" disabled>
                                        <i class="fa-solid fa-ban me-1"></i>Out of Stock
                                    </button>
                                <?php elseif ($isAdmin): ?>
                                    <button class="btn btn-sm w-100 btn-outline-secondary" disabled>
                                        <i class="fa-solid fa-eye me-1"></i>Admin View
                                    </button>
                                <?php else: ?>
                                    <a href="<?= BASE_PATH ?>/login"
                                       class="btn btn-sm w-100 btn-outline-primary fw-semibold">
                                        <i class="fa-solid fa-right-to-bracket me-1"></i>Login to Buy
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div><!-- /#medicineGrid -->

        <!-- Empty / no-results state -->
        <div id="noResults"
             class="text-center py-5 <?= empty($featuredMedicines) ? '' : 'd-none' ?>">
            <div class="mb-3" style="font-size:4rem;">&#128269;</div>
            <h5 class="fw-semibold" style="color:#1e293b;">No medicines found</h5>
            <p class="text-muted">Try adjusting your search or filter criteria.</p>
            <button id="resetFilters" class="btn btn-outline-primary rounded-pill px-4">
                <i class="fa-solid fa-rotate-left me-2"></i>Reset Filters
            </button>
        </div>

        <!-- Bottom CTA -->
        <div class="text-center mt-5">
            <a href="<?= BASE_PATH ?>/medicines"
               class="btn btn-primary btn-lg rounded-pill px-5 fw-semibold shadow-sm">
                <i class="fa-solid fa-th-large me-2"></i>View All Medicines
            </a>
        </div>
    </div>
</section>

<!-- ══════════════════════════ WHY CHOOSE US ════════════════════════════════ -->
<section class="py-5" style="background:#fff;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold" style="color:#1e293b;">Why Choose MediShop?</h2>
            <p class="text-muted">We make healthcare accessible, affordable and convenient</p>
        </div>
        <div class="row g-4">
            <?php
            $features = [
                ['icon'=>'fa-shield-halved', 'bg'=>'#dbeafe', 'color'=>'#2563eb',
                 'title'=>'Certified Quality',
                 'desc'=>'All medicines sourced from verified and certified manufacturers.'],
                ['icon'=>'fa-truck-fast', 'bg'=>'#dcfce7', 'color'=>'#16a34a',
                 'title'=>'Fast Delivery',
                 'desc'=>'Quick and reliable delivery to your doorstep when you need it.'],
                ['icon'=>'fa-tag', 'bg'=>'#fef3c7', 'color'=>'#d97706',
                 'title'=>'Best Prices',
                 'desc'=>'Competitive pricing with regular discounts for our valued customers.'],
                ['icon'=>'fa-headset', 'bg'=>'#fce7f3', 'color'=>'#db2777',
                 'title'=>'24/7 Support',
                 'desc'=>'Expert pharmacists available around the clock for guidance.'],
            ];
            foreach ($features as $f): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 p-4 text-center">
                        <div class="mb-3">
                            <span class="d-inline-flex align-items-center
                                          justify-content-center rounded-circle"
                                  style="width:64px;height:64px;
                                         background-color:<?= $f['bg'] ?>;">
                                <i class="fa-solid <?= $f['icon'] ?> fa-lg"
                                   style="color:<?= $f['color'] ?>;"></i>
                            </span>
                        </div>
                        <h6 class="fw-bold mb-2"><?= $f['title'] ?></h6>
                        <p class="text-muted small mb-0"><?= $f['desc'] ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
.medicine-card { transition: transform .2s ease, box-shadow .2s ease; }
.medicine-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 16px 32px rgba(0,0,0,.12) !important;
}
.category-card {
    transition: all .18s ease;
    cursor: pointer;
}
.category-card:hover {
    background-color: #f0f4f8 !important;
    border-color: #2563eb !important;
}
</style>

<script>
(function () {
    'use strict';

    /* ── Cache original grid so we can restore after clearing filters ────── */
    var grid          = document.getElementById('medicineGrid');
    var noResults     = document.getElementById('noResults');
    var resultsCount  = document.getElementById('resultsCount');
    var spinner       = document.getElementById('searchSpinner');
    var searchInput   = document.getElementById('medicineSearch');
    var vendorSel     = document.getElementById('vendorFilter');
    var genreSel      = document.getElementById('genreFilter');
    var clearSearchBtn= document.getElementById('clearSearch');
    var resetFiltersBtn=document.getElementById('resetFilters');
    var heroForm      = document.getElementById('heroSearchForm');
    var heroInput     = document.getElementById('heroSearch');
    var typeRadios    = document.querySelectorAll('input[name="typeFilter"]');

    var originalHTML  = grid ? grid.innerHTML : '';
    var debounceTimer = null;
    var currentData   = null;   // null = showing original PHP render
    var DEBOUNCE_MS   = 380;

    /* ── Hero search ─────────────────────────────────────────────────────── */
    if (heroForm && heroInput && searchInput) {
        heroForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var q = heroInput.value.trim();
            searchInput.value = q;
            if (clearSearchBtn) clearSearchBtn.style.display = q ? '' : 'none';
            var section = document.getElementById('medicines-section');
            if (section) {
                section.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
            triggerSearch();
        });
    }

    /* ── Clear search ────────────────────────────────────────────────────── */
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function () {
            if (searchInput) searchInput.value = '';
            this.style.display = 'none';
            triggerSearch();
        });
    }

    /* ── Search input ────────────────────────────────────────────────────── */
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            if (clearSearchBtn) {
                clearSearchBtn.style.display = this.value ? '' : 'none';
            }
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(triggerSearch, DEBOUNCE_MS);
        });
    }

    /* ── Dropdowns ───────────────────────────────────────────────────────── */
    [vendorSel, genreSel].forEach(function (el) {
        if (el) el.addEventListener('change', triggerSearch);
    });

    /* ── Type radios ─────────────────────────────────────────────────────── */
    typeRadios.forEach(function (r) {
        r.addEventListener('change', function () {
            if (currentData !== null) {
                renderGrid(currentData);
            } else {
                applyTypeToOriginal();
            }
        });
    });

    /* ── Reset filters ───────────────────────────────────────────────────── */
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', resetAll);
    }

    function resetAll() {
        if (searchInput)   searchInput.value = '';
        if (clearSearchBtn) clearSearchBtn.style.display = 'none';
        if (vendorSel)     vendorSel.value = '';
        if (genreSel)      genreSel.value  = '';
        var allRadio = document.getElementById('type_all');
        if (allRadio) allRadio.checked = true;
        currentData = null;
        restoreOriginal();
    }

    /* ── Get active type ─────────────────────────────────────────────────── */
    function getType() {
        for (var i = 0; i < typeRadios.length; i++) {
            if (typeRadios[i].checked) return typeRadios[i].value;
        }
        return '';
    }

    /* ── Trigger AJAX search ─────────────────────────────────────────────── */
    function triggerSearch() {
        var q      = searchInput  ? searchInput.value.trim()  : '';
        var vendor = vendorSel    ? vendorSel.value.trim()    : '';
        var genre  = genreSel     ? genreSel.value.trim()     : '';

        if (q === '' && vendor === '' && genre === '') {
            currentData = null;
            restoreOriginal();
            return;
        }

        if (spinner) spinner.classList.remove('d-none');

        var url = APP.basePath + '/api/medicines/search'
                + '?q='      + encodeURIComponent(q)
                + '&vendor=' + encodeURIComponent(vendor)
                + '&genre='  + encodeURIComponent(genre);

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                currentData = data.medicines || [];
                renderGrid(currentData);
            })
            .catch(function () {
                currentData = [];
                renderGrid([]);
            })
            .finally(function () {
                if (spinner) spinner.classList.add('d-none');
            });
    }

    /* ── Restore original PHP-rendered cards ─────────────────────────────── */
    function restoreOriginal() {
        if (grid) grid.innerHTML = originalHTML;
        applyTypeToOriginal();
    }

    /* ── Filter original PHP cards by type ───────────────────────────────── */
    function applyTypeToOriginal() {
        var type  = getType();
        var items = grid ? grid.querySelectorAll('.medicine-item') : [];
        var count = 0;
        items.forEach(function (item) {
            var match = !type || item.dataset.type === type;
            item.classList.toggle('d-none', !match);
            if (match) count++;
        });
        updateCount(count);
        toggleEmpty(count === 0);
    }

    /* ── Render from AJAX data ───────────────────────────────────────────── */
    function renderGrid(medicines) {
        var type     = getType();
        var filtered = type
            ? medicines.filter(function (m) { return m.category_type === type; })
            : medicines;

        if (grid) {
            grid.innerHTML = filtered.length ? filtered.map(buildCard).join('') : '';
        }
        updateCount(filtered.length);
        toggleEmpty(filtered.length === 0);
    }

    /* ── Build a card HTML string from an API result row ─────────────────── */
    function buildCard(m) {
        var isLiq  = m.category_type === 'liquid';
        var inStock= parseInt(m.availability, 10) > 0;
        var price  = '$' + parseFloat(m.price).toFixed(2);

        var imgHtml = m.image_path
            ? '<img src="' + APP.baseUrl + '/public/' + esc(m.image_path) + '" '
              + 'alt="' + esc(m.name) + '" class="w-100 h-100" style="object-fit:cover;" loading="lazy" '
              + 'onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'flex\'">'
              + '<div class="w-100 h-100 d-none align-items-center justify-content-center" '
              + 'style="font-size:4rem;position:absolute;top:0;left:0;background:#f8fafc;">&#128138;</div>'
            : '<div class="w-100 h-100 d-flex align-items-center justify-content-center" '
              + 'style="font-size:4rem;">&#128138;</div>';

        var typeBadge = '<span class="position-absolute top-0 start-0 m-2 badge rounded-pill" '
            + 'style="' + (isLiq ? 'background-color:#dcfce7;color:#16a34a;' : 'background-color:#dbeafe;color:#2563eb;') + '">'
            + '<i class="fa-solid ' + (isLiq ? 'fa-bottle-droplet' : 'fa-pills') + ' me-1"></i>'
            + (isLiq ? 'Liquid' : 'Solid') + '</span>';

        var stockBadge = '<span class="position-absolute top-0 end-0 m-2 badge rounded-pill" '
            + 'style="' + (inStock ? 'background-color:#dcfce7;color:#16a34a;' : 'background-color:#fee2e2;color:#dc2626;') + '">'
            + (inStock ? 'In Stock' : 'Out of Stock') + '</span>';

        var vendorBadge = m.vendor_name
            ? '<span class="badge rounded-pill" style="background:#f1f5f9;color:#475569;font-size:.68rem;">'
              + '<i class="fa-solid fa-building me-1"></i>' + esc(m.vendor_name) + '</span>'
            : '';
        var catBadge = m.category_name
            ? '<span class="badge rounded-pill" style="background:#f3f4f6;color:#6b7280;font-size:.68rem;">'
              + '<i class="fa-solid fa-tag me-1"></i>' + esc(m.category_name) + '</span>'
            : '';

        var btnHtml;
        if (APP.isLoggedIn && APP.role === 'customer' && inStock) {
            btnHtml = '<a href="' + APP.basePath + '/cart/add/' + m.id + '" '
                    + 'class="btn btn-sm w-100 fw-semibold text-white" '
                    + 'style="background-color:#16a34a;border-color:#16a34a;">'
                    + '<i class="fa-solid fa-cart-plus me-1"></i>Add to Cart</a>';
        } else if (APP.isLoggedIn && APP.role === 'customer') {
            btnHtml = '<button class="btn btn-sm w-100 btn-outline-secondary" disabled>'
                    + '<i class="fa-solid fa-ban me-1"></i>Out of Stock</button>';
        } else if (APP.isLoggedIn && APP.role === 'admin') {
            btnHtml = '<button class="btn btn-sm w-100 btn-outline-secondary" disabled>'
                    + '<i class="fa-solid fa-eye me-1"></i>Admin View</button>';
        } else {
            btnHtml = '<a href="' + APP.basePath + '/login" '
                    + 'class="btn btn-sm w-100 btn-outline-primary fw-semibold">'
                    + '<i class="fa-solid fa-right-to-bracket me-1"></i>Login to Buy</a>';
        }

        return '<div class="col medicine-item"'
             + ' data-type="' + esc(m.category_type) + '"'
             + ' data-vendor="' + esc((m.vendor_name||'').toLowerCase()) + '"'
             + ' data-category="' + esc((m.category_name||'').toLowerCase()) + '"'
             + ' data-name="' + esc(m.name.toLowerCase()) + '">'
             + '<div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden medicine-card">'
             + '<div class="position-relative" style="height:180px;overflow:hidden;background:#f8fafc;">'
             + imgHtml + typeBadge + stockBadge + '</div>'
             + '<div class="card-body d-flex flex-column p-3">'
             + '<h6 class="fw-semibold mb-2 lh-sm" style="color:#1e293b;display:-webkit-box;'
             + '-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">'
             + esc(m.name) + '</h6>'
             + '<div class="d-flex flex-wrap gap-1 mb-2">' + vendorBadge + catBadge + '</div>'
             + '<div class="mt-auto"><div class="mb-2">'
             + '<span class="fw-bold fs-5" style="color:#16a34a;">' + price + '</span>'
             + '</div>' + btnHtml + '</div>'
             + '</div></div></div>';
    }

    /* ── Helpers ─────────────────────────────────────────────────────────── */
    function updateCount(n) {
        if (resultsCount) {
            resultsCount.innerHTML = 'Showing <strong>' + n + '</strong> medicine'
                                   + (n !== 1 ? 's' : '');
        }
    }

    function toggleEmpty(show) {
        if (noResults) noResults.classList.toggle('d-none', !show);
        if (grid)      grid.classList.toggle('d-none',      show);
    }

    function esc(str) {
        return String(str)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
            .replace(/'/g, '&#x27;');
    }

    /* ── Initialise ──────────────────────────────────────────────────────── */
    applyTypeToOriginal();

}());
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
