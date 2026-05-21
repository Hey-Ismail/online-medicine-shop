/**
 * search.js
 * ─────────────────────────────────────────────────────────────────────────────
 * AJAX-powered medicine search for Online Medicine Shop (MediShop)
 * Pure vanilla JS – no jQuery dependency.
 *
 * Works on two pages simultaneously:
 *   • home/index.php  – featured medicines section with inline filter bar
 *   • medicines/browse.php – full catalogue with sidebar filters
 *
 * Public API (exposed as window.MediSearch):
 *   MediSearch.search(q)       – programmatic search
 *   MediSearch.clearAndFetch() – reset all filters and re-fetch
 * ─────────────────────────────────────────────────────────────────────────────
 */

(function () {
  "use strict";

  /* ════════════════════════════════════════════════════════════════════════
       SECTION 1 – CONFIGURATION & APP STATE
    ════════════════════════════════════════════════════════════════════════ */

  /**
   * BASE_PATH – the URL path prefix (e.g. '' for root or '/myapp').
   *
   * Priority:
   *   1. data-base-path attribute on <body>      (spec requirement)
   *   2. window.APP.basePath                     (set by PHP views)
   *   3. empty string (app is at web root)
   */
  const BASE_PATH =
    document.body.dataset.basePath || (window.APP && window.APP.basePath) || "";

  /**
   * BASE_URL – full origin + path prefix (needed for image src URLs).
   * PHP views expose this as APP.baseUrl.
   */
  const BASE_URL = (window.APP && window.APP.baseUrl) || "";

  /** AJAX endpoint for medicine search */
  const API_URL = BASE_PATH + "/api/medicines/search";

  /** Debounce delay in milliseconds for text inputs */
  const DEBOUNCE_MS = 350;

  /**
   * APP – shared application state.
   *
   * We merge in values already set by PHP (window.APP is defined at the
   * top of browse.php and home/index.php via an inline <script> block).
   * Values the PHP didn't set default to sensible JS values below.
   *
   * typeFilter: '' (from PHP) is normalised to 'all' here.
   */
  const APP = {
    /** 'all' | 'solid' | 'liquid' – active medicine type filter */
    typeFilter: (window.APP && window.APP.typeFilter) || "all",
    /** Current query string sent to the API */
    currentQ: "",
    /** Currently selected vendor filter */
    currentVendor: "",
    /** Currently selected genre/category filter */
    currentGenre: "",
    /** Whether the user is logged in (from PHP) */
    isLoggedIn: !!(window.APP && window.APP.isLoggedIn),
    /** User role: 'guest' | 'customer' | 'admin' */
    role: (window.APP && window.APP.role) || "guest",
  };

  /* Normalise typeFilter: PHP outputs '' for "all types" */
  if (!APP.typeFilter || APP.typeFilter === "") APP.typeFilter = "all";

  /* Expose the merged state so PHP inline scripts can also access it */
  window.APP = Object.assign(window.APP || {}, APP);

  /* ════════════════════════════════════════════════════════════════════════
       SECTION 2 – DOM ELEMENT REFERENCES
       All elements are optional – only pages that render them will have them.
    ════════════════════════════════════════════════════════════════════════ */

  /* ── Filter inputs ────────────────────────────────────────────────── */
  const searchInput = document.getElementById("medicineSearch"); // text input
  const vendorFilter = document.getElementById("vendorFilter"); // <select>
  const genreFilter = document.getElementById("genreFilter"); // <select>
  const typeRadios = document.querySelectorAll('input[name="typeFilter"]');

  /* ── Grid & status elements ────────────────────────────────────────── */
  const medicineGrid = document.getElementById("medicineGrid"); // results container

  // Home page uses #resultsCount (full sentence); browse page uses #resultsNum + #resultsLabel
  const resultsCountEl = document.getElementById("resultsCount"); // home – full text span
  const resultsNumEl = document.getElementById("resultsNum"); // browse – number only
  const resultsLabelEl = document.getElementById("resultsLabel"); // browse – "medicine(s)"
  const searchSpinner = document.getElementById("searchSpinner"); // spinner overlay

  /* ── Control buttons ───────────────────────────────────────────────── */
  // Spec says #clearFilters; browse.php uses #clearFiltersBtn
  const clearFiltersBtn =
    document.getElementById("clearFilters") ||
    document.getElementById("clearFiltersBtn");
  // Second "reset" button that lives inside the #noResults empty-state
  const resetFiltersBtn = document.getElementById("resetFiltersBtn");
  // The X button inside the search input-group
  const clearSearchBtn = document.getElementById("clearSearch");

  /* ── Empty-state container (browse.php) ────────────────────────────── */
  const noResultsEl = document.getElementById("noResults");

  /* ── Vendor checkboxes in sidebar ──────────────────────────────────── */
  // browse.php uses .vendor-check; spec mentions .vendor-checkbox – support both
  const vendorCheckboxes = document.querySelectorAll(
    ".vendor-checkbox, .vendor-check",
  );

  /* ── Hero search (home page) ───────────────────────────────────────── */
  const heroForm = document.getElementById("heroSearchForm");
  const heroInput = document.getElementById("heroSearch");

  /* ════════════════════════════════════════════════════════════════════════
       SECTION 3 – UTILITY FUNCTIONS
    ════════════════════════════════════════════════════════════════════════ */

  /**
   * Classic debounce – delays execution of `fn` until `delay` ms after
   * the last call.
   *
   * @param  {Function} fn
   * @param  {number}   delay  milliseconds
   * @returns {Function}
   */
  function debounce(fn, delay) {
    let timer;
    return function (...args) {
      clearTimeout(timer);
      timer = setTimeout(() => fn.apply(this, args), delay);
    };
  }

  /**
   * Safely escape a string for use inside HTML (prevents XSS in rendered cards).
   *
   * @param  {string} str
   * @returns {string}
   */
  function escapeHtml(str) {
    if (str == null) return "";
    const el = document.createElement("div");
    el.textContent = String(str);
    return el.innerHTML;
  }

  /**
   * Truncate a string to a maximum length, appending '…' when cut.
   *
   * @param  {string} str
   * @param  {number} maxLen
   * @returns {string}
   */
  function truncate(str, maxLen) {
    if (!str) return "";
    return str.length > maxLen ? str.slice(0, maxLen - 1) + "\u2026" : str;
  }

  /**
   * Get the current values from all active filter inputs.
   *
   * @returns {{ q: string, vendor: string, genre: string }}
   */
  function getCurrentFilters() {
    return {
      q: searchInput ? searchInput.value.trim() : "",
      vendor: vendorFilter ? vendorFilter.value.trim() : "",
      genre: genreFilter ? genreFilter.value.trim() : "",
    };
  }

  /* ════════════════════════════════════════════════════════════════════════
       SECTION 4 – LOADING STATE
    ════════════════════════════════════════════════════════════════════════ */

  /**
   * Show or hide a loading state on the medicine grid.
   * - Reduces opacity to 0.5 and blocks pointer events while fetching.
   * - Shows / hides the Bootstrap spinner (#searchSpinner).
   *
   * @param {boolean} isLoading
   */
  function setLoading(isLoading) {
    if (medicineGrid) {
      medicineGrid.style.opacity = isLoading ? "0.5" : "";
      medicineGrid.style.pointerEvents = isLoading ? "none" : "";
    }
    if (searchSpinner) {
      searchSpinner.classList.toggle("d-none", !isLoading);
    }
  }

  /* ════════════════════════════════════════════════════════════════════════
       SECTION 5 – RESULTS COUNT UPDATER
    ════════════════════════════════════════════════════════════════════════ */

  /**
   * Update the "Showing X medicines" text on whichever element exists.
   *
   * Home page  → updates #resultsCount with full sentence HTML
   * Browse page → updates #resultsNum (number) + #resultsLabel (word)
   *
   * @param {number} count
   */
  function updateResultsCount(count) {
    const word = count === 1 ? "medicine" : "medicines";

    /* Home page version – full sentence inside one span */
    if (resultsCountEl) {
      resultsCountEl.innerHTML = `Showing <strong>${count}</strong> ${word}`;
    }

    /* Browse page version – separate number and label elements */
    if (resultsNumEl) resultsNumEl.textContent = count;
    if (resultsLabelEl) resultsLabelEl.textContent = word;
  }

  /* ════════════════════════════════════════════════════════════════════════
       SECTION 6 – CARD HTML BUILDER
    ════════════════════════════════════════════════════════════════════════ */

  /**
   * Build the HTML string for a single medicine card.
   *
   * @param  {Object} med  – medicine object from the API
   * @param  {number} idx  – zero-based index (used for stagger animation delay)
   * @returns {string}      HTML string for one .col wrapper + card
   */
  function buildCardHTML(med, idx) {
    const isAvailable = parseInt(med.availability, 10) > 0;
    const isLiquid = med.category_type === "liquid";
    const isCustomer = APP.isLoggedIn && APP.role === "customer";
    const isAdmin = APP.isLoggedIn && APP.role === "admin";

    /* Image: use BASE_URL (full origin) so relative paths resolve correctly */
    const imgUrl =
      med.image_path && BASE_URL
        ? `${BASE_URL}/public/${med.image_path}`
        : null;

    /* Animated image block */
    const imageBlock = imgUrl
      ? `<img src="${escapeHtml(imgUrl)}"
                     alt="${escapeHtml(med.name)}"
                     class="w-100 h-100"
                     style="object-fit:cover;"
                     loading="lazy"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
               <div class="w-100 h-100 align-items-center justify-content-center"
                    style="font-size:4rem;position:absolute;top:0;left:0;
                           background:#f8fafc;display:none;">&#128138;</div>`
      : `<div class="w-100 h-100 d-flex align-items-center justify-content-center"
                    style="font-size:4rem;">&#128138;</div>`;

    /* Type badge (top-left) */
    const typeBadge = `
            <span class="position-absolute top-0 start-0 m-2 badge rounded-pill"
                  style="${
                    isLiquid
                      ? "background-color:#dcfce7;color:#16a34a;"
                      : "background-color:#dbeafe;color:#2563eb;"
                  }">
                <i class="fa-solid ${isLiquid ? "fa-bottle-droplet" : "fa-pills"} me-1"></i>
                ${isLiquid ? "Liquid" : "Solid"}
            </span>`;

    /* Stock badge (top-right) */
    const stockBadge = `
            <span class="position-absolute top-0 end-0 m-2 badge rounded-pill"
                  style="${
                    isAvailable
                      ? "background-color:#dcfce7;color:#16a34a;"
                      : "background-color:#fee2e2;color:#dc2626;"
                  }">
                ${isAvailable ? "In Stock" : "Out of Stock"}
            </span>`;

    /* Vendor badge */
    const vendorBadge = med.vendor_name
      ? `<span class="badge rounded-pill px-2 py-1"
                    style="background-color:#f1f5f9;color:#475569;font-size:.65rem;">
                   <i class="fa-solid fa-building me-1"></i>${escapeHtml(med.vendor_name)}
               </span>`
      : "";

    /* Category badge */
    const catBadge = med.category_name
      ? `<span class="badge rounded-pill px-2 py-1"
                    style="background-color:${isLiquid ? "#dcfce7" : "#dbeafe"};
                           color:${isLiquid ? "#16a34a" : "#2563eb"};font-size:.65rem;">
                   <i class="fa-solid ${isLiquid ? "fa-bottle-droplet" : "fa-pills"} me-1"></i>
                   ${escapeHtml(med.category_name)}
               </span>`
      : "";

    /* Description (truncated to 80 chars) */
    const desc = escapeHtml(truncate(med.description, 80));

    /* Cart / login button */
    let actionBtn = "";
    if (!isAvailable) {
      actionBtn = `<button class="btn btn-sm btn-outline-secondary rounded-pill" disabled>
                             <i class="fa-solid fa-ban me-1"></i>Out of Stock
                         </button>`;
    } else if (isCustomer) {
      /* POST to cart/add – wraps the button in a minimal form */
      actionBtn = `<form method="POST" action="${BASE_PATH}/cart/add" class="m-0">
                             <input type="hidden" name="medicine_id" value="${escapeHtml(String(med.id))}">
                             <button type="submit"
                                     class="btn btn-sm rounded-pill fw-semibold text-white"
                                     style="background-color:#2563eb;border-color:#2563eb;">
                                 <i class="fa-solid fa-cart-plus me-1"></i>Add to Cart
                             </button>
                         </form>`;
    } else if (isAdmin) {
      actionBtn = `<button class="btn btn-sm btn-outline-secondary rounded-pill" disabled>
                             <i class="fa-solid fa-eye me-1"></i>Admin View
                         </button>`;
    } else {
      /* Guest – prompt to log in */
      actionBtn = `<a href="${BASE_PATH}/login"
                            class="btn btn-sm btn-outline-primary rounded-pill fw-semibold">
                             <i class="fa-solid fa-right-to-bracket me-1"></i>Login to Buy
                         </a>`;
    }

    /* Stagger animation delay: each subsequent card is 50 ms later */
    const delay = (idx * 0.05).toFixed(2);

    return `
            <div class="col-sm-6 col-lg-4 fade-in-up" style="animation-delay:${delay}s;">
                <div class="medicine-card card h-100 border-0 shadow-sm rounded-4 overflow-hidden">

                    <!-- ── Image area ── -->
                    <div class="medicine-card-img position-relative"
                         style="height:180px;overflow:hidden;background:#f8fafc;">
                        ${imageBlock}
                        ${typeBadge}
                        ${stockBadge}
                    </div>

                    <!-- ── Card body ── -->
                    <div class="card-body d-flex flex-column p-3">
                        <h6 class="fw-bold mb-1 lh-sm"
                            style="color:#1e293b;display:-webkit-box;
                                   -webkit-line-clamp:2;-webkit-box-orient:vertical;
                                   overflow:hidden;">
                            ${escapeHtml(med.name)}
                        </h6>

                        <div class="d-flex flex-wrap gap-1 mb-2">
                            ${vendorBadge}
                            ${catBadge}
                        </div>

                        <p class="text-muted small mb-2 flex-grow-1">${desc}</p>

                        <div class="d-flex align-items-center justify-content-between mt-auto">
                            <span class="medicine-price-badge fw-bold"
                                  style="color:#16a34a;font-size:1rem;">
                                $${escapeHtml(String(med.price))}
                            </span>
                            ${actionBtn}
                        </div>
                    </div>

                </div>
            </div>`;
  }

  /* ════════════════════════════════════════════════════════════════════════
       SECTION 7 – RENDER MEDICINES
    ════════════════════════════════════════════════════════════════════════ */

  /**
   * Render a list of medicine objects into #medicineGrid.
   *
   * Steps:
   *   1. Apply the current APP.typeFilter client-side.
   *   2. Update result count text.
   *   3. Show empty state or render cards with stagger animation delays.
   *
   * @param {Array<Object>} medicines – raw array from API response
   */
  function renderMedicines(medicines) {
    if (!medicineGrid) return;

    /* ── Client-side type filter ─────────────────────────────────── */
    const filtered =
      APP.typeFilter && APP.typeFilter !== "all"
        ? medicines.filter((m) => m.category_type === APP.typeFilter)
        : medicines;

    /* ── Update count elements ───────────────────────────────────── */
    updateResultsCount(filtered.length);

    /* ── Empty state ─────────────────────────────────────────────── */
    if (filtered.length === 0) {
      /* Show the server-rendered #noResults div if it exists */
      if (noResultsEl) {
        medicineGrid.innerHTML = "";
        noResultsEl.classList.remove("d-none");
      } else {
        /* Inject a self-contained empty state into the grid */
        medicineGrid.innerHTML = `
                    <div class="col-12 py-5 text-center">
                        <div class="mb-3" style="font-size:3.5rem;opacity:.5;">&#128269;</div>
                        <h5 class="fw-semibold mb-2" style="color:#1e293b;">No medicines found</h5>
                        <p class="text-muted small mb-3">
                            Try adjusting your search terms or clearing the active filters.
                        </p>
                        <button class="btn btn-sm btn-outline-primary rounded-pill px-4"
                                onclick="window.MediSearch && window.MediSearch.clearAndFetch()">
                            <i class="fa-solid fa-rotate-left me-1"></i>Clear Filters
                        </button>
                    </div>`;
      }
      return;
    }

    /* Hide the empty-state container if it was showing */
    if (noResultsEl) noResultsEl.classList.add("d-none");

    /* ── Render cards ────────────────────────────────────────────── */
    medicineGrid.innerHTML = filtered.map(buildCardHTML).join("");
  }

  /* ════════════════════════════════════════════════════════════════════════
       SECTION 8 – AJAX FETCH
    ════════════════════════════════════════════════════════════════════════ */

  /**
   * Fetch medicines from the API and render the results.
   *
   * Endpoint: GET /api/medicines/search?q=…&vendor=…&genre=…
   *
   * @param {string} [q='']      – search query
   * @param {string} [vendor=''] – vendor name filter
   * @param {string} [genre='']  – category/genre name filter
   * @returns {Promise<void>}
   */
  async function fetchMedicines(q = "", vendor = "", genre = "") {
    if (!medicineGrid) return;

    /* Persist current params in APP state */
    APP.currentQ = q;
    APP.currentVendor = vendor;
    APP.currentGenre = genre;

    const url =
      `${API_URL}?q=${encodeURIComponent(q)}` +
      `&vendor=${encodeURIComponent(vendor)}` +
      `&genre=${encodeURIComponent(genre)}`;

    setLoading(true);

    try {
      const response = await fetch(url, {
        method: "GET",
        headers: { "X-Requested-With": "XMLHttpRequest" },
      });

      if (!response.ok) {
        throw new Error(`HTTP error – status ${response.status}`);
      }

      const data = await response.json();
      renderMedicines(data.medicines || []);
    } catch (err) {
      /* Log to console for developers; show a friendly message in the grid */
      console.error("[MediSearch] fetchMedicines error:", err);

      if (medicineGrid) {
        medicineGrid.innerHTML = `
                    <div class="col-12 py-5 text-center">
                        <i class="fa-solid fa-triangle-exclamation fa-2x mb-3"
                           style="color:#d97706;"></i>
                        <p class="text-muted mb-2 fw-semibold">Something went wrong.</p>
                        <p class="text-muted small mb-0">
                            Could not load medicines. Please try again in a moment.
                        </p>
                    </div>`;
      }
    } finally {
      setLoading(false);
    }
  }

  /* ════════════════════════════════════════════════════════════════════════
       SECTION 9 – EVENT BINDINGS
    ════════════════════════════════════════════════════════════════════════ */

  /* ── Debounced handler that reads current filter state and fetches ── */
  const debouncedFetch = debounce(() => {
    const { q, vendor, genre } = getCurrentFilters();
    fetchMedicines(q, vendor, genre);
  }, DEBOUNCE_MS);

  /* ── 9a. Medicine name search input ─────────────────────────────────── */
  if (searchInput) {
    searchInput.addEventListener("input", () => {
      /* Show / hide the inline clear (×) button */
      if (clearSearchBtn) {
        clearSearchBtn.style.display = searchInput.value ? "" : "none";
      }
      debouncedFetch();
    });
  }

  /* ── 9b. Inline clear-search (×) button ─────────────────────────────── */
  if (clearSearchBtn) {
    clearSearchBtn.addEventListener("click", () => {
      if (searchInput) {
        searchInput.value = "";
        searchInput.focus();
      }
      clearSearchBtn.style.display = "none";
      debouncedFetch();
    });
  }

  /* ── 9c. Vendor dropdown ─────────────────────────────────────────────── */
  if (vendorFilter) {
    vendorFilter.addEventListener("change", () => {
      /* Keep sidebar checkboxes in sync */
      vendorCheckboxes.forEach((cb) => {
        cb.checked = cb.value === vendorFilter.value;
      });

      const { q, genre } = getCurrentFilters();
      fetchMedicines(q, vendorFilter.value, genre);
    });
  }

  /* ── 9d. Genre / category dropdown ──────────────────────────────────── */
  if (genreFilter) {
    genreFilter.addEventListener("change", () => {
      const { q, vendor } = getCurrentFilters();
      fetchMedicines(q, vendor, genreFilter.value);
    });
  }

  /* ── 9e. Type filter radios ──────────────────────────────────────────── */
  /*
   * Present on home/index.php as three radios (name="typeFilter", values
   * "", "solid", "liquid"). Changes update APP.typeFilter and re-fetch so
   * the API response is re-filtered client-side when it arrives.
   */
  if (typeRadios.length > 0) {
    typeRadios.forEach((radio) => {
      radio.addEventListener("change", () => {
        /* Map the radio value: '' means 'all' */
        APP.typeFilter =
          radio.value === "" || radio.value === "all" ? "all" : radio.value;

        /* Re-fetch with current text/vendor/genre; renderMedicines applies type filter */
        const { q, vendor, genre } = getCurrentFilters();
        fetchMedicines(q, vendor, genre);
      });
    });
  }

  /* ── 9f. Clear-all-filters button ───────────────────────────────────── */
  function doResetFilters() {
    /* Reset all inputs to default/empty */
    if (searchInput) searchInput.value = "";
    if (vendorFilter) vendorFilter.value = "";
    if (genreFilter) genreFilter.value = "";
    if (clearSearchBtn) clearSearchBtn.style.display = "none";

    /* Uncheck every vendor checkbox */
    vendorCheckboxes.forEach((cb) => {
      cb.checked = false;
    });

    /* Reset type radios back to "all" */
    typeRadios.forEach((r) => {
      r.checked = r.value === "" || r.value === "all";
    });

    APP.typeFilter = "all";
    APP.currentQ = "";
    APP.currentVendor = "";
    APP.currentGenre = "";

    fetchMedicines("", "", "");
  }

  if (clearFiltersBtn)
    clearFiltersBtn.addEventListener("click", doResetFilters);
  if (resetFiltersBtn)
    resetFiltersBtn.addEventListener("click", doResetFilters);

  /* ── 9g. Sidebar vendor checkboxes ──────────────────────────────────── */
  /*
   * Checking a vendor checkbox selects that vendor exclusively and syncs
   * with the #vendorFilter <select>. Unchecking clears the vendor filter.
   */
  vendorCheckboxes.forEach((cb) => {
    cb.addEventListener("change", () => {
      if (cb.checked) {
        /* Uncheck all other vendor checkboxes (single-select behaviour) */
        vendorCheckboxes.forEach((other) => {
          if (other !== cb) other.checked = false;
        });
        /* Sync the dropdown */
        if (vendorFilter) vendorFilter.value = cb.value;
      } else {
        /* Clear the dropdown if this was the selected vendor */
        if (vendorFilter && vendorFilter.value === cb.value) {
          vendorFilter.value = "";
        }
      }

      const { q, genre } = getCurrentFilters();
      const vendor = vendorFilter
        ? vendorFilter.value
        : cb.checked
          ? cb.value
          : "";
      fetchMedicines(q, vendor, genre);
    });
  });

  /* ── 9h. Hero search form (home page) ───────────────────────────────── */
  /*
   * When the hero form (at the top of home/index.php) is submitted:
   *   1. Prevent navigation.
   *   2. Smooth-scroll to #medicines-section (the featured grid section).
   *   3. Copy the hero query into #medicineSearch and trigger AJAX.
   */
  if (heroForm) {
    heroForm.addEventListener("submit", (e) => {
      e.preventDefault();

      const q = heroInput ? heroInput.value.trim() : "";

      /* ── Scroll to the medicines section ── */
      const target =
        document.getElementById("medicines-section") ||
        medicineGrid ||
        document.getElementById("categories-section");

      if (target) {
        target.scrollIntoView({ behavior: "smooth", block: "start" });
      }

      /* ── Populate the main search input and fire AJAX ── */
      if (searchInput) {
        searchInput.value = q;
        if (clearSearchBtn && q) clearSearchBtn.style.display = "";
        searchInput.focus();

        const { vendor, genre } = getCurrentFilters();
        fetchMedicines(q, vendor, genre);
      } else if (q) {
        /*
         * Edge case: #medicineSearch doesn't exist on this page
         * (unlikely but safe) – navigate to the browse page with ?q=
         */
        window.location.href = `${BASE_PATH}/medicines?q=${encodeURIComponent(q)}`;
      }
    });
  }

  /* ════════════════════════════════════════════════════════════════════════
       SECTION 10 – INITIAL PAGE LOAD
    ════════════════════════════════════════════════════════════════════════ */

  /*
   * On pages that have the medicine grid and filter controls, run an
   * initial AJAX fetch to populate / refresh the grid from the database.
   *
   * Strategy:
   *   • If the URL contains ?q=…, populate the search input and fetch.
   *   • Otherwise, do a blank fetch only when the page has AJAX filter
   *     controls AND the grid currently contains server-rendered cards
   *     (replace them with a fresh AJAX response so all filters work).
   *   • If neither condition is met, leave server-rendered content alone.
   */
  if (medicineGrid) {
    const urlParams = new URLSearchParams(window.location.search);
    const urlQ = urlParams.get("q") || "";

    if (urlQ) {
      /* Restore query from URL into the search input */
      if (searchInput) {
        searchInput.value = urlQ;
        if (clearSearchBtn) clearSearchBtn.style.display = "";
      }

      /* Fetch with URL query + any pre-selected dropdown values */
      const vendor = vendorFilter ? vendorFilter.value : "";
      const genre = genreFilter ? genreFilter.value : "";
      fetchMedicines(urlQ, vendor, genre);
    } else if (searchInput || vendorFilter || genreFilter) {
      /*
       * AJAX filter controls are present but no query param –
       * fire an initial empty fetch so AJAX fully controls the grid
       * (this keeps the client in sync with the server data).
       */
      const vendor = vendorFilter ? vendorFilter.value : "";
      const genre = genreFilter ? genreFilter.value : "";

      /* Slight delay so the page paint completes before the request */
      setTimeout(() => fetchMedicines("", vendor, genre), 0);
    }
  }

  /* ════════════════════════════════════════════════════════════════════════
       SECTION 11 – PUBLIC API  (window.MediSearch)
    ════════════════════════════════════════════════════════════════════════ */

  /**
   * MediSearch – public interface for programmatic control from PHP views
   * or other inline scripts.
   *
   * Usage examples:
   *   MediSearch.search('paracetamol');
   *   MediSearch.clearAndFetch();
   */
  const MediSearch = {
    /**
     * Programmatically run a search by query string.
     * Sets the search input value and triggers the AJAX fetch.
     *
     * @param {string} q – search term
     */
    search(q = "") {
      if (searchInput) {
        searchInput.value = q;
        if (clearSearchBtn) clearSearchBtn.style.display = q ? "" : "none";
      }
      const { vendor, genre } = getCurrentFilters();
      fetchMedicines(q, vendor, genre);
    },

    /**
     * Reset all filters and fetch the full unfiltered list.
     */
    clearAndFetch() {
      doResetFilters();
    },

    /**
     * Read-only snapshot of the current filter state.
     * Useful for debugging or for other scripts to check active filters.
     *
     * @returns {{ q: string, vendor: string, genre: string, typeFilter: string }}
     */
    getState() {
      return {
        q: APP.currentQ,
        vendor: APP.currentVendor,
        genre: APP.currentGenre,
        typeFilter: APP.typeFilter,
      };
    },
  };

  window.MediSearch = MediSearch;
})(); /* end IIFE */
