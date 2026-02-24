<?php
/**
 * Flavor Theme — Wygląd sklepu w Customizerze
 *
 * Dodaje sekcję "Sidebar sklepu" w panelu Customizera (Wygląd sklepu).
 * Wymaga aktywnego pluginu Flavor Commerce (FC_VERSION).
 *
 * @package Flavor
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* =====================================================================
 *  Lista dostępnych bloków sidebara
 * ===================================================================== */

function flavor_get_available_blocks() {
    return array(
        'categories'    => array( 'label' => fc__( 'theme_widget_categories' ),    'icon' => 'dashicons-category' ),
        'brands'        => array( 'label' => fc__( 'theme_widget_brands' ),         'icon' => 'dashicons-tag' ),
        'attributes'    => array( 'label' => fc__( 'theme_widget_attributes' ),     'icon' => 'dashicons-networking' ),
        'price_filter'  => array( 'label' => fc__( 'theme_widget_price' ),          'icon' => 'dashicons-money-alt' ),
        'rating_filter' => array( 'label' => fc__( 'theme_widget_rating' ),         'icon' => 'dashicons-star-filled' ),
        'availability'  => array( 'label' => fc__( 'theme_widget_availability' ),   'icon' => 'dashicons-yes-alt' ),
        'search'        => array( 'label' => fc__( 'theme_widget_search' ),         'icon' => 'dashicons-search' ),
        'bestsellers'   => array( 'label' => fc__( 'theme_widget_bestsellers' ),    'icon' => 'dashicons-awards' ),
        'new_products'  => array( 'label' => fc__( 'theme_widget_new_products' ),   'icon' => 'dashicons-megaphone' ),
        'on_sale'       => array( 'label' => fc__( 'theme_widget_on_sale' ),        'icon' => 'dashicons-tickets-alt' ),
        'cta_banner'    => array( 'label' => fc__( 'theme_widget_cta_banner' ),     'icon' => 'dashicons-format-image' ),
        'custom_html'   => array( 'label' => fc__( 'theme_widget_custom_html' ),    'icon' => 'dashicons-editor-code' ),
    );
}

/* =====================================================================
 *  Custom Customizer Control — Sidebar Widgets Manager
 * ===================================================================== */

if ( class_exists( 'WP_Customize_Control' ) ) :

/**
 * Heading / separator control — renders a collapsible heading inside a Customizer section.
 * Clicking toggles visibility of all subsequent controls.
 */
class Flavor_Heading_Control extends WP_Customize_Control {
    public $type = 'fc_heading';
    public $collapsed = true;

    public function render_content() {
        $id = esc_attr( $this->id );
        $arrow = $this->collapsed ? '&#9654;' : '&#9660;';
        ?>
        <h4 class="fc-cust-heading" id="fc-heading-<?php echo $id; ?>"
            style="margin:18px 0 6px;padding:10px 0 6px;border-bottom:1px solid #ddd;font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#555;cursor:pointer;user-select:none;display:flex;align-items:center;gap:6px;">
            <span class="fc-heading-arrow" style="font-size:10px;transition:transform .2s;"><?php echo $arrow; ?></span>
            <?php echo esc_html( $this->label ); ?>
        </h4>
        <?php if ( $this->description ) : ?>
            <p class="description"><?php echo esc_html( $this->description ); ?></p>
        <?php endif; ?>
        <script>
        (function(){
            var heading = document.getElementById('fc-heading-<?php echo $id; ?>');
            if ( ! heading ) return;
            var li = heading.closest('li.customize-control');
            if ( ! li ) return;
            var collapsed = <?php echo $this->collapsed ? 'true' : 'false'; ?>;
            var arrow = heading.querySelector('.fc-heading-arrow');

            function getSiblings() {
                var sibs = [], el = li.nextElementSibling;
                while ( el ) {
                    if ( el.querySelector('.fc-cust-heading') ) break;
                    sibs.push( el );
                    el = el.nextElementSibling;
                }
                return sibs;
            }

            function toggle() {
                collapsed = ! collapsed;
                arrow.innerHTML = collapsed ? '&#9654;' : '&#9660;';
                var sibs = getSiblings();
                for ( var i = 0; i < sibs.length; i++ ) {
                    sibs[i].style.display = collapsed ? 'none' : '';
                }
            }

            heading.addEventListener('click', toggle);

            // initial state
            if ( collapsed ) {
                setTimeout(function(){
                    var sibs = getSiblings();
                    for ( var i = 0; i < sibs.length; i++ ) {
                        sibs[i].style.display = 'none';
                    }
                }, 50);
            }
        })();
        </script>
        <?php
    }
}

/**
 * Kontrolka Customizer: sortowalna lista kart z opcjami wewnątrz (drag & drop + expand)
 */
class Flavor_Sortable_Cards_Control extends WP_Customize_Control {
    public $type = 'fc_sortable_cards';
    public $cards = array();

    public function enqueue() {
        wp_enqueue_script( 'jquery-ui-sortable' );
    }

