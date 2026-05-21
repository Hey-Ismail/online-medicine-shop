<?php
/**
 * View: Medicines – Browse (all or by category)
 * Online Medicine Shop – Task 1 (23-50009-1)
 *
 * Variables:
 *   $medicines       array of medicine rows
 *   $allCategories   flat array of all category rows
 *   $vendors         array of vendor name strings
 *   $activeCategory  null | category row (id, name, category_type)
 *   $pageTitle       string
 */
$medicines      = $medicines      ?? [];
$allCategories  = $allCategories  ?? [];
$vendors        = $vendors        ?? [];
$activeCategory = $activeCategory ?? null;
$pageTitle      = $pageTitle      ?? 'Medicines – MediShop';

include __DIR__ . '/../layouts/header.php';

$typeFilter     = $_GET['type'] ?? '';   // '' | 'solid' | 'liquid'
$totalResults   = count($medicines);

// Base URL for type/category links
$catBase = $activeCategory
    ? BASE_PATH . '/medicines/category/' . (int)$activeCategory['id']
    : BASE_PATH . '/medicines';
?>

<!-- ── JS app config ──────────────────────────────────────────────────────── -->
<script>
const APP = {
    basePath:   "<?= BASE_PATH ?>",
    baseUrl:    "<?= BASE_URL ?>",
    isLoggedIn: <?= is_logged_in() ? 'true' : 'false' ?>,
    role:       "<?= e($_SESSION['role'] ?? 'guest') ?>",
    typeFilter: "<?= e($typeFilter) ?>"
};
</script>

