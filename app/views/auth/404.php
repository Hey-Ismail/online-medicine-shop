<?php
/**
 * View: 404 – Not Found
 * Online Medicine Shop – Task 1 (23-50009-1)
 */
$pageTitle = 'Page Not Found – MediShop';
include __DIR__ . '/layouts/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden text-center">

                <!-- Top gradient bar -->
                <div class="py-5 px-4"
                     style="background:linear-gradient(135deg,#1e3a8a 0%,#2563eb 60%,#16a34a 100%);">

                    <!-- Animated icon -->
                    <div class="mb-3">
                        <span class="d-inline-flex align-items-center justify-content-center
                                      rounded-circle"
                              style="width:88px;height:88px;
                                     background:rgba(255,255,255,.15);
                                     border:2px solid rgba(255,255,255,.3);">
                            <i class="fa-solid fa-triangle-exclamation fa-2x text-white"
                               style="opacity:.9;"></i>
                        </span>
                    </div>

                    <!-- 404 number -->
                    <div class="fw-bold text-white lh-1 mb-2"
                         style="font-size:6rem;letter-spacing:-.02em;
                                text-shadow:0 4px 16px rgba(0,0,0,.25);">
                        404
                    </div>

                    <p class="text-white mb-0"
                       style="font-size:1.1rem;opacity:.85;">
                        Page Not Found
                    </p>
                </div>

                <!-- Card body -->
                <div class="card-body p-5">

                    <div class="mb-4" style="font-size:3.5rem;" aria-hidden="true">
                        &#128142;
                    </div>

                    <h4 class="fw-bold mb-2" style="color:#1e293b;">
                        Oops! We lost this page.
                    </h4>
                    <p class="text-muted mb-4">
                        The page you&rsquo;re looking for doesn&rsquo;t exist,
                        may have been moved, or the link may be broken.
                    </p>

                    <!-- Action buttons -->
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center
                                 mb-4">
                        <a href="<?= BASE_PATH ?>/"
                           class="btn btn-lg fw-semibold text-white px-5"
                           style="background-color:#2563eb;border-color:#2563eb;">
                            <i class="fa-solid fa-house me-2"></i>Go Home
                        </a>
                        <a href="<?= BASE_PATH ?>/medicines"
                           class="btn btn-lg btn-outline-primary fw-semibold px-5">
                            <i class="fa-solid fa-capsules me-2"></i>Browse Medicines
                        </a>
                    </div>

                    <!-- Divider -->
                    <div class="position-relative mb-4">
                        <hr class="my-0">
                        <span class="position-absolute top-50 start-50 translate-middle
                                      bg-white px-3 text-muted small">
                            or try searching
                        </span>
                    </div>

                    <!-- Quick search -->
                    <form action="<?= BASE_PATH ?>/medicines"
                          method="GET"
                          class="d-flex gap-2">
                        <input type="text"
                               name="q"
                               class="form-control"
                               placeholder="Search for a medicine&hellip;">
                        <button type="submit"
                                class="btn text-white flex-shrink-0"
                                style="background-color:#16a34a;border-color:#16a34a;">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </form>
                </div><!-- /.card-body -->

                <!-- Subtle footer inside card -->
                <div class="py-3 px-4 text-center"
                     style="background:#f8fafc;border-top:1px solid #f0f4f8;">
                    <p class="mb-0 text-muted small">
                        <i class="fa-solid fa-circle-info me-1" style="color:#2563eb;"></i>
                        If you believe this is a mistake, please
                        <a href="<?= BASE_PATH ?>/"
                           class="text-decoration-none fw-semibold"
                           style="color:#2563eb;">contact support</a>.
                    </p>
                </div>
            </div><!-- /.card -->

        </div><!-- /.col -->
    </div><!-- /.row -->
</div><!-- /.container -->

<?php include __DIR__ . '/layouts/footer.php'; ?>
