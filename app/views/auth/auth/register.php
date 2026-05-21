<?php
/**
 * View: Register
 * Online Medicine Shop – Task 1 (23-50009-1)
 *
 * Available variables:
 *   $errors (array) – validation errors keyed by field name
 *            Keys: 'name' | 'email' | 'password' | 'password_confirm'
 *                  'role' | 'address' | 'phone' | 'general'
 */
$errors    = $errors ?? [];
$pageTitle = 'Register – MediShop';
include __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12" style="max-width: 550px;">

            <!-- ════════════════════ REGISTER CARD ═══════════════════════ -->
            <div class="card border-0 shadow rounded-4 overflow-hidden my-2">

                <!-- Card header -->
                <div class="card-header text-white text-center py-4 border-0"
                     style="background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);">
                    <div class="mb-2">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white bg-opacity-25"
                              style="width: 56px; height: 56px;">
                            <i class="fa-solid fa-user-plus fa-lg"></i>
                        </span>
                    </div>
                    <h4 class="fw-bold mb-1 mt-2">Create Your Account</h4>
                    <p class="mb-0 small" style="opacity: .8;">
                        Join MediShop – your trusted online pharmacy
                    </p>
                </div>

                <!-- Card body -->
                <div class="card-body p-4 p-md-5 bg-white">

                    <!-- General error alert -->
                    <?php if (!empty($errors['general'])): ?>
                        <div class="alert alert-danger d-flex align-items-center gap-2 mb-4"
                             role="alert">
                            <i class="fa-solid fa-circle-xmark flex-shrink-0"></i>
                            <div><?= e($errors['general']) ?></div>
                        </div>
                    <?php endif; ?>

                    <form method="POST"
                          action="<?= BASE_PATH ?>/register"
                          novalidate
                          id="register-form">

                        <?= csrf_field() ?>

                        <!-- ── Full Name ────────────────────────────────── -->
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">
                                <i class="fa-solid fa-user me-1"
                                   style="color: #16a34a;"></i>Full Name
                            </label>
                            <input type="text"
                                   class="form-control <?= !empty($errors['name']) ? 'is-invalid' : '' ?>"
                                   id="name"
                                   name="name"
                                   value="<?= old('name') ?>"
                                   placeholder="John Doe"
                                   autocomplete="name"
                                   required>
                            <?php if (!empty($errors['name'])): ?>
                                <div class="invalid-feedback">
                                    <i class="fa-solid fa-circle-exclamation me-1"></i>
                                    <?= e($errors['name']) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- ── Email ───────────────────────────────────── -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">
                                <i class="fa-solid fa-envelope me-1"
                                   style="color: #16a34a;"></i>Email Address
                            </label>
                            <input type="email"
                                   class="form-control <?= !empty($errors['email']) ? 'is-invalid' : '' ?>"
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

                        <!-- ── Password row (two columns on md+) ──────── -->
                        <div class="row g-3 mb-3">

                            <!-- Password -->
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-semibold">
                                    <i class="fa-solid fa-lock me-1"
                                       style="color: #16a34a;"></i>Password
                                </label>
                                <div class="input-group has-validation">
                                    <input type="password"
                                           class="form-control <?= !empty($errors['password']) ? 'is-invalid' : '' ?>"
                                           id="password"
                                           name="password"
                                           placeholder="Min. 8 characters"
                                           autocomplete="new-password"
                                           minlength="8"
                                           required>
                                    <button class="btn btn-outline-secondary"
                                            type="button"
                                            id="toggle-password"
                                            tabindex="-1"
                                            aria-label="Toggle password visibility"
                                            title="Show / hide password">
                                        <i class="fa-solid fa-eye" id="icon-password"></i>
                                    </button>
                                    <?php if (!empty($errors['password'])): ?>
                                        <div class="invalid-feedback order-last">
                                            <i class="fa-solid fa-circle-exclamation me-1"></i>
                                            <?= e($errors['password']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="form-text">At least 8 characters.</div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-md-6">
                                <label for="password_confirm" class="form-label fw-semibold">
                                    <i class="fa-solid fa-lock me-1"
                                       style="color: #16a34a;"></i>Confirm Password
                                </label>
                                <div class="input-group has-validation">
                                    <input type="password"
                                           class="form-control <?= !empty($errors['password_confirm']) ? 'is-invalid' : '' ?>"
                                           id="password_confirm"
                                           name="password_confirm"
                                           placeholder="Re-enter password"
                                           autocomplete="new-password"
                                           required>
                                    <button class="btn btn-outline-secondary"
                                            type="button"
                                            id="toggle-confirm"
                                            tabindex="-1"
                                            aria-label="Toggle confirm password visibility"
                                            title="Show / hide password">
                                        <i class="fa-solid fa-eye" id="icon-confirm"></i>
                                    </button>
                                    <?php if (!empty($errors['password_confirm'])): ?>
                                        <div class="invalid-feedback order-last">
                                            <i class="fa-solid fa-circle-exclamation me-1"></i>
                                            <?= e($errors['password_confirm']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div><!-- /.row passwords -->

                        <!-- ── Account type ────────────────────────────── -->
                        <div class="mb-3">
                            <label for="role" class="form-label fw-semibold">
                                <i class="fa-solid fa-user-tag me-1"
                                   style="color: #16a34a;"></i>Account Type
                            </label>
                            <select class="form-select <?= !empty($errors['role']) ? 'is-invalid' : '' ?>"
                                    id="role"
                                    name="role"
                                    required>
                                <option value=""
                                        disabled
                                        <?= (old('role') === '') ? 'selected' : '' ?>>
                                    — Select account type —
                                </option>
                                <option value="customer"
                                        <?= (old('role') === 'customer') ? 'selected' : '' ?>>
                                    <i class="fa-solid fa-user"></i> Customer
                                </option>
                                <option value="admin"
                                        <?= (old('role') === 'admin') ? 'selected' : '' ?>>
                                    <i class="fa-solid fa-shield-halved"></i> Admin
                                </option>
                            </select>
                            <?php if (!empty($errors['role'])): ?>
                                <div class="invalid-feedback">
                                    <i class="fa-solid fa-circle-exclamation me-1"></i>
                                    <?= e($errors['role']) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- ── Address ─────────────────────────────────── -->
                        <div class="mb-3">
                            <label for="address" class="form-label fw-semibold">
                                <i class="fa-solid fa-location-dot me-1"
                                   style="color: #16a34a;"></i>Address
                                <span class="fw-normal text-muted small ms-1">(optional)</span>
                            </label>
                            <textarea class="form-control <?= !empty($errors['address']) ? 'is-invalid' : '' ?>"
                                      id="address"
                                      name="address"
                                      rows="2"
                                      placeholder="123 Main St, City, Country"
                                      autocomplete="street-address"><?= old('address') ?></textarea>
                            <?php if (!empty($errors['address'])): ?>
                                <div class="invalid-feedback">
                                    <i class="fa-solid fa-circle-exclamation me-1"></i>
                                    <?= e($errors['address']) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- ── Phone ───────────────────────────────────── -->
                        <div class="mb-4">
                            <label for="phone" class="form-label fw-semibold">
                                <i class="fa-solid fa-phone me-1"
                                   style="color: #16a34a;"></i>Phone Number
                                <span class="fw-normal text-muted small ms-1">(optional)</span>
                            </label>
                            <input type="tel"
                                   class="form-control <?= !empty($errors['phone']) ? 'is-invalid' : '' ?>"
                                   id="phone"
                                   name="phone"
                                   value="<?= old('phone') ?>"
                                   placeholder="+1 234 567 8900"
                                   autocomplete="tel">
                            <?php if (!empty($errors['phone'])): ?>
                                <div class="invalid-feedback">
                                    <i class="fa-solid fa-circle-exclamation me-1"></i>
                                    <?= e($errors['phone']) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- ── Submit ──────────────────────────────────── -->
                        <div class="d-grid mb-4">
                            <button type="submit"
                                    class="btn btn-lg fw-bold text-white"
                                    style="background-color: #16a34a;
                                           border-color: #16a34a;">
                                <i class="fa-solid fa-user-check me-2"></i>Create Account
                            </button>
                        </div>

                        <!-- ── Divider ─────────────────────────────────── -->
                        <div class="position-relative text-center mb-4">
                            <hr class="my-0">
                            <span class="position-absolute top-50 start-50 translate-middle
                                         bg-white px-3 text-muted small">
                                Already have an account?
                            </span>
                        </div>

                        <!-- ── Login link ──────────────────────────────── -->
                        <div class="text-center">
                            <a href="<?= BASE_PATH ?>/login"
                               class="btn btn-outline-secondary w-100">
                                <i class="fa-solid fa-right-to-bracket me-2"></i>Sign in instead
                            </a>
                        </div>

                    </form>
                </div><!-- /.card-body -->
            </div><!-- /.card -->

        </div><!-- /.col -->
    </div><!-- /.row -->
</div><!-- /.container -->

<!-- ── Password visibility toggles ─────────────────────────────────────────── -->
<script>
(function () {
    'use strict';

    function makeToggle(btnId, inputId, iconId) {
        var btn  = document.getElementById(btnId);
        var inp  = document.getElementById(inputId);
        var icon = document.getElementById(iconId);
        if (!btn || !inp || !icon) { return; }
        btn.addEventListener('click', function () {
            var hidden     = (inp.type === 'password');
            inp.type       = hidden ? 'text' : 'password';
            icon.className = hidden ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
            btn.setAttribute('aria-label',
                hidden ? 'Hide password' : 'Show password');
        });
    }

    makeToggle('toggle-password', 'password',         'icon-password');
    makeToggle('toggle-confirm',  'password_confirm', 'icon-confirm');
}());
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
