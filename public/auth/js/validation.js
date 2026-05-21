/**
 * validation.js
 * ─────────────────────────────────────────────────────────────────────────────
 * Client-side form validation for Online Medicine Shop (MediShop)
 * Pure vanilla JS – no jQuery dependency.
 *
 * Handles three forms:
 *   1. Registration      (#registerForm  | #register-form)
 *   2. Profile update    (#profileForm)
 *   3. Change password   (#changePasswordForm | #passwordForm)
 *
 * Uses Bootstrap 5 is-valid / is-invalid classes plus .invalid-feedback
 * elements (creates them dynamically when the PHP view didn't render one).
 * ─────────────────────────────────────────────────────────────────────────────
 */

'use strict';

/* ════════════════════════════════════════════════════════════════════════════
   SECTION 1 – PURE HELPER FUNCTIONS
   (Exported on window so views can call them if needed)
════════════════════════════════════════════════════════════════════════════ */

/**
 * Mark a field as INVALID.
 * Adds Bootstrap `is-invalid` class and writes the error message into the
 * nearest `.invalid-feedback` element, creating one if it doesn't exist yet.
 *
 * @param {HTMLElement} input   – the form control that failed
 * @param {string}      message – human-readable error text
 */
function showError(input, message) {
    if (!input) return;

    input.classList.remove('is-valid');
    input.classList.add('is-invalid');

    /* ── Locate the feedback element ──────────────────────────────────────
       Search order:
         1. Inside the same .input-group (for password + toggle-btn combos)
         2. Inside the nearest .mb-3 / .col-* wrapper
         3. Sibling of the input itself                                    */
    const inputGroup = input.closest('.input-group');
    let   feedback   = null;

    if (inputGroup) {
        feedback = inputGroup.querySelector('.invalid-feedback')
                || inputGroup.parentElement?.querySelector('.invalid-feedback');
    }

    if (!feedback) {
        const wrapper = input.closest('.mb-3, .col-md-6, .col-sm-5, .col-12, .form-group');
        feedback = wrapper ? wrapper.querySelector('.invalid-feedback') : null;
    }

    if (!feedback) {
        feedback = input.nextElementSibling?.classList?.contains('invalid-feedback')
            ? input.nextElementSibling
            : null;
    }

    /* ── Create the element if it is still missing ───────────────────── */
    if (!feedback) {
        feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        const anchor = inputGroup || input;
        anchor.insertAdjacentElement('afterend', feedback);
    }

    feedback.textContent = message;
}

/**
 * Mark a field as VALID (Bootstrap `is-valid`).
 *
 * @param {HTMLElement} input
 */
function showValid(input) {
    if (!input) return;
    input.classList.remove('is-invalid');
    input.classList.add('is-valid');
}

/**
 * Remove both validation state classes (neutral state).
 *
 * @param {HTMLElement} input
 */
function clearValidation(input) {
    if (!input) return;
    input.classList.remove('is-valid', 'is-invalid');
}

/**
 * Validate an e-mail address format.
 * Uses a widely-accepted regex that covers the common cases.
 *
 * @param  {string}  email
 * @returns {boolean}
 */
function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(String(email).trim());
}

/**
 * Validate an optional phone number.
 * Accepts an optional leading +, then digits, spaces and hyphens, 7–20 chars.
 *
 * @param  {string}  phone
 * @returns {boolean}
 */
function validatePhone(phone) {
    return /^\+?[\d\s\-]{7,20}$/.test(String(phone).trim());
}

/**
 * Calculate password strength and return a descriptor object.
 *
 * Scoring rules:
 *   score 0 → empty
 *   score 1 → Weak       – shorter than 8 chars (but has some chars)
 *   score 2 → Medium     – 8+ chars with uppercase+lowercase OR letter+digit
 *   score 3 → Strong     – 8+ chars with uppercase+lowercase+digit
 *   score 4 → Very Strong– 8+ chars with upper+lower+digit+special character
 *
 * @param  {string} password
 * @returns {{ score: 0|1|2|3|4, label: string, color: string }}
 */
