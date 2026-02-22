/**
 * Flavor Theme — Customizer: Sidebar Widgets Control
 *
 * Custom control for managing sidebar widgets inside the WordPress Customizer.
 * Renders draggable widget cards with type-specific settings.
 */
(function ($, api) {
  "use strict";

  /* ================================================================
   *  Szablony HTML kart widgetów (per typ)
   * ================================================================ */

  var T = window.flavorAppI18n || {};

  function getWidgetFields(type, block, blocks) {
    block = block || {};
    var label = (blocks[type] || {}).label || type;
    var h = "";

    // Title (not for attributes)
    if (type !== "attributes") {
      h +=
        '<p><label class="fc-cust-field-label">' +
        esc(T.title || "Title") +
        "</label>" +
        '<input type="text" class="fc-cust-input" data-key="title" value="' +
        esc(block.title || label) +
        '"></p>';
    }

    switch (type) {
      case "categories":
        h += checkbox("show_count", T.show_count, block);
        h += checkbox("hierarchical", T.show_hierarchical, block);
        break;

      case "brands":
        h += checkbox("show_count", T.show_count, block);
        h +=
          '<p><label class="fc-cust-field-label">' +
          esc(T.display_style) +
          "</label>" +
          select("display_style", block, [
            ["list", T.list],
            ["tag_cloud", T.pills],
            ["logo", T.logo],
          ]) +
          "</p>";
        h +=
          '<p class="fc-brand-logo-size-row" style="' +
          ((block.display_style || "list") === "logo" ? "" : "display:none;") +
          '"><label class="fc-cust-field-label">' +
          esc(T.logo_size) +
          "</label>" +
          presets([40, 50, 60, 80, 100], block.logo_size || 60) +
          '<input type="number" class="fc-cust-input small-text fc-tile-size-input" data-key="logo_size" value="' +
          (block.logo_size || 60) +
          '" min="30" max="200"></p>';
        h +=
          '<p class="fc-brand-logo-tint-row" style="' +
          ((block.display_style || "list") === "logo" ? "" : "display:none;") +
          '"><label class="fc-cust-field-label">' +
          esc(T.logo_tint) +
          "</label>" +
          select("logo_tint", block, [
            ["none", T.tint_none],
            ["grayscale", T.tint_grayscale],
            ["mono", T.tint_mono],
            ["accent", T.tint_accent],
          ]) +
          "</p>";
        break;

      case "attributes":
        h += '<p class="description">' + esc(T.attr_auto_desc) + "</p>";
        h += checkbox("show_count", T.show_count, block);
        h +=
          '<p><label class="fc-cust-field-label">' +
          esc(T.color_style) +
          "</label>" +
          select("color_display", block, [
            ["tiles", T.tiles],
            ["circles", T.circles],
            ["list", T.list],
            ["dropdown", T.dropdown],
            ["pills", T.pills],
          ]) +
          "</p>";
        h +=
          '<p><label class="fc-cust-field-label">' +
          esc(T.color_size) +
          "</label>" +
          presets([20, 28, 36, 42], block.tile_size || 28) +
          '<input type="number" class="fc-cust-input small-text fc-tile-size-input" data-key="tile_size" value="' +
          (block.tile_size || 28) +
          '" min="16" max="120"></p>';
        h +=
          '<p><label class="fc-cust-field-label">' +
          esc(T.text_style) +
          "</label>" +
          select("text_display", block, [
            ["list", T.list],
            ["dropdown", T.dropdown],
            ["pills", T.pills],
          ]) +
          "</p>";
        h +=
          '<p><label class="fc-cust-field-label">' +
          esc(T.image_style) +
          "</label>" +
          select("image_display", block, [
            ["list", T.list],
            ["dropdown", T.dropdown],
            ["tiles", T.tiles],
            ["circles", T.circles],
            ["pills", T.pills],
          ]) +
          "</p>";
        h +=
          '<p><label class="fc-cust-field-label">' +
          esc(T.image_size) +
          "</label>" +
          presets([28, 36, 48, 60, 80], block.image_tile_size || 48) +
          '<input type="number" class="fc-cust-input small-text fc-tile-size-input" data-key="image_tile_size" value="' +
          (block.image_tile_size || 48) +
          '" min="16" max="200"></p>';
        break;

      case "price_filter":
        h +=
          '<p><label class="fc-cust-field-label">' +
          esc(T.style) +
          "</label>" +
          select("style", block, [
            ["inputs", T.inputs],
            ["slider", T.slider],
          ]) +
          "</p>";
        h +=
          '<p><label class="fc-cust-field-label">' +
          esc(T.step) +
          "</label>" +
          '<input type="number" class="fc-cust-input small-text" data-key="step" value="' +
          (block.step || 10) +
          '" min="1"></p>';
        break;

      case "custom_html":
        h +=
          '<p><label class="fc-cust-field-label">' +
          esc(T.html_content) +
          "</label>" +
          '<textarea class="fc-cust-input fc-cust-textarea" data-key="content">' +
          esc(block.content || "") +
          "</textarea></p>";
        break;

      case "bestsellers":
      case "new_products":
      case "on_sale":
        h +=
          '<p><label class="fc-cust-field-label">' +
          esc(T.products_count) +
          "</label>" +
          '<input type="number" class="fc-cust-input small-text" data-key="limit" value="' +
          (block.limit || 5) +
          '" min="1" max="20"></p>';
        break;

      case "cta_banner":
        h +=
          '<p><label class="fc-cust-field-label">' +
          esc(T.image_url) +
          "</label>" +
          '<input type="url" class="fc-cust-input" data-key="image_url" value="' +
          esc(block.image_url || "") +
          '" placeholder="https://..."></p>';
        h +=
          '<p><label class="fc-cust-field-label">' +
          esc(T.text) +
          "</label>" +
          '<input type="text" class="fc-cust-input" data-key="text" value="' +
          esc(block.text || "") +
          '"></p>';
        h +=
          '<p><label class="fc-cust-field-label">' +
          esc(T.button_text) +
          "</label>" +
          '<input type="text" class="fc-cust-input" data-key="button_text" value="' +
          esc(block.button_text || "") +
          '"></p>';
        h +=
          '<p><label class="fc-cust-field-label">' +
          esc(T.button_url) +
          "</label>" +
          '<input type="url" class="fc-cust-input" data-key="button_url" value="' +
          esc(block.button_url || "") +
          '" placeholder="https://..."></p>';
        h +=
          '<p><label class="fc-cust-field-label">' +
          esc(T.bg_color) +
          "</label>" +
          '<input type="color" class="fc-cust-input" data-key="bg_color" value="' +
          (block.bg_color || "#2271b1") +
          '"></p>';
        break;
    }

    return h;
  }

  /* ── Helpery HTML ── */

  function esc(str) {
    var div = document.createElement("div");
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
  }

  function checkbox(key, label, block) {
    var chk = block[key] ? " checked" : "";
    return (
      "<p><label>" +
      '<input type="checkbox" class="fc-cust-check" data-key="' +
      key +
      '"' +
      chk +
      "> " +
      label +
      "</label></p>"
    );
  }

  function select(key, block, options) {
    var val = block[key] || options[0][0];
    var h = '<select class="fc-cust-select" data-key="' + key + '">';
    for (var i = 0; i < options.length; i++) {
      var sel = val === options[i][0] ? " selected" : "";
      h +=
        '<option value="' +
        options[i][0] +
        '"' +
        sel +
        ">" +
        options[i][1] +
        "</option>";
    }
    h += "</select>";
    return h;
  }

  function presets(sizes, current) {
    var h = '<span class="fc-tile-size-presets">';
    for (var i = 0; i < sizes.length; i++) {
      var cls = parseInt(current, 10) === sizes[i] ? " button-primary" : "";
      h +=
        '<button type="button" class="button fc-tile-size-preset' +
        cls +
        '" data-size="' +
        sizes[i] +
        '">' +
        sizes[i] +
        "</button>";
    }
    h += "</span>";
    return h;
  }

  /* ── Buduj kartę widgetu ── */

  function buildCard(block, blocks) {
    var type = block.type;
    var info = blocks[type] || { label: type, icon: "dashicons-admin-generic" };

    var h = '<div class="fc-cust-widget-card" data-type="' + type + '">';
    h += '<div class="fc-cust-card-header">';
    h += '<span class="fc-cust-drag dashicons dashicons-menu"></span>';
    h += '<span class="dashicons ' + info.icon + ' fc-cust-card-icon"></span>';
    h += '<span class="fc-cust-card-label">' + info.label + "</span>";
    h +=
      '<button type="button" class="fc-cust-card-toggle dashicons dashicons-arrow-down-alt2"></button>';
    h +=
      '<button type="button" class="fc-cust-card-remove dashicons dashicons-no-alt"></button>';
    h += "</div>";
    h += '<div class="fc-cust-card-body" style="display:none;">';
    h += getWidgetFields(type, block, blocks);
    h += "</div>";
    h += "</div>";

    return h;
  }

  /* ================================================================
   *  Rejestracja kontrolera Customizera
   * ================================================================ */

  api.controlConstructor.fc_sidebar_widgets = api.Control.extend({
    ready: function () {
      var control = this;
      var $wrap = control.container.find(".fc-cust-widgets-wrap");
      var $list = $wrap.find(".fc-cust-widgets-list");
      var $empty = $wrap.find(".fc-cust-empty-msg");

      // Pobierz dane z atrybutów data-* (renderowane przez PHP)
      var blocks = {};
      var items = [];
      try {
        blocks = JSON.parse($wrap.attr("data-blocks") || "{}");
      } catch (e) {}
      try {
        items = JSON.parse($wrap.attr("data-value") || "[]");
      } catch (e) {}
      if (!$.isArray(items)) items = [];

      /* ── Renderuj istniejące widgety ── */
      renderAll(items);

      /* ── Sortable ── */
      $list.sortable({
        handle: ".fc-cust-drag",
        placeholder: "fc-cust-sortable-placeholder",
        tolerance: "pointer",
        update: function () {
          syncValue();
        },
      });

      /* ── Dodawanie widgetu ── */
      $wrap.find(".fc-cust-add-btn").on("click", function () {
        var $sel = $wrap.find(".fc-cust-add-select");
        var type = $sel.val();
        if (!type || !blocks[type]) return;

        var newBlock = { type: type, title: blocks[type].label };
        var $card = $(buildCard(newBlock, blocks));
        $list.append($card);
        $card.find(".fc-cust-card-body").slideDown(200);
        $card.find(".fc-cust-card-toggle").addClass("open");
        $sel.val("");
        toggleEmpty();
        syncValue();
      });

      /* ── Toggle (rozwiń/zwiń) ── */
      $wrap.on("click", ".fc-cust-card-toggle", function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $card = $(this).closest(".fc-cust-widget-card");
        $card.find(".fc-cust-card-body").slideToggle(200);
        $(this).toggleClass("open");
      });

      /* ── Kliknięcie nagłówka ── */
      $wrap.on("click", ".fc-cust-card-header", function (e) {
        if (
          $(e.target).hasClass("fc-cust-card-remove") ||
          $(e.target).hasClass("fc-cust-card-toggle")
        )
          return;
        $(this).find(".fc-cust-card-toggle").trigger("click");
      });

      /* ── Usuwanie ── */
      $wrap.on("click", ".fc-cust-card-remove", function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this)
          .closest(".fc-cust-widget-card")
          .slideUp(200, function () {
            $(this).remove();
            toggleEmpty();
            syncValue();
          });
      });

      /* ── Zmiana wartości w polach → syncValue ── */
      $wrap.on(
        "change input",
        ".fc-cust-input, .fc-cust-select, .fc-cust-check, .fc-cust-textarea",
        function () {
          syncValue();
        },
      );

      /* ── Brand display_style → pokaż/ukryj logo_size ── */
      $wrap.on(
        "change",
        '.fc-cust-select[data-key="display_style"]',
        function () {
          var $body = $(this).closest(".fc-cust-card-body");
          var $sizeRow = $body.find(".fc-brand-logo-size-row");
          var $tintRow = $body.find(".fc-brand-logo-tint-row");
          if ($(this).val() === "logo") {
            $sizeRow.slideDown(150);
            $tintRow.slideDown(150);
          } else {
            $sizeRow.slideUp(150);
            $tintRow.slideUp(150);
          }
        },
      );

      /* ── Tile size presets ── */
      $wrap.on("click", ".fc-tile-size-preset", function () {
        var sz = $(this).data("size");
        var $row = $(this).closest("p");
        $row.find(".fc-tile-size-input").val(sz).trigger("change");
        $(this).siblings(".fc-tile-size-preset").removeClass("button-primary");
        $(this).addClass("button-primary");
      });
      $wrap.on("input", ".fc-tile-size-input", function () {
        var val = parseInt($(this).val(), 10);
        var $pr = $(this).closest("p").find(".fc-tile-size-preset");
        $pr.removeClass("button-primary");
        $pr.filter('[data-size="' + val + '"]').addClass("button-primary");
      });

      /* ── Funkcje pomocnicze ── */

      function renderAll(arr) {
        $list.empty();
        for (var i = 0; i < arr.length; i++) {
          $list.append(buildCard(arr[i], blocks));
        }
        toggleEmpty();
      }

      function toggleEmpty() {
        if ($list.find(".fc-cust-widget-card").length) {
          $empty.hide();
        } else {
          $empty.show();
        }
      }

      function syncValue() {
        var arr = [];
        $list.find(".fc-cust-widget-card").each(function () {
          var $card = $(this);
          var obj = { type: $card.attr("data-type") };

          $card.find(".fc-cust-input, .fc-cust-textarea").each(function () {
            obj[$(this).data("key")] = $(this).val();
          });
          $card.find(".fc-cust-select").each(function () {
            obj[$(this).data("key")] = $(this).val();
          });
          $card.find(".fc-cust-check").each(function () {
            obj[$(this).data("key")] = $(this).is(":checked");
          });

          arr.push(obj);
        });

        control.setting.set(JSON.stringify(arr));
      }
    },
  });
})(jQuery, wp.customize);

