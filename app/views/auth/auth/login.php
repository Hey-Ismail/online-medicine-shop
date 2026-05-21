<?php
/**
 * View: Login
 * Online Medicine Shop – Task 1 (23-50009-1)
 *
 * Available variables:
 *   $errors (array) – validation errors keyed by field name
 *            Keys: 'email' | 'password' | 'general'
 */
$errors    = $errors ?? [];
$pageTitle = 'Login – MediShop';
include __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12" style="max-width: 450px;">

            <!-- ══════════════════════ LOGIN CARD ══════════════════════════ -->
            <div class="card border-0 shadow rounded-4 overflow-hidden my-2">

                <!-- Card header -->
                <div class="card-header text-white text-center py-4 border-0"
                     style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);">
                    <div class="mb-2">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white bg-opacity-25"
                              style="width: 56px; height: 56px;">
                            <i class="fa-solid fa-right-to-bracket fa-lg"></i>
                        </span>
                    </div>
                    <h4 class="fw-bold mb-1 mt-2">Welcome Back</h4>
                    <p class="mb-0 small" style="opacity: .8;">Sign in to your MediShop account</p>
                </div>

                <!-- Card body -->
                <div class="card-body p-4 p-md-5 bg-white">

                    <!-- General error alert (e.g. invalid credentials) -->
                    <?php if (!empty($errors['general'])): ?>
                        <div class="alert alert-danger d-flex align-items-center gap-2 mb-4"
                             role="alert">
                            <i class="fa-solid fa-circle-xmark flex-shrink-0"></i>
                            <div><?= e($errors['general']) ?></div>
                        </div>
                    <?php endif; ?>

                    <form method="POST"
                          action="<?= BASE_PATH ?>/login"
                          novalidate
                          id="login-form">

                        <?= csrf_field() ?>

                        <!-- ── Email ───────────────────────────────────── -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">
                                <i class="fa-solid fa-envelope me-1"
                                   style="color: #2563eb;"></i>Email Address
                            </label>
                            <input type="email"
                                   class="form-control form-control-lg <?= !empty($errors['email']) ? 'is-invalid' : '' ?>"
                                   id="email"
                                   name="email"
                                   value="<?= old('email') ?>"
                                   placeholder="you@example.com"
                                   autocomplete="email"
                                   required>
                            <?php if (!empty($errors['email'])): ?>
                                <div class="invalid-feedback">
                                    <i class="fa-solid fa-circle-exclamation me-1"></i>
                                    <?= e($errors['email']) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- ── Password ────────────────────────────────── -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">
                                <i class="fa-solid fa-lock me-1"
                                   style="color: #2563eb;"></i>Password
                            </label>
                            <div class="input-group has-validation">
                                <input type="password"
                                       class="form-control form-control-lg <?= !empty($errors['password']) ? 'is-invalid' : '' ?>"
                                       id="password"
                                       name="password"
                                       placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;"
                                       autocomplete="current-password"
                                       required>
                                <button class="btn btn-outline-secondary"
                                        type="button"
                                        id="toggle-password"
                                        tabindex="-1"
                                        title="Show / hide password"
                                        aria-label="Toggle password visibility">
                                    <i class="fa-solid fa-eye" id="toggle-icon"></i>
                                </button>
                                <?php if (!empty($errors['password'])): ?>
                                    <div class="invalid-feedback order-last">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>
                                        <?= e($errors['password']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- ── Remember me ─────────────────────────────── -->
                        <div class="mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="form-check mb-0">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="remember"
                                       id="remember"
                                       value="1">
                                <label class="form-check-label text-muted small"
                                       for="remember">
                                    Keep me signed in for 30&nbsp;days
                                </label>
                            </div>
                        </div>

                        <!-- ── Submit ──────────────────────────────────── -->
                        <div class="d-grid mb-4">
                            <button type="submit"
                                    class="btn btn-lg fw-bold text-white"
                                    style="background-color: #2563eb;
                                           border-color: #2563eb;">
                                <i class="fa-solid fa-right-to-bracket me-2"></i>Sign In
                            </button>
                        </div>

                        <!-- ── Divider ─────────────────────────────────── -->
                        <div class="position-relative text-center mb-4">
                            <hr class="my-0">
                            <span class="position-absolute top-50 start-50 translate-middle
                                         bg-white px-3 text-muted small">
                                New here?
                            </span>
                        </div>

                        <!-- ── Register link ───────────────────────────── -->
                        <div class="text-center">
                            <a href="<?= BASE_PATH ?>/register"
                               class="btn btn-outline-secondary w-100">
                                <i class="fa-solid fa-user-plus me-2"></i>Create a free account
                            </a>
                        </div>

                    </form>
                </div><!-- /.card-body -->
            </div><!-- /.card -->

        </div><!-- /.col -->
    </div><!-- /.row -->
</div><!-- /.container -->

<!-- ── Password visibility toggle ──────────────────────────────────────────── -->
<script>
(function () {
    'use strict';
    var btn  = document.getElementById('toggle-password');
    var inp  = document.getElementById('password');
    var icon = document.getElementById('toggle-icon');
    if (btn && inp && icon) {
        btn.addEventListener('click', function () {
            var hidden    = (inp.type === 'password');
            inp.type      = hidden ? 'text' : 'password';
            icon.className = hidden ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
            btn.setAttribute('aria-label',
                hidden ? 'Hide password' : 'Show password');
        });
    }
}());
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
