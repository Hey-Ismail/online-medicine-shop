/**
 * Client-side Form Validation
 */

/**
 * Validate email format
 */
function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

/**
 * Validate phone number
 */
function isValidPhone(phone) {
  const phoneRegex = /^\d{10,15}$/;
  return phoneRegex.test(phone.replace(/[\s-]/g, ""));
}

/**
 * Validate password strength
 */
function isStrongPassword(password) {
  return (
    password.length >= 8 &&
    /[a-z]/.test(password) &&
    /[A-Z]/.test(password) &&
    /\d/.test(password)
  );
}

/**
 * Clear error messages from form
 */
function clearErrors(form) {
  const errorMessages = form.querySelectorAll(".error-message");
  errorMessages.forEach((msg) => {
    msg.textContent = "";
  });
}

/**
 * Display validation errors
 */
function displayError(fieldId, message) {
  const errorElement = document.getElementById(fieldId + "-error");
  if (errorElement) {
    errorElement.textContent = message;
  }
}

/**
 * Validate file upload
 */
function validateFileUpload(fileInput, maxSizeMB, allowedTypes) {
  if (fileInput.files.length === 0) {
    return true; // File upload is optional
  }

  const file = fileInput.files[0];
  const maxSize = maxSizeMB * 1024 * 1024;

  if (file.size > maxSize) {
    displayError(fileInput.id, `File size must not exceed ${maxSizeMB}MB`);
    return false;
  }

  if (!allowedTypes.includes(file.type)) {
    displayError(
      fileInput.id,
      `File type must be one of: ${allowedTypes.join(", ")}`,
    );
    return false;
  }

  return true;
}

/**
 * Debounce function for real-time validation
 */
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

/**
 * Real-time email validation
 */
const validateEmailRealtime = debounce(function (emailInput) {
  if (emailInput.value && !isValidEmail(emailInput.value)) {
    displayError(emailInput.id, "Please enter a valid email address");
  } else {
    displayError(emailInput.id, "");
  }
}, 500);

/**
 * Real-time price validation
 */
const validatePriceRealtime = debounce(function (priceInput) {
  const price = parseFloat(priceInput.value);
  if (priceInput.value && (isNaN(price) || price <= 0)) {
    displayError(priceInput.id, "Price must be a positive number");
  } else {
    displayError(priceInput.id, "");
  }
}, 500);

// Add event listeners for real-time validation
document.addEventListener("DOMContentLoaded", function () {
  // Email validation listeners
  document.querySelectorAll('input[type="email"]').forEach((input) => {
    input.addEventListener("input", function () {
      validateEmailRealtime(this);
    });
  });

  // Price validation listeners
  document
    .querySelectorAll('input[type="number"][id*="price"]')
    .forEach((input) => {
      input.addEventListener("input", function () {
        validatePriceRealtime(this);
      });
    });

  // File input listeners
  document.querySelectorAll('input[type="file"]').forEach((input) => {
    input.addEventListener("change", function () {
      if (this.files.length > 0) {
        const maxSize = 2; // 2MB
        const allowedTypes = ["image/jpeg", "image/png"];
        validateFileUpload(this, maxSize, allowedTypes);
      }
    });
  });
});
