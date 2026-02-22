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
        foreach ( array( 'fc_page_sklep', 'fc_page_koszyk', 'fc_page_moje-konto', 'fc_page_zamowienie', 'fc_page_podziekowanie', 'fc_page_porownanie', 'fc_page_wishlist', 'fc_page_platnosc_nieudana' ) as $opt ) {
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
                    <button type="button" class="button fc-menu-add-special" data-special="fc_compare" style="width:100%;text-align:left;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:4px;"><path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="M7 21h10"/><path d="M12 3v18"/><path d="M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"/></svg>
                        <?php echo esc_html( fc__( 'cust_compare', 'admin' ) ); ?>
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
        if ( ! in_array( $type, array( 'page', 'custom', 'fc_shop', 'fc_cart', 'fc_account', 'fc_wishlist', 'fc_compare' ), true ) ) continue;

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
        'type_cart', 'type_account', 'type_wishlist', 'type_compare', 'type_shop',
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
