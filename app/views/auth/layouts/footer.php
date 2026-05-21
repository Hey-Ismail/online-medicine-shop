<?php
/**
 * Layout: Footer
 * Online Medicine Shop – Task 1 (23-50009-1)
 *
 * Closes </main>, renders the site footer, then </body></html>.
 * Also loads Bootstrap 5 JS bundle + custom JS files.
 */
?>

</main><!-- /.main ─────────────────────────────────────────────────────────── -->

<!-- ═══════════════════════════════ FOOTER ═══════════════════════════════════ -->
<footer class="text-white pt-5 pb-4" style="background-color: #1e293b;">
    <div class="container">
        <div class="row gy-5">

            <!-- ── Brand & tagline ────────────────────────────────────────── -->
            <div class="col-lg-4">
                <a href="<?= BASE_PATH ?>/"
                   class="d-inline-flex align-items-center text-white text-decoration-none fw-bold fs-4 mb-3">
                    <i class="fa-solid fa-pills me-2" style="color: #16a34a;"></i>MediShop
                </a>
                <p class="text-white-50 small mb-4">
                    Your health, our priority. Providing quality medicines<br>
                    and healthcare products you can trust.
                </p>
                <!-- Social icons -->
                <div class="d-flex gap-3">
                    <a href="#" class="text-white-50 fs-5 text-decoration-none" aria-label="Facebook"
                       title="Facebook">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                    <a href="#" class="text-white-50 fs-5 text-decoration-none" aria-label="Twitter / X"
                       title="Twitter / X">
                        <i class="fa-brands fa-x-twitter"></i>
                    </a>
                    <a href="#" class="text-white-50 fs-5 text-decoration-none" aria-label="Instagram"
                       title="Instagram">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                    <a href="#" class="text-white-50 fs-5 text-decoration-none" aria-label="LinkedIn"
                       title="LinkedIn">
                        <i class="fa-brands fa-linkedin-in"></i>
                    </a>
                </div>
            </div>

            <!-- ── Quick links ────────────────────────────────────────────── -->
            <div class="col-lg-4 col-md-6">
                <h6 class="fw-semibold text-uppercase mb-3 small letter-spacing-1"
                    style="color: #16a34a; letter-spacing: .08em;">
                    Quick Links
                </h6>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <a class="text-white-50 text-decoration-none footer-link d-inline-flex align-items-center gap-2"
                           href="<?= BASE_PATH ?>/">
                            <i class="fa-solid fa-chevron-right small"></i>Home
                        </a>
                    </li>
                    <li class="mb-2">
                        <a class="text-white-50 text-decoration-none footer-link d-inline-flex align-items-center gap-2"
                           href="<?= BASE_PATH ?>/medicines">
                            <i class="fa-solid fa-chevron-right small"></i>Medicines
                        </a>
                    </li>

                    <?php if (!is_logged_in()): ?>
                        <li class="mb-2">
                            <a class="text-white-50 text-decoration-none footer-link d-inline-flex align-items-center gap-2"
                               href="<?= BASE_PATH ?>/login">
                                <i class="fa-solid fa-chevron-right small"></i>Login
                            </a>
                        </li>
                        <li class="mb-2">
                            <a class="text-white-50 text-decoration-none footer-link d-inline-flex align-items-center gap-2"
                               href="<?= BASE_PATH ?>/register">
                                <i class="fa-solid fa-chevron-right small"></i>Register
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="mb-2">
                            <a class="text-white-50 text-decoration-none footer-link d-inline-flex align-items-center gap-2"
                               href="<?= BASE_PATH ?>/profile">
                                <i class="fa-solid fa-chevron-right small"></i>Profile
                            </a>
                        </li>
                        <?php if (($_SESSION['role'] ?? '') === 'customer'): ?>
                        <li class="mb-2">
                            <a class="text-white-50 text-decoration-none footer-link d-inline-flex align-items-center gap-2"
                               href="<?= BASE_PATH ?>/cart">
                                <i class="fa-solid fa-chevron-right small"></i>My Cart
                            </a>
                        </li>
                        <?php endif; ?>
                        <li class="mb-2">
                            <a class="text-white-50 text-decoration-none footer-link d-inline-flex align-items-center gap-2"
                               href="<?= BASE_PATH ?>/logout">
                                <i class="fa-solid fa-chevron-right small"></i>Logout
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- ── Contact info ───────────────────────────────────────────── -->
            <div class="col-lg-4 col-md-6">
                <h6 class="fw-semibold text-uppercase mb-3 small"
                    style="color: #16a34a; letter-spacing: .08em;">
                    Get In Touch
                </h6>
                <ul class="list-unstyled mb-0 text-white-50 small">
                    <li class="mb-3 d-flex align-items-start gap-2">
                        <i class="fa-solid fa-envelope mt-1 flex-shrink-0"
                           style="color: #16a34a;"></i>
                        <span>support@medishop.com</span>
                    </li>
                    <li class="mb-3 d-flex align-items-start gap-2">
                        <i class="fa-solid fa-phone mt-1 flex-shrink-0"
                           style="color: #16a34a;"></i>
                        <span>+1 (800) MEDI-SHOP</span>
                    </li>
                    <li class="mb-3 d-flex align-items-start gap-2">
                        <i class="fa-solid fa-location-dot mt-1 flex-shrink-0"
                           style="color: #16a34a;"></i>
                        <span>123 Health Avenue,<br>Wellness City, WC 45678</span>
                    </li>
                    <li class="d-flex align-items-start gap-2">
                        <i class="fa-solid fa-clock mt-1 flex-shrink-0"
                           style="color: #16a34a;"></i>
                        <span>Mon – Sat: 8 AM – 10 PM</span>
                    </li>
                </ul>
            </div>

        </div><!-- /.row -->

        <hr class="border-secondary my-4">

        <!-- ── Bottom bar ─────────────────────────────────────────────────── -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <p class="mb-0 text-white-50 small">
                &copy; 2024 <strong class="text-white">MediShop</strong>.
                All rights reserved.
            </p>
            <p class="mb-0 text-white-50 small">
                <i class="fa-solid fa-heart me-1" style="color: #16a34a;"></i>
                Task 1 &ndash; Student 23-50009-1
            </p>
        </div>

    </div><!-- /.container -->
</footer>

<!-- ── Bootstrap 5 JS Bundle (includes Popper) ──────────────────────────── -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- ── Custom Scripts ────────────────────────────────────────────────────── -->
<script src="<?= BASE_URL ?>/public/auth/js/validation.js"></script>
<script src="<?= BASE_URL ?>/public/auth/js/search.js"></script>

<!-- ── Auto-dismiss flash alert after 4 s (needs Bootstrap JS above) ─────── -->
<script>
(function () {
    'use strict';
    var flashEl = document.getElementById('flash-alert');
    if (flashEl) {
        setTimeout(function () {
            var bsAlert = bootstrap.Alert.getOrCreateInstance(flashEl);
            bsAlert.close();
        }, 4000);
    }
}());
</script>

</body>
</html>