function getPasswordStrength(password) {
    if (!password || password.length === 0) {
        return { score: 0, label: '', color: '' };
    }

    const len        = password.length;
    const hasUpper   = /[A-Z]/.test(password);
    const hasLower   = /[a-z]/.test(password);
    const hasDigit   = /\d/.test(password);
    const hasSpecial = /[^A-Za-z\d]/.test(password);

    /* Too short → always weak */
    if (len < 8) {
        return { score: 1, label: 'Weak', color: '#dc2626' };      // red
    }

    /* Very strong: upper + lower + digit + special */
    if (hasUpper && hasLower && hasDigit && hasSpecial) {
        return { score: 4, label: 'Very Strong', color: '#16a34a' }; // green
    }

    /* Strong: upper + lower + digit */
    if (hasUpper && hasLower && hasDigit) {
        return { score: 3, label: 'Strong', color: '#2563eb' };     // blue
    }

    /* Medium: upper+lower OR (letter+digit) */
    if ((hasUpper && hasLower) || ((hasUpper || hasLower) && hasDigit)) {
        return { score: 2, label: 'Medium', color: '#d97706' };     // amber/yellow
    }

    /* 8+ chars but very simple */
    return { score: 1, label: 'Weak', color: '#dc2626' };           // red
}

/* ════════════════════════════════════════════════════════════════════════════
   SECTION 2 – PASSWORD STRENGTH BAR
════════════════════════════════════════════════════════════════════════════ */

/**
 * Wire a password strength progress bar to a password input.
 *
 * Tries to find existing bar elements by ID (as used in the profile view):
 *   #strengthWrap  – the outer container to show/hide
 *   #strengthBar   – the Bootstrap progress-bar div
 *   #strengthLabel – the <small> text label
 *
 * If none of those exist (e.g. the register form), creates them dynamically
 * and inserts them after the input (or its .input-group wrapper).
 *
 * @param {HTMLInputElement} passwordInput – the <input type="password"> to watch
 * @param {string}           [idSuffix=''] – suffix to avoid ID collisions when
 *                                           two bars exist on the same page
 */
function wireStrengthBar(passwordInput, idSuffix) {
    if (!passwordInput) return;

    /* ── Try to find pre-existing bar elements in DOM ─────────────────── */
    let wrap  = document.getElementById('strengthWrap')
             || document.getElementById('passwordStrength');
    let bar   = document.getElementById('strengthBar');
    let label = document.getElementById('strengthLabel');

    /* ── If elements don't exist, create and inject them ─────────────── */
    if (!wrap) {
        const suffix = idSuffix || '';

        wrap  = document.createElement('div');
        bar   = document.createElement('div');
        label = document.createElement('small');

        wrap.className     = 'mt-2';
        wrap.style.display = 'none';
        if (suffix) wrap.id = 'strengthWrap' + suffix;

        /* Progress container (mimics Bootstrap's .progress) */
        const progressTrack = document.createElement('div');
        progressTrack.className     = 'progress';
        progressTrack.style.height  = '5px';
        progressTrack.style.borderRadius = '99px';

        bar.className             = 'progress-bar';
        bar.setAttribute('role', 'progressbar');
        bar.style.width           = '0%';
        bar.style.borderRadius    = '99px';
        bar.style.transition      = 'width .25s ease, background-color .25s ease';

        label.style.fontSize = '.78rem';
        label.style.fontWeight = '600';

        progressTrack.appendChild(bar);
        wrap.appendChild(progressTrack);
        wrap.appendChild(label);

        /* Insert right after the input's .input-group (or the input itself) */
        const insertAfter = passwordInput.closest('.input-group') || passwordInput;
        insertAfter.insertAdjacentElement('afterend', wrap);

    } else {
        /* Existing elements – resolve bar/label if null */
        if (!bar)   bar   = wrap.querySelector('.progress-bar') || wrap.querySelector('[role="progressbar"]');
        if (!label) label = wrap.querySelector('small');
    }

    /* ── Bind the input event ─────────────────────────────────────────── */
    passwordInput.addEventListener('input', () => {
        const val      = passwordInput.value;
        const strength = getPasswordStrength(val);

        /* Show / hide the wrapper */
        wrap.style.display = val ? 'block' : 'none';

        if (bar) {
            bar.style.width           = (strength.score * 25) + '%';
            bar.style.backgroundColor = strength.color;
        }

        if (label) {
            label.textContent = strength.label;
            label.style.color = strength.color;
        }
    });
}

