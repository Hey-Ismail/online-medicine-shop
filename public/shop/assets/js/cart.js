(() => {
  const baseUrlMeta = document.querySelector('meta[name="base-url"]');
  const csrfMeta = document.querySelector('meta[name="csrf-token"]');
  const baseUrl = baseUrlMeta ? baseUrlMeta.getAttribute("content") : "";
  const csrfToken = csrfMeta ? csrfMeta.getAttribute("content") : "";

  const cartError = document.querySelector(".js-cart-error");

  const formatMoney = (amount) => {
    const value = Number(amount || 0);
    return value.toFixed(2);
  };

  const showError = (message) => {
    if (cartError) {
      cartError.textContent = message;
      cartError.hidden = false;
    } else {
      alert(message);
    }
  };

  const clearError = () => {
    if (cartError) {
      cartError.textContent = "";
      cartError.hidden = true;
    }
  };

  const updateCartCount = (count) => {
    const countEl = document.querySelector(".js-cart-count");
    if (countEl) {
      countEl.textContent = count;
    }
    const inlineEl = document.querySelector(".js-cart-count-inline");
    if (inlineEl) {
      inlineEl.textContent = count;
    }
  };

  const updateCartTotal = (amount) => {
    const totalEl = document.querySelector(".js-cart-total");
    if (totalEl) {
      totalEl.textContent = formatMoney(amount);
    }
  };

  const sendRequest = async (url, method, data) => {
    const options = {
      method: method,
      headers: {
        Accept: "application/json",
        "X-CSRF-Token": csrfToken || "",
      },
      credentials: "same-origin",
    };

    if (data) {
      options.headers["Content-Type"] = "application/x-www-form-urlencoded";
      options.body = new URLSearchParams(data).toString();
    }

    const response = await fetch(baseUrl + url, options);
    const payload = await response.json().catch(() => null);

    if (!response.ok || !payload || !payload.success) {
      const message =
        payload && payload.error ? payload.error : "Request failed.";
      throw new Error(message);
    }

    return payload;
  };

  document.addEventListener("submit", async (event) => {
    const form = event.target.closest(
      ".add-to-cart-form, .js-add-to-cart-form",
    );
    if (!form) {
      return;
    }

    event.preventDefault();
    clearError();

    const medicineInput = form.querySelector('[name="medicine_id"]');
    const quantityInput = form.querySelector('[name="quantity"]');
    const medicineId = medicineInput ? medicineInput.value : "";
    const quantity = quantityInput ? quantityInput.value : "1";
    const qtyNumber = parseInt(quantity, 10);
    const maxAttr = quantityInput
      ? parseInt(quantityInput.getAttribute("max") || "0", 10)
      : 0;

    if (!medicineId || !qtyNumber || qtyNumber <= 0) {
      showError("Please enter a valid quantity.");
      return;
    }

    if (maxAttr > 0 && qtyNumber > maxAttr) {
      showError("Quantity exceeds available stock.");
      return;
    }

    try {
      const data = await sendRequest("/api/cart/add", "POST", {
        medicine_id: medicineId,
        quantity: qtyNumber,
        _csrf: csrfToken,
      });
      updateCartCount(data.cartCount);
    } catch (error) {
      showError(error.message);
    }
  });

  document.addEventListener("click", async (event) => {
    const plus = event.target.closest(".js-qty-plus");
    const minus = event.target.closest(".js-qty-minus");
    const remove = event.target.closest(".js-remove-item");

    if (!plus && !minus && !remove) {
      return;
    }

    const row = event.target.closest(".js-cart-row");
    if (!row) {
      return;
    }

    clearError();

    const medicineId = row.dataset.medicineId;
    const max = parseInt(row.dataset.max || "1", 10);
    const input = row.querySelector(".js-qty-input");

    if (remove) {
      try {
        const data = await sendRequest("/api/cart/remove", "DELETE", {
          medicine_id: medicineId,
          _csrf: csrfToken,
        });
        row.remove();
        updateCartCount(data.cartCount);
        updateCartTotal(data.cartTotal);
        updateEmptyStateIfNeeded();
      } catch (error) {
        showError(error.message);
      }
      return;
    }

    if (!input) {
      return;
    }

    let nextQty = parseInt(input.value || "1", 10);
    nextQty = plus ? nextQty + 1 : nextQty - 1;

    if (nextQty < 1) {
      nextQty = 1;
    }

    if (nextQty > max) {
      showError("Quantity exceeds available stock.");
      return;
    }

    try {
      const data = await sendRequest("/api/cart/update", "POST", {
        medicine_id: medicineId,
        quantity: nextQty,
        _csrf: csrfToken,
      });
      input.value = data.quantity;
      const subtotalEl = row.querySelector(".js-subtotal");
      if (subtotalEl) {
        subtotalEl.textContent = formatMoney(data.itemSubtotal);
      }
      updateCartCount(data.cartCount);
      updateCartTotal(data.cartTotal);
    } catch (error) {
      showError(error.message);
    }
  });

  document.addEventListener("change", async (event) => {
    const input = event.target.closest(".js-qty-input");
    if (!input) {
      return;
    }

    const row = event.target.closest(".js-cart-row");
    if (!row) {
      return;
    }

    clearError();

    const medicineId = row.dataset.medicineId;
    const max = parseInt(row.dataset.max || "1", 10);
    let nextQty = parseInt(input.value || "1", 10);

    if (!Number.isInteger(nextQty) || nextQty < 1) {
      nextQty = 1;
    }

    if (nextQty > max) {
      showError("Quantity exceeds available stock.");
      input.value = max;
      nextQty = max;
    }

    try {
      const data = await sendRequest("/api/cart/update", "POST", {
        medicine_id: medicineId,
        quantity: nextQty,
        _csrf: csrfToken,
      });
      input.value = data.quantity;
      const subtotalEl = row.querySelector(".js-subtotal");
      if (subtotalEl) {
        subtotalEl.textContent = formatMoney(data.itemSubtotal);
      }
      updateCartCount(data.cartCount);
      updateCartTotal(data.cartTotal);
    } catch (error) {
      showError(error.message);
    }
  });

  const updateEmptyStateIfNeeded = () => {
    const rows = document.querySelectorAll(".js-cart-row");
    if (rows.length > 0) {
      return;
    }

    const tableWrap = document.querySelector(".table-wrap");
    const summary = document.querySelector(".summary-card");
    if (tableWrap) {
      tableWrap.remove();
    }
    if (summary) {
      summary.remove();
    }

    const page = document.querySelector(".page");
    if (page) {
      const empty = document.createElement("div");
      empty.className = "empty-state";
      empty.innerHTML = "<p>Your cart is empty.</p>";
      page.appendChild(empty);
    }
  };
})();