<div class="container py-4">

    <!-- ── Breadcrumb ─────────────────────────────────────────────────────── -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item">
                <a href="<?= BASE_PATH ?>/" class="text-decoration-none"
                   style="color:#2563eb;">
                    <i class="fa-solid fa-house me-1"></i>Home
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= BASE_PATH ?>/medicines" class="text-decoration-none"
                   style="color:#2563eb;">
                    Medicines
                </a>
            </li>
            <?php if ($activeCategory): ?>
                <li class="breadcrumb-item active"><?= e($activeCategory['name']) ?></li>
            <?php elseif ($typeFilter): ?>
                <li class="breadcrumb-item active"><?= ucfirst(e($typeFilter)) ?> Medicines</li>
            <?php endif; ?>
        </ol>
    </nav>

    <!-- ── Page heading + type quick-pills ───────────────────────────────── -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <?php if ($activeCategory): ?>
                <?php $isCatLiquid = $activeCategory['category_type'] === 'liquid'; ?>
                <h2 class="fw-bold mb-1" style="color:#1e293b;">
                    <i class="fa-solid <?= $isCatLiquid
                        ? 'fa-bottle-droplet' : 'fa-pills' ?> me-2"
                       style="color:<?= $isCatLiquid ? '#16a34a' : '#2563eb' ?>;"></i>
                    <?= e($activeCategory['name']) ?>
                </h2>
                <span class="badge rounded-pill px-3 py-2"
                      style="<?= $isCatLiquid
                          ? 'background-color:#dcfce7;color:#16a34a;'
                          : 'background-color:#dbeafe;color:#2563eb;' ?>">
                    <i class="fa-solid <?= $isCatLiquid
                        ? 'fa-bottle-droplet' : 'fa-pills' ?> me-1"></i>
                    <?= ucfirst(e($activeCategory['category_type'])) ?> Medicines
                </span>
            <?php else: ?>
                <h2 class="fw-bold mb-1" style="color:#1e293b;">
                    <i class="fa-solid fa-capsules me-2" style="color:#2563eb;"></i>
                    All Medicines
                </h2>
                <p class="text-muted mb-0 small">Browse our complete catalogue</p>
            <?php endif; ?>
        </div>

        <!-- Type filter pills -->
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?= $catBase ?>"
               class="btn btn-sm rounded-pill fw-semibold
                      <?= $typeFilter === '' ? 'btn-primary' : 'btn-outline-secondary' ?>">
                <i class="fa-solid fa-layer-group me-1"></i>All
            </a>
            <a href="<?= $catBase ?>?type=solid"
               class="btn btn-sm rounded-pill fw-semibold
                      <?= $typeFilter === 'solid' ? 'btn-primary' : 'btn-outline-secondary' ?>">
                <i class="fa-solid fa-pills me-1"></i>Solid
            </a>
            <a href="<?= $catBase ?>?type=liquid"
               class="btn btn-sm rounded-pill fw-semibold
                      <?= $typeFilter === 'liquid' ? 'btn-primary' : 'btn-outline-secondary' ?>">
                <i class="fa-solid fa-bottle-droplet me-1"></i>Liquid
            </a>
        </div>
    </div>

    <div class="row g-4 align-items-start">

        <!-- ═══════════════════════════════ SIDEBAR ═══════════════════════════ -->
        <div class="col-lg-3">

            <!-- Mobile toggle -->
            <div class="d-lg-none mb-3">
                <button class="btn w-100 rounded-pill fw-semibold"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#sidebarCollapse"
                        aria-expanded="false"
                        aria-controls="sidebarCollapse"
                        style="background-color:#dbeafe;color:#2563eb;border-color:#bfdbfe;">
                    <i class="fa-solid fa-sliders me-2"></i>Show Filters
                </button>
            </div>

            <div class="collapse d-lg-block" id="sidebarCollapse">
                <div class="d-flex flex-column gap-3">

                    <!-- ── Category List ──────────────────────────────────── -->
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-header border-0 py-3 px-4"
                             style="background:#f8fafc;">
                            <h6 class="fw-bold mb-0 small" style="color:#1e293b;">
                                <i class="fa-solid fa-folder me-2"
                                   style="color:#2563eb;"></i>Filter by Category
                            </h6>
                        </div>
                        <div class="card-body p-0"
                             style="max-height:340px;overflow-y:auto;">
                            <ul class="list-unstyled mb-0">
                                <!-- All medicines -->
                                <li>
                                    <a href="<?= BASE_PATH ?>/medicines"
                                       class="sidebar-cat-link d-flex align-items-center
                                              gap-2 px-4 py-2 text-decoration-none
                                              <?= !$activeCategory && $typeFilter === ''
                                                  ? 'sidebar-active' : '' ?>"
                                       style="color:#374151;">
                                        <i class="fa-solid fa-border-all small"
                                           style="color:<?= !$activeCategory && $typeFilter === ''
                                               ? '#2563eb' : '#9ca3af' ?>;"></i>
                                        <span class="small <?= !$activeCategory && $typeFilter === ''
                                            ? 'fw-bold' : 'fw-normal' ?>">
                                            All Medicines
                                        </span>
                                        <?php if (!$activeCategory && $typeFilter === ''): ?>
                                            <i class="fa-solid fa-check ms-auto small"
                                               style="color:#2563eb;"></i>
                                        <?php endif; ?>
                                    </a>
                                </li>

                                <!-- Divider -->
                                <?php
                                $lastType = null;
                                foreach ($allCategories as $cat):
                                    $isActive = $activeCategory
                                        && (int)$activeCategory['id'] === (int)$cat['id'];
                                    // Group header
                                    if ($cat['category_type'] !== $lastType):
                                        $lastType = $cat['category_type'];
                                ?>
                                        <li class="px-4 pt-3 pb-1">
                                            <span class="text-uppercase fw-bold"
                                                  style="font-size:.6rem;
                                                         letter-spacing:.08em;
                                                         color:<?= $cat['category_type'] === 'liquid'
                                                             ? '#16a34a' : '#2563eb' ?>;">
                                                <i class="fa-solid <?= $cat['category_type'] === 'liquid'
                                                    ? 'fa-bottle-droplet' : 'fa-pills' ?> me-1"></i>
                                                <?= ucfirst($cat['category_type']) ?>
                                            </span>
                                        </li>
                                <?php endif; ?>

                                    <li>
                                        <a href="<?= BASE_PATH ?>/medicines/category/<?= (int)$cat['id'] ?>"
                                           class="sidebar-cat-link d-flex align-items-center
                                                  gap-2 px-4 py-2 text-decoration-none
                                                  <?= $isActive ? 'sidebar-active' : '' ?>"
                                           style="color:#374151;">
                                            <i class="fa-solid <?= $cat['category_type'] === 'liquid'
                                                ? 'fa-bottle-droplet' : 'fa-pills' ?> small"
                                               style="color:<?= $isActive ? '#2563eb' : '#9ca3af' ?>;"></i>
                                            <span class="small text-truncate
                                                         <?= $isActive ? 'fw-bold' : '' ?>">
                                                <?= e($cat['name']) ?>
                                            </span>
                                            <?php if ($isActive): ?>
                                                <i class="fa-solid fa-check ms-auto small"
                                                   style="color:#2563eb;"></i>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                    <!-- ── Type Filter ────────────────────────────────────── -->
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header border-0 py-3 px-4"
                             style="background:#f8fafc;">
                            <h6 class="fw-bold mb-0 small" style="color:#1e293b;">
                                <i class="fa-solid fa-filter me-2"
                                   style="color:#2563eb;"></i>Filter by Type
                            </h6>
                        </div>
                        <div class="card-body px-3 py-3">
                            <?php
                            $typeLinks = [
                                ''       => ['label' => 'All Types',        'icon' => 'fa-layer-group',    'clr' => '#6b7280'],
                                'solid'  => ['label' => 'Solid Medicines',  'icon' => 'fa-pills',          'clr' => '#2563eb'],
                                'liquid' => ['label' => 'Liquid Medicines', 'icon' => 'fa-bottle-droplet', 'clr' => '#16a34a'],
                            ];
                            foreach ($typeLinks as $tv => $tl):
                                $tActive = ($typeFilter === $tv);
                            ?>
                                <a href="<?= $catBase . ($tv ? '?type=' . $tv : '') ?>"
                                   class="d-flex align-items-center gap-2 px-3 py-2 mb-1
                                          rounded-3 text-decoration-none small fw-semibold"
                                   style="<?= $tActive
                                       ? 'background-color:#dbeafe;color:#2563eb;'
                                       : 'background-color:#f8fafc;color:#374151;' ?>
                                          transition:all .15s;">
                                    <i class="fa-solid <?= $tl['icon'] ?>"
                                       style="color:<?= $tActive ? '#2563eb' : $tl['clr'] ?>;
                                              width:16px;text-align:center;"></i>
                                    <?= $tl['label'] ?>
                                    <?php if ($tActive): ?>
                                        <i class="fa-solid fa-check ms-auto"
                                           style="color:#2563eb;"></i>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- ── Vendor Checkboxes ──────────────────────────────── -->
                    <?php if (!empty($vendors)): ?>
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header border-0 py-3 px-4"
                             style="background:#f8fafc;">
                            <h6 class="fw-bold mb-0 small" style="color:#1e293b;">
                                <i class="fa-solid fa-building me-2"
                                   style="color:#2563eb;"></i>Filter by Vendor
                                <span class="badge ms-1 rounded-pill"
                                      style="background:#dbeafe;color:#2563eb;font-size:.6rem;">
                                    AJAX
                                </span>
                            </h6>
                        </div>
                        <div class="card-body px-4 py-3"
                             style="max-height:200px;overflow-y:auto;">
                            <?php foreach ($vendors as $v): ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input vendor-check"
                                           type="checkbox"
                                           id="vc_<?= e(preg_replace('/\W+/', '_', $v)) ?>"
                                           value="<?= e($v) ?>">
                                    <label class="form-check-label small text-truncate"
                                           for="vc_<?= e(preg_replace('/\W+/', '_', $v)) ?>">
                                        <?= e($v) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                </div><!-- /.d-flex sidebar panels -->
            </div><!-- /#sidebarCollapse -->
        </div><!-- /.col sidebar -->

        <!-- ═══════════════════════════ MAIN CONTENT ══════════════════════════ -->
        <div class="col-lg-9">

            <!-- ── Search / Filter bar ────────────────────────────────────── -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-3">
                    <div class="row g-2 align-items-center">

                        <!-- Name search -->
                        <div class="col-sm-5">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"
                                      style="border-right:none;">
                                    <i class="fa-solid fa-magnifying-glass text-muted"></i>
                                </span>
                                <input type="text"
                                       id="medicineSearch"
                                       class="form-control border-start-0 ps-0"
                                       placeholder="Search medicine name&hellip;"
                                       autocomplete="off">
                                <button class="btn btn-outline-secondary border-start-0"
                                        type="button"
                                        id="clearSearch"
                                        title="Clear search"
                                        style="display:none;">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Vendor dropdown -->
                        <div class="col-sm-3">
                            <select id="vendorFilter" class="form-select">
                                <option value="">All Vendors</option>
                                <?php foreach ($vendors as $v): ?>
                                    <option value="<?= e($v) ?>"><?= e($v) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Genre dropdown -->
                        <div class="col-sm-3">
                            <select id="genreFilter" class="form-select">
                                <option value="">All Categories</option>
                                <?php foreach ($allCategories as $cat): ?>
                                    <option value="<?= e($cat['name']) ?>"
                                            <?= $activeCategory
                                                && (int)$activeCategory['id'] === (int)$cat['id']
                                                ? 'selected' : '' ?>>
                                        <?= e($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Clear button -->
                        <div class="col-sm-1 d-grid">
                            <button id="clearFiltersBtn"
                                    class="btn btn-outline-secondary"
                                    title="Clear all AJAX filters">
                                <i class="fa-solid fa-rotate-left"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Results meta row ───────────────────────────────────────── -->
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted small">
                    Showing
                    <strong id="resultsNum"><?= $totalResults ?></strong>
                    <span id="resultsLabel">
                        medicine<?= $totalResults !== 1 ? 's' : '' ?>
                    </span>
                    <?php if ($activeCategory): ?>
                        in <em><?= e($activeCategory['name']) ?></em>
                    <?php endif; ?>
                </span>
                <div id="searchSpinner"
                     class="spinner-border spinner-border-sm text-primary d-none"
                     role="status">
                    <span class="visually-hidden">Searching&hellip;</span>
                </div>
            </div>

            <!-- ── Medicine grid ──────────────────────────────────────────── -->
            <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-4"
                 id="medicineGrid">
                <?php foreach ($medicines as $med): ?>
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
                        <div class="card h-100 border-0 shadow-sm rounded-4
                                     overflow-hidden medicine-card">

                            <!-- Image -->
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
                                        <button class="btn btn-sm w-100 btn-outline-secondary"
                                                disabled>
                                            <i class="fa-solid fa-ban me-1"></i>Out of Stock
                                        </button>
                                    <?php elseif ($isAdmin): ?>
                                        <button class="btn btn-sm w-100 btn-outline-secondary"
                                                disabled>
                                            <i class="fa-solid fa-eye me-1"></i>Admin View
                                        </button>
                                    <?php else: ?>
                                        <a href="<?= BASE_PATH ?>/login"
                                           class="btn btn-sm w-100 btn-outline-primary fw-semibold">
                                            <i class="fa-solid fa-right-to-bracket me-1"></i>
                                            Login to Buy
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div><!-- /#medicineGrid -->

            <!-- ── Empty state ───────────────────────────────────────────── -->
            <div id="noResults"
                 class="text-center py-5 <?= empty($medicines) ? '' : 'd-none' ?>">
                <div class="mb-3" style="font-size:4.5rem;">&#128269;</div>
                <h5 class="fw-semibold" style="color:#1e293b;">No medicines found</h5>
                <p class="text-muted">
                    Try adjusting your search or filter criteria.
                </p>
                <div class="d-flex gap-2 justify-content-center flex-wrap">
                    <?php if ($activeCategory || $typeFilter): ?>
                        <a href="<?= BASE_PATH ?>/medicines"
                           class="btn btn-outline-primary rounded-pill px-4">
                            <i class="fa-solid fa-rotate-left me-2"></i>Clear Category Filter
                        </a>
                    <?php endif; ?>
                    <button id="resetFiltersBtn"
                            class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="fa-solid fa-xmark me-2"></i>Clear Search Filters
                    </button>
                </div>
            </div>

        </div><!-- /.col main -->
    </div><!-- /.row -->
</div><!-- /.container -->

<style>
.medicine-card { transition: transform .2s ease, box-shadow .2s ease; }
.medicine-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 16px 32px rgba(0,0,0,.12) !important;
}
.sidebar-cat-link { transition: background .15s; border-radius: 0; }
.sidebar-cat-link:hover { background-color: #f0f4f8; }
.sidebar-active {
    background-color: #eff6ff !important;
    color: #2563eb !important;
}
</style>

<script>
(function () {
    'use strict';

    /* ── Cache original rendered grid ────────────────────────────────────── */
    var grid           = document.getElementById('medicineGrid');
    var noResults      = document.getElementById('noResults');
    var resultsNum     = document.getElementById('resultsNum');
    var spinner        = document.getElementById('searchSpinner');
    var searchInput    = document.getElementById('medicineSearch');
    var vendorSel      = document.getElementById('vendorFilter');
    var genreSel       = document.getElementById('genreFilter');
    var clearSearchBtn = document.getElementById('clearSearch');
    var clearFilters   = document.getElementById('clearFiltersBtn');
    var resetFiltersBtn= document.getElementById('resetFiltersBtn');
    var vendorChecks   = document.querySelectorAll('.vendor-check');

    var originalHTML   = grid ? grid.innerHTML : '';
    var debounceTimer  = null;
    var currentData    = null;
    var DEBOUNCE_MS    = 380;

    /* ── Search input ────────────────────────────────────────────────────── */
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            if (clearSearchBtn) clearSearchBtn.style.display = this.value ? '' : 'none';
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(triggerSearch, DEBOUNCE_MS);
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

    /* ── Dropdowns ───────────────────────────────────────────────────────── */
    [vendorSel, genreSel].forEach(function (el) {
        if (el) el.addEventListener('change', triggerSearch);
    });

    /* ── Sidebar vendor checkboxes → sync main dropdown then search ──────── */
    vendorChecks.forEach(function (cb) {
        cb.addEventListener('change', function () {
            var checked = Array.from(vendorChecks).filter(function (c) { return c.checked; });
            if (vendorSel) {
                vendorSel.value = checked.length === 1 ? checked[0].value : '';
            }
            triggerSearch();
        });
    });

    /* ── Clear all AJAX filters ──────────────────────────────────────────── */
    function clearAll() {
        if (searchInput)    searchInput.value = '';
        if (clearSearchBtn) clearSearchBtn.style.display = 'none';
        if (vendorSel)      vendorSel.value = '';
        if (genreSel)       genreSel.value  = '';
        vendorChecks.forEach(function (c) { c.checked = false; });
        currentData = null;
        restoreOriginal();
    }
    if (clearFilters)    clearFilters.addEventListener('click', clearAll);
    if (resetFiltersBtn) resetFiltersBtn.addEventListener('click', clearAll);

    /* ── Trigger search ──────────────────────────────────────────────────── */
    function triggerSearch() {
        var q      = searchInput ? searchInput.value.trim()  : '';
        var vendor = vendorSel   ? vendorSel.value.trim()    : '';
        var genre  = genreSel    ? genreSel.value.trim()     : '';

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

    /* ── Restore PHP original ────────────────────────────────────────────── */
    function restoreOriginal() {
        if (grid) grid.innerHTML = originalHTML;
        var n = grid ? grid.querySelectorAll('.medicine-item').length : 0;
        updateCount(n);
        toggleEmpty(n === 0);
    }

    /* ── Render AJAX results ─────────────────────────────────────────────── */
    function renderGrid(medicines) {
        /* Apply server-side type filter from URL to AJAX results too */
        var tf = APP.typeFilter;
        var filtered = tf
            ? medicines.filter(function (m) { return m.category_type === tf; })
            : medicines;

        if (grid) grid.innerHTML = filtered.length ? filtered.map(buildCard).join('') : '';
        updateCount(filtered.length);
        toggleEmpty(filtered.length === 0);
    }

    /* ── Build card HTML ─────────────────────────────────────────────────── */
    function buildCard(m) {
        var isLiq   = m.category_type === 'liquid';
        var inStock = parseInt(m.availability, 10) > 0;
        var price   = '$' + parseFloat(m.price).toFixed(2);

        var imgHtml = m.image_path
            ? '<img src="' + APP.baseUrl + '/public/' + esc(m.image_path)
              + '" alt="' + esc(m.name) + '" class="w-100 h-100" style="object-fit:cover;" loading="lazy"'
              + ' onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'flex\'">'
              + '<div class="w-100 h-100 d-none align-items-center justify-content-center"'
              + ' style="font-size:4rem;position:absolute;top:0;left:0;background:#f8fafc;">&#128138;</div>'
            : '<div class="w-100 h-100 d-flex align-items-center justify-content-center"'
              + ' style="font-size:4rem;">&#128138;</div>';

        var typeBadge = '<span class="position-absolute top-0 start-0 m-2 badge rounded-pill"'
            + ' style="' + (isLiq ? 'background-color:#dcfce7;color:#16a34a;' : 'background-color:#dbeafe;color:#2563eb;') + '">'
            + '<i class="fa-solid ' + (isLiq ? 'fa-bottle-droplet' : 'fa-pills') + ' me-1"></i>'
            + (isLiq ? 'Liquid' : 'Solid') + '</span>';

        var stockBadge = '<span class="position-absolute top-0 end-0 m-2 badge rounded-pill"'
            + ' style="' + (inStock ? 'background-color:#dcfce7;color:#16a34a;' : 'background-color:#fee2e2;color:#dc2626;') + '">'
            + (inStock ? 'In Stock' : 'Out of Stock') + '</span>';

        var vBadge = m.vendor_name
            ? '<span class="badge rounded-pill" style="background:#f1f5f9;color:#475569;font-size:.68rem;">'
              + '<i class="fa-solid fa-building me-1"></i>' + esc(m.vendor_name) + '</span>' : '';
        var cBadge = m.category_name
            ? '<span class="badge rounded-pill" style="background:#f3f4f6;color:#6b7280;font-size:.68rem;">'
              + '<i class="fa-solid fa-tag me-1"></i>' + esc(m.category_name) + '</span>' : '';

        var btn;
        if (APP.isLoggedIn && APP.role === 'customer' && inStock) {
            btn = '<a href="' + APP.basePath + '/cart/add/' + m.id
                + '" class="btn btn-sm w-100 fw-semibold text-white"'
                + ' style="background-color:#16a34a;border-color:#16a34a;">'
                + '<i class="fa-solid fa-cart-plus me-1"></i>Add to Cart</a>';
        } else if (APP.isLoggedIn && APP.role === 'customer') {
            btn = '<button class="btn btn-sm w-100 btn-outline-secondary" disabled>'
                + '<i class="fa-solid fa-ban me-1"></i>Out of Stock</button>';
        } else if (APP.isLoggedIn && APP.role === 'admin') {
            btn = '<button class="btn btn-sm w-100 btn-outline-secondary" disabled>'
                + '<i class="fa-solid fa-eye me-1"></i>Admin View</button>';
        } else {
            btn = '<a href="' + APP.basePath + '/login"'
                + ' class="btn btn-sm w-100 btn-outline-primary fw-semibold">'
                + '<i class="fa-solid fa-right-to-bracket me-1"></i>Login to Buy</a>';
        }

        return '<div class="col medicine-item"'
             + ' data-type="' + esc(m.category_type) + '"'
             + ' data-vendor="' + esc((m.vendor_name || '').toLowerCase()) + '"'
             + ' data-category="' + esc((m.category_name || '').toLowerCase()) + '"'
             + ' data-name="' + esc(m.name.toLowerCase()) + '">'
             + '<div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden medicine-card">'
             + '<div class="position-relative" style="height:180px;overflow:hidden;background:#f8fafc;">'
             + imgHtml + typeBadge + stockBadge + '</div>'
             + '<div class="card-body d-flex flex-column p-3">'
             + '<h6 class="fw-semibold mb-2 lh-sm" style="color:#1e293b;display:-webkit-box;'
             + '-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">'
             + esc(m.name) + '</h6>'
             + '<div class="d-flex flex-wrap gap-1 mb-2">' + vBadge + cBadge + '</div>'
             + '<div class="mt-auto"><div class="mb-2">'
             + '<span class="fw-bold fs-5" style="color:#16a34a;">' + price + '</span>'
             + '</div>' + btn + '</div>'
             + '</div></div></div>';
    }

    /* ── Helpers ─────────────────────────────────────────────────────────── */
    function updateCount(n) {
        if (resultsNum) resultsNum.textContent = n;
    }

    function toggleEmpty(show) {
        if (noResults) noResults.classList.toggle('d-none', !show);
        if (grid)      grid.classList.toggle('d-none', show);
    }

    function esc(str) {
        return String(str)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
            .replace(/'/g, '&#x27;');
    }

}());
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