/* ================================================================
 *  Flavor Menu Control — Customizer Control Constructor
 * ================================================================ */
(function ($, api) {
  "use strict";

  var T = window.flavorAppI18n || {};

  var specialIcons = {
    fc_account:
      '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
    fc_cart:
      '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>',
    fc_wishlist:
      '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>',
    fc_compare:
      '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="M7 21h10"/><path d="M12 3v18"/><path d="M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"/></svg>',
    fc_shop:
      '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/><path d="M2 7h20"/><path d="M22 7v3a2 2 0 0 1-2 2a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12a2 2 0 0 1-2-2V7"/></svg>',
  };
  var specialLabels = {
    fc_shop: T.shop || "Shop",
    fc_account: T.my_account || "My account",
    fc_cart: T.cart || "Cart",
    fc_wishlist: T.wishlist || "Wishlist",
    fc_compare: T.compare || "Compare",
  };

  function isSpecial(type) {
    return (
      type === "fc_shop" ||
      type === "fc_account" ||
      type === "fc_cart" ||
      type === "fc_wishlist" ||
      type === "fc_compare"
    );
  }

  function escHtml(str) {
    var div = document.createElement("div");
    div.appendChild(document.createTextNode(str));
    return div.innerHTML.replace(/"/g, "&quot;");
  }

  api.controlConstructor.fc_menu = api.Control.extend({
    ready: function () {
      var control = this;
      var $wrap = control.container.find(".fc-menu-control");
      var wrap = $wrap[0];
      if (!wrap) return;

      var pages = [];
      var items = [];
      try {
        pages = JSON.parse(wrap.dataset.pages || "[]");
      } catch (e) {}
      try {
        items = JSON.parse(wrap.dataset.value || "[]");
      } catch (e) {}
      if (!Array.isArray(items)) items = [];

      var $list = $wrap.find(".fc-menu-items");
      var list = $list[0];
      var $pagesList = $wrap.find(".fc-menu-pages-list");
      var pagesList = $pagesList[0];

      /* ── Save value to Customizer setting ── */
      function save() {
        var val = [];
        $list.find(".fc-menu-item").each(function () {
          var el = this;
          var obj = {
            type: el.dataset.type,
            id: el.dataset.id ? parseInt(el.dataset.id, 10) : 0,
            title: $(el).find(".fc-menu-item-title").val(),
            url: el.dataset.url || "",
          };
          /* Shop-specific options */
          if (el.dataset.type === "fc_shop") {
            obj.shop_display =
              $(el).find(".fc-menu-shop-display").val() || "text";
          }
          /* Cart-specific options */
          if (el.dataset.type === "fc_cart") {
            obj.cart_action =
              $(el).find(".fc-menu-cart-action").val() || "minicart";
            obj.show_total = $(el)
              .find(".fc-menu-cart-show-total")
              .is(":checked");
          }
          val.push(obj);
        });
        control.setting.set(JSON.stringify(val));
      }

      /* ── Render a single menu item ── */
      function renderItem(item) {
        var special = isSpecial(item.type);
        var borderColor = special ? "#dab24a" : "#ddd";
        var bg = special ? "#fef9e7" : "#fff";

        var h =
          '<div class="fc-menu-item" draggable="true"' +
          ' data-type="' +
          escHtml(item.type) +
          '"' +
          ' data-id="' +
          (item.id || 0) +
          '"' +
          ' data-url="' +
          escHtml(item.url || "") +
          '"' +
          ' style="display:flex;flex-wrap:wrap;align-items:center;gap:6px;padding:6px 8px;margin:0 0 4px;background:' +
          bg +
          ";border:1px solid " +
          borderColor +
          ';border-radius:3px;cursor:grab;">';

        h +=
          '<span class="dashicons dashicons-menu" style="color:#999;font-size:16px;flex-shrink:0;"></span>';

        if (special) {
          h +=
            '<span style="flex-shrink:0;display:inline-flex;align-items:center;">' +
            specialIcons[item.type] +
            "</span>";
        }

        h +=
          '<input type="text" class="fc-menu-item-title" value="' +
          escHtml(item.title || "") +
          '" style="flex:1;border:1px solid #ddd;border-radius:3px;padding:3px 6px;font-size:12px;">';

        if (special) {
          var typeLabels = {
            fc_shop: T.type_shop || "shop",
            fc_cart: T.type_cart || "cart",
            fc_account: T.type_account || "account",
            fc_wishlist: T.type_wishlist || "wishlist",
            fc_compare: T.type_compare || "compare",
          };
          h +=
            '<span style="font-size:10px;color:#996;flex-shrink:0;">' +
            (typeLabels[item.type] || item.type) +
            "</span>";
        }

        h +=
          '<button type="button" class="fc-menu-item-remove" style="border:none;background:none;color:#a00;cursor:pointer;font-size:16px;padding:0 4px;" title="' +
          escHtml(T.remove || "Remove") +
          '">&times;</button>';
        /* Shop options panel */
        if (item.type === "fc_shop") {
          var shopDisplay = item.shop_display || "text";
          h +=
            '<div class="fc-menu-shop-options" style="width:100%;margin-top:6px;padding:6px 8px;background:#fff;border:1px solid #e8d68a;border-radius:3px;font-size:11px;">';
          h +=
            '<p style="margin:0;"><label style="font-weight:600;color:#555;">' +
            escHtml(T.shop_display || "Display:") +
            "</label><br>";
          h +=
            '<select class="fc-menu-shop-display" style="width:100%;font-size:11px;margin-top:2px;">';
          h +=
            '<option value="text"' +
            (shopDisplay === "text" ? " selected" : "") +
            ">" +
            escHtml(T.shop_text || "Text") +
            "</option>";
          h +=
            '<option value="icon"' +
            (shopDisplay === "icon" ? " selected" : "") +
            ">" +
            escHtml(T.shop_icon || "Icon") +
            "</option>";
          h += "</select></p>";
          h += "</div>";
        }
        /* Cart options panel */
        if (item.type === "fc_cart") {
          var actionVal = item.cart_action || "minicart";
          var totalChk = item.show_total ? " checked" : "";
          h +=
            '<div class="fc-menu-cart-options" style="width:100%;margin-top:6px;padding:6px 8px;background:#fff;border:1px solid #e8d68a;border-radius:3px;font-size:11px;">';
          h +=
            '<p style="margin:0 0 4px;"><label style="font-weight:600;color:#555;">' +
            escHtml(T.cart_action || "Click action:") +
            "</label><br>";
          h +=
            '<select class="fc-menu-cart-action" style="width:100%;font-size:11px;margin-top:2px;">';
          h +=
            '<option value="minicart"' +
            (actionVal === "minicart" ? " selected" : "") +
            ">" +
            escHtml(T.open_minicart || "Open mini cart") +
            "</option>";
          h +=
            '<option value="page"' +
            (actionVal === "page" ? " selected" : "") +
            ">" +
            escHtml(T.go_to_cart || "Go to cart page") +
            "</option>";
          h += "</select></p>";
          h +=
            '<p style="margin:0;"><label style="cursor:pointer;"><input type="checkbox" class="fc-menu-cart-show-total"' +
            totalChk +
            ' style="margin-right:4px;"> ' +
            escHtml(T.show_total || "Show cart total") +
            "</label></p>";
          h += "</div>";
        }
        h += "</div>";

        var $div = $(h);
        $list.append($div);

        /* Events */
        $div.find(".fc-menu-item-title").on("input", save);
        $div.find(".fc-menu-shop-display").on("change", save);
        $div.find(".fc-menu-cart-action").on("change", save);
        $div.find(".fc-menu-cart-show-total").on("change", save);
        $div.find(".fc-menu-item-remove").on("click", function () {
          $div.remove();
          save();
          renderPages();
          updateSpecialButtons();
        });
      }

      /* ── Render available pages ── */
      function renderPages() {
        $pagesList.empty();
        var usedIds = [];
        $list.find('.fc-menu-item[data-type="page"]').each(function () {
          usedIds.push(parseInt(this.dataset.id, 10));
        });

        var anyShown = false;
        $.each(pages, function (i, p) {
          if (usedIds.indexOf(p.id) !== -1) return;
          anyShown = true;
          var $label = $(
            '<label style="display:block;padding:3px 0;font-size:12px;cursor:pointer;"><input type="checkbox" style="margin-right:6px;"> ' +
              escHtml(p.title) +
              "</label>",
          );
          $label.find("input").on("change", function () {
            if (this.checked) {
              renderItem({
                type: "page",
                id: p.id,
                title: p.title,
                url: p.url,
              });
              save();
              renderPages();
            }
          });
          $pagesList.append($label);
        });

        if (!anyShown) {
          $pagesList.html(
            '<em style="font-size:12px;color:#999;">' +
              escHtml(T.all_pages_added || "All pages added.") +
              "</em>",
          );
        }
      }

      /* ── Update special buttons state ── */
      function updateSpecialButtons() {
        $wrap.find(".fc-menu-add-special").each(function () {
          var sType = this.dataset.special;
          var exists =
            $list.find('.fc-menu-item[data-type="' + sType + '"]').length > 0;
          this.disabled = exists;
          this.style.opacity = exists ? "0.4" : "1";
        });
      }

      /* ── Init: render existing items ── */
      $.each(items, function (i, item) {
        renderItem(item);
      });
      renderPages();
      updateSpecialButtons();

      /* ── Sortable drag & drop ── */
      $list.sortable({
        handle: ".dashicons-menu",
        placeholder: "fc-menu-sortable-placeholder",
        tolerance: "pointer",
        update: function () {
          save();
        },
      });

      /* ── Add custom link ── */
      $wrap.find(".fc-menu-add-custom").on("click", function () {
        var $t = $wrap.find(".fc-menu-custom-title");
        var $u = $wrap.find(".fc-menu-custom-url");
        var t = $t.val().trim();
        var u = $u.val().trim();
        if (!t || !u) return;
        renderItem({ type: "custom", id: 0, title: t, url: u });
        save();
        $t.val("");
        $u.val("");
      });

      /* ── Add special shop items ── */
      $wrap.on("click", ".fc-menu-add-special", function () {
        var sType = this.dataset.special;
        if ($list.find('.fc-menu-item[data-type="' + sType + '"]').length)
          return;
        renderItem({
          type: sType,
          id: 0,
          title: specialLabels[sType] || sType,
          url: "",
        });
        save();
        updateSpecialButtons();
      });
    },
  });

  /* ================================================================
   *  Side Panel Icons — sortable list with checkboxes
   * ================================================================ */

  api.controlConstructor.fc_side_panel_icons = api.Control.extend({
    ready: function () {
      var control = this;
      var $wrap = control.container.find(".fc-side-panel-icons-control");
      var wrap = $wrap[0];
      if (!wrap) return;

      var iconsMeta = {};
      var items = [];
      try {
        iconsMeta = JSON.parse(wrap.dataset.iconsMeta || "{}");
      } catch (e) {}
      try {
        items = JSON.parse(wrap.dataset.value || "[]");
      } catch (e) {}
      if (!Array.isArray(items) || items.length === 0) {
        items = [
          { key: "account", enabled: true },
          { key: "cart", enabled: true },
          { key: "wishlist", enabled: true },
          { key: "compare", enabled: true },
        ];
      }

      var $list = $wrap.find(".fc-sp-icons-list");
      var openOnAddLabel = wrap.dataset.openOnAddLabel || "Open on add";

      function save() {
        var val = [];
        $list.find(".fc-sp-icon-item").each(function () {
          var entry = {
            key: this.dataset.key,
            enabled: $(this).find(".fc-sp-icon-check").is(":checked"),
          };
          var $ooa = $(this).find(".fc-sp-open-on-add");
          if ($ooa.length) {
            entry.open_on_add = $ooa.is(":checked");
          }
          val.push(entry);
        });
        control.setting.set(JSON.stringify(val));
      }

      function renderItem(item) {
        var meta = iconsMeta[item.key] || {};
        var label = meta.label || item.key;
        var svg = meta.svg || "";
        var checked = item.enabled ? " checked" : "";
        var hasOpenOnAdd = meta.has_open_on_add;

        var h =
          '<div class="fc-sp-icon-item" data-key="' +
          escHtml(item.key) +
          '"' +
          ' style="display:flex;flex-wrap:wrap;align-items:center;gap:8px;padding:8px 10px;margin:0 0 4px;background:#fef9e7;border:1px solid #dab24a;border-radius:3px;cursor:grab;">';
        h +=
          '<span class="dashicons dashicons-menu" style="color:#999;font-size:16px;flex-shrink:0;cursor:grab;"></span>';
        h +=
          '<label style="display:flex;align-items:center;gap:6px;flex:1;cursor:pointer;font-size:12px;user-select:none;">' +
          '<input type="checkbox" class="fc-sp-icon-check"' +
          checked +
          ' style="margin:0;">' +
          svg +
          "<span>" +
          escHtml(label) +
          "</span></label>";

        if (hasOpenOnAdd) {
          var ooaChecked = item.open_on_add ? " checked" : "";
          h +=
            '<div style="width:100%;padding:4px 0 0 24px;">' +
            '<label style="display:flex;align-items:center;gap:4px;cursor:pointer;font-size:11px;color:#777;user-select:none;">' +
            '<input type="checkbox" class="fc-sp-open-on-add"' +
            ooaChecked +
            ' style="margin:0;"> ' +
            escHtml(openOnAddLabel) +
            "</label></div>";
        }

        h += "</div>";

        var $div = $(h);
        $list.append($div);
        $div.find(".fc-sp-icon-check").on("change", save);
        $div.find(".fc-sp-open-on-add").on("change", save);
      }

      $.each(items, function (i, item) {
        renderItem(item);
      });

      $list.sortable({
        handle: ".dashicons-menu",
        placeholder: "fc-sp-sortable-placeholder",
        tolerance: "pointer",
        update: function () {
          save();
        },
      });
    },
  });
})(jQuery, wp.customize);