    public function render_content() {
        $order = $this->value();
        $order_keys = array_filter( array_map( 'trim', explode( ',', $order ) ) );

        // Upewnij się, że wszystkie karty są w liście
        $all_keys = array_keys( $this->cards );
        foreach ( $all_keys as $k ) {
            if ( ! in_array( $k, $order_keys, true ) ) {
                $order_keys[] = $k;
            }
        }

        // CSS — render only once
        static $css_rendered = false;
        if ( ! $css_rendered ) {
            $css_rendered = true;
            ?>
            <style>
            /* ── Pinned card (not sortable, always on top) ── */
            .fc-card-pinned {
                margin: 8px 0 6px;
                background: #fff;
                border: 1px solid #ddd;
                border-radius: 4px;
                user-select: none;
                transition: box-shadow .15s, border-color .15s;
            }
            .fc-card-pinned:hover {
                border-color: #0073aa;
                box-shadow: 0 1px 3px rgba(0,0,0,.1);
            }
            .fc-card-pinned .fc-sortable-card-header {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 10px 12px;
                cursor: pointer;
            }
            .fc-card-pinned .fc-sortable-card-body {
                display: none;
                padding: 0 12px 12px;
                border-top: 1px solid #eee;
            }
            .fc-card-pinned.fc-card-open .fc-sortable-card-body {
                display: block;
            }
            .fc-card-pinned.fc-card-open .fc-sortable-arrow {
                transform: rotate(90deg);
            }
            .fc-sortable-cards {
                list-style: none;
                margin: 8px 0 0;
                padding: 0;
            }
            .fc-sortable-card {
                margin: 0 0 4px;
                background: #fff;
                border: 1px solid #ddd;
                border-radius: 4px;
                user-select: none;
                transition: box-shadow .15s, border-color .15s;
            }
            .fc-sortable-card:hover {
                border-color: #0073aa;
                box-shadow: 0 1px 3px rgba(0,0,0,.1);
            }
            .fc-sortable-card-header {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 10px 12px;
                cursor: pointer;
            }
            .fc-sortable-handle {
                color: #999;
                font-size: 16px;
                line-height: 1;
                flex-shrink: 0;
                cursor: grab;
            }
            .fc-sortable-card:active .fc-sortable-handle {
                cursor: grabbing;
            }
            .fc-sortable-icon {
                flex-shrink: 0;
                display: flex;
                align-items: center;
                color: #555;
            }
            .fc-sortable-icon svg {
                display: block;
            }
            .fc-sortable-label {
                font-size: 13px;
                font-weight: 500;
                color: #333;
                flex: 1;
            }
            .fc-sortable-arrow {
                font-size: 10px;
                color: #999;
                transition: transform .2s;
                flex-shrink: 0;
            }
            .fc-sortable-card.fc-card-open .fc-sortable-arrow {
                transform: rotate(90deg);
            }
            .fc-sortable-card-body {
                display: none;
                padding: 0 12px 12px;
                border-top: 1px solid #eee;
            }
            .fc-sortable-card.fc-card-open .fc-sortable-card-body {
                display: block;
            }
            .fc-sortable-card-body .fc-card-field {
                margin: 8px 0 0;
            }
            .fc-sortable-card-body .fc-card-field label {
                display: flex;
                align-items: center;
                gap: 6px;
                font-size: 12px;
                color: #444;
                cursor: pointer;
            }
            .fc-sortable-card-body .fc-card-field label input[type="checkbox"] {
                margin: 0;
            }
            .fc-sortable-placeholder {
                height: 42px;
                margin: 0 0 4px;
                border: 2px dashed #0073aa;
                border-radius: 4px;
                background: #f0f6fc;
            }
            .ui-sortable-helper {
                box-shadow: 0 3px 8px rgba(0,0,0,.15);
                border-color: #0073aa;
            }
            /* ── Repeater items & buttons ── */
            .fc-about-repeater {
                margin: 8px 0 0;
            }
            .fc-about-repeater-item {
                position: relative;
                display: flex;
                flex-direction: column;
                gap: 6px;
                align-items: stretch;
                padding: 10px 30px 10px 10px;
                margin: 0 0 6px;
                background: #fff;
                border: 1px solid #ddd;
                border-radius: 4px;
                transition: border-color .15s;
            }
            .fc-about-repeater-item:hover {
                border-color: #0073aa;
            }
            .fc-about-repeater-field {
                flex: 1;
                min-width: 0;
            }
            .fc-about-repeater-field label {
                font-size: 10px;
                color: #888;
                display: block;
                margin-bottom: 2px;
            }
            .fc-about-repeater-field input[type="text"],
            .fc-about-repeater-field textarea,
            .fc-about-repeater-field select {
                width: 100%;
                box-sizing: border-box;
                font-size: 12px;
                padding: 4px 8px;
                border: 1px solid #c3c4c7;
                border-radius: 3px;
                background: #fff;
                transition: border-color .15s;
            }
            .fc-about-repeater-field input[type="text"]:focus,
            .fc-about-repeater-field textarea:focus,
            .fc-about-repeater-field select:focus {
                border-color: #2271b1;
                outline: none;
                box-shadow: 0 0 0 1px #2271b1;
            }
            .fc-about-repeater-field textarea {
                resize: vertical;
            }
            .fc-about-repeater-remove {
                position: absolute;
                top: 6px;
                right: 6px;
                background: none;
                border: none;
                color: #a00;
                cursor: pointer;
                font-size: 18px;
                line-height: 1;
                padding: 0 4px;
                border-radius: 3px;
                transition: color .15s, background .15s;
            }
            .fc-about-repeater-remove:hover {
                color: #dc3232;
                background: #fef1f1;
            }
            .fc-about-repeater-add {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 6px 14px;
                font-size: 12px;
                font-weight: 500;
                background: #f0f0f1;
                border: 1px solid #c3c4c7;
                border-radius: 3px;
                cursor: pointer;
                color: #2271b1;
                transition: background .15s, border-color .15s, color .15s;
                margin-top: 2px;
            }
            .fc-about-repeater-add:hover {
                background: #e5e5e6;
                border-color: #999;
                color: #135e96;
            }
            .fc-rep-img-btn {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 4px 10px;
                font-size: 11px;
                background: #f0f0f1;
                border: 1px solid #c3c4c7;
                border-radius: 3px;
                cursor: pointer;
                color: #2271b1;
                transition: background .15s, border-color .15s;
            }
            .fc-rep-img-btn:hover {
                background: #e5e5e6;
                border-color: #999;
            }
            .fc-rep-img-preview {
                max-width: 60px;
                height: 60px;
                object-fit: cover;
                border-radius: 50%;
                margin-bottom: 4px;
            }
            /* ── Country custom dropdown ── */
            .fc-country-dropdown {
                position: relative;
            }
            .fc-country-trigger {
                display: flex;
                align-items: center;
                justify-content: space-between;
                width: 100%;
                box-sizing: border-box;
                padding: 5px 8px;
                font-size: 12px;
                background: #fff;
                border: 1px solid #c3c4c7;
                border-radius: 3px;
                cursor: pointer;
                text-align: left;
                transition: border-color .15s;
            }
            .fc-country-trigger:hover,
            .fc-country-trigger:focus {
                border-color: #2271b1;
                outline: none;
            }
            .fc-country-selected {
                display: flex;
                align-items: center;
                gap: 4px;
                flex: 1;
                min-width: 0;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .fc-country-selected img {
                flex-shrink: 0;
                width: 16px;
                height: 12px;
            }
            .fc-country-arrow {
                font-size: 10px;
                color: #888;
                flex-shrink: 0;
                margin-left: 4px;
            }
            .fc-country-list {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                z-index: 100;
                background: #fff;
                border: 1px solid #c3c4c7;
                border-radius: 3px;
                box-shadow: 0 3px 8px rgba(0,0,0,.12);
                margin-top: 2px;
                max-height: 200px;
                overflow: hidden;
                display: flex;
                flex-direction: column;
            }
            .fc-country-search {
                width: 100%;
                box-sizing: border-box;
                padding: 5px 8px;
                font-size: 12px;
                border: none;
                border-bottom: 1px solid #eee;
                outline: none;
                flex-shrink: 0;
            }
            .fc-country-list ul {
                list-style: none;
                margin: 0;
                padding: 0;
                overflow-y: auto;
                flex: 1;
            }
            .fc-country-list li {
                display: flex;
                align-items: center;
                gap: 6px;
                padding: 5px 8px;
                font-size: 12px;
                cursor: pointer;
                transition: background .1s;
            }
            .fc-country-list li:hover {
                background: #f0f6fc;
            }
            .fc-country-list li.fc-country-active {
                background: #e5f0ff;
                font-weight: 600;
            }
            .fc-country-list li img {
                width: 16px;
                height: 12px;
                flex-shrink: 0;
            }
            /* ── Social sortable items ── */
            .fc-social-sortable {
                list-style: none;
                margin: 8px 0 0;
                padding: 0;
            }
            .fc-social-item {
                margin: 0 0 3px;
                background: #fff;
                border: 1px solid #e0e0e0;
                border-radius: 3px;
                transition: border-color .15s, opacity .15s;
            }
            .fc-social-item:hover {
                border-color: #0073aa;
            }
            .fc-social-item.fc-social-disabled {
                opacity: .5;
            }
            .fc-social-item.fc-social-disabled .fc-social-item-url {
                display: none;
            }
            .fc-social-item-header {
                display: flex;
                align-items: center;
                gap: 6px;
                padding: 6px 8px;
            }
            .fc-social-handle {
                color: #bbb;
                font-size: 13px;
                cursor: grab;
                flex-shrink: 0;
            }
            .fc-social-item:active .fc-social-handle {
                cursor: grabbing;
            }
            .fc-social-item-icon {
                flex-shrink: 0;
                display: flex;
                align-items: center;
                color: #555;
            }
            .fc-social-item-label {
                font-size: 12px;
                font-weight: 500;
                color: #333;
                flex: 1;
            }
            /* Toggle switch */
            .fc-social-toggle {
                position: relative;
                display: inline-block;
                width: 30px;
                height: 16px;
                flex-shrink: 0;
            }
            .fc-social-toggle input {
                opacity: 0;
                width: 0;
                height: 0;
                position: absolute;
            }
            .fc-social-toggle-slider {
                position: absolute;
                cursor: pointer;
                inset: 0;
                background: #ccc;
                border-radius: 16px;
                transition: background .2s;
            }
            .fc-social-toggle-slider::before {
                content: '';
                position: absolute;
                width: 12px;
                height: 12px;
                left: 2px;
                bottom: 2px;
                background: #fff;
                border-radius: 50%;
                transition: transform .2s;
            }
            .fc-social-toggle input:checked + .fc-social-toggle-slider {
                background: #0073aa;
            }
            .fc-social-toggle input:checked + .fc-social-toggle-slider::before {
                transform: translateX(14px);
            }
            .fc-social-item-url {
                padding: 0 8px 6px;
            }
            .fc-social-sortable .ui-sortable-placeholder {
                height: 32px;
                margin: 0 0 3px;
                border: 2px dashed #0073aa;
                border-radius: 3px;
                background: #f0f6fc;
                visibility: visible !important;
            }
            </style>
            <?php
        }
        ?>
        <?php if ( $this->label ) : ?>
            <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
        <?php endif; ?>
        <?php if ( $this->description ) : ?>
            <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
        <?php endif; ?>

        <?php
        // ── Pinned cards (always on top, not sortable) ──
        foreach ( $this->cards as $key => $card ) :
            if ( empty( $card['pinned'] ) ) continue;
        ?>
        <div class="fc-sortable-card fc-card-pinned" data-key="<?php echo esc_attr( $key ); ?>">
            <div class="fc-sortable-card-header">
                <?php if ( ! empty( $card['icon'] ) ) : ?>
                    <span class="fc-sortable-icon"><?php echo $card['icon']; ?></span>
                <?php endif; ?>
                <span class="fc-sortable-label"><?php echo esc_html( $card['label'] ); ?></span>
                <span class="fc-sortable-arrow">&#9654;</span>
            </div>
            <div class="fc-sortable-card-body">
                <?php
                if ( ! empty( $card['fields'] ) && is_callable( $card['fields'] ) ) {
                    call_user_func( $card['fields'] );
                }
                ?>
            </div>
        </div>
        <?php endforeach; ?>

        <ul class="fc-sortable-cards" data-setting="<?php echo esc_attr( $this->id ); ?>">
            <?php foreach ( $order_keys as $key ) :
                if ( ! isset( $this->cards[ $key ] ) ) continue;
                $card = $this->cards[ $key ];
                if ( ! empty( $card['pinned'] ) ) continue;
            ?>
            <li class="fc-sortable-card" data-key="<?php echo esc_attr( $key ); ?>">
                <div class="fc-sortable-card-header">
                    <span class="fc-sortable-handle">&#9776;</span>
                    <?php if ( ! empty( $card['icon'] ) ) : ?>
                        <span class="fc-sortable-icon"><?php echo $card['icon']; ?></span>
                    <?php endif; ?>
                    <span class="fc-sortable-label"><?php echo esc_html( $card['label'] ); ?></span>
                    <span class="fc-sortable-arrow">&#9654;</span>
                </div>
                <div class="fc-sortable-card-body">
                    <?php
                    if ( ! empty( $card['fields'] ) && is_callable( $card['fields'] ) ) {
                        call_user_func( $card['fields'] );
                    }
                    ?>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
        <input type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr( $order ); ?>" />
        <script>
        (function($){
            var $cards = $('.fc-sortable-cards');
            $cards.on('click', '.fc-sortable-card-header', function(e) {
                if ($(e.target).closest('.fc-sortable-handle').length) return;
                $(this).closest('.fc-sortable-card').toggleClass('fc-card-open');
            });
            // Pinned cards — toggle open/close
            $('.fc-card-pinned').on('click', '.fc-sortable-card-header', function() {
                $(this).closest('.fc-card-pinned').toggleClass('fc-card-open');
            });
        })(jQuery);
        </script>
        <?php
    }
}

/**
 * Kontrolka Customizer: godziny otwarcia (repeater z dniami + od/do)
 *
 * Wartość: JSON — [{"day_from":"1","day_to":"5","from":"08:00","to":"16:00"}, ...]
 * Dni: 1=Pon, 2=Wt, 3=Śr, 4=Czw, 5=Pt, 6=Sob, 7=Ndz
 */
class Flavor_Hours_Control extends WP_Customize_Control {
    public $type = 'fc_hours';

