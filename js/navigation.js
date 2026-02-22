/**
 * Flavor Theme - Mobile Navigation Toggle
 */
(function () {
  const toggle = document.querySelector(".menu-toggle");
  const nav = document.querySelector(".main-navigation");

  if (!toggle || !nav) {
    return;
  }

  toggle.addEventListener("click", function () {
    nav.classList.toggle("toggled");
    const expanded = nav.classList.contains("toggled");
    toggle.setAttribute("aria-expanded", expanded);
    toggle.textContent = expanded ? "✕" : "☰";
  });

  // Zamknij menu po kliknięciu linku (mobile)
  nav.querySelectorAll("a").forEach(function (link) {
    link.addEventListener("click", function () {
      if (window.innerWidth <= 768) {
        nav.classList.remove("toggled");
        toggle.setAttribute("aria-expanded", "false");
        toggle.textContent = "☰";
      }
    });
  });
})();

/* ── Koszyk w menu — otwórz minikoszyk zamiast nawigować ── */
document.addEventListener(
  "click",
  function (e) {
    var link = e.target.closest('a[data-cart-action="minicart"]');
    if (!link) return;
    e.preventDefault();
    e.stopImmediatePropagation();

    // Przełącz na panel koszyka
    var panels = document.querySelectorAll(".fc-panel");
    panels.forEach(function (p) {
      p.classList.remove("fc-panel-active");
    });
    var cartPanel = document.querySelector(".fc-panel-cart");
    if (cartPanel) cartPanel.classList.add("fc-panel-active");

    // Aktywuj tab koszyka
    var tabs = document.querySelectorAll(".fc-mini-cart-tab");
    tabs.forEach(function (t) {
      t.classList.remove("fc-tab-active");
    });
    var cartTab = document.getElementById("fc-mini-cart-tab");
    if (cartTab) cartTab.classList.add("fc-tab-active");

    // Otwórz sidebar
    var cart = document.getElementById("fc-mini-cart");
    var overlay = document.getElementById("fc-mini-cart-overlay");
    if (cart && overlay) {
      cart.classList.add("active");
      overlay.classList.add("active");
    }
    document.body.style.overflow = "hidden";
  },
  true,
);

/* ── Porównywarka w menu — otwórz panel porównania ── */
document.addEventListener(
  "click",
  function (e) {
    var link = e.target.closest('a[data-compare-action="panel"]');
    if (!link) return;
    e.preventDefault();
    e.stopImmediatePropagation();
    var tab = document.getElementById("fc-mini-compare-tab");
    if (tab) {
      tab.click();
    }
  },
  true,
);

/* ── Lista życzeń w menu — otwórz panel listy życzeń ── */
document.addEventListener(
  "click",
  function (e) {
    var link = e.target.closest('a[data-wishlist-action="panel"]');
    if (!link) return;
    e.preventDefault();
    e.stopImmediatePropagation();
    var tab = document.getElementById("fc-mini-wishlist-tab");
    if (tab) {
      tab.click();
    }
  },
  true,
);
