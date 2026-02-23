/**
 * Flavor Theme – Customizer Controls JS (sidebar panel)
 *
 * Automatyczna regeneracja palety kolorów przy zmianie:
 *   - trybu kolorystycznego (jasny / ciemny)
 *   - koloru akcentu
 *
 * Wygenerowane wartości trafiają do poszczególnych setting-ów,
 * co z kolei powoduje aktualizację zmiennych CSS w podglądzie
 * (przez customizer-preview.js).
 */
(function ($) {
  "use strict";

  /* ═══════════════════════════════════════════════════════════
     Helpers – konwersja kolorów (identyczne z PHP)
     ═══════════════════════════════════════════════════════════ */

  function hexToHSL(hex) {
    hex = hex.replace("#", "");
    var r = parseInt(hex.substring(0, 2), 16) / 255;
    var g = parseInt(hex.substring(2, 4), 16) / 255;
    var b = parseInt(hex.substring(4, 6), 16) / 255;

    var max = Math.max(r, g, b),
      min = Math.min(r, g, b);
    var h = 0,
      s = 0,
      l = (max + min) / 2;
    var d = max - min;

    if (d !== 0) {
      s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
      if (max === r) h = ((g - b) / d + (g < b ? 6 : 0)) / 6;
      else if (max === g) h = ((b - r) / d + 2) / 6;
      else h = ((r - g) / d + 4) / 6;
    }

    return {
      h: Math.round(h * 360),
      s: Math.round(s * 100),
      l: Math.round(l * 100),
    };
  }

  function hue2rgb(p, q, t) {
    if (t < 0) t += 1;
    if (t > 1) t -= 1;
    if (t < 1 / 6) return p + (q - p) * 6 * t;
    if (t < 1 / 2) return q;
    if (t < 2 / 3) return p + (q - p) * (2 / 3 - t) * 6;
    return p;
  }

  function hslToHex(h, s, l) {
    h = h / 360;
    s = s / 100;
    l = l / 100;
    var r, g, b;

    if (s === 0) {
      r = g = b = l;
    } else {
      var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
      var p = 2 * l - q;
      r = hue2rgb(p, q, h + 1 / 3);
      g = hue2rgb(p, q, h);
      b = hue2rgb(p, q, h - 1 / 3);
    }

    var toHex = function (c) {
      var hex = Math.round(c * 255).toString(16);
      return hex.length === 1 ? "0" + hex : hex;
    };
    return "#" + toHex(r) + toHex(g) + toHex(b);
  }

  function clamp(v, min, max) {
    return Math.max(min, Math.min(max, v));
  }

  /* ═══════════════════════════════════════════════════════════
     Generowanie palety — lustrzane odzwierciedlenie PHP
     flavor_generate_palette()
     ═══════════════════════════════════════════════════════════ */

  function generatePalette(accentHex, mode) {
    var a = hexToHSL(accentHex);
    var isDark = mode === "dark";

    return {
      flavor_color_accent: accentHex,

      flavor_color_accent_hover: hslToHex(
        a.h,
        clamp(a.s + 5, 0, 100),
        clamp(a.l - 10, 5, 90),
      ),

      flavor_color_accent_light: isDark
        ? hslToHex(a.h, clamp(Math.round(a.s * 0.4), 5, 30), 18)
        : hslToHex(a.h, clamp(Math.round(a.s * 0.5), 10, 50), 95),

      flavor_color_text: isDark
        ? hslToHex(a.h, 8, 88)
        : hslToHex(a.h, clamp(Math.round(a.s * 0.35), 10, 30), 20),

      flavor_color_text_light: isDark
        ? hslToHex(a.h, 8, 60)
        : hslToHex(a.h, clamp(Math.round(a.s * 0.25), 8, 20), 50),

      flavor_color_bg: isDark
        ? hslToHex(a.h, clamp(Math.round(a.s * 0.2), 5, 15), 9)
        : hslToHex(a.h, clamp(Math.round(a.s * 0.1), 3, 12), 97),

      flavor_color_surface: isDark
        ? hslToHex(a.h, clamp(Math.round(a.s * 0.2), 5, 15), 14)
        : "#ffffff",

      flavor_color_border: isDark
        ? hslToHex(a.h, clamp(Math.round(a.s * 0.15), 5, 12), 22)
        : hslToHex(a.h, clamp(Math.round(a.s * 0.15), 8, 20), 88),

      flavor_color_header_bg: isDark
        ? hslToHex(a.h, clamp(Math.round(a.s * 0.2), 5, 15), 12)
        : "#ffffff",

      flavor_color_footer_bg: isDark
        ? hslToHex(a.h, clamp(Math.round(a.s * 0.15), 5, 12), 7)
        : hslToHex(a.h, clamp(Math.round(a.s * 0.12), 5, 15), 94),

      flavor_color_success: isDark ? "#2ecc71" : "#27ae60",
      flavor_color_danger: isDark ? "#e74c3c" : "#e74c3c",
      flavor_color_warning: isDark ? "#f1c40f" : "#f39c12",
    };
  }

  /* ═══════════════════════════════════════════════════════════
     Zastosuj paletę — ustaw wartości w Customizerze
     ═══════════════════════════════════════════════════════════ */

  var _applying = false;

  function applyPalette() {
    if (_applying) return;

    var accent = wp.customize("flavor_color_accent").get();
    var mode = wp.customize("flavor_color_mode").get();

    if (!accent) return;

    var palette = generatePalette(accent, mode);

    _applying = true;

    $.each(palette, function (settingId, value) {
      if (settingId === "flavor_color_accent") return; // already set
      var setting = wp.customize(settingId);
      if (setting) {
        setting.set(value);
      }
    });

    _applying = false;
  }

  /* ═══════════════════════════════════════════════════════════
     Bind: reaguj na zmianę trybu lub koloru akcentu
     ═══════════════════════════════════════════════════════════ */

  wp.customize.bind("ready", function () {
    wp.customize("flavor_color_mode", function (setting) {
      setting.bind(function () {
        applyPalette();
      });
    });

    wp.customize("flavor_color_accent", function (setting) {
      setting.bind(function () {
        applyPalette();
      });
    });

    /* ── Sekcja Zamówienia → podgląd strony checkout ── */
    if (
      typeof flavorCustomizer !== "undefined" &&
      flavorCustomizer.checkoutUrl
    ) {
      wp.customize.section("flavor_orders", function (section) {
        section.expanded.bind(function (isExpanded) {
          if (isExpanded) {
            wp.customize.previewer.previewUrl.set(flavorCustomizer.checkoutUrl);
          }
        });
      });
    }

    /* ── Sekcja Strona kontaktowa → podgląd strony kontakt ── */
    if (
      typeof flavorCustomizer !== "undefined" &&
      flavorCustomizer.contactUrl
    ) {
      wp.customize.section("flavor_contact_info", function (section) {
        section.expanded.bind(function (isExpanded) {
          if (isExpanded) {
            wp.customize.previewer.previewUrl.set(flavorCustomizer.contactUrl);
          }
        });
      });
    }

    /* ── Sekcja O nas → podgląd strony o nas ── */
    if (typeof flavorCustomizer !== "undefined" && flavorCustomizer.aboutUrl) {
      wp.customize.section("flavor_about_info", function (section) {
        section.expanded.bind(function (isExpanded) {
          if (isExpanded) {
            wp.customize.previewer.previewUrl.set(flavorCustomizer.aboutUrl);
          }
        });
      });
    }

    /* ── Mapa: pokaż pole adresu tylko gdy „Własny adres" ── */
    wp.customize("flavor_contact_map_source", function (setting) {
      wp.customize.control("flavor_contact_map_custom", function (control) {
        function toggle() {
          control.container.toggle(setting.get() === "custom");
        }
        toggle();
        setting.bind(toggle);
      });
    });

    /* ── Sortowalne karty (drag & drop) — generyczny dla każdej kontrolki ── */
    function initSortableCards(controlId) {
      wp.customize.control(controlId, function (control) {
        var initSortable = function () {
          var $list = control.container.find(".fc-sortable-cards");
          if (!$list.length) {
            return;
          }

          $list.sortable({
            handle: ".fc-sortable-handle",
            axis: "y",
            containment: "parent",
            placeholder: "fc-sortable-placeholder",
            tolerance: "pointer",
            update: function () {
              var order = $list
                .find(".fc-sortable-card")
                .map(function () {
                  return $(this).data("key");
                })
                .get()
                .join(",");

              control.setting.set(order);
            },
          });
        };

        if (control.container.find(".fc-sortable-cards").length) {
          initSortable();
        } else {
          setTimeout(initSortable, 500);
        }
      });
    }

    initSortableCards("flavor_contact_cards_order");
    initSortableCards("flavor_about_sections_order");

    /* ── Sortowalne ikonki social media (drag & drop + toggle) ── */
    function initSocialSortable() {
      var $list = $(".fc-social-sortable");
      if (!$list.length) return;

      function updateSocialOrder() {
        var order = $list
          .find(".fc-social-item")
          .map(function () {
            var key = $(this).data("key");
            var enabled = $(this).find(".fc-social-enabled").is(":checked")
              ? "1"
              : "0";
            return key + ":" + enabled;
          })
          .get()
          .join(",");

        wp.customize("flavor_social_order").set(order);
      }

      $list.sortable({
        handle: ".fc-social-handle",
        axis: "y",
        containment: "parent",
        placeholder: "ui-sortable-placeholder",
        tolerance: "pointer",
        update: function () {
          updateSocialOrder();
        },
      });

      $list.on("change", ".fc-social-enabled", function () {
        var $item = $(this).closest(".fc-social-item");
        if ($(this).is(":checked")) {
          $item.removeClass("fc-social-disabled");
        } else {
          $item.addClass("fc-social-disabled");
        }
        updateSocialOrder();
      });
    }

    // Init when the social card is opened (may be deferred)
    $(document).on("click", ".fc-sortable-card-header", function () {
      setTimeout(initSocialSortable, 100);
    });
    // Also try on load
    setTimeout(initSocialSortable, 800);
  });
})(jQuery);
