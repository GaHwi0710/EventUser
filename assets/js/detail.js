// ==== EventUser | DETAIL PAGE SCRIPTS ====
(function () {
  "use strict";

  /** Utils **/
  const fmt = new Intl.NumberFormat("vi-VN");

  // Parse price from option: prefer data-price, else parse digits in text (e.g. "VIP - 120,000đ")
  function parsePriceFromOption(opt) {
    if (!opt) return 0;
    if (opt.dataset && opt.dataset.price) {
      const v = Number(opt.dataset.price);
      return Number.isFinite(v) ? v : 0;
    }
    const text = opt.textContent || "";
    const digits = text.replace(/[^\d]/g, "");
    return digits ? Number(digits) : 0;
  }

  // Ensure n is an integer >= min
  function clampInt(n, min) {
    n = Number.isFinite(+n) ? Math.floor(+n) : min;
    return n < min ? min : n;
  }

  // Find or create a total line element
  function ensureTotalContainer(formEl) {
    let box = formEl.querySelector("#buy-total");
    if (!box) {
      box = document.createElement("div");
      box.id = "buy-total";
      box.style.margin = "8px 0 4px";
      box.style.fontWeight = "700";
      box.style.display = "flex";
      box.style.alignItems = "center";
      box.style.gap = "8px";

      const label = document.createElement("span");
      label.textContent = "Tổng thanh toán:";
      label.style.color = "#a0a0a0";

      const price = document.createElement("span");
      price.id = "buy-total-value";
      price.textContent = "0đ";
      price.style.fontSize = "16px";
      price.style.color = "#03bf00";

      box.appendChild(label);
      box.appendChild(price);

      // Chèn trước nút submit nếu có
      const submitBtn = formEl.querySelector(".btn-buy-tickets, button[type='submit']");
      if (submitBtn && submitBtn.parentElement === formEl) {
        formEl.insertBefore(box, submitBtn);
      } else {
        formEl.appendChild(box);
      }
    }
    return box;
  }

  // Main init
  function initDetail() {
    const ticketSelect = document.getElementById("ticket_id");
    const qtyInput = document.getElementById("qty");
    const buyBtn = document.querySelector(".btn-buy-tickets");
    const formEl = (ticketSelect && ticketSelect.form) || document.querySelector("form[action='']") || document.querySelector("form");

    if (!ticketSelect || !qtyInput || !formEl) return;

    const totalBox = ensureTotalContainer(formEl);
    const totalValueEl = totalBox.querySelector("#buy-total-value");

    // Disable buy button if no ticket
    function updateBuyEnabled() {
      const hasTicket = !!ticketSelect.value;
      if (buyBtn) buyBtn.disabled = !hasTicket;
      buyBtn && (buyBtn.style.opacity = hasTicket ? "1" : "0.7");
      buyBtn && (buyBtn.style.cursor = hasTicket ? "pointer" : "not-allowed");
    }

    // Calculate and render total
    function updateTotal() {
      const opt = ticketSelect.options[ticketSelect.selectedIndex];
      const unit = parsePriceFromOption(opt);
      const qty = clampInt(qtyInput.value, 1);
      const total = unit * qty;

      if (totalValueEl) {
        totalValueEl.textContent = `${fmt.format(total)}đ`;
      }

      // Optional: hiển thị “Miễn phí” khi unit = 0
      if (unit === 0) {
        totalValueEl.textContent = "Miễn phí";
      }
    }

    // Guard qty input
    function normalizeQty() {
      qtyInput.value = clampInt(qtyInput.value, 1);
    }

    // Bind events
    ticketSelect.addEventListener("change", function () {
      updateBuyEnabled();
      updateTotal();
    });

    qtyInput.addEventListener("input", function () {
      normalizeQty();
      updateTotal();
    });

    formEl.addEventListener("submit", function (e) {
      // Basic front-end validation
      if (!ticketSelect.value) {
        e.preventDefault();
        alert("Vui lòng chọn loại vé.");
        ticketSelect.focus();
        return;
      }
      normalizeQty();
    });

    // First paint
    normalizeQty();
    updateBuyEnabled();
    updateTotal();

    // ——— UX nhỏ: scroll đến alert sau submit (nếu có) ———
    const successAlert = document.querySelector(".alert.alert-success");
    const errorAlert = document.querySelector(".alert.alert-error");
    const alertEl = successAlert || errorAlert;
    if (alertEl && typeof alertEl.scrollIntoView === "function") {
      alertEl.scrollIntoView({ behavior: "smooth", block: "center" });
    }
  }

  // DOM ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initDetail);
  } else {
    initDetail();
  }
})();
