/**
 * Flavor Theme – Customizer Live Preview
 * Natychmiastowa aktualizacja zmiennych CSS w podglądzie (iframe).
 */
(function ($) {
  "use strict";

  /* -----------------------------------------------------------
   *  Helper: set a CSS variable on :root
   * --------------------------------------------------------- */
  function setCSSVar(name, value) {
    document.documentElement.style.setProperty(name, value);
  }

  /* -----------------------------------------------------------
   *  Helper: hex to hue
   * --------------------------------------------------------- */
  function hexToHue(hex) {
    hex = hex.replace("#", "");
    var r = parseInt(hex.substring(0, 2), 16) / 255;
    var g = parseInt(hex.substring(2, 4), 16) / 255;
    var b = parseInt(hex.substring(4, 6), 16) / 255;
    var max = Math.max(r, g, b),
      min = Math.min(r, g, b);
    var h = 0,
      d = max - min;
    if (d) {
      if (max === r) h = ((g - b) / d + (g < b ? 6 : 0)) * 60;
      else if (max === g) h = ((b - r) / d + 2) * 60;
      else h = ((r - g) / d + 4) * 60;
    }
    return Math.round(h);
  }

  /* -----------------------------------------------------------
   *  Color map: setting_id → CSS variable(s)
   * --------------------------------------------------------- */
  var colorMap = {
    flavor_color_accent: ["--color-accent", "--fc-accent"],
    flavor_color_accent_hover: ["--color-accent-hover", "--fc-accent-hover"],
    flavor_color_accent_light: ["--color-accent-light", "--fc-accent-light"],
    flavor_color_text: ["--color-text", "--fc-text"],
    flavor_color_text_light: ["--color-text-light", "--fc-text-light"],
    flavor_color_bg: ["--color-bg", "--fc-bg"],
    flavor_color_surface: ["--color-surface", "--fc-surface"],
    flavor_color_border: ["--color-border", "--fc-border"],
    flavor_color_header_bg: ["--color-header-bg"],
    flavor_color_footer_bg: ["--color-footer-bg"],
    flavor_color_success: ["--fc-success"],
    flavor_color_danger: ["--fc-danger"],
    flavor_color_warning: ["--fc-warning"],
  };

  /* Bind all color settings */
  $.each(colorMap, function (settingId, vars) {
    wp.customize(settingId, function (setting) {
      setting.bind(function (val) {
        vars.forEach(function (v) {
          setCSSVar(v, val);
        });
        /* Update brand hue when accent changes */
        if (settingId === "flavor_color_accent") {
          setCSSVar("--fc-brand-hue", hexToHue(val) - 180 + "deg");
        }
      });
    });
  });

  /* Mode change — no CSS vars to set directly; the controls JS
       handles regenerating the palette which triggers each color
       setting to update via the bindings above. */
  wp.customize("flavor_color_mode", function (setting) {
    setting.bind(function (mode) {
      /* palette colors will fire individually */
      /* Switch logos */
      var hasDark = $(".site-logo-dark").length > 0;
      if (hasDark) {
        $(".site-logo-light").toggle(mode !== "dark");
        $(".site-logo-dark").toggle(mode === "dark");
      }

      /* Auto-sync dark map when user hasn't explicitly overridden it */
      var mapDarkSetting = wp.customize("flavor_contact_map_dark");
      if (mapDarkSetting) {
        mapDarkSetting.set(mode === "dark");
      }
    });
  });

  /* -----------------------------------------------------------
   *  Dark map — live filter toggle
   * --------------------------------------------------------- */
  wp.customize("flavor_contact_map_dark", function (s) {
    s.bind(function (v) {
      var $iframe = $(".flavor-contact-map iframe");
      if (!$iframe.length) return;
      $iframe.css(
        "filter",
        v ? "invert(90%) hue-rotate(180deg)" : "none"
      );
    });
  });

  /* -----------------------------------------------------------
   *  Typography
   * --------------------------------------------------------- */
  wp.customize("flavor_font_size", function (setting) {
    setting.bind(function (val) {
      document.documentElement.style.fontSize = val + "px";
    });
  });

  wp.customize("flavor_font_family", function (setting) {
    setting.bind(function (val) {
      var fontStacks = {
        system:
          '"Segoe UI", -apple-system, BlinkMacSystemFont, "Helvetica Neue", Arial, sans-serif',
        inter: '"Inter", -apple-system, BlinkMacSystemFont, sans-serif',
        poppins: '"Poppins", -apple-system, BlinkMacSystemFont, sans-serif',
        roboto: '"Roboto", -apple-system, BlinkMacSystemFont, sans-serif',
        lato: '"Lato", -apple-system, BlinkMacSystemFont, sans-serif',
        "open-sans":
          '"Open Sans", -apple-system, BlinkMacSystemFont, sans-serif',
        montserrat:
          '"Montserrat", -apple-system, BlinkMacSystemFont, sans-serif',
        nunito: '"Nunito", -apple-system, BlinkMacSystemFont, sans-serif',
        raleway: '"Raleway", -apple-system, BlinkMacSystemFont, sans-serif',
        "dm-sans": '"DM Sans", -apple-system, BlinkMacSystemFont, sans-serif',
      };
      var stack = fontStacks[val] || fontStacks["system"];
      setCSSVar("--font-main", stack);
      setCSSVar("--font-heading", stack);
      setCSSVar("--fc-font", stack);
    });
  });

  /* -----------------------------------------------------------
   *  Layout
   * --------------------------------------------------------- */
  wp.customize("flavor_container_max", function (s) {
    s.bind(function (v) {
      setCSSVar("--container-max", v + "px");
    });
  });
  wp.customize("flavor_border_radius", function (s) {
    s.bind(function (v) {
      setCSSVar("--radius", v + "px");
    });
  });

  /* -----------------------------------------------------------
   *  Shop
   * --------------------------------------------------------- */
  wp.customize("flavor_shop_card_radius", function (s) {
    s.bind(function (v) {
      setCSSVar("--fc-card-radius", v + "px");
    });
  });
  wp.customize("flavor_shop_btn_radius", function (s) {
    s.bind(function (v) {
      setCSSVar("--fc-btn-radius", v + "px");
    });
  });
  wp.customize("flavor_shop_input_radius", function (s) {
    s.bind(function (v) {
      setCSSVar("--fc-input-radius", v + "px");
    });
  });
  wp.customize("flavor_shop_img_ratio", function (s) {
    s.bind(function (v) {
      setCSSVar("--fc-img-ratio", v);
    });
  });
  wp.customize("flavor_shop_img_fit", function (s) {
    s.bind(function (v) {
      setCSSVar("--fc-img-fit", v);
    });
  });
  wp.customize("flavor_shop_sale_color", function (s) {
    s.bind(function (v) {
      setCSSVar("--fc-sale-color", v);
    });
  });
  wp.customize("flavor_shop_badge_bg", function (s) {
    s.bind(function (v) {
      setCSSVar("--fc-badge-bg", v);
    });
  });
  wp.customize("flavor_shop_preorder_bg", function (s) {
    s.bind(function (v) {
      setCSSVar("--fc-preorder-bg", v);
    });
  });

  /* -----------------------------------------------------------
   *  Archiwum
   * --------------------------------------------------------- */
  function fcSyncGrid() {
    var cols = parseInt(wp.customize("flavor_archive_columns").get()) || 3;
    var minW =
      parseInt(wp.customize("flavor_archive_card_min_width").get()) || 200;
    var grid = document.querySelector(".fc-products-grid");
    if (!grid) return;
    setCSSVar("--fc-grid-columns", cols);
    setCSSVar("--fc-card-min-width", minW + "px");
    var avail = grid.clientWidth;
    var gap = parseFloat(getComputedStyle(grid).columnGap) || 24;
    var fit = cols;
    while (fit > 1 && (avail - gap * (fit - 1)) / fit < minW) fit--;
    grid.style.gridTemplateColumns = "repeat(" + fit + ", 1fr)";
  }
  wp.customize("flavor_archive_columns", function (s) {
    s.bind(function () {
      fcSyncGrid();
    });
  });
  wp.customize("flavor_archive_card_min_width", function (s) {
    s.bind(function () {
      fcSyncGrid();
    });
  });
  $(window).on("resize", function () {
    if (document.querySelector(".fc-products-grid")) fcSyncGrid();
  });

  /* -----------------------------------------------------------
   *  Visibility toggles
   * --------------------------------------------------------- */
  wp.customize("flavor_hide_site_title", function (s) {
    s.bind(function (v) {
      $(".site-title").toggle(!v);
    });
  });
  wp.customize("flavor_hide_site_desc", function (s) {
    s.bind(function (v) {
      $(".site-description").toggle(!v);
    });
  });

  wp.customize("flavor_logo_height", function (s) {
    s.bind(function (v) {
      $(".site-logo img").css("max-height", v + "px");
    });
  });

  wp.customize("flavor_logo_dark", function (s) {
    s.bind(function (attachmentId) {
      if (!attachmentId) {
        $(".site-logo-dark").hide();
        $(".site-logo-light").show();
        return;
      }
      wp.media
        .attachment(attachmentId)
        .fetch()
        .then(function () {
          var url = wp.media.attachment(attachmentId).get("url");
          var $dark = $(".site-logo-dark");
          if ($dark.length) {
            $dark.find("img").attr("src", url);
          } else {
            var html =
              '<div class="site-logo site-logo-dark" style="display:none"><a href="/" class="custom-logo-link"><img src="' +
              url +
              '" class="custom-logo"></a></div>';
            $(".site-logo-light").after(html);
          }
          /* Show/hide based on current mode */
          var mode = wp.customize("flavor_color_mode").get();
          $(".site-logo-light").toggle(mode !== "dark");
          $(".site-logo-dark").toggle(mode === "dark");
        });
    });
  });

  wp.customize("flavor_sticky_header_desktop", function (s) {
    s.bind(function (v) {
      $(".site-header").attr("data-sticky-desktop", v ? "1" : "0");
    });
  });
  wp.customize("flavor_sticky_header_tablet", function (s) {
    s.bind(function (v) {
      $(".site-header").attr("data-sticky-tablet", v ? "1" : "0");
    });
  });
  wp.customize("flavor_sticky_header_mobile", function (s) {
    s.bind(function (v) {
      $(".site-header").attr("data-sticky-mobile", v ? "1" : "0");
    });
  });
  wp.customize("flavor_sticky_header_opacity", function (s) {
    s.bind(function (v) {
      document.documentElement.style.setProperty(
        "--header-sticky-opacity",
        v + "%",
      );
    });
  });
  /* -----------------------------------------------------------
   *  Sidebar – mobile mode live preview
   * --------------------------------------------------------- */
  wp.customize("fc_tablet_sidebar", function (s) {
    s.bind(function (v) {
      $(".fc-shop-has-sidebar")
        .removeClass("fc-mobile-sidebar-open")
        .attr("data-tablet-sidebar", v);
    });
  });
  wp.customize("fc_phone_sidebar", function (s) {
    s.bind(function (v) {
      $(".fc-shop-has-sidebar")
        .removeClass("fc-mobile-sidebar-open")
        .attr("data-phone-sidebar", v);
    });
  });
})(jQuery);