    private static function get_day_options() {
        return array(
            '1' => fc__( 'day_mon', 'admin' ),
            '2' => fc__( 'day_tue', 'admin' ),
            '3' => fc__( 'day_wed', 'admin' ),
            '4' => fc__( 'day_thu', 'admin' ),
            '5' => fc__( 'day_fri', 'admin' ),
            '6' => fc__( 'day_sat', 'admin' ),
            '7' => fc__( 'day_sun', 'admin' ),
        );
    }

    private static function render_day_select( $class, $selected ) {
        $days = self::get_day_options();
        echo '<select class="' . esc_attr( $class ) . '">';
        foreach ( $days as $val => $label ) {
            echo '<option value="' . esc_attr( $val ) . '"' . selected( $selected, $val, false ) . '>' . esc_html( $label ) . '</option>';
        }
        echo '</select>';
    }

    public function render_content() {
        $value = $this->value();
        $rows  = json_decode( $value, true );
        if ( ! is_array( $rows ) ) {
            $rows = array();
        }

        $days_opts = self::get_day_options();

        // CSS
        static $hours_css = false;
        if ( ! $hours_css ) {
            $hours_css = true;
            ?>
            <style>
            .fc-hours-rows { margin: 6px 0 0; }
            .fc-hours-row {
                display: grid;
                grid-template-columns: 1fr auto 1fr auto;
                gap: 4px;
                align-items: center;
                margin: 0 0 6px;
                padding: 8px;
                background: #fff;
                border: 1px solid #ddd;
                border-radius: 4px;
            }
            .fc-hours-row-top {
                grid-column: 1 / -1;
                display: flex;
                align-items: center;
                gap: 4px;
                margin-bottom: 4px;
            }
            .fc-hours-row-top select {
                flex: 1;
                padding: 3px 4px;
                font-size: 12px;
                border: 1px solid #ccc;
                border-radius: 3px;
            }
            .fc-hours-row-bottom {
                grid-column: 1 / -1;
                display: flex;
                align-items: center;
                gap: 4px;
            }
            .fc-hours-row-bottom input[type="time"] {
                flex: 1;
                padding: 3px 4px;
                font-size: 12px;
                border: 1px solid #ccc;
                border-radius: 3px;
            }
            .fc-hours-sep {
                font-size: 12px;
                color: #888;
                flex-shrink: 0;
            }
            .fc-hours-remove {
                background: none;
                border: none;
                color: #a00;
                cursor: pointer;
                font-size: 18px;
                line-height: 1;
                padding: 0 4px;
                flex-shrink: 0;
                margin-left: auto;
            }
            .fc-hours-remove:hover { color: #dc3232; }
            .fc-hours-add {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 6px 12px;
                font-size: 12px;
                background: #f0f0f1;
                border: 1px solid #c3c4c7;
                border-radius: 3px;
                cursor: pointer;
                color: #2271b1;
            }
            .fc-hours-add:hover {
                background: #e5e5e6;
                border-color: #999;
            }
            </style>
            <?php
        }

        $ctrl_id = esc_attr( $this->id );

        // Build JS days map
        $js_days = array();
        foreach ( $days_opts as $v => $l ) {
            $js_days[] = '{v:"' . esc_js( $v ) . '",l:"' . esc_js( $l ) . '"}';
        }
        $js_days_str = '[' . implode( ',', $js_days ) . ']';
        ?>
        <?php if ( $this->label ) : ?>
            <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
        <?php endif; ?>
        <?php if ( $this->description ) : ?>
            <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
        <?php endif; ?>

        <div class="fc-hours-wrap" id="fc-hours-<?php echo $ctrl_id; ?>">
            <div class="fc-hours-rows">
                <?php foreach ( $rows as $row ) : ?>
                <div class="fc-hours-row">
                    <div class="fc-hours-row-top">
                        <?php self::render_day_select( 'fc-hours-day-from', $row['day_from'] ?? '1' ); ?>
                        <span class="fc-hours-sep">&ndash;</span>
                        <?php self::render_day_select( 'fc-hours-day-to', $row['day_to'] ?? '5' ); ?>
                        <button type="button" class="fc-hours-remove" title="<?php echo esc_attr( fc__( 'cust_hours_remove', 'admin' ) ); ?>">&times;</button>
                    </div>
                    <div class="fc-hours-row-bottom">
                        <input type="time" class="fc-hours-from" value="<?php echo esc_attr( $row['from'] ?? '' ); ?>">
                        <span class="fc-hours-sep">&ndash;</span>
                        <input type="time" class="fc-hours-to" value="<?php echo esc_attr( $row['to'] ?? '' ); ?>">
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="fc-hours-add">+ <?php fc_e( 'cust_hours_add', 'admin' ); ?></button>
        </div>

        <input type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr( $value ); ?>" />

        <script>
        (function(){
            var wrap = document.getElementById('fc-hours-<?php echo $ctrl_id; ?>');
            if (!wrap) return;
            var container = wrap.querySelector('.fc-hours-rows');
            var hidden    = wrap.parentElement.querySelector('input[type="hidden"][data-customize-setting-link]')
                         || wrap.nextElementSibling;
            var DAYS = <?php echo $js_days_str; ?>;

            function serialize() {
                var rows = [];
                container.querySelectorAll('.fc-hours-row').forEach(function(row) {
                    var dayFrom = row.querySelector('.fc-hours-day-from').value;
                    var dayTo   = row.querySelector('.fc-hours-day-to').value;
                    var from    = row.querySelector('.fc-hours-from').value;
                    var to      = row.querySelector('.fc-hours-to').value;
                    rows.push({ day_from: dayFrom, day_to: dayTo, from: from, to: to });
                });
                var json = JSON.stringify(rows);
                hidden.value = json;
                hidden.dispatchEvent(new Event('change', { bubbles: true }));
            }

            function buildSelect(cls, selected) {
                var html = '<select class="' + cls + '">';
                DAYS.forEach(function(d) {
                    html += '<option value="' + d.v + '"' + (d.v === selected ? ' selected' : '') + '>' + d.l + '</option>';
                });
                html += '</select>';
                return html;
            }

            function createRow(dayFrom, dayTo, from, to) {
                var row = document.createElement('div');
                row.className = 'fc-hours-row';
                row.innerHTML =
                    '<div class="fc-hours-row-top">' +
                        buildSelect('fc-hours-day-from', dayFrom || '1') +
                        '<span class="fc-hours-sep">&ndash;</span>' +
                        buildSelect('fc-hours-day-to', dayTo || '5') +
                        '<button type="button" class="fc-hours-remove" title="<?php echo esc_js( fc__( 'cust_hours_remove', 'admin' ) ); ?>">&times;</button>' +
                    '</div>' +
                    '<div class="fc-hours-row-bottom">' +
                        '<input type="time" class="fc-hours-from" value="' + (from||'') + '">' +
                        '<span class="fc-hours-sep">&ndash;</span>' +
                        '<input type="time" class="fc-hours-to" value="' + (to||'') + '">' +
                    '</div>';

                bindRow(row);
                return row;
            }

            function bindRow(row) {
                row.querySelectorAll('select, input').forEach(function(el) {
                    el.addEventListener('change', serialize);
                    el.addEventListener('input', serialize);
                });
                row.querySelector('.fc-hours-remove').addEventListener('click', function() {
                    row.remove();
                    serialize();
                });
            }

            // Bind existing rows
            container.querySelectorAll('.fc-hours-row').forEach(bindRow);

            // Add button
            wrap.querySelector('.fc-hours-add').addEventListener('click', function() {
                container.appendChild(createRow('1', '5', '', ''));
                serialize();
            });
        })();
        </script>
        <?php
    }

    /**
     * Render the hours repeater inline (no WP_Customize_Control instance needed).
     *
     * Used to embed the repeater inside Flavor_Sortable_Cards_Control cards.
     *
     * @param string $setting_id  The Customizer setting ID (e.g. 'flavor_contact_hours').
     */
    public static function render_inline( $setting_id ) {
        $value = get_theme_mod( $setting_id, '' );
        $rows  = json_decode( $value, true );
        if ( ! is_array( $rows ) ) {
            $rows = array();
        }

        $days_opts = self::get_day_options();

        // CSS — reuse the same styles as render_content()
        static $hours_css_inline = false;
        if ( ! $hours_css_inline ) {
            $hours_css_inline = true;
            ?>
            <style>
            .fc-hours-rows { margin: 6px 0 0; }
            .fc-hours-row {
                display: grid;
                grid-template-columns: 1fr auto 1fr auto;
                gap: 4px;
                align-items: center;
                margin: 0 0 6px;
                padding: 8px;
                background: #fff;
                border: 1px solid #ddd;
                border-radius: 4px;
            }
            .fc-hours-row-top {
                grid-column: 1 / -1;
                display: flex;
                align-items: center;
                gap: 4px;
                margin-bottom: 4px;
            }
            .fc-hours-row-top select {
                flex: 1;
                padding: 3px 4px;
                font-size: 12px;
                border: 1px solid #ccc;
                border-radius: 3px;
            }
            .fc-hours-row-bottom {
                grid-column: 1 / -1;
                display: flex;
                align-items: center;
                gap: 4px;
            }
            .fc-hours-row-bottom input[type="time"] {
                flex: 1;
                padding: 3px 4px;
                font-size: 12px;
                border: 1px solid #ccc;
                border-radius: 3px;
            }
            .fc-hours-sep {
                font-size: 12px;
                color: #888;
                flex-shrink: 0;
            }
            .fc-hours-remove {
                background: none;
                border: none;
                color: #a00;
                cursor: pointer;
                font-size: 18px;
                line-height: 1;
                padding: 0 4px;
                flex-shrink: 0;
                margin-left: auto;
            }
            .fc-hours-remove:hover { color: #dc3232; }
            .fc-hours-add {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 6px 12px;
                font-size: 12px;
                background: #f0f0f1;
                border: 1px solid #c3c4c7;
                border-radius: 3px;
                cursor: pointer;
                color: #2271b1;
            }
            .fc-hours-add:hover {
                background: #e5e5e6;
                border-color: #999;
            }
            </style>
            <?php
        }

        $ctrl_id = esc_attr( $setting_id );

        // Build JS days map
        $js_days = array();
        foreach ( $days_opts as $v => $l ) {
            $js_days[] = '{v:"' . esc_js( $v ) . '",l:"' . esc_js( $l ) . '"}';
        }
        $js_days_str = '[' . implode( ',', $js_days ) . ']';
        ?>
        <div class="fc-hours-wrap" id="fc-hours-<?php echo $ctrl_id; ?>">
            <div class="fc-hours-rows">
                <?php foreach ( $rows as $row ) : ?>
                <div class="fc-hours-row">
                    <div class="fc-hours-row-top">
                        <?php self::render_day_select( 'fc-hours-day-from', $row['day_from'] ?? '1' ); ?>
                        <span class="fc-hours-sep">&ndash;</span>
                        <?php self::render_day_select( 'fc-hours-day-to', $row['day_to'] ?? '5' ); ?>
                        <button type="button" class="fc-hours-remove" title="<?php echo esc_attr( fc__( 'cust_hours_remove', 'admin' ) ); ?>">&times;</button>
                    </div>
                    <div class="fc-hours-row-bottom">
                        <input type="time" class="fc-hours-from" value="<?php echo esc_attr( $row['from'] ?? '' ); ?>">
                        <span class="fc-hours-sep">&ndash;</span>
                        <input type="time" class="fc-hours-to" value="<?php echo esc_attr( $row['to'] ?? '' ); ?>">
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="fc-hours-add">+ <?php fc_e( 'cust_hours_add', 'admin' ); ?></button>
            <input type="hidden" data-customize-setting-link="<?php echo esc_attr( $setting_id ); ?>" value="<?php echo esc_attr( $value ); ?>" />
        </div>

        <script>
        (function(){
            var wrap = document.getElementById('fc-hours-<?php echo $ctrl_id; ?>');
            if (!wrap) return;
            var container = wrap.querySelector('.fc-hours-rows');
            var hidden    = wrap.querySelector('input[type="hidden"][data-customize-setting-link]');
            var DAYS = <?php echo $js_days_str; ?>;

            function serialize() {
                var rows = [];
                container.querySelectorAll('.fc-hours-row').forEach(function(row) {
                    var dayFrom = row.querySelector('.fc-hours-day-from').value;
                    var dayTo   = row.querySelector('.fc-hours-day-to').value;
                    var from    = row.querySelector('.fc-hours-from').value;
                    var to      = row.querySelector('.fc-hours-to').value;
                    rows.push({ day_from: dayFrom, day_to: dayTo, from: from, to: to });
                });
                var json = JSON.stringify(rows);
                hidden.value = json;
                hidden.dispatchEvent(new Event('change', { bubbles: true }));
            }

            function buildSelect(cls, selected) {
                var html = '<select class="' + cls + '">';
                DAYS.forEach(function(d) {
                    html += '<option value="' + d.v + '"' + (d.v === selected ? ' selected' : '') + '>' + d.l + '</option>';
                });
                html += '</select>';
                return html;
            }

            function createRow(dayFrom, dayTo, from, to) {
                var row = document.createElement('div');
                row.className = 'fc-hours-row';
                row.innerHTML =
                    '<div class="fc-hours-row-top">' +
                        buildSelect('fc-hours-day-from', dayFrom || '1') +
                        '<span class="fc-hours-sep">&ndash;</span>' +
                        buildSelect('fc-hours-day-to', dayTo || '5') +
                        '<button type="button" class="fc-hours-remove" title="<?php echo esc_js( fc__( 'cust_hours_remove', 'admin' ) ); ?>">&times;</button>' +
                    '</div>' +
                    '<div class="fc-hours-row-bottom">' +
                        '<input type="time" class="fc-hours-from" value="' + (from||'') + '">' +
                        '<span class="fc-hours-sep">&ndash;</span>' +
                        '<input type="time" class="fc-hours-to" value="' + (to||'') + '">' +
                    '</div>';

                bindRow(row);
                return row;
            }

            function bindRow(row) {
                row.querySelectorAll('select, input').forEach(function(el) {
                    el.addEventListener('change', serialize);
                    el.addEventListener('input', serialize);
                });
                row.querySelector('.fc-hours-remove').addEventListener('click', function() {
                    row.remove();
                    serialize();
                });
            }

            // Bind existing rows
            container.querySelectorAll('.fc-hours-row').forEach(bindRow);

            // Add button
            wrap.querySelector('.fc-hours-add').addEventListener('click', function() {
                container.appendChild(createRow('1', '5', '', ''));
                serialize();
            });
        })();
        </script>
        <?php
    }
}

/**
 * Menu control — shows WP pages as checkboxes + custom links with drag & drop ordering.
 */
class Flavor_Menu_Control extends WP_Customize_Control {
    public $type = 'fc_menu';

    public function render_content() {
        $val   = $this->value();
        if ( is_string( $val ) ) {
            $val = json_decode( $val, true );
        }
        $items = is_array( $val ) ? $val : array();
        $pages = get_pages( array( 'sort_column' => 'menu_order,post_title', 'post_status' => 'publish' ) );
        // Exclude FC plugin pages (they have dedicated special types or don't belong in nav)
        $fc_page_ids = array();
        foreach ( array( 'fc_page_sklep', 'fc_page_koszyk', 'fc_page_moje-konto', 'fc_page_zamowienie', 'fc_page_podziekowanie', 'fc_page_porownanie', 'fc_page_wishlist', 'fc_page_platnosc_nieudana', 'fc_page_o-nas', 'fc_page_kontakt' ) as $opt ) {
            $pid = get_option( $opt );
            if ( $pid ) $fc_page_ids[] = (int) $pid;
        }
        if ( $fc_page_ids ) {
            $pages = array_filter( $pages, function( $p ) use ( $fc_page_ids ) {
                return ! in_array( $p->ID, $fc_page_ids, true );
            });
        }
        ?>
        <?php if ( $this->label ) : ?>
            <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
        <?php endif; ?>
        <?php if ( $this->description ) : ?>
            <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
        <?php endif; ?>

        <div class="fc-menu-control"
             data-pages="<?php echo esc_attr( wp_json_encode( array_map( function( $p ) {
                 return array( 'id' => $p->ID, 'title' => $p->post_title, 'url' => get_permalink( $p->ID ) );
             }, $pages ) ) ); ?>"
             data-value="<?php echo esc_attr( wp_json_encode( $items ) ); ?>">

