<?php
/**
 * Flavor Theme — Strona kontaktowa
 *
 * - Customizer: dane kontaktowe, social media, godziny otwarcia, karty sidebar
 * - Szablon strony kontaktowej z formularzem + info + social
 * - AJAX handler formularza kontaktowego
 * - Strony (O nas, Kontakt) tworzone przez Flavor Commerce plugin
 *
 * @package Flavor
 * @since 1.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Flavor_Pages {

    /**
     * Social media platforms
     */
    private static $socials = array(
        'facebook'  => array( 'label' => 'Facebook',  'icon' => 'facebook'  ),
        'instagram' => array( 'label' => 'Instagram', 'icon' => 'instagram' ),
        'x'         => array( 'label' => 'X (Twitter)', 'icon' => 'x'      ),
        'youtube'   => array( 'label' => 'YouTube',   'icon' => 'youtube'   ),
        'tiktok'    => array( 'label' => 'TikTok',    'icon' => 'tiktok'    ),
        'linkedin'  => array( 'label' => 'LinkedIn',  'icon' => 'linkedin'  ),
        'pinterest' => array( 'label' => 'Pinterest', 'icon' => 'pinterest' ),
    );

    /**
     * Inicjalizacja
     */
    public static function init() {
        add_action( 'wp_ajax_flavor_contact_form',        array( __CLASS__, 'handle_contact_form' ) );
        add_action( 'wp_ajax_nopriv_flavor_contact_form', array( __CLASS__, 'handle_contact_form' ) );
        add_filter( 'page_template',                      array( __CLASS__, 'maybe_force_contact_template' ) );
    }

    /**
     * Wymuszaj szablon kontaktowy dla strony kontaktowej
     */
    public static function maybe_force_contact_template( $template ) {
        $contact_id = absint( get_option( 'fc_page_kontakt', 0 ) );
        if ( $contact_id && is_page( $contact_id ) ) {
            $contact_tpl = get_template_directory() . '/template-contact.php';
            if ( file_exists( $contact_tpl ) ) {
                return $contact_tpl;
            }
        }
        return $template;
    }

    /* =================================================================
     *  Customizer — rejestracja sekcji i kontrolek
     * ================================================================= */

    public static function customize_register( $wp_customize ) {

        // =============================================================
        //  Sekcja: Dane kontaktowe
        // =============================================================
        $wp_customize->add_section( 'flavor_contact_info', array(
            'title'       => fc__( 'cust_contact_info', 'admin' ),
            'description' => fc__( 'cust_contact_info_desc', 'admin' ),
            'panel'       => 'flavor_panel',
            'priority'    => 70,
        ) );

        // =============================================================
        //  Ustawienia kart sidebarowych
        // =============================================================

        /* --- Company card settings --- */
        $company_fields = array(
            'flavor_contact_show_company' => 'cust_contact_show_company',
            'flavor_contact_show_address' => 'cust_contact_show_address',
            'flavor_contact_show_tax_no'  => 'cust_contact_show_tax_no',
            'flavor_contact_show_crn'     => 'cust_contact_show_crn',
        );
        foreach ( $company_fields as $sid => $lk ) {
            $wp_customize->add_setting( $sid, array(
                'default'           => true,
                'sanitize_callback' => 'wp_validate_boolean',
            ) );
        }

        /* --- Reach card settings --- */
        $reach_fields = array(
            'flavor_contact_show_phone' => 'cust_contact_show_phone',
            'flavor_contact_show_email' => 'cust_contact_show_email',
        );
        foreach ( $reach_fields as $sid => $lk ) {
            $wp_customize->add_setting( $sid, array(
                'default'           => true,
                'sanitize_callback' => 'wp_validate_boolean',
            ) );
        }

        /* --- Hours card settings --- */
        $wp_customize->add_setting( 'flavor_contact_show_hours', array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ) );
        $wp_customize->add_setting( 'flavor_contact_hours', array(
            'default'           => '',
            'sanitize_callback' => function( $val ) {
                $decoded = json_decode( $val, true );
                if ( ! is_array( $decoded ) ) {
                    return '';
                }
                $clean = array();
                foreach ( $decoded as $row ) {
                    $clean[] = array(
                        'day_from' => sanitize_text_field( $row['day_from'] ?? '1' ),
                        'day_to'   => sanitize_text_field( $row['day_to']   ?? '5' ),
                        'from'     => sanitize_text_field( $row['from']     ?? '' ),
                        'to'       => sanitize_text_field( $row['to']       ?? '' ),
                    );
                }
                return wp_json_encode( $clean );
            },
        ) );

        // =============================================================
        //  Kolejność kart w sidebarze (drag & drop) + inline pola
        // =============================================================
        $wp_customize->add_setting( 'flavor_contact_cards_order', array(
            'default'           => 'company,reach,hours,social',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( new Flavor_Sortable_Cards_Control( $wp_customize, 'flavor_contact_cards_order', array(
            'label'       => fc__( 'cust_contact_cards_order', 'admin' ),
            'description' => fc__( 'cust_contact_cards_order_desc', 'admin' ),
            'section'     => 'flavor_contact_info',
            'cards'       => array(
                'company' => array(
                    'label'  => fc__( 'contact_info_title' ),
                    'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/><path d="M2 7h20"/></svg>',
                    'fields' => function() use ( $company_fields ) {
                        foreach ( $company_fields as $sid => $lk ) {
                            $val = get_theme_mod( $sid, true );
                            ?>
                            <div class="fc-card-field">
                                <label>
                                    <input type="checkbox"
                                           data-customize-setting-link="<?php echo esc_attr( $sid ); ?>"
                                           <?php checked( $val ); ?>>
                                    <?php echo esc_html( fc__( $lk, 'admin' ) ); ?>
                                </label>
                            </div>
                            <?php
                        }
                    },
                ),
                'reach'   => array(
                    'label'  => fc__( 'contact_reach_us' ),
                    'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',
                    'fields' => function() use ( $reach_fields ) {
                        foreach ( $reach_fields as $sid => $lk ) {
                            $val = get_theme_mod( $sid, true );
                            ?>
                            <div class="fc-card-field">
                                <label>
                                    <input type="checkbox"
                                           data-customize-setting-link="<?php echo esc_attr( $sid ); ?>"
                                           <?php checked( $val ); ?>>
                                    <?php echo esc_html( fc__( $lk, 'admin' ) ); ?>
                                </label>
                            </div>
                            <?php
                        }
                    },
                ),
                'hours'   => array(
                    'label'  => fc__( 'contact_hours_title' ),
                    'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
                    'fields' => function() {
                        $show = get_theme_mod( 'flavor_contact_show_hours', true );
                        ?>
                        <div class="fc-card-field">
                            <label>
                                <input type="checkbox"
                                       data-customize-setting-link="flavor_contact_show_hours"
                                       <?php checked( $show ); ?>>
                                <?php echo esc_html( fc__( 'cust_contact_show_hours', 'admin' ) ); ?>
                            </label>
                        </div>
                        <?php
                        Flavor_Hours_Control::render_inline( 'flavor_contact_hours' );
                    },
                ),
                'social'  => array(
                    'label'  => fc__( 'contact_social_title' ),
                    'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>',
                    'fields' => function() {
                        // Parse social order setting
                        $default_order = implode( ',', array_map( function( $k ) { return $k . ':1'; }, array_keys( self::$socials ) ) );
                        $order_raw = get_theme_mod( 'flavor_social_order', $default_order );
                        $items = array();
                        foreach ( explode( ',', $order_raw ) as $part ) {
                            $bits = explode( ':', trim( $part ) );
                            if ( count( $bits ) === 2 && isset( self::$socials[ $bits[0] ] ) ) {
                                $items[ $bits[0] ] = (bool) $bits[1];
                            }
                        }
                        // Ensure all socials are present
                        foreach ( self::$socials as $k => $v ) {
                            if ( ! array_key_exists( $k, $items ) ) {
                                $items[ $k ] = true;
                            }
                        }

                        $social_icons_small = array(
                            'facebook'  => '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>',
                            'instagram' => '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>',
                            'x'         => '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
                            'youtube'   => '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
                            'tiktok'    => '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1v-3.5a6.37 6.37 0 0 0-.79-.05A6.34 6.34 0 0 0 3.15 15a6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.34-6.34V8.73a8.19 8.19 0 0 0 4.76 1.52v-3.4a4.85 4.85 0 0 1-1-.16z"/></svg>',
                            'linkedin'  => '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
                            'pinterest' => '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0a12 12 0 0 0-4.373 23.178c-.1-.937-.19-2.376.04-3.401.208-.925 1.345-5.698 1.345-5.698s-.344-.687-.344-1.703c0-1.595.924-2.785 2.075-2.785.978 0 1.45.734 1.45 1.614 0 .983-.626 2.453-.948 3.815-.27 1.14.572 2.068 1.696 2.068 2.036 0 3.6-2.147 3.6-5.244 0-2.741-1.97-4.656-4.781-4.656-3.257 0-5.168 2.442-5.168 4.966 0 .983.379 2.038.852 2.612.094.114.107.213.08.33-.087.363-.281 1.14-.32 1.298-.05.213-.168.258-.388.156-1.453-.676-2.361-2.798-2.361-4.504 0-3.664 2.663-7.027 7.68-7.027 4.032 0 7.166 2.874 7.166 6.711 0 4.007-2.525 7.233-6.032 7.233-1.178 0-2.285-.612-2.664-1.335l-.724 2.762c-.262 1.01-.972 2.275-1.447 3.046A12 12 0 1 0 12 0z"/></svg>',
                        );
                        ?>
                        <p class="fc-card-field" style="font-size:12px;color:#999;margin:0">
                            <?php echo esc_html( fc__( 'cust_contact_social_hint', 'admin' ) ); ?>
                        </p>
                        <ul class="fc-social-sortable" data-setting="flavor_social_order">
                            <?php foreach ( $items as $key => $enabled ) :
                                $social = self::$socials[ $key ];
                                $sid    = 'flavor_social_' . $key;
                                $val    = get_theme_mod( $sid, '' );
                            ?>
                            <li class="fc-social-item<?php echo $enabled ? '' : ' fc-social-disabled'; ?>" data-key="<?php echo esc_attr( $key ); ?>">
                                <div class="fc-social-item-header">
                                    <span class="fc-social-handle">&#9776;</span>
                                    <span class="fc-social-item-icon"><?php echo $social_icons_small[ $key ]; ?></span>
                                    <span class="fc-social-item-label"><?php echo esc_html( $social['label'] ); ?></span>
                                    <label class="fc-social-toggle">
                                        <input type="checkbox" class="fc-social-enabled" <?php checked( $enabled ); ?>>
                                        <span class="fc-social-toggle-slider"></span>
                                    </label>
                                </div>
                                <div class="fc-social-item-url">
                                    <input type="url"
                                           data-customize-setting-link="<?php echo esc_attr( $sid ); ?>"
                                           value="<?php echo esc_attr( $val ); ?>"
                                           placeholder="https://"
                                           style="width:100%;box-sizing:border-box;font-size:12px">
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php
                    },
                ),
            ),
        ) ) );

        // =============================================================
        //  Heading: Mapa
        // =============================================================
        $wp_customize->add_setting( 'flavor_contact_heading_map', array(
            'sanitize_callback' => '__return_empty_string',
        ) );
        $wp_customize->add_control( new Flavor_Heading_Control( $wp_customize, 'flavor_contact_heading_map', array(
            'label'   => fc__( 'cust_contact_map_heading', 'admin' ),
            'section' => 'flavor_contact_info',
        ) ) );

        // Google Maps — włącz/wyłącz
        $wp_customize->add_setting( 'flavor_contact_map_enabled', array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ) );
        $wp_customize->add_control( 'flavor_contact_map_enabled', array(
            'label'   => fc__( 'cust_contact_map_enabled', 'admin' ),
            'section' => 'flavor_contact_info',
            'type'    => 'checkbox',
        ) );

        // Google Maps — źródło adresu
        $wp_customize->add_setting( 'flavor_contact_map_source', array(
            'default'           => 'store',
            'sanitize_callback' => function( $val ) {
                return in_array( $val, array( 'store', 'custom' ), true ) ? $val : 'store';
            },
        ) );
        $wp_customize->add_control( 'flavor_contact_map_source', array(
            'label'   => fc__( 'cust_contact_map_source', 'admin' ),
            'section' => 'flavor_contact_info',
            'type'    => 'radio',
            'choices' => array(
                'store'  => fc__( 'cust_contact_map_source_store', 'admin' ),
                'custom' => fc__( 'cust_contact_map_source_custom', 'admin' ),
            ),
        ) );

        // Google Maps — własny adres
        $wp_customize->add_setting( 'flavor_contact_map_custom', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_control( 'flavor_contact_map_custom', array(
            'label'   => fc__( 'cust_contact_map_custom', 'admin' ),
            'section' => 'flavor_contact_info',
            'type'    => 'text',
        ) );

        // Google Maps — pozycja mapy
        $wp_customize->add_setting( 'flavor_contact_map_position', array(
            'default'           => 'below_all',
            'sanitize_callback' => function( $val ) {
                return in_array( $val, array( 'below_all', 'above_all', 'above_form', 'below_form' ), true ) ? $val : 'below_all';
            },
        ) );
        $wp_customize->add_control( 'flavor_contact_map_position', array(
            'label'   => fc__( 'cust_contact_map_position', 'admin' ),
            'section' => 'flavor_contact_info',
            'type'    => 'select',
            'choices' => array(
                'below_all'  => fc__( 'cust_contact_map_pos_below_all', 'admin' ),
                'above_all'  => fc__( 'cust_contact_map_pos_above_all', 'admin' ),
                'above_form' => fc__( 'cust_contact_map_pos_above_form', 'admin' ),
                'below_form' => fc__( 'cust_contact_map_pos_below_form', 'admin' ),
            ),
        ) );

        // Google Maps — ciemny motyw (domyślnie włączony gdy tryb dark)
        $wp_customize->add_setting( 'flavor_contact_map_dark', array(
            'default'           => ( get_theme_mod( 'flavor_color_mode', 'light' ) === 'dark' ),
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( 'flavor_contact_map_dark', array(
            'label'   => fc__( 'cust_contact_map_dark', 'admin' ),
            'section' => 'flavor_contact_info',
            'type'    => 'checkbox',
        ) );

        // =============================================================
        //  Social Media — ustawienia (renderowane wewnątrz kart)
        // =============================================================
        $default_social_order = implode( ',', array_map( function( $k ) { return $k . ':1'; }, array_keys( self::$socials ) ) );
        $wp_customize->add_setting( 'flavor_social_order', array(
            'default'           => $default_social_order,
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );

        foreach ( self::$socials as $key => $social ) {
            $wp_customize->add_setting( 'flavor_social_' . $key, array(
                'default'           => '',
                'sanitize_callback' => 'esc_url_raw',
            ) );
        }
    }

    /* =================================================================
     *  AJAX — obsługa formularza kontaktowego
     * ================================================================= */

    public static function handle_contact_form() {
        check_ajax_referer( 'flavor_contact_nonce', 'nonce' );

        // Zalogowany użytkownik — dane z bazy
        if ( is_user_logged_in() ) {
            $user  = wp_get_current_user();
            $name  = trim( $user->first_name . ' ' . $user->last_name );
            if ( ! $name ) {
                $name = $user->display_name;
            }
            $email = $user->user_email;
        } else {
            $name  = sanitize_text_field( wp_unslash( $_POST['contact_name'] ?? '' ) );
            $email = sanitize_email( wp_unslash( $_POST['contact_email'] ?? '' ) );
        }

        $subject = sanitize_text_field( wp_unslash( $_POST['contact_subject'] ?? '' ) );
        $message = sanitize_textarea_field( wp_unslash( $_POST['contact_message'] ?? '' ) );

        if ( ! $name || ! $email || ! $message ) {
            wp_send_json_error( array( 'message' => fc__( 'contact_error_required' ) ) );
        }

        if ( ! is_email( $email ) ) {
            wp_send_json_error( array( 'message' => fc__( 'contact_error_email' ) ) );
        }

        // Honeypot
        if ( ! empty( $_POST['contact_website'] ) ) {
            wp_send_json_success( array( 'message' => fc__( 'contact_success' ) ) );
        }

        $to = get_option( 'fc_store_email_contact', get_option( 'admin_email' ) );
        if ( ! $to ) {
            $to = get_option( 'admin_email' );
        }

        $site_name   = get_bloginfo( 'name' );
        $mail_subject = sprintf( '[%s] %s', $site_name, $subject ? $subject : fc__( 'contact_new_message' ) );

        $body  = fc__( 'contact_mail_name' ) . ': ' . $name . "\r\n";
        $body .= fc__( 'contact_mail_email' ) . ': ' . $email . "\r\n";
        if ( $subject ) {
            $body .= fc__( 'contact_mail_subject' ) . ': ' . $subject . "\r\n";
        }
        $body .= "\r\n" . fc__( 'contact_mail_message' ) . ":\r\n" . $message;

        $headers = array(
            'Content-Type: text/plain; charset=UTF-8',
            'Reply-To: ' . $name . ' <' . $email . '>',
        );

        $sent = wp_mail( $to, $mail_subject, $body, $headers );

        if ( $sent ) {
            wp_send_json_success( array( 'message' => fc__( 'contact_success' ) ) );
        } else {
            wp_send_json_error( array( 'message' => fc__( 'contact_error_send' ) ) );
        }
    }

    /* =================================================================
     *  SVG ikony social media
     * ================================================================= */

    public static function get_social_icon( $key ) {
        $icons = array(
            'facebook'  => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>',
            'instagram' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>',
            'x'         => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
            'youtube'   => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
            'tiktok'    => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1v-3.5a6.37 6.37 0 0 0-.79-.05A6.34 6.34 0 0 0 3.15 15a6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.34-6.34V8.73a8.19 8.19 0 0 0 4.76 1.52v-3.4a4.85 4.85 0 0 1-1-.16z"/></svg>',
            'linkedin'  => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
            'pinterest' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0a12 12 0 0 0-4.373 23.178c-.1-.937-.19-2.376.04-3.401.208-.925 1.345-5.698 1.345-5.698s-.344-.687-.344-1.703c0-1.595.924-2.785 2.075-2.785.978 0 1.45.734 1.45 1.614 0 .983-.626 2.453-.948 3.815-.27 1.14.572 2.068 1.696 2.068 2.036 0 3.6-2.147 3.6-5.244 0-2.741-1.97-4.656-4.781-4.656-3.257 0-5.168 2.442-5.168 4.966 0 .983.379 2.038.852 2.612.094.114.107.213.08.33-.087.363-.281 1.14-.32 1.298-.05.213-.168.258-.388.156-1.453-.676-2.361-2.798-2.361-4.504 0-3.664 2.663-7.027 7.68-7.027 4.032 0 7.166 2.874 7.166 6.711 0 4.007-2.525 7.233-6.032 7.233-1.178 0-2.285-.612-2.664-1.335l-.724 2.762c-.262 1.01-.972 2.275-1.447 3.046A12 12 0 1 0 12 0z"/></svg>',
        );
        return $icons[ $key ] ?? '';
    }

    /**
     * Get active social links
     *
     * @return array [ ['key' => '...', 'url' => '...', 'label' => '...'] ]
     */
    public static function get_social_links() {
        // Parse order + enabled state
        $default_order = implode( ',', array_map( function( $k ) { return $k . ':1'; }, array_keys( self::$socials ) ) );
        $order_raw = get_theme_mod( 'flavor_social_order', $default_order );
        $ordered = array();
        foreach ( explode( ',', $order_raw ) as $part ) {
            $bits = explode( ':', trim( $part ) );
            if ( count( $bits ) === 2 && isset( self::$socials[ $bits[0] ] ) ) {
                $ordered[ $bits[0] ] = (bool) $bits[1];
            }
        }
        // Add any missing socials at the end (enabled by default)
        foreach ( self::$socials as $k => $v ) {
            if ( ! array_key_exists( $k, $ordered ) ) {
                $ordered[ $k ] = true;
            }
        }

        $links = array();
        foreach ( $ordered as $key => $enabled ) {
            if ( ! $enabled ) {
                continue;
            }
            $url = get_theme_mod( 'flavor_social_' . $key, '' );
            if ( $url ) {
                $links[] = array(
                    'key'   => $key,
                    'url'   => $url,
                    'label' => self::$socials[ $key ]['label'],
                );
            }
        }
        return $links;
    }
}

Flavor_Pages::init();
add_action( 'customize_register', array( 'Flavor_Pages', 'customize_register' ), 1001 );

/* =====================================================================
 *  Helper functions
 * ===================================================================== */

/**
 * Pobierz ID strony "O nas"
 */
function flavor_get_about_page_id() {
    return absint( get_option( 'fc_page_o-nas', 0 ) );
}

/**
 * Pobierz ID strony "Kontakt"
 */
function flavor_get_contact_page_id() {
    return absint( get_option( 'fc_page_kontakt', 0 ) );
}

/**
 * Pobierz URL strony "O nas"
 */
function flavor_get_about_page_url() {
    $id = flavor_get_about_page_id();
    return $id ? get_permalink( $id ) : '';
}

/**
 * Pobierz URL strony "Kontakt"
 */
function flavor_get_contact_page_url() {
    $id = flavor_get_contact_page_id();
    return $id ? get_permalink( $id ) : '';
}

/**
 * Pobierz aktywne linki social media
 *
 * @return array
 */
function flavor_get_social_links() {
    return Flavor_Pages::get_social_links();
}
