const ADMIN_BASE_PATH = window.location.pathname.replace(/\/index\.php.*$/, "").replace(/\/$/, "");

/**
 * Admin Panel JavaScript Functions
 */

/**
 * Delete Category with confirmation
 */
function deleteCategory(categoryId) {
  if (
    !confirm(
      "Are you sure you want to delete this category? This action cannot be undone.",
    )
  ) {
    return;
  }

  const formData = new FormData();
  formData.append("action", "delete_category");
  formData.append("category_id", categoryId);

  fetch(`${ADMIN_BASE_PATH}/index.php?action=delete_category`, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showNotification("Category deleted successfully", "success");
        setTimeout(() => location.reload(), 1500);
      } else {
        showNotification("Error: " + (data.error || "Unknown error"), "error");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showNotification("An error occurred: " + error.message, "error");
    });
}

/**
 * Delete Medicine with confirmation
 */
function deleteMedicine(medicineId) {
  if (
    !confirm(
      "Are you sure you want to delete this medicine? This action cannot be undone.",
    )
  ) {
    return;
  }

  const formData = new FormData();
  formData.append("action", "delete_medicine");
  formData.append("medicine_id", medicineId);

  fetch(`${ADMIN_BASE_PATH}/index.php?action=delete_medicine`, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showNotification("Medicine deleted successfully", "success");
        setTimeout(() => location.reload(), 1500);
      } else {
        showNotification("Error: " + (data.error || "Unknown error"), "error");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showNotification("An error occurred: " + error.message, "error");
    });
}

/**
 * Delete Customer with confirmation
 */
function deleteCustomer(customerId) {
  if (
    !confirm(
      "Are you sure you want to delete this customer? This action will also delete all their orders and cart items. This cannot be undone.",
    )
  ) {
    return;
  }

  const formData = new FormData();
  formData.append("action", "delete_customer");
  formData.append("customer_id", customerId);

  fetch(`${ADMIN_BASE_PATH}/index.php?action=delete_customer`, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showNotification("Customer deleted successfully", "success");
        setTimeout(() => location.reload(), 1500);
      } else {
        showNotification("Error: " + (data.error || "Unknown error"), "error");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showNotification("An error occurred: " + error.message, "error");
    });
}

/**
 * Show notification message
 */
function showNotification(message, type = "info") {
  const notification = document.createElement("div");
  notification.className = `alert alert-${type}`;
  notification.textContent = message;
  notification.style.position = "fixed";
  notification.style.top = "20px";
  notification.style.right = "20px";
  notification.style.zIndex = "9999";
  notification.style.minWidth = "300px";

  document.body.appendChild(notification);

  setTimeout(() => {
    notification.remove();
  }, 5000);
}

/**
 * Toggle mobile sidebar
 */
function toggleSidebar() {
  const sidebar = document.querySelector(".admin-sidebar");
  if (sidebar) {
    sidebar.classList.toggle("active");
  }
}

/**
 * Close sidebar when clicking on a link
 */
document.querySelectorAll(".nav-item").forEach((link) => {
  link.addEventListener("click", function () {
    if (window.innerWidth <= 768) {
      toggleSidebar();
    }
  });
});
