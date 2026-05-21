(() => {
  const addressForm = document.querySelector("#checkout-address-form");
  if (addressForm) {
    addressForm.addEventListener("submit", (event) => {
      const addressInput = addressForm.querySelector(
        '[name="shipping_address"]',
      );
      const errorEl =
        addressForm.querySelector(".form-error") || createErrorEl(addressForm);
      if (!addressInput || !addressInput.value.trim()) {
        event.preventDefault();
        errorEl.textContent = "Shipping address is required.";
      } else {
        errorEl.textContent = "";
      }
    });
  }

  const paymentForm = document.querySelector("#payment-form");
  if (paymentForm) {
    paymentForm.addEventListener("submit", (event) => {
      const selected = paymentForm.querySelector(
        'input[name="payment_method"]:checked',
      );
      const errorEl =
        paymentForm.querySelector(".form-error") || createErrorEl(paymentForm);
      if (!selected) {
        event.preventDefault();
        errorEl.textContent = "Please select a payment method.";
      } else {
        errorEl.textContent = "";
      }
    });
  }

  function createErrorEl(form) {
    const error = document.createElement("p");
    error.className = "form-error";
    form.appendChild(error);
    return error;
  }
})();
