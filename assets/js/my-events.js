// ==== EvenUser | MY EVENTS PAGE SCRIPTS ====
(function () {
  "use strict";

  function $(sel, root=document){ return root.querySelector(sel); }
  function $all(sel, root=document){ return Array.from(root.querySelectorAll(sel)); }

  function initFilterAutoSubmit() {
    const form = $("#filter-form");
    if (!form) return;
    const status = $("#status");
    if (status) {
      status.addEventListener("change", () => form.submit());
    }
  }

  function initConfirmDelete() {
    $all("form.inline-form").forEach(f => {
      f.addEventListener("submit", (e) => {
        // đã có confirm inline trong HTML, để phòng trường hợp trình duyệt bỏ qua:
        if (!confirm("Xoá sự kiện này?")) {
          e.preventDefault();
        }
      });
    });
  }

  function initPaginationScroll() {
    $all(".pagination .page-link").forEach(a => {
      if (a.classList.contains("disabled")) return;
      a.addEventListener("click", () => {
        const hdr = $(".list-head");
        if (hdr && "scrollIntoView" in hdr) {
          setTimeout(() => hdr.scrollIntoView({ behavior: "smooth", block: "start" }), 0);
        }
      });
    });
  }

  function init() {
    initFilterAutoSubmit();
    initConfirmDelete();
    initPaginationScroll();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