            <div class="fc-menu-items" style="margin:8px 0;"></div>

            <details style="margin:10px 0 0;border:1px solid #ddd;border-radius:4px;padding:0;">
                <summary style="padding:8px 10px;cursor:pointer;font-size:12px;font-weight:600;color:#555;background:#fafafa;user-select:none;">
                    <?php echo esc_html( fc__( 'cust_add_page', 'admin' ) ); ?>
                </summary>
                <div class="fc-menu-pages-list" style="padding:6px 10px;max-height:200px;overflow-y:auto;"></div>
            </details>

            <details style="margin:6px 0 0;border:1px solid #ddd;border-radius:4px;padding:0;">
                <summary style="padding:8px 10px;cursor:pointer;font-size:12px;font-weight:600;color:#555;background:#fafafa;user-select:none;">
                    <?php echo esc_html( fc__( 'cust_add_custom_link', 'admin' ) ); ?>
                </summary>
                <div style="padding:8px 10px;">
                    <p style="margin:0 0 6px;">
                        <input type="text" class="fc-menu-custom-title" placeholder="<?php echo esc_attr( fc__( 'cust_title_placeholder', 'admin' ) ); ?>" style="width:100%;margin-bottom:4px;">
                        <input type="url" class="fc-menu-custom-url" placeholder="https://" style="width:100%;">
                    </p>
                    <button type="button" class="button fc-menu-add-custom"><?php echo esc_html( fc__( 'cust_add', 'admin' ) ); ?></button>
                </div>
            </details>