/* ════════════════════════════════════════════════════════════════════════════
   SECTION 3 – MAIN INITIALISATION (runs after DOM is ready)
════════════════════════════════════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {

    /* ╔══════════════════════════════════════════════════════════════════╗
       ║  FORM 1 – REGISTRATION                                          ║
       ║  The PHP view uses id="register-form"; the spec id="registerForm"║
       ║  We check both so the file works regardless of which is present. ║
       ╚══════════════════════════════════════════════════════════════════╝ */

    const registerForm = document.getElementById('registerForm')
                      || document.getElementById('register-form');

    if (registerForm) {

        /* Always disable native browser validation so we control it fully */
        registerForm.setAttribute('novalidate', '');

        /* ── Field refs ─────────────────────────────────────────────── */
        const nameInput    = registerForm.querySelector('#name');
        const emailInput   = registerForm.querySelector('#email');
        const passwordInput= registerForm.querySelector('#password');
        const confirmInput = registerForm.querySelector('#password_confirm');
        const roleInput    = registerForm.querySelector('#role');
        const phoneInput   = registerForm.querySelector('#phone');

        /* ── Password strength bar ──────────────────────────────────── */
        wireStrengthBar(passwordInput, '_reg');

        /* ── Live validation: email format ──────────────────────────── */
        if (emailInput) {
            emailInput.addEventListener('input', () => {
                if (!emailInput.value.trim()) { clearValidation(emailInput); return; }
                validateEmail(emailInput.value)
                    ? showValid(emailInput)
                    : showError(emailInput, 'Please enter a valid email address (e.g. you@example.com).');
            });
            emailInput.addEventListener('blur', () => {
                if (!emailInput.value.trim()) {
                    showError(emailInput, 'Email address is required.');
                }
            });
        }

        /* ── Live validation: confirm password match ────────────────── */
        const checkRegisterConfirm = () => {
            if (!confirmInput || !confirmInput.value) {
                clearValidation(confirmInput);
                return;
            }
            confirmInput.value === (passwordInput ? passwordInput.value : '')
                ? showValid(confirmInput)
                : showError(confirmInput, 'Passwords do not match.');
        };
        if (confirmInput)  confirmInput.addEventListener('input',  checkRegisterConfirm);
        if (passwordInput) passwordInput.addEventListener('input', checkRegisterConfirm);

        /* ── Live validation: optional phone ────────────────────────── */
        if (phoneInput) {
            phoneInput.addEventListener('input', () => {
                if (!phoneInput.value.trim()) { clearValidation(phoneInput); return; }
                validatePhone(phoneInput.value)
                    ? showValid(phoneInput)
                    : showError(phoneInput, 'Enter a valid phone number (7–20 digits; may include +, spaces or hyphens).');
            });
        }

        /* ── Submit handler ─────────────────────────────────────────── */
        registerForm.addEventListener('submit', (e) => {
            e.preventDefault();
            let isValid = true;

            /* Name – required, max 100 */
            if (!nameInput?.value.trim()) {
                showError(nameInput, 'Full name is required.');
                isValid = false;
            } else if (nameInput.value.trim().length > 100) {
                showError(nameInput, 'Name must not exceed 100 characters.');
                isValid = false;
            } else {
                showValid(nameInput);
            }

            /* Email – required, valid format */
            if (!emailInput?.value.trim()) {
                showError(emailInput, 'Email address is required.');
                isValid = false;
            } else if (!validateEmail(emailInput.value)) {
                showError(emailInput, 'Please enter a valid email address.');
                isValid = false;
            } else {
                showValid(emailInput);
            }

            /* Password – required, min 8 chars */
            if (!passwordInput?.value) {
                showError(passwordInput, 'Password is required.');
                isValid = false;
            } else if (passwordInput.value.length < 8) {
                showError(passwordInput, 'Password must be at least 8 characters long.');
                isValid = false;
            } else {
                showValid(passwordInput);
            }

            /* Confirm password – must match */
            if (!confirmInput?.value) {
                showError(confirmInput, 'Please confirm your password.');
                isValid = false;
            } else if (confirmInput.value !== (passwordInput?.value ?? '')) {
                showError(confirmInput, 'Passwords do not match.');
                isValid = false;
            } else {
                showValid(confirmInput);
            }

            /* Role – must select a non-empty option */
            if (!roleInput?.value) {
                showError(roleInput, 'Please select an account type.');
                isValid = false;
            } else {
                showValid(roleInput);
            }

            /* Phone – optional but validated when filled */
            if (phoneInput?.value.trim()) {
                if (!validatePhone(phoneInput.value)) {
                    showError(phoneInput, 'Enter a valid phone number (7–20 digits; may include +, spaces or hyphens).');
                    isValid = false;
                } else {
                    showValid(phoneInput);
                }
            }

            /* Only submit when all fields pass */
            if (isValid) {
                registerForm.submit();
            } else {
                /* Scroll the first invalid field into view */
                const firstInvalid = registerForm.querySelector('.is-invalid');
                firstInvalid?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });

    } /* end registerForm */


    /* ╔══════════════════════════════════════════════════════════════════╗
       ║  FORM 2 – PROFILE UPDATE (#profileForm)                         ║
       ╚══════════════════════════════════════════════════════════════════╝ */

    const profileForm = document.getElementById('profileForm');

    if (profileForm) {

        profileForm.setAttribute('novalidate', '');

        /* ── Field refs ─────────────────────────────────────────────── */
        const nameInput  = profileForm.querySelector('#name');
        const emailInput = profileForm.querySelector('#email');
        const phoneInput = profileForm.querySelector('#phone');
        const picInput   = profileForm.querySelector('#profile_picture');

        /* ── Avatar preview element
               Spec says #avatarPreview > img; actual view uses #profilePreview ── */
        const avatarImg = document.querySelector('#avatarPreview img')
                       || document.getElementById('profilePreview');

        /* ── Live: email ────────────────────────────────────────────── */
        if (emailInput) {
            emailInput.addEventListener('input', () => {
                if (!emailInput.value.trim()) { clearValidation(emailInput); return; }
                validateEmail(emailInput.value)
                    ? showValid(emailInput)
                    : showError(emailInput, 'Please enter a valid email address.');
            });
        }

        /* ── Live: phone (optional) ─────────────────────────────────── */
        if (phoneInput) {
            phoneInput.addEventListener('input', () => {
                if (!phoneInput.value.trim()) { clearValidation(phoneInput); return; }
                validatePhone(phoneInput.value)
                    ? showValid(phoneInput)
                    : showError(phoneInput, 'Enter a valid phone number (7–20 digits; may include +, spaces or hyphens).');
            });
        }

        /* ── Live: profile picture – validate & show preview ─────────── */
        if (picInput) {
            picInput.addEventListener('change', () => {
                const file    = picInput.files[0];
                const MAX_MB  = 5 * 1024 * 1024;                           // 5 MB
                const ALLOWED = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

                if (!file) return;

                /* MIME type check */
                if (!ALLOWED.includes(file.type)) {
                    showError(picInput, 'Only JPEG, PNG, GIF or WebP images are accepted.');
                    picInput.value = '';
                    return;
                }

                /* File size check */
                if (file.size > MAX_MB) {
                    showError(picInput, 'Image file must be smaller than 5 MB.');
                    picInput.value = '';
                    return;
                }

                showValid(picInput);

                /* ── Live preview ─────────────────────────────────────
                   Show the selected image in #profilePreview (or #avatarPreview img).
                   Also hide the placeholder icon div if it exists.       */
                if (avatarImg) {
                    const reader = new FileReader();
                    reader.onload = (ev) => {
                        avatarImg.src = ev.target.result;
                        avatarImg.classList.remove('d-none');

                        /* Hide the placeholder div (#profilePlaceholder) */
                        const placeholder = document.getElementById('profilePlaceholder');
                        if (placeholder) placeholder.style.display = 'none';
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        /* ── Submit handler ─────────────────────────────────────────── */
        profileForm.addEventListener('submit', (e) => {
            e.preventDefault();
            let isValid = true;

            /* Name – required, max 100 */
            if (!nameInput?.value.trim()) {
                showError(nameInput, 'Full name is required.');
                isValid = false;
            } else if (nameInput.value.trim().length > 100) {
                showError(nameInput, 'Name must not exceed 100 characters.');
                isValid = false;
            } else {
                showValid(nameInput);
            }

            /* Email – required, valid format */
            if (!emailInput?.value.trim()) {
                showError(emailInput, 'Email address is required.');
                isValid = false;
            } else if (!validateEmail(emailInput.value)) {
                showError(emailInput, 'Please enter a valid email address.');
                isValid = false;
            } else {
                showValid(emailInput);
            }

            /* Phone – optional, validated if filled */
            if (phoneInput?.value.trim()) {
                if (!validatePhone(phoneInput.value)) {
                    showError(phoneInput, 'Enter a valid phone number (7–20 digits; may include +, spaces or hyphens).');
                    isValid = false;
                } else {
                    showValid(phoneInput);
                }
            }

            /* Profile picture – optional, but validate if a new file was selected */
            if (picInput?.files.length > 0) {
                const file    = picInput.files[0];
                const MAX_MB  = 5 * 1024 * 1024;
                const ALLOWED = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

                if (!ALLOWED.includes(file.type)) {
                    showError(picInput, 'Only JPEG, PNG, GIF or WebP images are accepted.');
                    isValid = false;
                } else if (file.size > MAX_MB) {
                    showError(picInput, 'Image file must be smaller than 5 MB.');
                    isValid = false;
                } else {
                    showValid(picInput);
                }
            }

            if (isValid) {
                profileForm.submit();
            } else {
                const firstInvalid = profileForm.querySelector('.is-invalid');
                firstInvalid?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });

    } /* end profileForm */


    /* ╔══════════════════════════════════════════════════════════════════╗
       ║  FORM 3 – CHANGE PASSWORD                                       ║
       ║  Spec id: #changePasswordForm; actual view id: #passwordForm     ║
       ╚══════════════════════════════════════════════════════════════════╝ */

    const changePasswordForm = document.getElementById('changePasswordForm')
                            || document.getElementById('passwordForm');

    if (changePasswordForm) {

        changePasswordForm.setAttribute('novalidate', '');

        /* ── Field refs ─────────────────────────────────────────────── */
        const currentPwdInput = changePasswordForm.querySelector('#current_password');
        const newPwdInput     = changePasswordForm.querySelector('#new_password');
        const confirmPwdInput = changePasswordForm.querySelector('#confirm_password');

        /* ── Match indicator (#matchIndicator exists in profile view) ── */
        const matchIndicator = document.getElementById('matchIndicator');

        /* ── Wire strength bar to the new password field ────────────── */
        wireStrengthBar(newPwdInput);

        /* ── Password visibility toggle buttons (.pwd-toggle) ───────── */
        changePasswordForm.querySelectorAll('.pwd-toggle').forEach(btn => {
            btn.addEventListener('click', () => {
                const targetId = btn.dataset.target;
                const field    = document.getElementById(targetId);
                if (!field) return;

                const nowHidden = (field.type === 'password');
                field.type      = nowHidden ? 'text' : 'password';

                const icon = btn.querySelector('i');
                if (icon) {
                    icon.className = nowHidden ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
                }
                btn.setAttribute('title', nowHidden ? 'Hide password' : 'Show password');
                btn.setAttribute('aria-label', nowHidden ? 'Hide password' : 'Show password');
            });
        });

        /* ── Live: confirm password match ───────────────────────────── */
        const checkPwdMatch = () => {
            if (!confirmPwdInput?.value) {
                clearValidation(confirmPwdInput);
                if (matchIndicator) matchIndicator.classList.add('d-none');
                return;
            }

            if (confirmPwdInput.value === (newPwdInput?.value ?? '')) {
                showValid(confirmPwdInput);
                if (matchIndicator) matchIndicator.classList.add('d-none');
            } else {
                showError(confirmPwdInput, 'Passwords do not match.');
                if (matchIndicator) matchIndicator.classList.remove('d-none');
            }
        };

        if (confirmPwdInput) confirmPwdInput.addEventListener('input', checkPwdMatch);
        if (newPwdInput)     newPwdInput.addEventListener('input',     checkPwdMatch);

        /* ── Submit handler ─────────────────────────────────────────── */
        changePasswordForm.addEventListener('submit', (e) => {
            e.preventDefault();
            let isValid = true;

            /* Current password – required */
            if (!currentPwdInput?.value) {
                showError(currentPwdInput, 'Current password is required.');
                isValid = false;
            } else {
                showValid(currentPwdInput);
            }

            /* New password – required, min 8 chars */
            if (!newPwdInput?.value) {
                showError(newPwdInput, 'New password is required.');
                isValid = false;
            } else if (newPwdInput.value.length < 8) {
                showError(newPwdInput, 'New password must be at least 8 characters long.');
                isValid = false;
            } else {
                showValid(newPwdInput);
            }

            /* Confirm password – must match new */
            if (!confirmPwdInput?.value) {
                showError(confirmPwdInput, 'Please confirm your new password.');
                if (matchIndicator) matchIndicator.classList.remove('d-none');
                isValid = false;
            } else if (confirmPwdInput.value !== (newPwdInput?.value ?? '')) {
                showError(confirmPwdInput, 'Passwords do not match.');
                if (matchIndicator) matchIndicator.classList.remove('d-none');
                isValid = false;
            } else {
                showValid(confirmPwdInput);
                if (matchIndicator) matchIndicator.classList.add('d-none');
            }

            if (isValid) {
                changePasswordForm.submit();
            } else {
                const firstInvalid = changePasswordForm.querySelector('.is-invalid');
                firstInvalid?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });

    } /* end changePasswordForm */


    /* ════════════════════════════════════════════════════════════════════
       MISC – Flash alert auto-dismiss is handled in footer.php inline script.
       No additional setup needed here.
    ════════════════════════════════════════════════════════════════════ */

}); /* end DOMContentLoaded */

/* ════════════════════════════════════════════════════════════════════════════
   Expose helpers globally so PHP views / inline scripts can call them if needed
════════════════════════════════════════════════════════════════════════════ */
window.MediValidation = {
    showError,
    showValid,
    clearValidation,
    validateEmail,
    validatePhone,
    getPasswordStrength,
};
