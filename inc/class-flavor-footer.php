<?php
/**
 * Flavor_Footer — Customizer footer settings & rendering helpers.
 *
 * @package Flavor
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Flavor_Footer {

    /* ───────────────────────────────────────────────────────
     *  Bootstrap
     * ─────────────────────────────────────────────────────── */
    public static function init() {
        add_action( 'customize_register', array( __CLASS__, 'customize_register' ), 25 );
    }

    /* ───────────────────────────────────────────────────────
     *  Customizer registration
     * ─────────────────────────────────────────────────────── */
    public static function customize_register( $wp_customize ) {

        // ── Section ──
        $wp_customize->add_section( 'flavor_footer', array(
            'title'    => fc__( 'cust_footer_section', 'admin' ),
            'panel'    => 'flavor_panel',
            'priority' => 80,
        ) );

        // =============================================================
        //  Copyright settings
        // =============================================================
        $wp_customize->add_setting( 'flavor_footer_copyright_enabled', array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_setting( 'flavor_footer_copyright_text', array(
            'default'           => '© {year} {site}. {rights}',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_setting( 'flavor_footer_credit_enabled', array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );

        // =============================================================
        //  Widget settings per card
        // =============================================================

        /* ── About / description text ── */
        $wp_customize->add_setting( 'flavor_footer_about_text', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );

        /* ── Menu — which WP nav‑menu to show ── */
        $wp_customize->add_setting( 'flavor_footer_menu_title', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_setting( 'flavor_footer_menu', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );

        /* ── Menu 2 — optional second menu column ── */
        $wp_customize->add_setting( 'flavor_footer_menu2_title', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_setting( 'flavor_footer_menu2', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );

        /* ── Contact info widget ── */
        $wp_customize->add_setting( 'flavor_footer_contact_show_address', array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ) );
        $wp_customize->add_setting( 'flavor_footer_contact_show_phone', array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ) );
        $wp_customize->add_setting( 'flavor_footer_contact_show_email', array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ) );

        /* ── Newsletter ── */
        $wp_customize->add_setting( 'flavor_footer_newsletter_text', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );

        // =============================================================
        //  Widget order  (Sortable Cards)
        // =============================================================
        $wp_customize->add_setting( 'flavor_footer_widgets_order', array(
            'default'           => 'about,menu,menu2,contact,social,newsletter',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );

        $svg_arrow = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>';

        $wp_customize->add_control( new Flavor_Sortable_Cards_Control( $wp_customize, 'flavor_footer_widgets_order', array(
            'label'       => fc__( 'cust_footer_widgets_order', 'admin' ),
            'description' => fc__( 'cust_footer_widgets_order_desc', 'admin' ),
            'section'     => 'flavor_footer',
            'cards'       => array(

                /* ── Pinned: Copyright ── */
                'copyright' => array(
                    'label'  => fc__( 'cust_footer_card_copyright', 'admin' ),
                    'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M14.83 14.83a4 4 0 1 1 0-5.66"/></svg>',
                    'pinned' => true,
                    'fields' => function() {
                        $enabled = get_theme_mod( 'flavor_footer_copyright_enabled', true );
                        $text    = get_theme_mod( 'flavor_footer_copyright_text', '© {year} {site}. {rights}' );
                        $credit  = get_theme_mod( 'flavor_footer_credit_enabled', true );
                        ?>
                        <div class="fc-card-field">
                            <label>
                                <input type="checkbox"
                                       data-customize-setting-link="flavor_footer_copyright_enabled"
                                       <?php checked( $enabled ); ?>>
                                <?php echo esc_html( fc__( 'cust_footer_show_copyright', 'admin' ) ); ?>
                            </label>
                        </div>
                        <div class="fc-card-field" style="margin-top:8px">
                            <label style="display:block;font-size:12px;color:#444;margin-bottom:4px">
                                <?php echo esc_html( fc__( 'cust_footer_copyright_text_label', 'admin' ) ); ?>
                            </label>
                            <input type="text"
                                   data-customize-setting-link="flavor_footer_copyright_text"
                                   value="<?php echo esc_attr( $text ); ?>"
                                   style="width:100%;box-sizing:border-box;font-size:12px">
                            <p style="font-size:11px;color:#999;margin:4px 0 0">
                                <?php echo esc_html( fc__( 'cust_footer_copyright_placeholders', 'admin' ) ); ?>
                            </p>
                        </div>
                        <div class="fc-card-field" style="margin-top:8px">
                            <label>
                                <input type="checkbox"
                                       data-customize-setting-link="flavor_footer_credit_enabled"
                                       <?php checked( $credit ); ?>>
                                <?php echo esc_html( fc__( 'cust_footer_show_credit', 'admin' ) ); ?>
                            </label>
                        </div>
                        <?php
                    },
                ),

                /* ── About / description ── */
                'about' => array(
                    'label'  => fc__( 'cust_footer_card_about', 'admin' ),
                    'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>',
                    'fields' => function() {
                        $text = get_theme_mod( 'flavor_footer_about_text', '' );
                        ?>
                        <div class="fc-card-field">
                            <label style="display:block;font-size:12px;color:#444;margin-bottom:4px">
                                <?php echo esc_html( fc__( 'cust_footer_about_text_label', 'admin' ) ); ?>
                            </label>
                            <textarea data-customize-setting-link="flavor_footer_about_text"
                                      rows="3"
                                      style="width:100%;box-sizing:border-box;font-size:12px"><?php echo esc_textarea( $text ); ?></textarea>
                        </div>
                        <?php
                    },
                ),

                /* ── Menu 1 ── */
                'menu' => array(
                    'label'  => fc__( 'cust_footer_card_menu', 'admin' ),
                    'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" x2="21" y1="6" y2="6"/><line x1="3" x2="21" y1="12" y2="12"/><line x1="3" x2="21" y1="18" y2="18"/></svg>',
                    'fields' => function() {
                        $title = get_theme_mod( 'flavor_footer_menu_title', '' );
                        $menu  = get_theme_mod( 'flavor_footer_menu', '' );
                        $menus = wp_get_nav_menus();
                        ?>
                        <div class="fc-card-field">
                            <label style="display:block;font-size:12px;color:#444;margin-bottom:4px">
                                <?php echo esc_html( fc__( 'cust_footer_menu_title_label', 'admin' ) ); ?>
                            </label>
                            <input type="text"
                                   data-customize-setting-link="flavor_footer_menu_title"
                                   value="<?php echo esc_attr( $title ); ?>"
                                   style="width:100%;box-sizing:border-box;font-size:12px">
                        </div>
                        <div class="fc-card-field" style="margin-top:8px">
                            <label style="display:block;font-size:12px;color:#444;margin-bottom:4px">
                                <?php echo esc_html( fc__( 'cust_footer_menu_select', 'admin' ) ); ?>
                            </label>
                            <select data-customize-setting-link="flavor_footer_menu"
                                    style="width:100%;box-sizing:border-box;font-size:12px">
                                <option value=""><?php echo esc_html( fc__( 'cust_footer_menu_none', 'admin' ) ); ?></option>
                                <?php foreach ( $menus as $m ) : ?>
                                    <option value="<?php echo esc_attr( $m->term_id ); ?>" <?php selected( $menu, $m->term_id ); ?>>
                                        <?php echo esc_html( $m->name ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php
                    },
                ),

                /* ── Menu 2 ── */
                'menu2' => array(
                    'label'  => fc__( 'cust_footer_card_menu2', 'admin' ),
                    'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" x2="21" y1="6" y2="6"/><line x1="3" x2="21" y1="12" y2="12"/><line x1="3" x2="21" y1="18" y2="18"/></svg>',
                    'fields' => function() {
                        $title = get_theme_mod( 'flavor_footer_menu2_title', '' );
                        $menu  = get_theme_mod( 'flavor_footer_menu2', '' );
                        $menus = wp_get_nav_menus();
                        ?>
                        <div class="fc-card-field">
                            <label style="display:block;font-size:12px;color:#444;margin-bottom:4px">
                                <?php echo esc_html( fc__( 'cust_footer_menu_title_label', 'admin' ) ); ?>
                            </label>
                            <input type="text"
                                   data-customize-setting-link="flavor_footer_menu2_title"
                                   value="<?php echo esc_attr( $title ); ?>"
                                   style="width:100%;box-sizing:border-box;font-size:12px">
                        </div>
                        <div class="fc-card-field" style="margin-top:8px">
                            <label style="display:block;font-size:12px;color:#444;margin-bottom:4px">
                                <?php echo esc_html( fc__( 'cust_footer_menu_select', 'admin' ) ); ?>
                            </label>
                            <select data-customize-setting-link="flavor_footer_menu2"
                                    style="width:100%;box-sizing:border-box;font-size:12px">
                                <option value=""><?php echo esc_html( fc__( 'cust_footer_menu_none', 'admin' ) ); ?></option>
                                <?php foreach ( $menus as $m ) : ?>
                                    <option value="<?php echo esc_attr( $m->term_id ); ?>" <?php selected( $menu, $m->term_id ); ?>>
                                        <?php echo esc_html( $m->name ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php
                    },
                ),

                /* ── Contact info ── */
                'contact' => array(
                    'label'  => fc__( 'cust_footer_card_contact', 'admin' ),
                    'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',
                    'fields' => function() {
                        $show_address = get_theme_mod( 'flavor_footer_contact_show_address', true );
                        $show_phone   = get_theme_mod( 'flavor_footer_contact_show_phone', true );
                        $show_email   = get_theme_mod( 'flavor_footer_contact_show_email', true );
                        ?>
                        <div class="fc-card-field">
                            <label>
                                <input type="checkbox"
                                       data-customize-setting-link="flavor_footer_contact_show_address"
                                       <?php checked( $show_address ); ?>>
                                <?php echo esc_html( fc__( 'cust_footer_contact_show_address', 'admin' ) ); ?>
                            </label>
                        </div>
                        <div class="fc-card-field">
                            <label>
                                <input type="checkbox"
                                       data-customize-setting-link="flavor_footer_contact_show_phone"
                                       <?php checked( $show_phone ); ?>>
                                <?php echo esc_html( fc__( 'cust_footer_contact_show_phone', 'admin' ) ); ?>
                            </label>
                        </div>
                        <div class="fc-card-field">
                            <label>
                                <input type="checkbox"
                                       data-customize-setting-link="flavor_footer_contact_show_email"
                                       <?php checked( $show_email ); ?>>
                                <?php echo esc_html( fc__( 'cust_footer_contact_show_email', 'admin' ) ); ?>
                            </label>
                        </div>
                        <p style="font-size:11px;color:#999;margin:6px 0 0">
                            <?php echo esc_html( fc__( 'cust_footer_contact_hint', 'admin' ) ); ?>
                        </p>
                        <?php
                    },
                ),

                /* ── Social icons ── */
                'social' => array(
                    'label'  => fc__( 'cust_footer_card_social', 'admin' ),
                    'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>',
                    'fields' => function() {
                        ?>
                        <p style="font-size:12px;color:#999;margin:0">
                            <?php echo esc_html( fc__( 'cust_footer_social_hint', 'admin' ) ); ?>
                        </p>
                        <?php
                    },
                ),

                /* ── Newsletter ── */
                'newsletter' => array(
                    'label'  => fc__( 'cust_footer_card_newsletter', 'admin' ),
                    'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>',
                    'fields' => function() {
                        $text = get_theme_mod( 'flavor_footer_newsletter_text', '' );
                        ?>
                        <div class="fc-card-field">
                            <label style="display:block;font-size:12px;color:#444;margin-bottom:4px">
                                <?php echo esc_html( fc__( 'cust_footer_newsletter_text_label', 'admin' ) ); ?>
                            </label>
                            <input type="text"
                                   data-customize-setting-link="flavor_footer_newsletter_text"
                                   value="<?php echo esc_attr( $text ); ?>"
                                   placeholder="<?php echo esc_attr( fc__( 'footer_newsletter_default_text' ) ); ?>"
                                   style="width:100%;box-sizing:border-box;font-size:12px">
                        </div>
                        <?php
                    },
                ),

            ),
        ) ) );
    }

    /* ───────────────────────────────────────────────────────
     *  Get ordered widget keys (for template)
     * ─────────────────────────────────────────────────────── */
    public static function get_widget_order() {
        $order_raw = get_theme_mod( 'flavor_footer_widgets_order', 'about,menu,menu2,contact,social,newsletter' );
        return array_filter( array_map( 'trim', explode( ',', $order_raw ) ) );
    }

    /* ───────────────────────────────────────────────────────
     *  Render copyright bar
     * ─────────────────────────────────────────────────────── */
    public static function render_copyright() {
        if ( ! get_theme_mod( 'flavor_footer_copyright_enabled', true ) && ! get_theme_mod( 'flavor_footer_credit_enabled', true ) ) {
            return;
        }

        echo '<div class="footer-bottom">';

        if ( get_theme_mod( 'flavor_footer_copyright_enabled', true ) ) {
            $text = get_theme_mod( 'flavor_footer_copyright_text', '© {year} {site}. {rights}' );
            $text = str_replace(
                array( '{year}', '{site}', '{rights}' ),
                array(
                    date( 'Y' ),
                    '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html( get_bloginfo( 'name' ) ) . '</a>',
                    esc_html( fc__( 'theme_all_rights' ) ),
                ),
                $text
            );
            echo '<p class="footer-copyright">' . $text . '</p>';
        }

        if ( get_theme_mod( 'flavor_footer_credit_enabled', true ) ) {
            printf(
                '<p class="footer-credit">' . esc_html( fc__( 'theme_theme_by' ) ) . '</p>',
                '<a href="https://flavor-theme.dev">Flavor</a>'
            );
        }

        echo '</div>';
    }

    /* ───────────────────────────────────────────────────────
     *  Render widget: about
     * ─────────────────────────────────────────────────────── */
    public static function render_widget_about() {
        $text = get_theme_mod( 'flavor_footer_about_text', '' );
        if ( ! $text ) {
            return;
        }
        echo '<div class="footer-widget footer-widget-about">';
        if ( has_custom_logo() ) {
            echo '<div class="footer-logo">' . get_custom_logo() . '</div>';
        }
        echo '<p>' . esc_html( $text ) . '</p>';
        echo '</div>';
    }

    /* ───────────────────────────────────────────────────────
     *  Render widget: menu
     * ─────────────────────────────────────────────────────── */
    public static function render_widget_menu( $suffix = '' ) {
        $menu_id = get_theme_mod( 'flavor_footer_menu' . $suffix, '' );
        $title   = get_theme_mod( 'flavor_footer_menu' . $suffix . '_title', '' );

        if ( ! $menu_id ) {
            return;
        }

        echo '<div class="footer-widget footer-widget-menu">';
        if ( $title ) {
            echo '<h4 class="footer-widget-title">' . esc_html( $title ) . '</h4>';
        }
        wp_nav_menu( array(
            'menu'            => (int) $menu_id,
            'container'       => false,
            'menu_class'      => 'footer-menu-list',
            'depth'           => 1,
            'fallback_cb'     => false,
        ) );
        echo '</div>';
    }

    /* ───────────────────────────────────────────────────────
     *  Render widget: contact
     * ─────────────────────────────────────────────────────── */
    public static function render_widget_contact() {
        $show_address = get_theme_mod( 'flavor_footer_contact_show_address', true );
        $show_phone   = get_theme_mod( 'flavor_footer_contact_show_phone', true );
        $show_email   = get_theme_mod( 'flavor_footer_contact_show_email', true );

        // Get store data from FC options
        $address_parts = array();
        $street  = get_option( 'fc_store_address', '' );
        $city    = get_option( 'fc_store_city', '' );
        $zip     = get_option( 'fc_store_postcode', '' );
        if ( $street ) $address_parts[] = $street;
        if ( $zip && $city ) {
            $address_parts[] = $zip . ' ' . $city;
        } elseif ( $city ) {
            $address_parts[] = $city;
        }

        $phone = get_option( 'fc_store_phone', '' );
        $email = get_option( 'fc_store_email', '' );

        $has_content = ( $show_address && $address_parts ) || ( $show_phone && $phone ) || ( $show_email && $email );
        if ( ! $has_content ) {
            return;
        }

        echo '<div class="footer-widget footer-widget-contact">';
        echo '<h4 class="footer-widget-title">' . esc_html( fc__( 'footer_contact_title' ) ) . '</h4>';
        echo '<ul class="footer-contact-list">';

        if ( $show_address && $address_parts ) {
            echo '<li class="footer-contact-item">';
            echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>';
            echo '<span>' . esc_html( implode( ', ', $address_parts ) ) . '</span>';
            echo '</li>';
        }

        if ( $show_phone && $phone ) {
            echo '<li class="footer-contact-item">';
            echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>';
            echo '<a href="tel:' . esc_attr( preg_replace( '/[^+0-9]/', '', $phone ) ) . '">' . esc_html( $phone ) . '</a>';
            echo '</li>';
        }

        if ( $show_email && $email ) {
            echo '<li class="footer-contact-item">';
            echo '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>';
            echo '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>';
            echo '</li>';
        }

        echo '</ul>';
        echo '</div>';
    }

    /* ───────────────────────────────────────────────────────
     *  Render widget: social icons
     * ─────────────────────────────────────────────────────── */
    public static function render_widget_social() {
        if ( ! class_exists( 'Flavor_Pages' ) ) {
            return;
        }
        $links = Flavor_Pages::get_social_links();
        if ( empty( $links ) ) {
            return;
        }

        echo '<div class="footer-widget footer-widget-social">';
        echo '<h4 class="footer-widget-title">' . esc_html( fc__( 'contact_social_title' ) ) . '</h4>';
        echo '<div class="footer-social-icons">';
        foreach ( $links as $link ) {
            $icon = Flavor_Pages::get_social_icon( $link['key'] );
            echo '<a href="' . esc_url( $link['url'] ) . '" target="_blank" rel="noopener noreferrer" class="footer-social-link" title="' . esc_attr( $link['label'] ) . '">' . $icon . '</a>';
        }
        echo '</div>';
        echo '</div>';
    }

    /* ───────────────────────────────────────────────────────
     *  Render widget: newsletter
     * ─────────────────────────────────────────────────────── */
    public static function render_widget_newsletter() {
        $text = get_theme_mod( 'flavor_footer_newsletter_text', '' );
        if ( ! $text ) {
            $text = fc__( 'footer_newsletter_default_text' );
        }

        echo '<div class="footer-widget footer-widget-newsletter">';
        echo '<h4 class="footer-widget-title">' . esc_html( fc__( 'footer_newsletter_title' ) ) . '</h4>';
        if ( $text ) {
            echo '<p class="footer-newsletter-desc">' . esc_html( $text ) . '</p>';
        }
        echo '<form class="footer-newsletter-form" method="post" action="#">';
        echo '<div class="footer-newsletter-input-wrap">';
        echo '<input type="email" name="newsletter_email" placeholder="' . esc_attr( fc__( 'footer_newsletter_placeholder' ) ) . '" required>';
        echo '<button type="submit">' . esc_html( fc__( 'footer_newsletter_button' ) ) . '</button>';
        echo '</div>';
        echo '</form>';
        echo '</div>';
    }

    /* ───────────────────────────────────────────────────────
     *  Render all footer widgets in order
     * ─────────────────────────────────────────────────────── */
    public static function render_widgets() {
        $order = self::get_widget_order();
        if ( empty( $order ) ) {
            return;
        }

        echo '<div class="footer-widgets">';
        foreach ( $order as $widget ) {
            switch ( $widget ) {
                case 'about':
                    self::render_widget_about();
                    break;
                case 'menu':
                    self::render_widget_menu();
                    break;
                case 'menu2':
                    self::render_widget_menu2();
                    break;
                case 'contact':
                    self::render_widget_contact();
                    break;
                case 'social':
                    self::render_widget_social();
                    break;
                case 'newsletter':
                    self::render_widget_newsletter();
                    break;
            }
        }
        echo '</div>';
    }

    /* ───────────────────────────────────────────────────────
     *  Render widget: menu2 (shortcut)
     * ─────────────────────────────────────────────────────── */
    public static function render_widget_menu2() {
        self::render_widget_menu( '2' );
    }
}

Flavor_Footer::init();