            <?php if ( defined( 'FC_VERSION' ) ) : ?>
            <details style="margin:6px 0 0;border:1px solid #ddd;border-radius:4px;padding:0;">
                <summary style="padding:8px 10px;cursor:pointer;font-size:12px;font-weight:600;color:#555;background:#fafafa;user-select:none;">
                    <?php echo esc_html( fc__( 'cust_add_store_element', 'admin' ) ); ?>
                </summary>
                <div style="padding:8px 10px;">
                    <button type="button" class="button fc-menu-add-special" data-special="fc_shop" style="width:100%;margin-bottom:4px;text-align:left;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:4px;"><path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/><path d="M2 7h20"/><path d="M22 7v3a2 2 0 0 1-2 2a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12a2 2 0 0 1-2-2V7"/></svg>
                        <?php echo esc_html( fc__( 'cust_shop', 'admin' ) ); ?>
                    </button>
                    <button type="button" class="button fc-menu-add-special" data-special="fc_account" style="width:100%;margin-bottom:4px;text-align:left;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:4px;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <?php echo esc_html( fc__( 'cust_my_account', 'admin' ) ); ?>
                    </button>
                    <button type="button" class="button fc-menu-add-special" data-special="fc_cart" style="width:100%;margin-bottom:4px;text-align:left;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:4px;"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                        <?php echo esc_html( fc__( 'cust_cart', 'admin' ) ); ?>
                    </button>
                    <button type="button" class="button fc-menu-add-special" data-special="fc_wishlist" style="width:100%;margin-bottom:4px;text-align:left;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:4px;"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                        <?php echo esc_html( fc__( 'cust_wishlist', 'admin' ) ); ?>
                    </button>
                    <button type="button" class="button fc-menu-add-special" data-special="fc_compare" style="width:100%;margin-bottom:4px;text-align:left;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:4px;"><path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="M7 21h10"/><path d="M12 3v18"/><path d="M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"/></svg>
                        <?php echo esc_html( fc__( 'cust_compare', 'admin' ) ); ?>
                    </button>
                    <button type="button" class="button fc-menu-add-special" data-special="fc_about" style="width:100%;margin-bottom:4px;text-align:left;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:4px;"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        <?php echo esc_html( fc__( 'cust_about', 'admin' ) ); ?>
                    </button>
                    <button type="button" class="button fc-menu-add-special" data-special="fc_contact" style="width:100%;text-align:left;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:4px;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <?php echo esc_html( fc__( 'cust_contact', 'admin' ) ); ?>
                    </button>
                </div>
            </details>
            <?php endif; ?>
        </div>
        <?php
    }
}

class Flavor_Sidebar_Widgets_Control extends WP_Customize_Control {

    public $type = 'fc_sidebar_widgets';

    /**
     * Przekaż dane do JS przez data-atrybuty i wp_localize_script
     */
    public function to_json() {
        parent::to_json();
        $this->json['available_blocks'] = flavor_get_available_blocks();
        $val = $this->value();
        $this->json['value'] = is_array( $val ) ? $val : array();
    }

    /**
     * Renderuj HTML bezpośrednio w PHP (bez szablonu Underscore)
     */
    public function render_content() {
        $blocks = flavor_get_available_blocks();
        $val    = $this->value();
        $items  = is_array( $val ) ? $val : array();
        ?>
        <?php if ( $this->label ) : ?>
            <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
        <?php endif; ?>
        <?php if ( $this->description ) : ?>
            <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
        <?php endif; ?>

        <div class="fc-cust-widgets-wrap"
             data-blocks="<?php echo esc_attr( wp_json_encode( $blocks ) ); ?>"
             data-value="<?php echo esc_attr( wp_json_encode( $items ) ); ?>">

            <!-- Dropdown dodawania -->
            <div class="fc-cust-add-row">
                <select class="fc-cust-add-select">
                    <option value=""><?php echo esc_html( fc__( 'cust_add_widget_placeholder', 'admin' ) ); ?></option>
                    <?php foreach ( $blocks as $type => $info ) : ?>
                        <option value="<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $info['label'] ); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="button" class="button fc-cust-add-btn" title="<?php echo esc_attr( fc__( 'cust_add', 'admin' ) ); ?>">
                    <span class="dashicons dashicons-plus-alt2"></span>
                </button>
            </div>

            <!-- Lista aktywnych widgetów -->
            <div class="fc-cust-widgets-list"></div>

            <p class="fc-cust-empty-msg" style="display:none;">
                <em><?php echo esc_html( fc__( 'cust_no_widgets', 'admin' ) ); ?></em>
            </p>
        </div>
        <?php
    }
}

