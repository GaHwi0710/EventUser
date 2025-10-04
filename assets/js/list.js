// ==== EvenUser | LIST PAGE SCRIPTS ====
(function () {
  "use strict";

  // keep view mode (grid/list) in localStorage
  const KEY_VIEW = "evenuser_list_view"; // "grid" | "list"

  function $(sel, root = document) { return root.querySelector(sel); }
  function $all(sel, root = document) { return Array.from(root.querySelectorAll(sel)); }

  function setView(mode) {
    const list = $("#event-list");
    if (!list) return;
    if (mode === "list") {
      list.classList.remove("event-grid");
      list.classList.add("event-list");
    } else {
      list.classList.remove("event-list");
      list.classList.add("event-grid");
      mode = "grid";
    }
    try { localStorage.setItem(KEY_VIEW, mode); } catch {}
    // toggle selected state buttons
    const btnGrid = $("#btn-grid");
    const btnList = $("#btn-list");
    if (btnGrid && btnList) {
      btnGrid.style.opacity = (mode === "grid") ? "1" : "0.7";
      btnList.style.opacity = (mode === "list") ? "1" : "0.7";
    }
  }

  function initViewToggle() {
    const btnGrid = $("#btn-grid");
    const btnList = $("#btn-list");
    if (btnGrid) btnGrid.addEventListener("click", () => setView("grid"));
    if (btnList) btnList.addEventListener("click", () => setView("list"));

    // restore
    let mode = "grid";
    try {
      const saved = localStorage.getItem(KEY_VIEW);
      if (saved === "list" || saved === "grid") mode = saved;
    } catch {}
    setView(mode);
  }

  function initSearchUX() {
    const form = $("#search-form");
    if (!form) return;
    const input = form.querySelector("input[name='q']");
    if (!input) return;

    // Enter to submit (default), add small helper: clear with ESC
    input.addEventListener("keydown", (e) => {
      if (e.key === "Escape") {
        input.value = "";
      }
    });
  }

  function initFilterUX() {
    const form = $("#filter-form");
    if (!form) return;

    // When change date, auto-submit after small debounce
    const from = $("#from");
    const to = $("#to");
    let t;
    function autoSubmit() {
      clearTimeout(t);
      t = setTimeout(() => form.submit(), 300);
    }
    from && from.addEventListener("change", autoSubmit);
    to && to.addEventListener("change", autoSubmit);
  }

  function initPaginationUX() {
    // Smooth scroll to results header when clicking pagination
    $all(".pagination .page-link").forEach(a => {
      if (a.classList.contains("disabled")) return;
      a.addEventListener("click", (e) => {
        // allow navigation, but scroll first for nicer UX
        // (will navigate right after default)
        const hdr = $(".results-header");
        if (hdr && "scrollIntoView" in hdr) {
          setTimeout(() => hdr.scrollIntoView({ behavior: "smooth", block: "start" }), 0);
        }
      });
    });
  }

  function init() {
    initViewToggle();
    initSearchUX();
    initFilterUX();
    initPaginationUX();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
