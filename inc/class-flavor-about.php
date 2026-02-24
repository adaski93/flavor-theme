<?php
/**
 * Flavor Theme — Strona „O nas"
 *
 * - Customizer: hero banner, zespół, wartości, statystyki, opinie, oś czasu
 * - Wymuszanie szablonu template-about.php dla strony „O nas"
 * - Sortowalna kolejność sekcji (drag & drop)
 *
 * @package Flavor
 * @since 1.5.3
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Flavor_About {

    /**
     * Available countries (ISO 3166-1 alpha-2 => name)
     */
    private static $countries = array(
        ''   => '—',
        'PL' => 'Polska',
        'DE' => 'Deutschland',
        'GB' => 'United Kingdom',
        'US' => 'United States',
        'FR' => 'France',
        'IT' => 'Italia',
        'ES' => 'España',
        'CZ' => 'Česko',
        'SK' => 'Slovensko',
        'UA' => 'Україна',
        'NL' => 'Nederland',
        'BE' => 'Belgique',
        'AT' => 'Österreich',
        'CH' => 'Schweiz',
        'SE' => 'Sverige',
        'NO' => 'Norge',
        'DK' => 'Danmark',
        'FI' => 'Suomi',
        'PT' => 'Portugal',
        'IE' => 'Ireland',
        'LT' => 'Lietuva',
        'LV' => 'Latvija',
        'EE' => 'Eesti',
        'RO' => 'România',
        'HU' => 'Magyarország',
        'HR' => 'Hrvatska',
        'BG' => 'България',
        'GR' => 'Ελλάδα',
        'RU' => 'Россия',
        'TR' => 'Türkiye',
        'JP' => '日本',
        'CN' => '中国',
        'KR' => '한국',
        'IN' => 'India',
        'AU' => 'Australia',
        'CA' => 'Canada',
        'BR' => 'Brasil',
        'MX' => 'México',
        'AR' => 'Argentina',
    );

    /**
     * Convert ISO country code to flag emoji
     */
    public static function country_flag( $code ) {
        $code = strtoupper( trim( $code ) );
        if ( strlen( $code ) !== 2 ) {
            return '';
        }
        // Regional indicator symbols: A = U+1F1E6
        $first  = mb_chr( 0x1F1E6 + ord( $code[0] ) - ord( 'A' ) );
        $second = mb_chr( 0x1F1E6 + ord( $code[1] ) - ord( 'A' ) );
        return $first . $second;
    }

    /**
     * Available value icons (key => SVG)
     */
    private static $value_icons = array(
        'star'      => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
        'heart'     => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>',
        'shield'    => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>',
        'leaf'      => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 9.8 6.9C15.5 4.9 17 3.5 19 2c1 2 2 4.5 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>',
        'lightbulb' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/></svg>',
        'handshake' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m11 17 2 2a1 1 0 1 0 3-3"/><path d="m14 14 2.5 2.5a1 1 0 1 0 3-3l-3.88-3.88a3 3 0 0 0-4.24 0l-.88.88a1 1 0 1 1-3-3l2.81-2.81a5.79 5.79 0 0 1 7.06-.87l.47.28a2 2 0 0 0 1.42.25L21 4"/><path d="m21 3 1 11h-2"/><path d="M3 3 2 14h2"/><path d="m8 7-1.25-1.25a1 1 0 0 0-1.5 0L3 8"/></svg>',
        'truck'     => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>',
        'award'     => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526"/><circle cx="12" cy="8" r="6"/></svg>',
        'globe'     => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>',
        'clock'     => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
        'users'     => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 21a8 8 0 0 0-16 0"/><circle cx="10" cy="8" r="5"/><path d="M22 20c0-3.37-2-6.5-4-8a5 5 0 0 0-.45-8.3"/></svg>',
        'gem'       => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h12l4 6-10 13L2 9Z"/><path d="M11 3 8 9l4 13 4-13-3-6"/><path d="M2 9h20"/></svg>',
    );

    /**
     * Inicjalizacja
     */
    public static function init() {
        add_filter( 'page_template', array( __CLASS__, 'maybe_force_about_template' ) );
    }

    /**
     * Wymuszaj szablon „O nas" dla przypisanej strony
     */
    public static function maybe_force_about_template( $template ) {
        $about_id = absint( get_option( 'fc_page_o-nas', 0 ) );
        if ( $about_id && is_page( $about_id ) ) {
            $about_tpl = get_template_directory() . '/template-about.php';
            if ( file_exists( $about_tpl ) ) {
                return $about_tpl;
            }
        }
        return $template;
    }

    /* =================================================================
     *  Customizer — rejestracja sekcji i kontrolek
     * ================================================================= */

    public static function customize_register( $wp_customize ) {

        // =============================================================
        //  Sekcja: Strona o nas
        // =============================================================
        $wp_customize->add_section( 'flavor_about_info', array(
            'title'       => fc__( 'cust_about_info', 'admin' ),
            'description' => fc__( 'cust_about_info_desc', 'admin' ),
            'panel'       => 'flavor_panel',
            'priority'    => 65,
        ) );

        // =============================================================
        //  Settings — JSON data for each section
        // =============================================================
        $wp_customize->add_setting( 'flavor_about_hero_image', array(
            'default'           => '',
            'transport'         => 'refresh',
            'sanitize_callback' => 'esc_url_raw',
        ) );
        $wp_customize->add_setting( 'flavor_about_hero_bg_mode', array(
            'default'           => 'custom',
            'transport'         => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_setting( 'flavor_about_hero_bg_variant', array(
            'default'           => 'light',
            'transport'         => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_setting( 'flavor_about_hero_image_position', array(
            'default'           => 'center center',
            'transport'         => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_setting( 'flavor_about_hero_overlay', array(
            'default'           => true,
            'transport'         => 'refresh',
            'sanitize_callback' => function( $val ) { return (bool) $val; },
        ) );
        $wp_customize->add_setting( 'flavor_about_hero_overlay_color', array(
            'default'           => 'rgba(0,0,0,0.45)',
            'transport'         => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_setting( 'flavor_about_hero_subtitle', array(
            'default'           => '',
            'transport'         => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_setting( 'flavor_about_hero_text_align', array(
            'default'           => 'center',
            'transport'         => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_setting( 'flavor_about_team_title', array(
            'default'           => '',
            'transport'         => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_setting( 'flavor_about_team', array(
            'default'           => '[]',
            'transport'         => 'refresh',
            'sanitize_callback' => array( __CLASS__, 'sanitize_json_array' ),
        ) );
        $wp_customize->add_setting( 'flavor_about_values_title', array(
            'default'           => '',
            'transport'         => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_setting( 'flavor_about_values', array(
            'default'           => '[]',
            'transport'         => 'refresh',
            'sanitize_callback' => array( __CLASS__, 'sanitize_json_array' ),
        ) );
        $wp_customize->add_setting( 'flavor_about_stats', array(
            'default'           => '[]',
            'transport'         => 'refresh',
            'sanitize_callback' => array( __CLASS__, 'sanitize_json_array' ),
        ) );
        $wp_customize->add_setting( 'flavor_about_testimonials_title', array(
            'default'           => '',
            'transport'         => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_setting( 'flavor_about_testimonials', array(
            'default'           => '[]',
            'transport'         => 'refresh',
            'sanitize_callback' => array( __CLASS__, 'sanitize_json_array' ),
        ) );
        $wp_customize->add_setting( 'flavor_about_timeline_title', array(
            'default'           => '',
            'transport'         => 'refresh',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_setting( 'flavor_about_timeline', array(
            'default'           => '[]',
            'transport'         => 'refresh',
            'sanitize_callback' => array( __CLASS__, 'sanitize_json_array' ),
        ) );

        // =============================================================
        //  Kolejność sekcji (drag & drop) + inline pola
        // =============================================================
        $wp_customize->add_setting( 'flavor_about_sections_order', array(
            'default'           => 'hero,content,values,stats,team,testimonials,timeline',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( new Flavor_Sortable_Cards_Control( $wp_customize, 'flavor_about_sections_order', array(
            'label'       => fc__( 'cust_about_sections_order', 'admin' ),
            'description' => fc__( 'cust_about_sections_order_desc', 'admin' ),
            'section'     => 'flavor_about_info',
            'cards'       => array(
                'hero' => array(
                    'label'  => fc__( 'cust_about_hero', 'admin' ),
                    'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>',
                    'fields' => function() {
                        self::render_hero_fields();
                    },
                ),
                'content' => array(
                    'label'  => fc__( 'cust_about_content', 'admin' ),
                    'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>',
                    'fields' => function() {
                        ?>
                        <p class="fc-card-field" style="font-size:12px;color:#888;margin:0">
                            <?php echo esc_html( fc__( 'cust_about_content_hint', 'admin' ) ); ?>
                        </p>
                        <?php
                    },
                ),
                'team' => array(
                    'label'  => fc__( 'cust_about_team', 'admin' ),
                    'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 21a8 8 0 0 0-16 0"/><circle cx="10" cy="8" r="5"/><path d="M22 20c0-3.37-2-6.5-4-8a5 5 0 0 0-.45-8.3"/></svg>',
                    'fields' => function() {
                        self::render_title_field( 'flavor_about_team_title', 'cust_about_team_title' );
                        self::render_repeater_inline( 'flavor_about_team', array(
                            array( 'key' => 'name',      'type' => 'text',  'label' => fc__( 'cust_about_team_name', 'admin' ) ),
                            array( 'key' => 'role',      'type' => 'text',  'label' => fc__( 'cust_about_team_role', 'admin' ) ),
                            array( 'key' => 'image_url', 'type' => 'image', 'label' => fc__( 'cust_about_team_photo', 'admin' ), 'btn' => fc__( 'cust_about_team_photo_btn', 'admin' ) ),
                        ), fc__( 'cust_about_team_add', 'admin' ) );
                    },
                ),
                'values' => array(
                    'label'  => fc__( 'cust_about_values', 'admin' ),
                    'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
                    'fields' => function() {
                        self::render_title_field( 'flavor_about_values_title', 'cust_about_values_title' );
                        self::render_repeater_inline( 'flavor_about_values', array(
                            array( 'key' => 'icon',  'type' => 'icon_select', 'label' => fc__( 'cust_about_values_icon', 'admin' ) ),
                            array( 'key' => 'title', 'type' => 'text',        'label' => fc__( 'cust_about_values_name', 'admin' ) ),
                            array( 'key' => 'desc',  'type' => 'textarea',    'label' => fc__( 'cust_about_values_desc', 'admin' ) ),
                        ), fc__( 'cust_about_values_add', 'admin' ) );
                    },
                ),
                'stats' => array(
                    'label'  => fc__( 'cust_about_stats', 'admin' ),
                    'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>',
                    'fields' => function() {
                        self::render_repeater_inline( 'flavor_about_stats', array(
                            array( 'key' => 'number', 'type' => 'text', 'label' => fc__( 'cust_about_stats_number', 'admin' ), 'width' => '60px' ),
                            array( 'key' => 'suffix', 'type' => 'text', 'label' => fc__( 'cust_about_stats_suffix', 'admin' ), 'width' => '50px' ),
                            array( 'key' => 'label',  'type' => 'text', 'label' => fc__( 'cust_about_stats_label', 'admin' ) ),
                        ), fc__( 'cust_about_stats_add', 'admin' ) );
                    },
                ),
                'testimonials' => array(
                    'label'  => fc__( 'cust_about_testimonials', 'admin' ),
                    'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>',
                    'fields' => function() {
                        self::render_title_field( 'flavor_about_testimonials_title', 'cust_about_testimonials_title' );
                        self::render_repeater_inline( 'flavor_about_testimonials', array(
                            array( 'key' => 'quote',   'type' => 'textarea',       'label' => fc__( 'cust_about_testimonials_quote', 'admin' ) ),
                            array( 'key' => 'author',  'type' => 'text',            'label' => fc__( 'cust_about_testimonials_author', 'admin' ) ),
                            array( 'key' => 'role',    'type' => 'text',            'label' => fc__( 'cust_about_testimonials_role', 'admin' ) ),
                            array( 'key' => 'country', 'type' => 'country_select',  'label' => fc__( 'cust_about_testimonials_country', 'admin' ), 'width' => '120px' ),
                        ), fc__( 'cust_about_testimonials_add', 'admin' ) );
                    },
                ),
                'timeline' => array(
                    'label'  => fc__( 'cust_about_timeline', 'admin' ),
                    'icon'   => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
                    'fields' => function() {
                        self::render_title_field( 'flavor_about_timeline_title', 'cust_about_timeline_title' );
                        self::render_repeater_inline( 'flavor_about_timeline', array(
                            array( 'key' => 'year',  'type' => 'text',     'label' => fc__( 'cust_about_timeline_year', 'admin' ), 'width' => '70px' ),
                            array( 'key' => 'title', 'type' => 'text',     'label' => fc__( 'cust_about_timeline_name', 'admin' ) ),
                            array( 'key' => 'desc',  'type' => 'textarea', 'label' => fc__( 'cust_about_timeline_desc', 'admin' ) ),
                        ), fc__( 'cust_about_timeline_add', 'admin' ) );
                    },
                ),
            ),
        ) ) );
    }

    /* =================================================================
     *  Helper: render section title field
     * ================================================================= */

    private static function render_title_field( $setting_id, $label_key ) {
        $val = get_theme_mod( $setting_id, '' );
        ?>
        <div class="fc-card-field">
            <label style="display:block;font-size:11px;font-weight:600;margin-bottom:3px">
                <?php echo esc_html( fc__( $label_key, 'admin' ) ); ?>
            </label>
            <input type="text"
                   data-customize-setting-link="<?php echo esc_attr( $setting_id ); ?>"
                   value="<?php echo esc_attr( $val ); ?>"
                   style="width:100%;box-sizing:border-box;font-size:12px"
                   placeholder="<?php echo esc_attr( fc__( $label_key, 'admin' ) ); ?>">
        </div>
        <?php
    }

    /* =================================================================
     *  Helper: render hero banner fields (image + subtitle)
     * ================================================================= */

    private static function render_hero_fields() {
        $bg_mode    = get_theme_mod( 'flavor_about_hero_bg_mode', 'custom' );
        $bg_variant = get_theme_mod( 'flavor_about_hero_bg_variant', 'light' );
        $image      = get_theme_mod( 'flavor_about_hero_image', '' );
        $position   = get_theme_mod( 'flavor_about_hero_image_position', 'center center' );
        $overlay    = get_theme_mod( 'flavor_about_hero_overlay', true );
        $ov_color   = get_theme_mod( 'flavor_about_hero_overlay_color', 'rgba(0,0,0,0.45)' );
        $subtitle   = get_theme_mod( 'flavor_about_hero_subtitle', '' );
        $text_align = get_theme_mod( 'flavor_about_hero_text_align', 'center' );
        $uid        = 'fc-about-hero-' . wp_rand();
        ?>
        <!-- Background mode selector -->
        <div class="fc-card-field">
            <label style="display:block;font-size:11px;font-weight:600;margin-bottom:3px">
                <?php echo esc_html( fc__( 'cust_about_hero_bg_mode', 'admin' ) ); ?>
            </label>
            <select class="fc-hero-bg-mode" data-customize-setting-link="flavor_about_hero_bg_mode" style="width:100%;box-sizing:border-box;font-size:12px">
                <?php
                $modes = array(
                    'custom'            => fc__( 'cust_about_hero_bg_custom', 'admin' ),
                    'pattern-hexagons'  => fc__( 'cust_about_hero_bg_hexagons', 'admin' ),
                    'pattern-waves'     => fc__( 'cust_about_hero_bg_waves', 'admin' ),
                    'pattern-circles'   => fc__( 'cust_about_hero_bg_circles', 'admin' ),
                    'pattern-grid'      => fc__( 'cust_about_hero_bg_grid', 'admin' ),
                    'pattern-diagonal'  => fc__( 'cust_about_hero_bg_diagonal', 'admin' ),
                );
                foreach ( $modes as $val => $label ) :
                ?>
                    <option value="<?php echo esc_attr( $val ); ?>" <?php selected( $bg_mode, $val ); ?>><?php echo esc_html( $label ); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Pattern variant: light / dark -->
        <div class="fc-card-field fc-hero-pattern-fields" style="margin-top:8px;<?php echo $bg_mode === 'custom' ? 'display:none' : ''; ?>">
            <label style="display:block;font-size:11px;font-weight:600;margin-bottom:3px">
                <?php echo esc_html( fc__( 'cust_about_hero_bg_variant', 'admin' ) ); ?>
            </label>
            <select data-customize-setting-link="flavor_about_hero_bg_variant" style="width:100%;box-sizing:border-box;font-size:12px">
                <option value="light" <?php selected( $bg_variant, 'light' ); ?>><?php echo esc_html( fc__( 'cust_about_hero_bg_light', 'admin' ) ); ?></option>
                <option value="dark" <?php selected( $bg_variant, 'dark' ); ?>><?php echo esc_html( fc__( 'cust_about_hero_bg_dark', 'admin' ) ); ?></option>
            </select>
        </div>

        <!-- Custom image fields -->
        <div class="fc-card-field fc-about-hero-field fc-hero-custom-fields" id="<?php echo esc_attr( $uid ); ?>" style="margin-top:8px;<?php echo $bg_mode !== 'custom' ? 'display:none' : ''; ?>">
            <label style="display:block;font-size:11px;font-weight:600;margin-bottom:3px">
                <?php echo esc_html( fc__( 'cust_about_hero_image', 'admin' ) ); ?>
            </label>
            <div class="fc-about-hero-preview" style="margin-bottom:6px;<?php echo $image ? '' : 'display:none'; ?>">
                <img src="<?php echo esc_url( $image ); ?>" style="max-width:100%;height:auto;border-radius:4px;display:block">
            </div>
            <input type="hidden" data-customize-setting-link="flavor_about_hero_image" value="<?php echo esc_attr( $image ); ?>">
            <button type="button" class="fc-about-hero-choose" style="font-size:12px;padding:4px 10px;cursor:pointer;background:#f0f0f1;border:1px solid #c3c4c7;border-radius:3px;color:#2271b1">
                <?php echo esc_html( fc__( 'cust_about_hero_image_btn', 'admin' ) ); ?>
            </button>
            <button type="button" class="fc-about-hero-remove" style="font-size:12px;padding:4px 10px;cursor:pointer;background:none;border:1px solid #c3c4c7;border-radius:3px;color:#a00;<?php echo $image ? '' : 'display:none'; ?>">
                <?php echo esc_html( fc__( 'cust_about_hero_image_remove', 'admin' ) ); ?>
            </button>
        </div>
        <div class="fc-card-field fc-hero-custom-fields" style="margin-top:8px;<?php echo $bg_mode !== 'custom' ? 'display:none' : ''; ?>">
            <label style="display:block;font-size:11px;font-weight:600;margin-bottom:3px">
                <?php echo esc_html( fc__( 'cust_about_hero_image_position', 'admin' ) ); ?>
            </label>
            <select data-customize-setting-link="flavor_about_hero_image_position" style="width:100%;box-sizing:border-box;font-size:12px">
                <?php
                $positions = array(
                    'center top'    => fc__( 'cust_about_hero_pos_top', 'admin' ),
                    'center center' => fc__( 'cust_about_hero_pos_center', 'admin' ),
                    'center bottom' => fc__( 'cust_about_hero_pos_bottom', 'admin' ),
                    'left top'      => fc__( 'cust_about_hero_pos_left_top', 'admin' ),
                    'left center'   => fc__( 'cust_about_hero_pos_left_center', 'admin' ),
                    'left bottom'   => fc__( 'cust_about_hero_pos_left_bottom', 'admin' ),
                    'right top'     => fc__( 'cust_about_hero_pos_right_top', 'admin' ),
                    'right center'  => fc__( 'cust_about_hero_pos_right_center', 'admin' ),
                    'right bottom'  => fc__( 'cust_about_hero_pos_right_bottom', 'admin' ),
                );
                foreach ( $positions as $val => $label ) :
                ?>
                    <option value="<?php echo esc_attr( $val ); ?>" <?php selected( $position, $val ); ?>><?php echo esc_html( $label ); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="fc-card-field" style="margin-top:8px">
            <label style="display:block;font-size:11px;font-weight:600;margin-bottom:3px">
                <?php echo esc_html( fc__( 'cust_about_hero_subtitle', 'admin' ) ); ?>
            </label>
            <input type="text"
                   data-customize-setting-link="flavor_about_hero_subtitle"
                   value="<?php echo esc_attr( $subtitle ); ?>"
                   style="width:100%;box-sizing:border-box;font-size:12px">
        </div>
        <div class="fc-card-field" style="margin-top:8px">
            <label style="display:block;font-size:11px;font-weight:600;margin-bottom:3px">
                <?php echo esc_html( fc__( 'cust_about_hero_text_align', 'admin' ) ); ?>
            </label>
            <select data-customize-setting-link="flavor_about_hero_text_align" style="width:100%;box-sizing:border-box;font-size:12px">
                <?php
                $aligns = array(
                    'left'   => fc__( 'cust_about_hero_align_left', 'admin' ),
                    'center' => fc__( 'cust_about_hero_align_center', 'admin' ),
                    'right'  => fc__( 'cust_about_hero_align_right', 'admin' ),
                );
                foreach ( $aligns as $val => $label ) :
                ?>
                    <option value="<?php echo esc_attr( $val ); ?>" <?php selected( $text_align, $val ); ?>><?php echo esc_html( $label ); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="fc-card-field" style="margin-top:10px">
            <label style="display:flex;align-items:center;gap:6px;font-size:11px;font-weight:600;cursor:pointer">
                <input type="checkbox" data-customize-setting-link="flavor_about_hero_overlay" <?php checked( $overlay ); ?>>
                <?php echo esc_html( fc__( 'cust_about_hero_overlay', 'admin' ) ); ?>
            </label>
        </div>
        <div class="fc-card-field fc-about-hero-ov-color-wrap" style="margin-top:6px;<?php echo $overlay ? '' : 'display:none'; ?>">
            <label style="display:block;font-size:11px;font-weight:600;margin-bottom:3px">
                <?php echo esc_html( fc__( 'cust_about_hero_overlay_color', 'admin' ) ); ?>
            </label>
            <input type="text" class="fc-about-hero-ov-color" data-customize-setting-link="flavor_about_hero_overlay_color" value="<?php echo esc_attr( $ov_color ); ?>" style="width:100%;box-sizing:border-box;font-size:12px">
        </div>
        <script>
        (function(){
            var wrap = document.getElementById('<?php echo esc_js( $uid ); ?>');
            if (!wrap) return;
            var hidden  = wrap.querySelector('input[type="hidden"]');
            var preview = wrap.querySelector('.fc-about-hero-preview');
            var img     = preview.querySelector('img');
            var removeBtn = wrap.querySelector('.fc-about-hero-remove');

            wrap.querySelector('.fc-about-hero-choose').addEventListener('click', function(e) {
                e.preventDefault();
                var frame = wp.media({ title: '<?php echo esc_js( fc__( 'cust_about_hero_image', 'admin' ) ); ?>', multiple: false, library: { type: 'image' } });
                frame.on('select', function() {
                    var url = frame.state().get('selection').first().toJSON().url;
                    hidden.value = url;
                    img.src = url;
                    preview.style.display = '';
                    removeBtn.style.display = '';
                    hidden.dispatchEvent(new Event('change', { bubbles: true }));
                });
                frame.open();
            });

            removeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                hidden.value = '';
                preview.style.display = 'none';
                removeBtn.style.display = 'none';
                hidden.dispatchEvent(new Event('change', { bubbles: true }));
            });

            /* Overlay toggle → show/hide color field */
            var section = wrap.closest('.fc-card-body') || wrap.parentNode;
            var ovCheckbox  = section.querySelector('[data-customize-setting-link="flavor_about_hero_overlay"]');
            var ovColorWrap = section.querySelector('.fc-about-hero-ov-color-wrap');
            if (ovCheckbox && ovColorWrap) {
                ovCheckbox.addEventListener('change', function() {
                    ovColorWrap.style.display = this.checked ? '' : 'none';
                });
            }

            /* Background mode toggle → show/hide custom vs pattern fields */
            var modeSelect     = section.querySelector('.fc-hero-bg-mode');
            var customFields   = section.querySelectorAll('.fc-hero-custom-fields');
            var patternFields  = section.querySelectorAll('.fc-hero-pattern-fields');
            if (modeSelect) {
                modeSelect.addEventListener('change', function() {
                    var isCustom = this.value === 'custom';
                    customFields.forEach(function(el)  { el.style.display = isCustom ? '' : 'none'; });
                    patternFields.forEach(function(el) { el.style.display = isCustom ? 'none' : ''; });
                });
            }
        })();
        </script>
        <?php
    }

    /* =================================================================
     *  Generic repeater renderer (inline in sortable card body)
     * ================================================================= */

    /**
     * Render a generic repeater control inside a sortable card.
     *
     * @param string $setting_id   Customizer setting ID (JSON string).
     * @param array  $fields       Field definitions: [ ['key'=>..., 'type'=>'text|textarea|image|icon_select', 'label'=>..., 'width'=>''] ]
     * @param string $add_label    Label for the "Add" button.
     */
    private static function render_repeater_inline( $setting_id, $fields, $add_label ) {
        $value = get_theme_mod( $setting_id, '[]' );
        $items = json_decode( $value, true );
        if ( ! is_array( $items ) ) {
            $items = array();
        }

        $uid         = 'fc-rep-' . sanitize_key( $setting_id ) . '-' . wp_rand();
        $remove_label = fc__( 'cust_about_remove_item', 'admin' );

        // Build JS fields config
        $js_fields = array();
        foreach ( $fields as $f ) {
            $js_fields[] = array(
                'key'   => $f['key'],
                'type'  => $f['type'],
                'label' => $f['label'],
                'width' => $f['width'] ?? '',
                'btn'   => $f['btn'] ?? '',
            );
        }

        // Icon options for icon_select type
        $icon_keys = array_keys( self::$value_icons );

        // Country options for country_select type
        $country_options = array();
        foreach ( self::$countries as $code => $name ) {
            $flag = $code ? self::country_flag( $code ) : '';
            $country_options[] = array( 'code' => $code, 'label' => $flag ? $flag . ' ' . $name : $name );
        }
        ?>
        <div class="fc-about-repeater" id="<?php echo esc_attr( $uid ); ?>">
            <div class="fc-about-repeater-items">
                <?php foreach ( $items as $item ) : ?>
                <div class="fc-about-repeater-item">
                    <?php foreach ( $fields as $f ) : ?>
                    <div class="fc-about-repeater-field" style="<?php echo ! empty( $f['width'] ) ? 'max-width:' . esc_attr( $f['width'] ) : ''; ?>">
                        <label style="font-size:10px;color:#888;display:block"><?php echo esc_html( $f['label'] ); ?></label>
                        <?php if ( $f['type'] === 'text' ) : ?>
                            <input type="text" class="fc-rep-input" data-key="<?php echo esc_attr( $f['key'] ); ?>"
                                   value="<?php echo esc_attr( $item[ $f['key'] ] ?? '' ); ?>"
                                   style="width:100%;box-sizing:border-box;font-size:12px;padding:3px 6px">
                        <?php elseif ( $f['type'] === 'textarea' ) : ?>
                            <textarea class="fc-rep-input" data-key="<?php echo esc_attr( $f['key'] ); ?>"
                                      rows="2" style="width:100%;box-sizing:border-box;font-size:12px;padding:3px 6px;resize:vertical"><?php echo esc_textarea( $item[ $f['key'] ] ?? '' ); ?></textarea>
                        <?php elseif ( $f['type'] === 'image' ) : ?>
                            <div class="fc-rep-image-wrap">
                                <?php $img_url = $item[ $f['key'] ] ?? ''; ?>
                                <img src="<?php echo esc_url( $img_url ); ?>" class="fc-rep-img-preview" style="max-width:60px;height:60px;object-fit:cover;border-radius:50%;display:<?php echo $img_url ? 'block' : 'none'; ?>;margin-bottom:4px">
                                <input type="hidden" class="fc-rep-input" data-key="<?php echo esc_attr( $f['key'] ); ?>" value="<?php echo esc_attr( $img_url ); ?>">
                                <button type="button" class="fc-rep-img-btn" style="font-size:11px;padding:2px 8px;cursor:pointer;background:#f0f0f1;border:1px solid #c3c4c7;border-radius:3px;color:#2271b1"><?php echo esc_html( $f['btn'] ?? fc__( 'cust_about_hero_image_btn', 'admin' ) ); ?></button>
                            </div>
                        <?php elseif ( $f['type'] === 'icon_select' ) : ?>
                            <select class="fc-rep-input" data-key="<?php echo esc_attr( $f['key'] ); ?>" style="font-size:12px;padding:3px;width:100%">
                                <?php foreach ( $icon_keys as $ik ) : ?>
                                    <option value="<?php echo esc_attr( $ik ); ?>" <?php selected( $item[ $f['key'] ] ?? 'star', $ik ); ?>><?php echo esc_html( $ik ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php elseif ( $f['type'] === 'country_select' ) : ?>
                            <select class="fc-rep-input" data-key="<?php echo esc_attr( $f['key'] ); ?>" style="font-size:12px;padding:3px;width:100%">
                                <?php foreach ( self::$countries as $ccode => $cname ) :
                                    $cflag = $ccode ? self::country_flag( $ccode ) : ''; ?>
                                    <option value="<?php echo esc_attr( $ccode ); ?>" <?php selected( $item[ $f['key'] ] ?? '', $ccode ); ?>><?php echo $cflag ? esc_html( $cflag . ' ' . $cname ) : esc_html( $cname ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                    <button type="button" class="fc-about-repeater-remove" title="<?php echo esc_attr( $remove_label ); ?>">&times;</button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="fc-about-repeater-add">+ <?php echo esc_html( $add_label ); ?></button>
            <input type="hidden" data-customize-setting-link="<?php echo esc_attr( $setting_id ); ?>" value="<?php echo esc_attr( $value ); ?>">
        </div>

        <script>
        (function(){
            var wrap      = document.getElementById('<?php echo esc_js( $uid ); ?>');
            if (!wrap) return;
            var container = wrap.querySelector('.fc-about-repeater-items');
            var hidden    = wrap.querySelector('input[type="hidden"][data-customize-setting-link]');
            var FIELDS    = <?php echo wp_json_encode( $js_fields ); ?>;
            var ICONS     = <?php echo wp_json_encode( $icon_keys ); ?>;
            var COUNTRIES = <?php echo wp_json_encode( $country_options ); ?>;
            var REMOVE    = <?php echo wp_json_encode( $remove_label ); ?>;

            function serialize() {
                var items = [];
                container.querySelectorAll('.fc-about-repeater-item').forEach(function(row) {
                    var obj = {};
                    row.querySelectorAll('.fc-rep-input').forEach(function(inp) {
                        obj[inp.getAttribute('data-key')] = inp.value;
                    });
                    items.push(obj);
                });
                hidden.value = JSON.stringify(items);
                hidden.dispatchEvent(new Event('change', { bubbles: true }));
            }

            function buildItem(data) {
                data = data || {};
                var item = document.createElement('div');
                item.className = 'fc-about-repeater-item';
                var html = '';
                FIELDS.forEach(function(f) {
                    var val = data[f.key] || '';
                    var style = f.width ? 'max-width:' + f.width : '';
                    html += '<div class="fc-about-repeater-field" style="' + style + '">';
                    html += '<label style="font-size:10px;color:#888;display:block">' + f.label + '</label>';
                    if (f.type === 'text') {
                        html += '<input type="text" class="fc-rep-input" data-key="' + f.key + '" value="' + escAttr(val) + '" style="width:100%;box-sizing:border-box;font-size:12px;padding:3px 6px">';
                    } else if (f.type === 'textarea') {
                        html += '<textarea class="fc-rep-input" data-key="' + f.key + '" rows="2" style="width:100%;box-sizing:border-box;font-size:12px;padding:3px 6px;resize:vertical">' + escHtml(val) + '</textarea>';
                    } else if (f.type === 'image') {
                        html += '<div class="fc-rep-image-wrap">';
                        html += '<img src="' + escAttr(val) + '" class="fc-rep-img-preview" style="max-width:60px;height:60px;object-fit:cover;border-radius:50%;display:' + (val ? 'block' : 'none') + ';margin-bottom:4px">';
                        html += '<input type="hidden" class="fc-rep-input" data-key="' + f.key + '" value="' + escAttr(val) + '">';
                        html += '<button type="button" class="fc-rep-img-btn" style="font-size:11px;padding:2px 8px;cursor:pointer;background:#f0f0f1;border:1px solid #c3c4c7;border-radius:3px;color:#2271b1">' + (f.btn || '📷') + '</button>';
                        html += '</div>';
                    } else if (f.type === 'icon_select') {
                        html += '<select class="fc-rep-input" data-key="' + f.key + '" style="font-size:12px;padding:3px;width:100%">';
                        ICONS.forEach(function(ik) {
                            html += '<option value="' + ik + '"' + (val === ik ? ' selected' : '') + '>' + ik + '</option>';
                        });
                        html += '</select>';
                    } else if (f.type === 'country_select') {
                        html += '<select class="fc-rep-input" data-key="' + f.key + '" style="font-size:12px;padding:3px;width:100%">';
                        COUNTRIES.forEach(function(c) {
                            html += '<option value="' + c.code + '"' + (val === c.code ? ' selected' : '') + '>' + escHtml(c.label) + '</option>';
                        });
                        html += '</select>';
                    }
                    html += '</div>';
                });
                html += '<button type="button" class="fc-about-repeater-remove" title="' + REMOVE + '">&times;</button>';
                item.innerHTML = html;
                bindItem(item);
                return item;
            }

            function bindItem(item) {
                item.querySelectorAll('.fc-rep-input').forEach(function(inp) {
                    inp.addEventListener('change', serialize);
                    inp.addEventListener('input', serialize);
                });
                item.querySelector('.fc-about-repeater-remove').addEventListener('click', function() {
                    item.remove();
                    serialize();
                });
                // Image picker buttons
                item.querySelectorAll('.fc-rep-img-btn').forEach(function(btn) {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        var imgWrap = btn.closest('.fc-rep-image-wrap');
                        var inp     = imgWrap.querySelector('.fc-rep-input');
                        var prev    = imgWrap.querySelector('.fc-rep-img-preview');
                        var frame   = wp.media({ multiple: false, library: { type: 'image' } });
                        frame.on('select', function() {
                            var url = frame.state().get('selection').first().toJSON().url;
                            inp.value = url;
                            prev.src = url;
                            prev.style.display = 'block';
                            serialize();
                        });
                        frame.open();
                    });
                });
            }

            // Bind existing items
            container.querySelectorAll('.fc-about-repeater-item').forEach(bindItem);

            // Add button
            wrap.querySelector('.fc-about-repeater-add').addEventListener('click', function() {
                var defaults = {};
                FIELDS.forEach(function(f) {
                    defaults[f.key] = f.type === 'icon_select' ? (ICONS[0] || 'star') : '';
                });
                container.appendChild(buildItem(defaults));
                serialize();
            });

            function escAttr(s) { var d = document.createElement('div'); d.textContent = s; return d.innerHTML.replace(/"/g, '&quot;'); }
            function escHtml(s) { var d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
        })();
        </script>
        <?php
    }

    /* =================================================================
     *  Sanitize callback for JSON arrays
     * ================================================================= */

    public static function sanitize_json_array( $val ) {
        $decoded = json_decode( $val, true );
        if ( ! is_array( $decoded ) ) {
            return '[]';
        }
        $clean = array();
        foreach ( $decoded as $item ) {
            if ( ! is_array( $item ) ) continue;
            $row = array();
            foreach ( $item as $k => $v ) {
                $row[ sanitize_key( $k ) ] = sanitize_text_field( $v );
            }
            $clean[] = $row;
        }
        return wp_json_encode( $clean );
    }

    /* =================================================================
     *  Getters — pobieranie danych sekcji z Customizera
     * ================================================================= */

    /**
     * Get section order as array
     */
    public static function get_sections_order() {
        $default = 'hero,content,values,stats,team,testimonials,timeline';
        $order   = get_theme_mod( 'flavor_about_sections_order', $default );
        $parts   = array_filter( array_map( 'trim', explode( ',', $order ) ) );
        $all     = array( 'hero', 'content', 'values', 'stats', 'team', 'testimonials', 'timeline' );
        foreach ( $all as $s ) {
            if ( ! in_array( $s, $parts, true ) ) {
                $parts[] = $s;
            }
        }
        return $parts;
    }

    /**
     * Get hero data
     */
    public static function get_hero() {
        return array(
            'image'         => get_theme_mod( 'flavor_about_hero_image', '' ),
            'bg_mode'       => get_theme_mod( 'flavor_about_hero_bg_mode', 'custom' ),
            'bg_variant'    => get_theme_mod( 'flavor_about_hero_bg_variant', 'light' ),
            'position'      => get_theme_mod( 'flavor_about_hero_image_position', 'center center' ),
            'subtitle'      => get_theme_mod( 'flavor_about_hero_subtitle', '' ) ?: fc__( 'about_hero_default_subtitle', 'frontend' ),
            'text_align'    => get_theme_mod( 'flavor_about_hero_text_align', 'center' ),
            'overlay'       => get_theme_mod( 'flavor_about_hero_overlay', true ),
            'overlay_color' => get_theme_mod( 'flavor_about_hero_overlay_color', 'rgba(0,0,0,0.45)' ),
        );
    }

    /**
     * Generate inline SVG pattern for hero background.
     *
     * @param  string $pattern  Pattern name (hexagons|waves|circles|grid|diagonal).
     * @param  string $variant  'light' or 'dark'.
     * @return string           Inline CSS for background property.
     */
    public static function get_hero_pattern_css( $pattern, $variant = 'light' ) {
        // Read accent color from Customizer (or fallback).
        $accent = get_theme_mod( 'flavor_color_accent', '#4a90d9' );

        // Convert hex to RGB for SVG.
        list( $r, $g, $b ) = sscanf( $accent, '#%02x%02x%02x' );

        if ( $variant === 'dark' ) {
            $bg    = '#1a1a2e';
            $fg1   = "rgba({$r},{$g},{$b},0.25)";
            $fg2   = "rgba({$r},{$g},{$b},0.12)";
            $fg3   = "rgba(255,255,255,0.06)";
        } else {
            $bg    = '#f8f9fc';
            $fg1   = "rgba({$r},{$g},{$b},0.18)";
            $fg2   = "rgba({$r},{$g},{$b},0.08)";
            $fg3   = "rgba(0,0,0,0.04)";
        }

        switch ( $pattern ) {
            case 'hexagons':
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="56" height="100">'
                     . '<path d="M28 66L0 50L0 16L28 0L56 16L56 50Z" fill="none" stroke="' . $fg1 . '" stroke-width="1"/>'
                     . '<path d="M28 100L0 84L0 50L28 34L56 50L56 84Z" fill="none" stroke="' . $fg2 . '" stroke-width="0.5"/>'
                     . '</svg>';
                break;

            case 'waves':
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="120" height="40">'
                     . '<path d="M0 20 Q15 0,30 20 Q45 40,60 20 Q75 0,90 20 Q105 40,120 20" fill="none" stroke="' . $fg1 . '" stroke-width="1.5"/>'
                     . '<path d="M0 30 Q15 10,30 30 Q45 50,60 30 Q75 10,90 30 Q105 50,120 30" fill="none" stroke="' . $fg2 . '" stroke-width="1"/>'
                     . '</svg>';
                break;

            case 'circles':
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="80" height="80">'
                     . '<circle cx="40" cy="40" r="24" fill="none" stroke="' . $fg1 . '" stroke-width="1"/>'
                     . '<circle cx="0"  cy="0"  r="16" fill="none" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<circle cx="80" cy="80" r="16" fill="none" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<circle cx="40" cy="40" r="4"  fill="' . $fg3 . '"/>'
                     . '</svg>';
                break;

            case 'grid':
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40">'
                     . '<rect width="40" height="40" fill="none"/>'
                     . '<path d="M40 0H0V40" fill="none" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     . '<circle cx="20" cy="20" r="2" fill="' . $fg1 . '"/>'
                     . '<circle cx="0"  cy="0"  r="1.5" fill="' . $fg2 . '"/>'
                     . '</svg>';
                break;

            case 'diagonal':
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40">'
                     . '<path d="M-10,10 l20,-20 M0,40 l40,-40 M30,50 l20,-20" stroke="' . $fg1 . '" stroke-width="1"/>'
                     . '<path d="M-10,30 l20,-20 M20,50 l20,-20" stroke="' . $fg2 . '" stroke-width="0.5"/>'
                     . '</svg>';
                break;

            default:
                return "background:{$bg}";
        }

        $encoded = 'data:image/svg+xml,' . rawurlencode( $svg );

        return "background-color:{$bg};background-image:url(\"{$encoded}\");background-repeat:repeat;background-size:auto";
    }

    /**
     * Get team members
     */
    public static function get_team() {
        $title = get_theme_mod( 'flavor_about_team_title', '' );
        $json  = get_theme_mod( 'flavor_about_team', '[]' );
        $items = json_decode( $json, true );
        return array(
            'title' => $title ? $title : fc__( 'about_team_title' ),
            'items' => is_array( $items ) ? $items : array(),
        );
    }

    /**
     * Get values
     */
    public static function get_values() {
        $title = get_theme_mod( 'flavor_about_values_title', '' );
        $json  = get_theme_mod( 'flavor_about_values', '[]' );
        $items = json_decode( $json, true );
        return array(
            'title' => $title ? $title : fc__( 'about_values_title' ),
            'items' => is_array( $items ) ? $items : array(),
        );
    }

    /**
     * Get stats
     */
    public static function get_stats() {
        $json  = get_theme_mod( 'flavor_about_stats', '[]' );
        $items = json_decode( $json, true );
        return is_array( $items ) ? $items : array();
    }

    /**
     * Get testimonials
     */
    public static function get_testimonials() {
        $title = get_theme_mod( 'flavor_about_testimonials_title', '' );
        $json  = get_theme_mod( 'flavor_about_testimonials', '[]' );
        $items = json_decode( $json, true );
        return array(
            'title' => $title ? $title : fc__( 'about_testimonials_title' ),
            'items' => is_array( $items ) ? $items : array(),
        );
    }

    /**
     * Get timeline
     */
    public static function get_timeline() {
        $title = get_theme_mod( 'flavor_about_timeline_title', '' );
        $json  = get_theme_mod( 'flavor_about_timeline', '[]' );
        $items = json_decode( $json, true );
        return array(
            'title' => $title ? $title : fc__( 'about_timeline_title' ),
            'items' => is_array( $items ) ? $items : array(),
        );
    }

    /**
     * Get value icon SVG by key
     */
    public static function get_value_icon( $key ) {
        return self::$value_icons[ $key ] ?? self::$value_icons['star'];
    }
}

Flavor_About::init();
add_action( 'customize_register', array( 'Flavor_About', 'customize_register' ), 1002 );