/**
 * Side Panel Icons — sortable list of icons with on/off toggle.
 * Stores JSON: [{"key":"account","enabled":true},{"key":"cart","enabled":true}, …]
 */
class Flavor_Side_Panel_Icons_Control extends WP_Customize_Control {
    public $type = 'fc_side_panel_icons';

    public function render_content() {
        $val   = $this->value();
        if ( is_string( $val ) ) {
            $val = json_decode( $val, true );
        }
        $items = is_array( $val ) ? $val : array();

        $icons_meta = array(
            'account'  => array(
                'label' => fc__( 'cust_side_panel_account', 'admin' ),
                'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-3px;margin-right:4px;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
                'has_open_on_add' => false,
            ),
            'cart'     => array(
                'label' => fc__( 'cust_side_panel_cart', 'admin' ),
                'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-3px;margin-right:4px;"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>',
                'has_open_on_add' => true,
            ),
            'wishlist' => array(
                'label' => fc__( 'cust_side_panel_wishlist', 'admin' ),
                'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-3px;margin-right:4px;"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>',
                'has_open_on_add' => true,
            ),
            'compare'  => array(
                'label' => fc__( 'cust_side_panel_compare', 'admin' ),
                'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-3px;margin-right:4px;"><path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="M7 21h10"/><path d="M12 3v18"/><path d="M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"/></svg>',
                'has_open_on_add' => true,
            ),
        );
        ?>
        <?php if ( $this->label ) : ?>
            <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
        <?php endif; ?>
        <?php if ( $this->description ) : ?>
            <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
        <?php endif; ?>

        <div class="fc-side-panel-icons-control"
             data-icons-meta="<?php echo esc_attr( wp_json_encode( $icons_meta ) ); ?>"
             data-value="<?php echo esc_attr( wp_json_encode( $items ) ); ?>"
             data-open-on-add-label="<?php echo esc_attr( fc__( 'cust_side_panel_open_on_add', 'admin' ) ); ?>">
            <div class="fc-sp-icons-list" style="margin:8px 0;"></div>
        </div>
        <?php
    }
}

endif;

/* =====================================================================
 *  Rejestracja sekcji, ustawień i kontrolerów w Customizerze
 * ===================================================================== */

