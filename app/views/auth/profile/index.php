<?php
/**
 * View: Profile – index
 * Online Medicine Shop – Task 1 (23-50009-1)
 *
 * Variables:
 *   $user     array (id, name, email, address, phone, profile_picture,
 *                    role, created_at, password_hash)
 *   $errors   array – keyed by field name or 0 for general CSRF error
 *   $success  string|false
 *   $pageTitle string
 */
$user      = $user      ?? [];
$errors    = $errors    ?? [];
$success   = $success   ?? false;
$pageTitle = $pageTitle ?? 'My Profile – MediShop';

include __DIR__ . '/../layouts/header.php';

/* Separate general errors (CSRF/numeric) from field-specific ones */
$generalErrors  = array_filter($errors, 'is_int', ARRAY_FILTER_USE_KEY);
$fieldErrors    = array_filter($errors, 'is_string', ARRAY_FILTER_USE_KEY);

/* Determine which tab should open (if password errors exist → show password tab) */
$pwdKeys    = ['current_password', 'new_password', 'confirm_password'];
$showPwdTab = !empty(array_intersect($pwdKeys, array_keys($fieldErrors)));
$activeTab  = $showPwdTab ? 'password' : 'profile';

/* Profile picture */
$picPath = !empty($user['profile_picture'])
           ? BASE_URL . '/public/' . $user['profile_picture']
           : null;

/* Role helpers */
$role    = $user['role'] ?? 'customer';
$isAdmin = ($role === 'admin');

/* Member since */
$memberSince = !empty($user['created_at'])
               ? date('F j, Y', strtotime($user['created_at']))
               : 'Unknown';
?>