function flavor_appearance_customize_register( $wp_customize ) {
    if ( ! defined( 'FC_VERSION' ) ) return;

    // ── Sekcja: Archiwum produktów ──
    $wp_customize->add_section( 'flavor_archive', array(
        'title'       => fc__( 'cust_archive', 'admin' ),
        'panel'       => 'flavor_panel',
        'priority'    => 50,
        'description' => fc__( 'cust_archive_desc', 'admin' ),
    ) );

    // ── Setting: Ilość produktów w linii ──
    $wp_customize->add_setting( 'flavor_archive_columns', array(
        'default'           => 3,
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'flavor_archive_columns', array(
        'label'       => fc__( 'cust_products_per_row', 'admin' ),
        'section'     => 'flavor_archive',
        'type'        => 'number',
        'input_attrs' => array( 'min' => 2, 'max' => 6, 'step' => 1 ),
    ) );

    // ── Setting: Min. szerokość kafelka ──
    $wp_customize->add_setting( 'flavor_archive_card_min_width', array(
        'default'           => 200,
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'flavor_archive_card_min_width', array(
        'label'       => fc__( 'cust_card_min_width', 'admin' ),
        'section'     => 'flavor_archive',
        'type'        => 'number',
        'input_attrs' => array( 'min' => 120, 'max' => 400, 'step' => 10 ),
    ) );

    // ── Setting: Przełącznik widoku siatka/lista ──
    $wp_customize->add_setting( 'flavor_archive_view_toggle', array(
        'default'           => true,
        'sanitize_callback' => function ( $val ) { return (bool) $val; },
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'flavor_archive_view_toggle', array(
        'label'   => fc__( 'cust_archive_view_toggle', 'admin' ),
        'description' => fc__( 'cust_archive_view_toggle_desc', 'admin' ),
        'section' => 'flavor_archive',
        'type'    => 'checkbox',
    ) );

    // ── Setting: Szybki podgląd (Quick View) ──
    $wp_customize->add_setting( 'flavor_archive_quick_view', array(
        'default'           => true,
        'sanitize_callback' => function ( $val ) { return (bool) $val; },
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'flavor_archive_quick_view', array(
        'label'       => fc__( 'cust_archive_quick_view', 'admin' ),
        'description' => fc__( 'cust_archive_quick_view_desc', 'admin' ),
        'section'     => 'flavor_archive',
        'type'        => 'checkbox',
    ) );

    // ── Setting: Lista życzeń ──
    $wp_customize->add_setting( 'flavor_archive_wishlist', array(
        'default'           => true,
        'sanitize_callback' => function ( $val ) { return (bool) $val; },
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'flavor_archive_wishlist', array(
        'label'       => fc__( 'cust_archive_wishlist', 'admin' ),
        'description' => fc__( 'cust_archive_wishlist_desc', 'admin' ),
        'section'     => 'flavor_archive',
        'type'        => 'checkbox',
    ) );

    // ── Setting: Porównywarka produktów ──
    $wp_customize->add_setting( 'flavor_archive_compare', array(
        'default'           => true,
        'sanitize_callback' => function ( $val ) { return (bool) $val; },
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'flavor_archive_compare', array(
        'label'       => fc__( 'cust_archive_compare', 'admin' ),
        'description' => fc__( 'cust_archive_compare_desc', 'admin' ),
        'section'     => 'flavor_archive',
        'type'        => 'checkbox',
    ) );

    // ── Heading: Ustawienia pojedynczego produktu ──
    $wp_customize->add_setting( 'flavor_single_product_heading', array(
        'sanitize_callback' => '__return_empty_string',
    ) );
    $wp_customize->add_control( new Flavor_Heading_Control( $wp_customize, 'flavor_single_product_heading', array(
        'label'   => fc__( 'cust_single_product_heading', 'admin' ),
        'section' => 'flavor_archive',
    ) ) );

    // ── Setting: Proporcje zdjęcia produktu ──
    $wp_customize->add_setting( 'flavor_shop_img_ratio', array(
        'default'           => '4/3',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'flavor_shop_img_ratio', array(
        'label'   => fc__( 'cust_img_ratio', 'admin' ),
        'section' => 'flavor_archive',
        'type'    => 'select',
        'choices' => array(
            '1/1'  => fc__( 'cust_img_ratio_square', 'admin' ),
            '4/3'  => '4:3',
            '3/4'  => '3:4',
            '16/9' => '16:9',
            '3/2'  => '3:2',
        ),
    ) );

    // ── Setting: Dopasowanie zdjęcia ──
    $wp_customize->add_setting( 'flavor_shop_img_fit', array(
        'default'           => 'cover',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'flavor_shop_img_fit', array(
        'label'   => fc__( 'cust_img_fit', 'admin' ),
        'section' => 'flavor_archive',
        'type'    => 'select',
        'choices' => array(
            'cover'   => fc__( 'cust_img_fit_cover', 'admin' ),
            'contain' => fc__( 'cust_img_fit_contain', 'admin' ),
        ),
    ) );

    // ── Heading: Ustawienia sidebar ──
    $wp_customize->add_setting( 'flavor_sidebar_heading', array(
        'sanitize_callback' => '__return_empty_string',
    ) );
    $wp_customize->add_control( new Flavor_Heading_Control( $wp_customize, 'flavor_sidebar_heading', array(
        'label'   => fc__( 'cust_sidebar_heading', 'admin' ),
        'section' => 'flavor_archive',
    ) ) );

    // ── Setting: Sidebar position ──
    $wp_customize->add_setting( 'fc_shop_sidebar', array(
        'type'              => 'option',
        'default'           => 'none',
        'sanitize_callback' => function ( $val ) {
            return in_array( $val, array( 'none', 'left', 'right' ), true ) ? $val : 'none';
        },
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'fc_shop_sidebar', array(
        'label'   => fc__( 'cust_sidebar_desktop', 'admin' ),
        'section' => 'flavor_archive',
        'type'    => 'select',
        'choices' => array(
            'none'  => fc__( 'cust_sidebar_none', 'admin' ),
            'left'  => fc__( 'cust_sidebar_left', 'admin' ),
            'right' => fc__( 'cust_sidebar_right', 'admin' ),
        ),
    ) );

    // ── Setting: Tablet sidebar style ──
    $wp_customize->add_setting( 'fc_tablet_sidebar', array(
        'type'              => 'option',
        'default'           => 'offcanvas',
        'sanitize_callback' => function ( $val ) {
            return in_array( $val, array( 'none', 'offcanvas', 'bottom_sheet' ), true ) ? $val : 'offcanvas';
        },
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'fc_tablet_sidebar', array(
        'label'   => fc__( 'cust_sidebar_tablet', 'admin' ),
        'section' => 'flavor_archive',
        'type'    => 'select',
        'choices' => array(
            'none'         => fc__( 'cust_sidebar_none', 'admin' ),
            'offcanvas'    => fc__( 'cust_sidebar_offcanvas', 'admin' ),
            'bottom_sheet' => fc__( 'cust_sidebar_bottom_sheet', 'admin' ),
        ),
    ) );

    // ── Setting: Phone sidebar style ──
    $wp_customize->add_setting( 'fc_phone_sidebar', array(
        'type'              => 'option',
        'default'           => 'bottom_sheet',
        'sanitize_callback' => function ( $val ) {
            return in_array( $val, array( 'none', 'offcanvas', 'bottom_sheet' ), true ) ? $val : 'bottom_sheet';
        },
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'fc_phone_sidebar', array(
        'label'   => fc__( 'cust_sidebar_phone', 'admin' ),
        'section' => 'flavor_archive',
        'type'    => 'select',
        'choices' => array(
            'none'         => fc__( 'cust_sidebar_none', 'admin' ),
            'offcanvas'    => fc__( 'cust_sidebar_offcanvas', 'admin' ),
            'bottom_sheet' => fc__( 'cust_sidebar_bottom_sheet', 'admin' ),
        ),
    ) );

    // ── Setting: Sidebar blocks (JSON ↔ array) ──
    $wp_customize->add_setting( 'fc_sidebar_blocks', array(
        'type'              => 'option',
        'default'           => array(),
        'sanitize_callback' => 'flavor_sanitize_sidebar_blocks',
        'transport'         => 'refresh',
    ) );

    $wp_customize->add_control( new Flavor_Sidebar_Widgets_Control( $wp_customize, 'fc_sidebar_blocks', array(
        'label'       => fc__( 'cust_sidebar_widgets', 'admin' ),
        'description' => fc__( 'cust_sidebar_widgets_desc', 'admin' ),
        'section'     => 'flavor_archive',
    ) ) );

    // =================================================================
    //  Menu nagłówka (w sekcji Nagłówek)
    // =================================================================
    $wp_customize->add_setting( 'flavor_menu_items', array(
        'default'           => array(),
        'sanitize_callback' => 'flavor_sanitize_menu_items',
        'transport'         => 'refresh',
    ) );

    $wp_customize->add_control( new Flavor_Menu_Control( $wp_customize, 'flavor_menu_items', array(
        'label'       => fc__( 'cust_menu_items', 'admin' ),
        'description' => fc__( 'cust_menu_items_desc', 'admin' ),
        'section'     => 'flavor_header',
    ) ) );

    // =================================================================
    //  Sekcja: Zamówienia (w panelu Ustawienia sklepu)
    // =================================================================
    $wp_customize->add_section( 'flavor_orders', array(
        'title'       => fc__( 'cust_orders', 'admin' ),
        'panel'       => 'flavor_panel',
        'priority'    => 55,
    ) );

    $wp_customize->add_setting( 'flavor_checkout_layout', array(
        'default'           => 'steps',
        'sanitize_callback' => function( $val ) {
            return in_array( $val, array( 'steps', 'onepage', 'twocol' ), true ) ? $val : 'steps';
        },
    ) );
    $wp_customize->add_control( 'flavor_checkout_layout', array(
        'label'       => fc__( 'cust_checkout_layout', 'admin' ),
        'description' => fc__( 'cust_checkout_layout_desc', 'admin' ),
        'section'     => 'flavor_orders',
        'type'        => 'radio',
        'choices'     => array(
            'steps'   => fc__( 'cust_checkout_layout_steps', 'admin' ),
            'onepage' => fc__( 'cust_checkout_layout_onepage', 'admin' ),
            'twocol'  => fc__( 'cust_checkout_layout_twocol', 'admin' ),
        ),
    ) );

    $wp_customize->add_setting( 'flavor_checkout_hide_header', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ) );
    $wp_customize->add_control( 'flavor_checkout_hide_header', array(
        'label'   => fc__( 'cust_checkout_hide_header', 'admin' ),
        'description' => fc__( 'cust_checkout_hide_header_desc', 'admin' ),
        'section' => 'flavor_orders',
        'type'    => 'checkbox',
    ) );

    // =================================================================
    //  Sekcja: Panel boczny (w panelu Ustawienia sklepu)
    // =================================================================
    $wp_customize->add_section( 'flavor_side_panel', array(
        'title'       => fc__( 'cust_side_panel', 'admin' ),
        'panel'       => 'flavor_panel',
        'priority'    => 60,
        'description' => fc__( 'cust_side_panel_desc', 'admin' ),
    ) );

    $wp_customize->add_setting( 'flavor_side_panel_enabled', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'flavor_side_panel_enabled', array(
        'label'   => fc__( 'cust_side_panel_enabled', 'admin' ),
        'section' => 'flavor_side_panel',
        'type'    => 'checkbox',
    ) );

    $default_icons = array(
        array( 'key' => 'account',  'enabled' => true ),
        array( 'key' => 'cart',     'enabled' => true, 'open_on_add' => true ),
        array( 'key' => 'wishlist', 'enabled' => true, 'open_on_add' => false ),
        array( 'key' => 'compare',  'enabled' => true, 'open_on_add' => false ),
    );
    $wp_customize->add_setting( 'flavor_side_panel_icons', array(
        'default'           => wp_json_encode( $default_icons ),
        'sanitize_callback' => 'flavor_sanitize_side_panel_icons',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( new Flavor_Side_Panel_Icons_Control( $wp_customize, 'flavor_side_panel_icons', array(
        'label'       => fc__( 'cust_side_panel_icons', 'admin' ),
        'description' => fc__( 'cust_side_panel_icons_desc', 'admin' ),
        'section'     => 'flavor_side_panel',
    ) ) );
}
add_action( 'customize_register', 'flavor_appearance_customize_register' );

/* =====================================================================
 *  Sanitize callback — side panel icons
 * ===================================================================== */

function flavor_sanitize_side_panel_icons( $input ) {
    if ( is_string( $input ) ) {
        $input = json_decode( wp_unslash( $input ), true );
    }
    if ( ! is_array( $input ) ) {
        return wp_json_encode( array(
            array( 'key' => 'account',  'enabled' => true ),
            array( 'key' => 'cart',     'enabled' => true, 'open_on_add' => true ),
            array( 'key' => 'wishlist', 'enabled' => true, 'open_on_add' => false ),
            array( 'key' => 'compare',  'enabled' => true, 'open_on_add' => false ),
        ) );
    }
    $allowed = array( 'account', 'cart', 'wishlist', 'compare' );
    $has_open = array( 'cart', 'wishlist', 'compare' );
    $clean   = array();
    foreach ( $input as $item ) {
        if ( ! is_array( $item ) ) continue;
        $key = sanitize_key( $item['key'] ?? '' );
        if ( ! in_array( $key, $allowed, true ) ) continue;
        $entry = array(
            'key'     => $key,
            'enabled' => ! empty( $item['enabled'] ),
        );
        if ( in_array( $key, $has_open, true ) ) {
            $entry['open_on_add'] = ! empty( $item['open_on_add'] );
        }
        $clean[] = $entry;
    }
    return wp_json_encode( $clean );
}

/* =====================================================================
 *  Sanitize callback — sidebar blocks
 * ===================================================================== */

function flavor_sanitize_sidebar_blocks( $input ) {
    if ( is_string( $input ) ) {
        $input = json_decode( wp_unslash( $input ), true );
    }
    if ( ! is_array( $input ) ) return array();

    $allowed_types = array_keys( flavor_get_available_blocks() );
    $clean = array();

    foreach ( $input as $b ) {
        if ( ! is_array( $b ) ) continue;
        $type = sanitize_key( $b['type'] ?? '' );
        if ( ! in_array( $type, $allowed_types, true ) ) continue;

        $item = array(
            'type'  => $type,
            'title' => sanitize_text_field( $b['title'] ?? '' ),
        );

        if ( $type === 'custom_html' ) {
            $item['content'] = wp_kses_post( $b['content'] ?? '' );
        }
        if ( $type === 'categories' ) {
            $item['show_count']   = ! empty( $b['show_count'] );
            $item['hierarchical'] = ! empty( $b['hierarchical'] );
        }
        if ( $type === 'brands' ) {
            $item['show_count']    = ! empty( $b['show_count'] );
            $item['display_style'] = in_array( ( $b['display_style'] ?? 'list' ), array( 'list', 'tag_cloud', 'logo' ), true ) ? $b['display_style'] : 'list';
            $item['logo_size']     = absint( $b['logo_size'] ?? 60 ) ?: 60;
            $item['logo_tint']     = in_array( ( $b['logo_tint'] ?? 'none' ), array( 'none', 'grayscale', 'mono', 'accent' ), true ) ? $b['logo_tint'] : 'none';
        }
        if ( in_array( $type, array( 'bestsellers', 'new_products', 'on_sale' ), true ) ) {
            $item['limit'] = absint( $b['limit'] ?? 5 ) ?: 5;
        }
        if ( $type === 'cta_banner' ) {
            $item['image_url']   = esc_url_raw( $b['image_url'] ?? '' );
            $item['text']        = sanitize_text_field( $b['text'] ?? '' );
            $item['button_text'] = sanitize_text_field( $b['button_text'] ?? '' );
            $item['button_url']  = esc_url_raw( $b['button_url'] ?? '' );
            $item['bg_color']    = sanitize_hex_color( $b['bg_color'] ?? '#2271b1' ) ?: '#2271b1';
        }
        if ( $type === 'price_filter' ) {
            $item['step']  = absint( $b['step'] ?? 10 ) ?: 10;
            $item['style'] = in_array( ( $b['style'] ?? 'inputs' ), array( 'inputs', 'slider' ), true ) ? $b['style'] : 'inputs';
        }
        if ( $type === 'attributes' ) {
            $item['show_count']      = ! empty( $b['show_count'] );
            $item['tile_size']       = absint( $b['tile_size'] ?? 28 ) ?: 28;
            $item['color_display']   = in_array( ( $b['color_display'] ?? 'tiles' ), array( 'tiles', 'circles', 'list', 'dropdown', 'pills' ), true ) ? $b['color_display'] : 'tiles';
            $item['text_display']    = in_array( ( $b['text_display'] ?? 'list' ), array( 'list', 'dropdown', 'pills' ), true ) ? $b['text_display'] : 'list';
            $item['image_display']   = in_array( ( $b['image_display'] ?? 'list' ), array( 'list', 'dropdown', 'tiles', 'circles', 'pills' ), true ) ? $b['image_display'] : 'list';
            $item['image_tile_size'] = absint( $b['image_tile_size'] ?? 48 ) ?: 48;
        }

        $clean[] = $item;
    }

    return $clean;
}

/* =====================================================================
 *  Sanitize callback — menu items
 * ===================================================================== */

function flavor_sanitize_menu_items( $input ) {
    if ( is_string( $input ) ) {
        $input = json_decode( wp_unslash( $input ), true );
    }
    if ( ! is_array( $input ) ) return array();

    $clean = array();
    foreach ( $input as $item ) {
        if ( ! is_array( $item ) ) continue;
        $type = sanitize_key( $item['type'] ?? 'custom' );
        if ( ! in_array( $type, array( 'page', 'custom', 'fc_shop', 'fc_cart', 'fc_account', 'fc_wishlist', 'fc_compare', 'fc_about', 'fc_contact' ), true ) ) continue;

        $clean[] = array(
            'type'  => $type,
            'id'    => absint( $item['id'] ?? 0 ),
            'title' => sanitize_text_field( $item['title'] ?? '' ),
            'url'   => esc_url_raw( $item['url'] ?? '' ),
            'shop_display' => in_array( ( $item['shop_display'] ?? 'text' ), array( 'text', 'icon' ), true ) ? $item['shop_display'] : 'text',
            'cart_action' => in_array( ( $item['cart_action'] ?? 'minicart' ), array( 'minicart', 'page' ), true ) ? $item['cart_action'] : 'minicart',
            'show_total'  => ! empty( $item['show_total'] ),
        );
    }
    return $clean;
}

/* =====================================================================
 *  Enqueue CSS + JS w panelu Customizera
 * ===================================================================== */

function flavor_appearance_customizer_scripts() {
    if ( ! defined( 'FC_VERSION' ) ) return;

    wp_enqueue_style(
        'flavor-appearance-customizer',
        get_template_directory_uri() . '/css/admin-appearance.css',
        array( 'dashicons' ),
        FLAVOR_VERSION
    );

    wp_enqueue_script( 'jquery-ui-sortable' );

    wp_enqueue_script(
        'flavor-appearance-customizer',
        get_template_directory_uri() . '/js/admin-appearance.js',
        array( 'customize-controls', 'jquery', 'jquery-ui-sortable', 'underscore' ),
        FLAVOR_VERSION,
        true
    );

    // Pass admin translations to JS for widget & menu controls.
    $js_keys = array(
        'title', 'show_count', 'show_hierarchical', 'display_style',
        'list', 'pills', 'logo', 'logo_size', 'logo_tint',
        'tint_none', 'tint_grayscale', 'tint_mono', 'tint_accent',
        'attr_auto_desc', 'color_style', 'tiles', 'circles', 'dropdown',
        'color_size', 'text_style', 'image_style', 'image_size',
        'style', 'inputs', 'slider', 'step', 'html_content',
        'products_count', 'image_url', 'text', 'button_text', 'button_url', 'bg_color',
        'type_cart', 'type_account', 'type_wishlist', 'type_compare', 'type_shop', 'type_about', 'type_contact',
        'remove', 'shop_display', 'shop_text', 'shop_icon',
        'cart_action', 'open_minicart', 'go_to_cart', 'show_total',
        'all_pages_added',
    );
    $i18n = array();
    foreach ( $js_keys as $k ) {
        $i18n[ $k ] = fc__( 'cust_js_' . $k, 'admin' );
    }
    // Also pass special element labels (already translated in admin.php).
    $i18n['my_account'] = fc__( 'cust_my_account', 'admin' );
    $i18n['cart']       = fc__( 'cust_cart', 'admin' );
    $i18n['wishlist']   = fc__( 'cust_wishlist', 'admin' );
    $i18n['compare']    = fc__( 'cust_compare', 'admin' );
    $i18n['shop']       = fc__( 'cust_shop', 'admin' );
    $i18n['about']      = fc__( 'cust_about', 'admin' );
    $i18n['contact']    = fc__( 'cust_contact', 'admin' );

    wp_localize_script( 'flavor-appearance-customizer', 'flavorAppI18n', $i18n );
}
add_action( 'customize_controls_enqueue_scripts', 'flavor_appearance_customizer_scripts' );

/* =====================================================================
 *  Nadpisanie breakpointów podglądu urządzeń w Customizerze
 *  Tablet: 1024px (mieści się w naszym 768–1024px)
 *  Mobile: 480px  (mieści się w naszym ≤767px)
 * ===================================================================== */
function flavor_customizer_preview_breakpoints() {
    ?>
    <style>
        .preview-tablet .wp-full-overlay-main {
            width: 1024px !important;
            margin: 0 auto !important;
            left: 0 !important;
            right: 0 !important;
        }
        .preview-mobile .wp-full-overlay-main {
            width: 480px !important;
            margin: 0 auto !important;
            left: 0 !important;
            right: 0 !important;
            top: 50% !important;
            bottom: auto !important;
            transform: translateY(-50%) !important;
            height: 80% !important;
        }
    </style>
    <?php
}
add_action( 'customize_controls_print_styles', 'flavor_customizer_preview_breakpoints' );