<div class="container py-4">

    <!-- ── Page heading ──────────────────────────────────────────────────── -->
    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
             style="width:48px;height:48px;background-color:#dbeafe;">
            <i class="fa-solid fa-circle-user fa-lg" style="color:#2563eb;"></i>
        </div>
        <div>
            <h2 class="fw-bold mb-0" style="color:#1e293b;">My Profile</h2>
            <p class="text-muted mb-0 small">
                Manage your account settings and security
            </p>
        </div>
    </div>

    <!-- ── General / CSRF error alert ───────────────────────────────────── -->
    <?php if (!empty($generalErrors)): ?>
        <div class="alert alert-danger d-flex align-items-start gap-2 mb-4 shadow-sm"
             role="alert">
            <i class="fa-solid fa-circle-xmark mt-1 flex-shrink-0"></i>
            <div>
                <?php foreach ($generalErrors as $ge): ?>
                    <div><?= e($ge) ?></div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="row g-4 align-items-start">

        <!-- ════════════════════════ MAIN COLUMN ════════════════════════════ -->
        <div class="col-lg-8">

            <!-- Bootstrap nav tabs -->
            <ul class="nav nav-tabs mb-0 border-0" id="profileTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold px-4 py-3
                                   <?= $activeTab === 'profile' ? 'active' : '' ?>"
                            id="tab-profile"
                            data-bs-toggle="tab"
                            data-bs-target="#pane-profile"
                            type="button"
                            role="tab"
                            aria-controls="pane-profile"
                            aria-selected="<?= $activeTab === 'profile' ? 'true' : 'false' ?>">
                        <i class="fa-solid fa-user me-2"></i>Profile Information
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold px-4 py-3
                                   <?= $activeTab === 'password' ? 'active' : '' ?>"
                            id="tab-password"
                            data-bs-toggle="tab"
                            data-bs-target="#pane-password"
                            type="button"
                            role="tab"
                            aria-controls="pane-password"
                            aria-selected="<?= $activeTab === 'password' ? 'true' : 'false' ?>">
                        <i class="fa-solid fa-lock me-2"></i>Change Password
                    </button>
                </li>
            </ul>

            <div class="tab-content">

                <!-- ════════════ TAB 1 – PROFILE INFORMATION ════════════════ -->
                <div class="tab-pane fade <?= $activeTab === 'profile' ? 'show active' : '' ?>"
                     id="pane-profile"
                     role="tabpanel"
                     aria-labelledby="tab-profile">
                    <div class="card border-0 shadow-sm rounded-bottom-4 rounded-end-4">
                        <div class="card-body p-4 p-md-5">

                            <!-- Success alert -->
                            <?php if ($success && !$showPwdTab): ?>
                                <div class="alert alert-success d-flex align-items-center gap-2 mb-4"
                                     role="alert">
                                    <i class="fa-solid fa-circle-check flex-shrink-0"></i>
                                    <div><?= e($success) ?></div>
                                </div>
                            <?php endif; ?>

                            <!-- Profile picture display + preview -->
                            <div class="d-flex align-items-center gap-4 mb-4 pb-4"
                                 style="border-bottom:1px solid #f0f4f8;">
                                <div class="position-relative flex-shrink-0">
                                    <?php if ($picPath): ?>
                                        <img id="profilePreview"
                                             src="<?= e($picPath) ?>"
                                             alt="Profile picture"
                                             class="rounded-circle object-fit-cover border border-3"
                                             style="width:120px;height:120px;
                                                    border-color:#dbeafe !important;">
                                    <?php else: ?>
                                        <div id="profilePlaceholder"
                                             class="rounded-circle d-flex align-items-center
                                                     justify-content-center border border-3"
                                             style="width:120px;height:120px;
                                                    background-color:#f0f4f8;
                                                    border-color:#dbeafe !important;">
                                            <i class="fa-solid fa-user-large fa-3x"
                                               style="color:#94a3b8;"></i>
                                        </div>
                                        <img id="profilePreview"
                                             src=""
                                             alt="Preview"
                                             class="rounded-circle object-fit-cover
                                                     border border-3 d-none"
                                             style="width:120px;height:120px;
                                                    border-color:#dbeafe !important;">
                                    <?php endif; ?>

                                    <!-- Camera icon overlay -->
                                    <label for="profile_picture"
                                           class="position-absolute bottom-0 end-0
                                                  rounded-circle d-flex align-items-center
                                                  justify-content-center shadow-sm"
                                           style="width:32px;height:32px;
                                                  background-color:#2563eb;
                                                  cursor:pointer;"
                                           title="Change photo">
                                        <i class="fa-solid fa-camera text-white"
                                           style="font-size:.7rem;"></i>
                                    </label>
                                </div>

                                <div>
                                    <h5 class="fw-bold mb-1" style="color:#1e293b;">
                                        <?= e($user['name'] ?? 'User') ?>
                                    </h5>
                                    <p class="text-muted small mb-2">
                                        <?= e($user['email'] ?? '') ?>
                                    </p>
                                    <span class="badge rounded-pill px-3 py-2"
                                          style="<?= $isAdmin
                                              ? 'background-color:#fef3c7;color:#92400e;'
                                              : 'background-color:#dcfce7;color:#166534;' ?>">
                                        <i class="fa-solid <?= $isAdmin
                                            ? 'fa-shield-halved' : 'fa-user-check' ?> me-1"></i>
                                        <?= $isAdmin ? 'Administrator' : 'Customer' ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Profile update form -->
                            <form method="POST"
                                  action="<?= BASE_PATH ?>/profile"
                                  enctype="multipart/form-data"
                                  id="profileForm"
                                  novalidate>
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="update_profile">

                                <div class="row g-3">

                                    <!-- Name -->
                                    <div class="col-md-6">
                                        <label for="name" class="form-label fw-semibold small">
                                            <i class="fa-solid fa-user me-1"
                                               style="color:#2563eb;"></i>Full Name
                                            <span class="text-danger ms-1">*</span>
                                        </label>
                                        <input type="text"
                                               class="form-control <?= !empty($fieldErrors['name'])
                                                   ? 'is-invalid' : '' ?>"
                                               id="name"
                                               name="name"
                                               value="<?= e($user['name'] ?? '') ?>"
                                               placeholder="Your full name"
                                               required
                                               maxlength="100">
                                        <?php if (!empty($fieldErrors['name'])): ?>
                                            <div class="invalid-feedback">
                                                <i class="fa-solid fa-circle-exclamation me-1"></i>
                                                <?= e($fieldErrors['name']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Email -->
                                    <div class="col-md-6">
                                        <label for="email" class="form-label fw-semibold small">
                                            <i class="fa-solid fa-envelope me-1"
                                               style="color:#2563eb;"></i>Email Address
                                            <span class="text-danger ms-1">*</span>
                                        </label>
                                        <input type="email"
                                               class="form-control <?= !empty($fieldErrors['email'])
                                                   ? 'is-invalid' : '' ?>"
                                               id="email"
                                               name="email"
                                               value="<?= e($user['email'] ?? '') ?>"
                                               placeholder="you@example.com"
                                               required>
                                        <?php if (!empty($fieldErrors['email'])): ?>
                                            <div class="invalid-feedback">
                                                <i class="fa-solid fa-circle-exclamation me-1"></i>
                                                <?= e($fieldErrors['email']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Phone -->
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label fw-semibold small">
                                            <i class="fa-solid fa-phone me-1"
                                               style="color:#2563eb;"></i>Phone Number
                                        </label>
                                        <input type="tel"
                                               class="form-control <?= !empty($fieldErrors['phone'])
                                                   ? 'is-invalid' : '' ?>"
                                               id="phone"
                                               name="phone"
                                               value="<?= e($user['phone'] ?? '') ?>"
                                               placeholder="+1 (555) 000-0000">
                                        <?php if (!empty($fieldErrors['phone'])): ?>
                                            <div class="invalid-feedback">
                                                <i class="fa-solid fa-circle-exclamation me-1"></i>
                                                <?= e($fieldErrors['phone']) ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="form-text">Optional</div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Profile Picture file input -->
                                    <div class="col-md-6">
                                        <label for="profile_picture"
                                               class="form-label fw-semibold small">
                                            <i class="fa-solid fa-image me-1"
                                               style="color:#2563eb;"></i>Profile Picture
                                        </label>
                                        <input type="file"
                                               class="form-control <?= !empty($fieldErrors['profile_picture'])
                                                   ? 'is-invalid' : '' ?>"
                                               id="profile_picture"
                                               name="profile_picture"
                                               accept="image/jpeg,image/png,image/gif,image/webp">
                                        <?php if (!empty($fieldErrors['profile_picture'])): ?>
                                            <div class="invalid-feedback">
                                                <i class="fa-solid fa-circle-exclamation me-1"></i>
                                                <?= e($fieldErrors['profile_picture']) ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="form-text">
                                                JPEG, PNG, GIF or WebP — max 5 MB
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Address -->
                                    <div class="col-12">
                                        <label for="address" class="form-label fw-semibold small">
                                            <i class="fa-solid fa-location-dot me-1"
                                               style="color:#2563eb;"></i>Delivery Address
                                        </label>
                                        <textarea class="form-control"
                                                  id="address"
                                                  name="address"
                                                  rows="3"
                                                  placeholder="Street address, city, postcode&hellip;"><?= e($user['address'] ?? '') ?></textarea>
                                        <div class="form-text">Optional — used for deliveries</div>
                                    </div>

                                </div><!-- /.row fields -->

                                <div class="mt-4 d-flex gap-2 flex-wrap">
                                    <button type="submit"
                                            class="btn fw-semibold text-white px-4"
                                            style="background-color:#2563eb;
                                                   border-color:#2563eb;">
                                        <i class="fa-solid fa-floppy-disk me-2"></i>
                                        Save Changes
                                    </button>
                                    <button type="reset"
                                            class="btn btn-outline-secondary px-4">
                                        <i class="fa-solid fa-rotate-left me-2"></i>Reset
                                    </button>
                                </div>
                            </form>
                        </div><!-- /.card-body -->
                    </div>
                </div><!-- /#pane-profile -->

                <!-- ════════════ TAB 2 – CHANGE PASSWORD ════════════════════ -->
                <div class="tab-pane fade <?= $activeTab === 'password' ? 'show active' : '' ?>"
                     id="pane-password"
                     role="tabpanel"
                     aria-labelledby="tab-password">
                    <div class="card border-0 shadow-sm rounded-bottom-4 rounded-end-4">
                        <div class="card-body p-4 p-md-5">

                            <!-- Success alert (password change) -->
                            <?php if ($success && $showPwdTab): ?>
                                <div class="alert alert-success d-flex align-items-center gap-2 mb-4"
                                     role="alert">
                                    <i class="fa-solid fa-circle-check flex-shrink-0"></i>
                                    <div><?= e($success) ?></div>
                                </div>
                            <?php endif; ?>

                            <!-- Info callout -->
                            <div class="rounded-3 p-3 mb-4 d-flex align-items-start gap-3"
                                 style="background-color:#eff6ff;">
                                <i class="fa-solid fa-circle-info mt-1 flex-shrink-0"
                                   style="color:#2563eb;"></i>
                                <div class="small" style="color:#1e40af;">
                                    Choose a strong password of at least
                                    <strong>8 characters</strong>. Avoid using your name,
                                    birthdate, or common words.
                                </div>
                            </div>

                            <form method="POST"
                                  action="<?= BASE_PATH ?>/profile"
                                  id="passwordForm"
                                  novalidate>
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="change_password">

                                <!-- Current password -->
                                <div class="mb-3">
                                    <label for="current_password"
                                           class="form-label fw-semibold small">
                                        <i class="fa-solid fa-lock me-1"
                                           style="color:#2563eb;"></i>Current Password
                                        <span class="text-danger ms-1">*</span>
                                    </label>
                                    <div class="input-group has-validation">
                                        <input type="password"
                                               class="form-control <?= !empty($fieldErrors['current_password'])
                                                   ? 'is-invalid' : '' ?>"
                                               id="current_password"
                                               name="current_password"
                                               autocomplete="current-password"
                                               placeholder="Your current password"
                                               required>
                                        <button class="btn btn-outline-secondary pwd-toggle"
                                                type="button"
                                                data-target="current_password"
                                                title="Show / hide">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                        <?php if (!empty($fieldErrors['current_password'])): ?>
                                            <div class="invalid-feedback order-last">
                                                <i class="fa-solid fa-circle-exclamation me-1"></i>
                                                <?= e($fieldErrors['current_password']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- New password -->
                                <div class="mb-3">
                                    <label for="new_password"
                                           class="form-label fw-semibold small">
                                        <i class="fa-solid fa-key me-1"
                                           style="color:#2563eb;"></i>New Password
                                        <span class="text-danger ms-1">*</span>
                                    </label>
                                    <div class="input-group has-validation">
                                        <input type="password"
                                               class="form-control <?= !empty($fieldErrors['new_password'])
                                                   ? 'is-invalid' : '' ?>"
                                               id="new_password"
                                               name="new_password"
                                               autocomplete="new-password"
                                               placeholder="At least 8 characters"
                                               minlength="8"
                                               required>
                                        <button class="btn btn-outline-secondary pwd-toggle"
                                                type="button"
                                                data-target="new_password"
                                                title="Show / hide">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                        <?php if (!empty($fieldErrors['new_password'])): ?>
                                            <div class="invalid-feedback order-last">
                                                <i class="fa-solid fa-circle-exclamation me-1"></i>
                                                <?= e($fieldErrors['new_password']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <!-- Strength bar -->
                                    <div class="mt-2" id="strengthWrap" style="display:none;">
                                        <div class="progress" style="height:4px;">
                                            <div class="progress-bar" id="strengthBar"
                                                 role="progressbar" style="width:0%;"></div>
                                        </div>
                                        <small id="strengthLabel" class="text-muted"></small>
                                    </div>
                                </div>

                                <!-- Confirm new password -->
                                <div class="mb-4">
                                    <label for="confirm_password"
                                           class="form-label fw-semibold small">
                                        <i class="fa-solid fa-lock-open me-1"
                                           style="color:#2563eb;"></i>Confirm New Password
                                        <span class="text-danger ms-1">*</span>
                                    </label>
                                    <div class="input-group has-validation">
                                        <input type="password"
                                               class="form-control <?= !empty($fieldErrors['confirm_password'])
                                                   ? 'is-invalid' : '' ?>"
                                               id="confirm_password"
                                               name="confirm_password"
                                               autocomplete="new-password"
                                               placeholder="Repeat new password"
                                               required>
                                        <button class="btn btn-outline-secondary pwd-toggle"
                                                type="button"
                                                data-target="confirm_password"
                                                title="Show / hide">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                        <?php if (!empty($fieldErrors['confirm_password'])): ?>
                                            <div class="invalid-feedback order-last">
                                                <i class="fa-solid fa-circle-exclamation me-1"></i>
                                                <?= e($fieldErrors['confirm_password']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <!-- Match indicator -->
                                    <div id="matchIndicator" class="mt-1 small d-none">
                                        <i class="fa-solid fa-circle-xmark me-1"
                                           style="color:#dc2626;"></i>
                                        <span style="color:#dc2626;">Passwords do not match</span>
                                    </div>
                                </div>

                                <button type="submit"
                                        class="btn fw-semibold text-white px-4"
                                        style="background-color:#2563eb;
                                               border-color:#2563eb;">
                                    <i class="fa-solid fa-shield-halved me-2"></i>
                                    Update Password
                                </button>
                            </form>
                        </div><!-- /.card-body -->
                    </div>
                </div><!-- /#pane-password -->

            </div><!-- /.tab-content -->
        </div><!-- /.col main -->

        <!-- ════════════════════════ SIDEBAR COLUMN ═════════════════════════ -->
        <div class="col-lg-4">

            <!-- Account Info card -->
            <div class="card border-0 shadow-sm rounded-4 mb-3 overflow-hidden">
                <div class="card-header border-0 py-4 px-4 text-white text-center"
                     style="background:linear-gradient(135deg,#2563eb 0%,#1d4ed8 100%);">
                    <!-- Avatar -->
                    <div class="mx-auto mb-3">
                        <?php if ($picPath): ?>
                            <img src="<?= e($picPath) ?>"
                                 alt="Profile"
                                 class="rounded-circle border border-3 border-white
                                         object-fit-cover"
                                 style="width:80px;height:80px;">
                        <?php else: ?>
                            <div class="rounded-circle d-flex align-items-center
                                         justify-content-center border border-3
                                         border-white mx-auto"
                                 style="width:80px;height:80px;
                                        background:rgba(255,255,255,.2);">
                                <i class="fa-solid fa-user fa-2x text-white"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <h6 class="fw-bold text-white mb-1">
                        <?= e($user['name'] ?? 'User') ?>
                    </h6>
                    <p class="mb-0 small" style="opacity:.8;">
                        <?= e($user['email'] ?? '') ?>
                    </p>
                </div>

                <div class="card-body p-4">
                    <!-- Role badge -->
                    <div class="d-flex align-items-center justify-content-between mb-3
                                 pb-3" style="border-bottom:1px solid #f0f4f8;">
                        <span class="small fw-semibold text-muted">Account Role</span>
                        <span class="badge rounded-pill px-3 py-2"
                              style="<?= $isAdmin
                                  ? 'background-color:#fef3c7;color:#92400e;'
                                  : 'background-color:#dcfce7;color:#166534;' ?>">
                            <i class="fa-solid <?= $isAdmin
                                ? 'fa-shield-halved' : 'fa-user-check' ?> me-1"></i>
                            <?= $isAdmin ? 'Administrator' : 'Customer' ?>
                        </span>
                    </div>

                    <!-- Member since -->
                    <div class="d-flex align-items-center justify-content-between mb-3
                                 pb-3" style="border-bottom:1px solid #f0f4f8;">
                        <span class="small fw-semibold text-muted">
                            <i class="fa-solid fa-calendar me-1"
                               style="color:#2563eb;"></i>Member Since
                        </span>
                        <span class="small fw-semibold" style="color:#1e293b;">
                            <?= e($memberSince) ?>
                        </span>
                    </div>

                    <!-- Email -->
                    <div class="d-flex align-items-center justify-content-between mb-3
                                 pb-3" style="border-bottom:1px solid #f0f4f8;">
                        <span class="small fw-semibold text-muted">
                            <i class="fa-solid fa-envelope me-1"
                               style="color:#2563eb;"></i>Email
                        </span>
                        <span class="small text-truncate ms-2"
                              style="color:#1e293b;max-width:160px;"
                              title="<?= e($user['email'] ?? '') ?>">
                            <?= e($user['email'] ?? '') ?>
                        </span>
                    </div>

                    <!-- Phone -->
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="small fw-semibold text-muted">
                            <i class="fa-solid fa-phone me-1"
                               style="color:#2563eb;"></i>Phone
                        </span>
                        <span class="small" style="color:#1e293b;">
                            <?= !empty($user['phone'])
                                ? e($user['phone'])
                                : '<em class="text-muted">Not set</em>' ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick actions card -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header border-0 py-3 px-4" style="background:#f8fafc;">
                    <h6 class="fw-bold mb-0 small" style="color:#1e293b;">
                        <i class="fa-solid fa-bolt me-2"
                           style="color:#2563eb;"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body px-3 py-3">
                    <?php if (!$isAdmin): ?>
                        <a href="<?= BASE_PATH ?>/medicines"
                           class="d-flex align-items-center gap-3 px-3 py-2 mb-2
                                   rounded-3 text-decoration-none"
                           style="background-color:#f8fafc;color:#374151;
                                  transition:background .15s;">
                            <i class="fa-solid fa-capsules" style="color:#2563eb;"></i>
                            <span class="small fw-semibold">Browse Medicines</span>
                        </a>
                        <a href="<?= BASE_PATH ?>/cart"
                           class="d-flex align-items-center gap-3 px-3 py-2 mb-2
                                   rounded-3 text-decoration-none"
                           style="background-color:#f8fafc;color:#374151;">
                            <i class="fa-solid fa-cart-shopping" style="color:#16a34a;"></i>
                            <span class="small fw-semibold">My Cart</span>
                        </a>
                    <?php endif; ?>
                    <a href="<?= BASE_PATH ?>/logout"
                       class="d-flex align-items-center gap-3 px-3 py-2 rounded-3
                               text-decoration-none"
                       style="background-color:#fff0f0;color:#dc2626;">
                        <i class="fa-solid fa-right-from-bracket"
                           style="color:#dc2626;"></i>
                        <span class="small fw-semibold">Sign Out</span>
                    </a>
                </div>
            </div>

        </div><!-- /.col sidebar -->
    </div><!-- /.row -->
</div><!-- /.container -->

<style>
.nav-tabs .nav-link {
    border: none;
    border-bottom: 3px solid transparent;
    color: #6b7280;
    border-radius: 0;
}
.nav-tabs .nav-link:hover {
    color: #2563eb;
    border-bottom-color: #bfdbfe;
}
.nav-tabs .nav-link.active {
    color: #2563eb;
    font-weight: 700;
    border-bottom: 3px solid #2563eb;
    background: transparent;
}
.tab-content > .tab-pane {
    border-top: none;
}
</style>

<script>
(function () {
    'use strict';

    /* ── Password visibility toggles ──────────────────────────────────────── */
    document.querySelectorAll('.pwd-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var inp = document.getElementById(this.dataset.target);
            var ico = this.querySelector('i');
            if (!inp) return;
            var hidden = inp.type === 'password';
            inp.type   = hidden ? 'text' : 'password';
            ico.className = hidden ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
            this.setAttribute('title', hidden ? 'Hide' : 'Show');
        });
    });

    /* ── Profile picture live preview ─────────────────────────────────────── */
    var fileInput       = document.getElementById('profile_picture');
    var preview         = document.getElementById('profilePreview');
    var placeholder     = document.getElementById('profilePlaceholder');

    if (fileInput && preview) {
        fileInput.addEventListener('change', function () {
            if (!this.files || !this.files[0]) return;
            var reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                if (preview.classList.contains('d-none')) {
                    preview.classList.remove('d-none');
                    if (placeholder) placeholder.classList.add('d-none');
                }
            };
            reader.readAsDataURL(this.files[0]);
        });
    }

    /* ── Password strength meter ───────────────────────────────────────────── */
    var newPwdInput  = document.getElementById('new_password');
    var strengthWrap = document.getElementById('strengthWrap');
    var strengthBar  = document.getElementById('strengthBar');
    var strengthLbl  = document.getElementById('strengthLabel');

    if (newPwdInput && strengthWrap) {
        newPwdInput.addEventListener('input', function () {
            var val = this.value;
            if (!val) {
                strengthWrap.style.display = 'none';
                return;
            }
            strengthWrap.style.display = '';
            var score = 0;
            if (val.length >= 8)  score++;
            if (val.length >= 12) score++;
            if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score++;
            if (/[0-9]/.test(val))  score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            var levels = [
                { pct: 20,  cls: 'bg-danger',   lbl: 'Very weak' },
                { pct: 40,  cls: 'bg-danger',   lbl: 'Weak' },
                { pct: 60,  cls: 'bg-warning',  lbl: 'Fair' },
                { pct: 80,  cls: 'bg-info',     lbl: 'Good' },
                { pct: 100, cls: 'bg-success',  lbl: 'Strong' },
            ];
            var lvl = levels[Math.min(score, levels.length - 1)];
            strengthBar.style.width     = lvl.pct + '%';
            strengthBar.className       = 'progress-bar ' + lvl.cls;
            strengthLbl.textContent     = lvl.lbl;
            strengthLbl.className       = 'small text-muted';
        });
    }

    /* ── Confirm password match indicator ──────────────────────────────────── */
    var confirmInp   = document.getElementById('confirm_password');
    var matchInd     = document.getElementById('matchIndicator');

    if (confirmInp && newPwdInput && matchInd) {
        function checkMatch() {
            if (!confirmInp.value) {
                matchInd.classList.add('d-none');
                return;
            }
            var match = confirmInp.value === newPwdInput.value;
            matchInd.classList.remove('d-none');
            matchInd.innerHTML = match
                ? '<i class="fa-solid fa-circle-check me-1" style="color:#16a34a;"></i>'
                  + '<span style="color:#16a34a;">Passwords match</span>'
                : '<i class="fa-solid fa-circle-xmark me-1" style="color:#dc2626;"></i>'
                  + '<span style="color:#dc2626;">Passwords do not match</span>';
        }
        confirmInp.addEventListener('input', checkMatch);
        newPwdInput.addEventListener('input', checkMatch);
    }

}());
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
