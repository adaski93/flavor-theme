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
        $wp_customize->add_setting( 'flavor_about_hero_height', array(
            'default'           => '55vh',
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
        $height     = get_theme_mod( 'flavor_about_hero_height', '55vh' );
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
                    'custom'                => fc__( 'cust_about_hero_bg_custom', 'admin' ),
                    'pattern-topographic'   => fc__( 'cust_about_hero_bg_topographic', 'admin' ),
                    'pattern-circuit'       => fc__( 'cust_about_hero_bg_circuit', 'admin' ),
                    'pattern-mosaic'        => fc__( 'cust_about_hero_bg_mosaic', 'admin' ),
                    'pattern-constellation' => fc__( 'cust_about_hero_bg_constellation', 'admin' ),
                    'pattern-deco'          => fc__( 'cust_about_hero_bg_deco', 'admin' ),
                    'pattern-printing3d'    => fc__( 'cust_about_hero_bg_printing3d', 'admin' ),
                    'pattern-printing3d-resin'    => fc__( 'cust_about_hero_bg_printing3d_resin', 'admin' ),
                    'pattern-printing3d-slicer'   => fc__( 'cust_about_hero_bg_printing3d_slicer', 'admin' ),
                    'pattern-printing3d-filament'  => fc__( 'cust_about_hero_bg_printing3d_filament', 'admin' ),
                    'pattern-printing3d-scan'      => fc__( 'cust_about_hero_bg_printing3d_scan', 'admin' ),
                    'pattern-printing3d-models'    => fc__( 'cust_about_hero_bg_printing3d_models', 'admin' ),
                    'pattern-automotive'    => fc__( 'cust_about_hero_bg_automotive', 'admin' ),
                    'pattern-medical'       => fc__( 'cust_about_hero_bg_medical', 'admin' ),
                    'pattern-gastro'        => fc__( 'cust_about_hero_bg_gastro', 'admin' ),
                    'pattern-fashion'       => fc__( 'cust_about_hero_bg_fashion', 'admin' ),
                    'pattern-construction'  => fc__( 'cust_about_hero_bg_construction', 'admin' ),
                    'pattern-education'     => fc__( 'cust_about_hero_bg_education', 'admin' ),
                    'pattern-fitness'       => fc__( 'cust_about_hero_bg_fitness', 'admin' ),
                    'pattern-music'         => fc__( 'cust_about_hero_bg_music', 'admin' ),
                    'pattern-tech'          => fc__( 'cust_about_hero_bg_tech', 'admin' ),
                    'pattern-nature'        => fc__( 'cust_about_hero_bg_nature', 'admin' ),
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
        <div class="fc-card-field" style="margin-top:8px">
            <label style="display:block;font-size:11px;font-weight:600;margin-bottom:3px">
                <?php echo esc_html( fc__( 'cust_about_hero_height', 'admin' ) ); ?>
            </label>
            <select data-customize-setting-link="flavor_about_hero_height" style="width:100%;box-sizing:border-box;font-size:12px">
                <?php
                $heights = array(
                    '30vh'  => fc__( 'cust_about_hero_height_small', 'admin' ),
                    '45vh'  => fc__( 'cust_about_hero_height_medium', 'admin' ),
                    '55vh'  => fc__( 'cust_about_hero_height_large', 'admin' ),
                );
                foreach ( $heights as $val => $label ) :
                ?>
                    <option value="<?php echo esc_attr( $val ); ?>" <?php selected( $height, $val ); ?>><?php echo esc_html( $label ); ?></option>
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

            /* Explicit sync: push every hero field change into wp.customize & refresh preview */
            if (typeof wp !== 'undefined' && wp.customize) {
                section.querySelectorAll('[data-customize-setting-link]').forEach(function(el) {
                    var settingId = el.getAttribute('data-customize-setting-link');
                    el.addEventListener('change', function() {
                        var val = (el.type === 'checkbox') ? el.checked : el.value;
                        wp.customize(settingId, function(s) { s.set(val); });
                    });
                    if (el.tagName === 'INPUT' && el.type !== 'checkbox' && el.type !== 'hidden') {
                        el.addEventListener('input', function() {
                            wp.customize(settingId, function(s) { s.set(el.value); });
                        });
                    }
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
            'height'        => get_theme_mod( 'flavor_about_hero_height', '55vh' ),
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
            $fg1   = "rgba({$r},{$g},{$b},0.50)";
            $fg2   = "rgba({$r},{$g},{$b},0.30)";
            $fg3   = "rgba(255,255,255,0.12)";
        } else {
            $bg    = '#f8f9fc';
            $fg1   = "rgba({$r},{$g},{$b},0.40)";
            $fg2   = "rgba({$r},{$g},{$b},0.22)";
            $fg3   = "rgba(0,0,0,0.08)";
        }

        switch ( $pattern ) {
            // Topographic — flowing contour map lines with elevation dots
            case 'topographic':
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200">'
                     . '<path d="M0,30 C40,10 60,50 100,30 S160,10 200,30" fill="none" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<path d="M0,60 C30,42 70,78 100,55 S170,32 200,60" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<path d="M0,85 C50,70 80,100 130,80 S180,65 200,85" fill="none" stroke="' . $fg3 . '" stroke-width="0.6"/>'
                     . '<path d="M0,110 C40,95 90,125 130,105 S180,88 200,110" fill="none" stroke="' . $fg2 . '" stroke-width="0.9"/>'
                     . '<path d="M0,140 C35,122 65,158 100,135 S165,112 200,140" fill="none" stroke="' . $fg1 . '" stroke-width="1"/>'
                     . '<path d="M0,168 C50,155 80,182 120,162 S170,148 200,168" fill="none" stroke="' . $fg3 . '" stroke-width="0.6"/>'
                     . '<path d="M0,192 C30,182 70,200 110,188 S170,178 200,192" fill="none" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     . '<circle cx="100" cy="55" r="2" fill="' . $fg1 . '"/>'
                     . '<circle cx="55" cy="135" r="1.5" fill="' . $fg2 . '"/>'
                     . '<circle cx="160" cy="105" r="1.5" fill="' . $fg2 . '"/>'
                     . '</svg>';
                break;

            // Circuit board — traces, pads and SMD components
            case 'circuit':
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="160" height="160">'
                     . '<path d="M0,20 H50 V60 H110 V20 H160" fill="none" stroke="' . $fg2 . '" stroke-width="1" stroke-linecap="round"/>'
                     . '<path d="M0,100 H30 V140 H90 V100 H160" fill="none" stroke="' . $fg1 . '" stroke-width="1.2" stroke-linecap="round"/>'
                     . '<path d="M80,0 V60" fill="none" stroke="' . $fg3 . '" stroke-width="0.8" stroke-linecap="round"/>'
                     . '<path d="M80,100 V160" fill="none" stroke="' . $fg3 . '" stroke-width="0.8" stroke-linecap="round"/>'
                     . '<path d="M130,55 V105" fill="none" stroke="' . $fg2 . '" stroke-width="0.8" stroke-linecap="round"/>'
                     . '<circle cx="50" cy="20" r="5" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<circle cx="50" cy="20" r="2" fill="' . $fg1 . '"/>'
                     . '<circle cx="110" cy="60" r="4" fill="none" stroke="' . $fg1 . '" stroke-width="1"/>'
                     . '<circle cx="110" cy="60" r="1.5" fill="' . $fg1 . '"/>'
                     . '<circle cx="90" cy="100" r="5" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<circle cx="90" cy="100" r="2" fill="' . $fg1 . '"/>'
                     . '<circle cx="30" cy="140" r="4" fill="none" stroke="' . $fg2 . '" stroke-width="1"/>'
                     . '<circle cx="30" cy="140" r="1.5" fill="' . $fg2 . '"/>'
                     . '<rect x="125" y="75" width="12" height="8" rx="1.5" fill="none" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<rect x="55" y="50" width="8" height="12" rx="1.5" fill="none" stroke="' . $fg3 . '" stroke-width="0.6"/>'
                     . '</svg>';
                break;

            // Geometric mosaic — nested diamonds with cross axes and accent dots
            case 'mosaic':
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100">'
                     . '<path d="M50,0 L100,50 L50,100 L0,50 Z" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<path d="M50,15 L85,50 L50,85 L15,50 Z" fill="none" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<path d="M50,30 L70,50 L50,70 L30,50 Z" fill="none" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     . '<line x1="50" y1="0" x2="50" y2="100" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<line x1="0" y1="50" x2="100" y2="50" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<circle cx="50" cy="0" r="3" fill="' . $fg1 . '"/>'
                     . '<circle cx="100" cy="50" r="3" fill="' . $fg1 . '"/>'
                     . '<circle cx="50" cy="100" r="3" fill="' . $fg1 . '"/>'
                     . '<circle cx="0" cy="50" r="3" fill="' . $fg1 . '"/>'
                     . '<circle cx="50" cy="50" r="5" fill="none" stroke="' . $fg1 . '" stroke-width="1"/>'
                     . '<circle cx="50" cy="50" r="2" fill="' . $fg2 . '"/>'
                     . '</svg>';
                break;

            // Constellation — connected star dots with halos
            case 'constellation':
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200">'
                     . '<line x1="30" y1="40" x2="90" y2="25" stroke="' . $fg3 . '" stroke-width="0.6"/>'
                     . '<line x1="90" y1="25" x2="150" y2="55" stroke="' . $fg3 . '" stroke-width="0.6"/>'
                     . '<line x1="150" y1="55" x2="180" y2="30" stroke="' . $fg3 . '" stroke-width="0.6"/>'
                     . '<line x1="30" y1="40" x2="65" y2="90" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     . '<line x1="65" y1="90" x2="130" y2="110" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     . '<line x1="130" y1="110" x2="150" y2="55" stroke="' . $fg3 . '" stroke-width="0.6"/>'
                     . '<line x1="65" y1="90" x2="40" y2="140" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     . '<line x1="130" y1="110" x2="170" y2="145" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     . '<line x1="40" y1="140" x2="100" y2="175" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     . '<line x1="170" y1="145" x2="100" y2="175" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     . '<line x1="20" y1="185" x2="40" y2="140" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<circle cx="30" cy="40" r="3" fill="' . $fg1 . '"/>'
                     . '<circle cx="90" cy="25" r="2" fill="' . $fg2 . '"/>'
                     . '<circle cx="150" cy="55" r="3.5" fill="' . $fg1 . '"/>'
                     . '<circle cx="65" cy="90" r="2.5" fill="' . $fg2 . '"/>'
                     . '<circle cx="130" cy="110" r="3" fill="' . $fg1 . '"/>'
                     . '<circle cx="40" cy="140" r="2" fill="' . $fg2 . '"/>'
                     . '<circle cx="170" cy="145" r="2.5" fill="' . $fg2 . '"/>'
                     . '<circle cx="100" cy="175" r="3.5" fill="' . $fg1 . '"/>'
                     . '<circle cx="20" cy="185" r="1.5" fill="' . $fg2 . '"/>'
                     . '<circle cx="180" cy="30" r="2" fill="' . $fg2 . '"/>'
                     . '<circle cx="150" cy="55" r="8" fill="none" stroke="' . $fg2 . '" stroke-width="0.5"/>'
                     . '<circle cx="100" cy="175" r="8" fill="none" stroke="' . $fg2 . '" stroke-width="0.5"/>'
                     . '<circle cx="30" cy="40" r="6" fill="none" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '</svg>';
                break;

            // Art Deco — fan arcs with half-drop offset for scallop effect
            case 'deco':
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="120">'
                     . '<line x1="50" y1="60" x2="50" y2="5" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     . '<line x1="50" y1="60" x2="18" y2="10" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<line x1="50" y1="60" x2="82" y2="10" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<path d="M10,20 A50,50 0 0,1 90,20" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<path d="M20,35 A35,35 0 0,1 80,35" fill="none" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<path d="M28,48 A25,25 0 0,1 72,48" fill="none" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     . '<circle cx="50" cy="60" r="2.5" fill="' . $fg1 . '"/>'
                     . '<line x1="0" y1="120" x2="0" y2="65" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     . '<line x1="0" y1="120" x2="32" y2="70" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<path d="M-40,80 A50,50 0 0,1 40,80" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<path d="M-30,95 A35,35 0 0,1 30,95" fill="none" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<circle cx="0" cy="120" r="2.5" fill="' . $fg1 . '"/>'
                     . '<line x1="100" y1="120" x2="100" y2="65" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     . '<line x1="100" y1="120" x2="68" y2="70" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<path d="M60,80 A50,50 0 0,1 140,80" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<path d="M70,95 A35,35 0 0,1 130,95" fill="none" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<circle cx="100" cy="120" r="2.5" fill="' . $fg1 . '"/>'
                     . '</svg>';
                break;

            // ── Industry-specific patterns ──────────────────────────

            // 3D Printing — layered infill pattern, nozzle path, extruder head, printed object
            case 'printing3d':
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="280" height="280">'
                     // Honeycomb infill pattern (lower left)
                     . '<path d="M20,230 L35,220 L50,230 L50,250 L35,260 L20,250Z" fill="none" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<path d="M50,230 L65,220 L80,230 L80,250 L65,260 L50,250Z" fill="none" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<path d="M35,210 L50,200 L65,210 L65,230 L50,240 L35,230Z" fill="none" stroke="' . $fg3 . '" stroke-width="0.6"/>'
                     . '<path d="M65,210 L80,200 L95,210 L95,230 L80,240 L65,230Z" fill="none" stroke="' . $fg3 . '" stroke-width="0.6"/>'
                     . '<path d="M20,250 L35,240 L50,250 L50,270 L35,280 L20,270Z" fill="none" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     // Print bed (bottom area)
                     . '<rect x="110" y="220" width="120" height="8" rx="2" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<line x1="115" y1="228" x2="115" y2="235" stroke="' . $fg3 . '" stroke-width="0.6"/>'
                     . '<line x1="225" y1="228" x2="225" y2="235" stroke="' . $fg3 . '" stroke-width="0.6"/>'
                     . '<line x1="170" y1="228" x2="170" y2="240" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     // Printed object — layer lines
                     . '<rect x="140" y="190" width="60" height="30" rx="1" fill="none" stroke="' . $fg1 . '" stroke-width="0.9"/>'
                     . '<line x1="140" y1="196" x2="200" y2="196" stroke="' . $fg2 . '" stroke-width="0.5"/>'
                     . '<line x1="140" y1="202" x2="200" y2="202" stroke="' . $fg2 . '" stroke-width="0.5"/>'
                     . '<line x1="140" y1="208" x2="200" y2="208" stroke="' . $fg2 . '" stroke-width="0.5"/>'
                     . '<line x1="140" y1="214" x2="200" y2="214" stroke="' . $fg2 . '" stroke-width="0.5"/>'
                     // Extruder / nozzle above object
                     . '<rect x="158" y="168" width="24" height="16" rx="3" fill="none" stroke="' . $fg1 . '" stroke-width="1.3"/>'
                     . '<path d="M165,184 L170,192 L175,184" fill="none" stroke="' . $fg1 . '" stroke-width="1"/>'
                     . '<circle cx="170" cy="192" r="1.5" fill="' . $fg1 . '"/>'
                     // Filament spool (upper right)
                     . '<circle cx="230" cy="60" r="30" fill="none" stroke="' . $fg2 . '" stroke-width="1"/>'
                     . '<circle cx="230" cy="60" r="12" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<circle cx="230" cy="60" r="4" fill="' . $fg2 . '"/>'
                     . '<line x1="230" y1="30" x2="230" y2="28" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     // Filament path from spool to extruder
                     . '<path d="M230,90 Q230,130 200,150 Q180,160 170,168" fill="none" stroke="' . $fg1 . '" stroke-width="0.9" stroke-dasharray="4,3"/>'
                     // Nozzle path traces (zigzag)
                     . '<path d="M20,80 L70,80 L70,86 L20,86 L20,92 L70,92 L70,98 L20,98" fill="none" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     . '<path d="M20,110 L70,110 L70,116 L20,116 L20,122 L70,122" fill="none" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     // G-code coordinates
                     . '<text x="85" y="88" font-family="monospace" font-size="6" fill="' . $fg3 . '">G1 X50 Y20</text>'
                     . '<text x="85" y="98" font-family="monospace" font-size="6" fill="' . $fg3 . '">G1 Z0.2 E1</text>'
                     // Layer indicators
                     . '<text x="15" y="160" font-family="monospace" font-size="7" fill="' . $fg2 . '">Layer 42</text>'
                     . '<path d="M15,163 L80,163" fill="none" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     // Small gear (feeder)
                     . '<circle cx="50" cy="45" r="10" fill="none" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<circle cx="50" cy="45" r="4" fill="none" stroke="' . $fg1 . '" stroke-width="0.6"/>'
                     . '<line x1="50" y1="35" x2="50" y2="33" stroke="' . $fg2 . '" stroke-width="1"/>'
                     . '<line x1="50" y1="55" x2="50" y2="57" stroke="' . $fg2 . '" stroke-width="1"/>'
                     . '<line x1="40" y1="45" x2="38" y2="45" stroke="' . $fg2 . '" stroke-width="1"/>'
                     . '<line x1="60" y1="45" x2="62" y2="45" stroke="' . $fg2 . '" stroke-width="1"/>'
                     . '</svg>';
                break;

            // 3D Printing — SLA / Resin — resin vat, build platform, UV projection, cured layers
            case 'printing3d-resin':
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="280" height="280">'
                     // Resin vat (transparent container)
                     . '<rect x="60" y="140" width="120" height="80" rx="3" fill="none" stroke="' . $fg1 . '" stroke-width="1.3"/>'
                     . '<rect x="65" y="150" width="110" height="65" rx="1" fill="none" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     // Resin liquid surface
                     . '<path d="M65,165 Q90,161 120,165 Q150,169 175,165" fill="none" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     // Build platform lifting up
                     . '<rect x="80" y="100" width="80" height="8" rx="2" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<line x1="120" y1="108" x2="120" y2="140" stroke="' . $fg1 . '" stroke-width="0.8" stroke-dasharray="3,2"/>'
                     // Lift rod / z-axis
                     . '<line x1="120" y1="30" x2="120" y2="100" stroke="' . $fg2 . '" stroke-width="1"/>'
                     . '<rect x="115" y="25" width="10" height="10" rx="1" fill="none" stroke="' . $fg1 . '" stroke-width="0.9"/>'
                     // Cured layers on platform (hanging down)
                     . '<rect x="90" y="108" width="60" height="18" rx="1" fill="none" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<line x1="90" y1="112" x2="150" y2="112" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<line x1="90" y1="116" x2="150" y2="116" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<line x1="90" y1="120" x2="150" y2="120" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     // UV light source below vat
                     . '<rect x="75" y="230" width="90" height="15" rx="3" fill="none" stroke="' . $fg1 . '" stroke-width="1"/>'
                     . '<text x="95" y="241" font-family="monospace" font-size="7" fill="' . $fg2 . '">UV LCD</text>'
                     // UV rays projecting upward
                     . '<line x1="90" y1="230" x2="85" y2="220" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     . '<line x1="120" y1="230" x2="120" y2="220" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     . '<line x1="150" y1="230" x2="155" y2="220" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     // Dripping resin
                     . '<path d="M95,126 Q96,132 95,136" fill="none" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     . '<circle cx="95" cy="138" r="1.5" fill="' . $fg3 . '"/>'
                     . '<path d="M140,126 Q141,130 140,133" fill="none" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     . '<circle cx="140" cy="135" r="1.2" fill="' . $fg3 . '"/>'
                     // Resin bottle (upper left)
                     . '<rect x="15" y="40" width="20" height="40" rx="2" fill="none" stroke="' . $fg2 . '" stroke-width="0.9"/>'
                     . '<rect x="19" y="34" width="12" height="8" rx="1" fill="none" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     . '<line x1="15" y1="55" x2="35" y2="55" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<text x="18" y="70" font-family="monospace" font-size="5" fill="' . $fg3 . '">500ml</text>'
                     // Layer thickness indicator (right side)
                     . '<line x1="200" y1="100" x2="200" y2="130" stroke="' . $fg2 . '" stroke-width="0.6"/>'
                     . '<line x1="196" y1="100" x2="204" y2="100" stroke="' . $fg2 . '" stroke-width="0.6"/>'
                     . '<line x1="196" y1="130" x2="204" y2="130" stroke="' . $fg2 . '" stroke-width="0.6"/>'
                     . '<text x="208" y="118" font-family="monospace" font-size="6" fill="' . $fg3 . '">0.05mm</text>'
                     // Wavelength label
                     . '<text x="200" y="248" font-family="monospace" font-size="5" fill="' . $fg3 . '">405nm</text>'
                     // FEP film indicator
                     . '<line x1="60" y1="218" x2="180" y2="218" stroke="' . $fg2 . '" stroke-width="0.6" stroke-dasharray="2,2"/>'
                     . '<text x="185" y="221" font-family="monospace" font-size="5" fill="' . $fg3 . '">FEP</text>'
                     . '</svg>';
                break;

            // 3D Printing — Slicer view — model cross-section, supports, layers, infill
            case 'printing3d-slicer':
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="280" height="280">'
                     // 3D model wireframe outline (upper area)
                     . '<path d="M80,30 L160,30 L180,50 L180,130 L100,130 L80,110Z" fill="none" stroke="' . $fg1 . '" stroke-width="1"/>'
                     . '<line x1="80" y1="30" x2="100" y2="50" stroke="' . $fg2 . '" stroke-width="0.6"/>'
                     . '<line x1="160" y1="30" x2="180" y2="50" stroke="' . $fg2 . '" stroke-width="0.6"/>'
                     . '<line x1="100" y1="50" x2="180" y2="50" stroke="' . $fg2 . '" stroke-width="0.6"/>'
                     . '<line x1="100" y1="50" x2="100" y2="130" stroke="' . $fg2 . '" stroke-width="0.6"/>'
                     // Layer slicing lines
                     . '<line x1="80" y1="50" x2="180" y2="50" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="82" y1="60" x2="180" y2="60" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="84" y1="70" x2="180" y2="70" stroke="' . $fg1 . '" stroke-width="0.5" stroke-dasharray="3,2"/>'
                     . '<line x1="86" y1="80" x2="180" y2="80" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="88" y1="90" x2="180" y2="90" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="90" y1="100" x2="180" y2="100" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="92" y1="110" x2="180" y2="110" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="94" y1="120" x2="180" y2="120" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     // Highlighted slice (cross-section) pulled out
                     . '<rect x="30" y="170" width="100" height="60" rx="2" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     // Infill pattern inside slice (grid)
                     . '<line x1="35" y1="180" x2="125" y2="180" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="35" y1="190" x2="125" y2="190" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="35" y1="200" x2="125" y2="200" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="50" y1="173" x2="50" y2="227" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="70" y1="173" x2="70" y2="227" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="90" y1="173" x2="90" y2="227" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="110" y1="173" x2="110" y2="227" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     // Perimeter wall (thick)
                     . '<rect x="33" y="173" width="94" height="54" rx="1" fill="none" stroke="' . $fg1 . '" stroke-width="0.9"/>'
                     // Connection arrow from model to slice
                     . '<path d="M140,90 Q160,150 130,170" fill="none" stroke="' . $fg2 . '" stroke-width="0.7" stroke-dasharray="4,3"/>'
                     . '<polygon points="130,170 133,165 127,165" fill="' . $fg2 . '"/>'
                     // Support structures (right side)
                     . '<line x1="160" y1="170" x2="160" y2="230" stroke="' . $fg2 . '" stroke-width="0.6"/>'
                     . '<line x1="175" y1="170" x2="175" y2="230" stroke="' . $fg2 . '" stroke-width="0.6"/>'
                     . '<line x1="190" y1="175" x2="190" y2="230" stroke="' . $fg2 . '" stroke-width="0.6"/>'
                     // Zigzag support interface
                     . '<path d="M155,170 L165,168 L170,172 L180,168 L185,172 L195,170" fill="none" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     // Layer slider (right edge)
                     . '<line x1="240" y1="30" x2="240" y2="230" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<rect x="234" y="65" width="12" height="8" rx="2" fill="none" stroke="' . $fg1 . '" stroke-width="0.9"/>'
                     . '<line x1="234" y1="69" x2="246" y2="69" stroke="' . $fg1 . '" stroke-width="0.5"/>'
                     // Layer numbers
                     . '<text x="250" y="35" font-family="monospace" font-size="5" fill="' . $fg3 . '">200</text>'
                     . '<text x="250" y="72" font-family="monospace" font-size="5" fill="' . $fg1 . '">142</text>'
                     . '<text x="250" y="233" font-family="monospace" font-size="5" fill="' . $fg3 . '">0</text>'
                     // Toolbar icons (top left)
                     . '<rect x="10" y="10" width="14" height="14" rx="2" fill="none" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     . '<rect x="10" y="28" width="14" height="14" rx="2" fill="none" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     . '<rect x="10" y="46" width="14" height="14" rx="2" fill="none" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     // Infill % label
                     . '<text x="35" y="218" font-family="monospace" font-size="6" fill="' . $fg2 . '">20% infill</text>'
                     // Speed label
                     . '<text x="35" y="246" font-family="monospace" font-size="5" fill="' . $fg3 . '">60mm/s</text>'
                     . '</svg>';
                break;

            // 3D Printing — Filament / FDM — spools, extruder motor, bowden tube, temp gauge
            case 'printing3d-filament':
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="280" height="280">'
                     // Large filament spool (main)
                     . '<circle cx="80" cy="80" r="45" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<circle cx="80" cy="80" r="28" fill="none" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<circle cx="80" cy="80" r="10" fill="none" stroke="' . $fg1 . '" stroke-width="1"/>'
                     . '<circle cx="80" cy="80" r="3" fill="' . $fg2 . '"/>'
                     // Filament wound around spool
                     . '<ellipse cx="80" cy="80" rx="38" ry="35" fill="none" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<ellipse cx="80" cy="80" rx="34" ry="32" fill="none" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     // Small spool (second color, upper right)
                     . '<circle cx="210" cy="55" r="25" fill="none" stroke="' . $fg2 . '" stroke-width="0.9"/>'
                     . '<circle cx="210" cy="55" r="15" fill="none" stroke="' . $fg3 . '" stroke-width="0.6"/>'
                     . '<circle cx="210" cy="55" r="5" fill="none" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     // Filament path from spool
                     . '<path d="M80,125 Q80,160 100,180 Q120,200 140,200" fill="none" stroke="' . $fg1 . '" stroke-width="1" stroke-dasharray="5,3"/>'
                     // Bowden tube
                     . '<path d="M140,200 Q170,200 190,210 Q210,220 220,240" fill="none" stroke="' . $fg2 . '" stroke-width="2.5" stroke-linecap="round"/>'
                     . '<path d="M140,200 Q170,200 190,210 Q210,220 220,240" fill="none" stroke="' . $bg . '" stroke-width="1.2" stroke-linecap="round"/>'
                     // Extruder stepper motor
                     . '<rect x="120" y="182" width="26" height="26" rx="2" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<circle cx="133" cy="195" r="8" fill="none" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     . '<circle cx="133" cy="195" r="3" fill="' . $fg3 . '"/>'
                     // Drive gear teeth
                     . '<line x1="133" y1="186" x2="133" y2="183" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<line x1="133" y1="204" x2="133" y2="207" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<line x1="124" y1="195" x2="121" y2="195" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<line x1="142" y1="195" x2="145" y2="195" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     // Temperature gauge (lower left)
                     . '<rect x="20" y="190" width="12" height="50" rx="6" fill="none" stroke="' . $fg1 . '" stroke-width="1"/>'
                     . '<circle cx="26" cy="232" r="7" fill="none" stroke="' . $fg1 . '" stroke-width="1"/>'
                     . '<circle cx="26" cy="232" r="3" fill="' . $fg1 . '"/>'
                     . '<line x1="26" y1="232" x2="26" y2="200" stroke="' . $fg1 . '" stroke-width="1.5"/>'
                     // Temperature scale marks
                     . '<line x1="33" y1="195" x2="37" y2="195" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<line x1="33" y1="205" x2="37" y2="205" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<line x1="33" y1="215" x2="37" y2="215" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<text x="39" y="198" font-family="monospace" font-size="5" fill="' . $fg3 . '">220°</text>'
                     . '<text x="39" y="218" font-family="monospace" font-size="5" fill="' . $fg3 . '">180°</text>'
                     // Cooling fan (right middle)
                     . '<circle cx="230" cy="160" r="22" fill="none" stroke="' . $fg2 . '" stroke-width="1"/>'
                     . '<circle cx="230" cy="160" r="5" fill="none" stroke="' . $fg1 . '" stroke-width="0.8"/>'
                     // Fan blades
                     . '<path d="M230,155 Q220,145 225,138" fill="none" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     . '<path d="M235,160 Q245,150 248,155" fill="none" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     . '<path d="M230,165 Q220,175 225,182" fill="none" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     . '<path d="M225,160 Q215,170 212,165" fill="none" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     // Filament diameter indicator
                     . '<line x1="160" y1="40" x2="160" y2="60" stroke="' . $fg2 . '" stroke-width="0.6"/>'
                     . '<line x1="155" y1="45" x2="165" y2="45" stroke="' . $fg2 . '" stroke-width="0.6"/>'
                     . '<line x1="155" y1="55" x2="165" y2="55" stroke="' . $fg2 . '" stroke-width="0.6"/>'
                     . '<text x="168" y="53" font-family="monospace" font-size="6" fill="' . $fg3 . '">1.75mm</text>'
                     // Material label
                     . '<text x="15" y="165" font-family="monospace" font-size="6" fill="' . $fg2 . '">PLA+</text>'
                     . '<text x="15" y="175" font-family="monospace" font-size="5" fill="' . $fg3 . '">1kg</text>'
                     . '</svg>';
                break;

            // 3D Printing — 3D Scanning — scanner, turntable, laser lines, point cloud
            case 'printing3d-scan':
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="280" height="280">'
                     // Turntable base
                     . '<ellipse cx="140" cy="220" rx="60" ry="15" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<ellipse cx="140" cy="225" rx="60" ry="15" fill="none" stroke="' . $fg2 . '" stroke-width="0.6"/>'
                     // Object on turntable (bust/head shape)
                     . '<path d="M120,180 Q120,150 130,140 Q140,130 150,140 Q160,150 160,180" fill="none" stroke="' . $fg1 . '" stroke-width="1"/>'
                     . '<line x1="120" y1="180" x2="120" y2="220" stroke="' . $fg2 . '" stroke-width="0.6"/>'
                     . '<line x1="160" y1="180" x2="160" y2="220" stroke="' . $fg2 . '" stroke-width="0.6"/>'
                     // Rotation arrow on turntable
                     . '<path d="M95,210 A50,12 0 0,1 175,205" fill="none" stroke="' . $fg3 . '" stroke-width="0.6"/>'
                     . '<polygon points="175,205 172,200 170,207" fill="' . $fg3 . '"/>'
                     // Scanner device (left side, on tripod)
                     . '<rect x="20" y="100" width="30" height="50" rx="3" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<circle cx="35" cy="115" r="8" fill="none" stroke="' . $fg2 . '" stroke-width="0.9"/>'
                     . '<circle cx="35" cy="115" r="3" fill="' . $fg1 . '"/>'
                     . '<circle cx="35" cy="138" r="5" fill="none" stroke="' . $fg3 . '" stroke-width="0.6"/>'
                     // Tripod legs
                     . '<line x1="25" y1="150" x2="10" y2="220" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     . '<line x1="35" y1="150" x2="35" y2="225" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     . '<line x1="45" y1="150" x2="55" y2="220" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     // Laser lines scanning object
                     . '<line x1="43" y1="115" x2="120" y2="155" stroke="' . $fg1 . '" stroke-width="0.6" stroke-dasharray="4,2"/>'
                     . '<line x1="43" y1="115" x2="120" y2="165" stroke="' . $fg1 . '" stroke-width="0.5" stroke-dasharray="4,2"/>'
                     . '<line x1="43" y1="115" x2="120" y2="175" stroke="' . $fg1 . '" stroke-width="0.4" stroke-dasharray="4,2"/>'
                     // Laser stripe on object
                     . '<path d="M120,155 Q130,148 140,147 Q150,148 160,155" fill="none" stroke="' . $fg1 . '" stroke-width="0.8"/>'
                     // Point cloud (upper right)
                     . '<circle cx="200" cy="40" r="1" fill="' . $fg2 . '"/>'
                     . '<circle cx="208" cy="38" r="1" fill="' . $fg3 . '"/>'
                     . '<circle cx="215" cy="42" r="1" fill="' . $fg2 . '"/>'
                     . '<circle cx="222" cy="36" r="1" fill="' . $fg3 . '"/>'
                     . '<circle cx="203" cy="48" r="1" fill="' . $fg3 . '"/>'
                     . '<circle cx="211" cy="50" r="1" fill="' . $fg2 . '"/>'
                     . '<circle cx="218" cy="46" r="1" fill="' . $fg3 . '"/>'
                     . '<circle cx="225" cy="50" r="1" fill="' . $fg2 . '"/>'
                     . '<circle cx="205" cy="58" r="1" fill="' . $fg2 . '"/>'
                     . '<circle cx="213" cy="56" r="1" fill="' . $fg3 . '"/>'
                     . '<circle cx="220" cy="60" r="1" fill="' . $fg2 . '"/>'
                     . '<circle cx="228" cy="55" r="1" fill="' . $fg3 . '"/>'
                     . '<circle cx="207" cy="66" r="1" fill="' . $fg3 . '"/>'
                     . '<circle cx="215" cy="64" r="1" fill="' . $fg2 . '"/>'
                     . '<circle cx="223" cy="68" r="1" fill="' . $fg3 . '"/>'
                     . '<circle cx="199" cy="70" r="1" fill="' . $fg2 . '"/>'
                     . '<circle cx="230" cy="62" r="1" fill="' . $fg2 . '"/>'
                     // Point cloud label
                     . '<text x="196" y="85" font-family="monospace" font-size="5" fill="' . $fg3 . '">12,847 pts</text>'
                     // Reference markers on turntable
                     . '<circle cx="110" cy="218" r="2" fill="none" stroke="' . $fg2 . '" stroke-width="0.5"/>'
                     . '<circle cx="140" cy="207" r="2" fill="none" stroke="' . $fg2 . '" stroke-width="0.5"/>'
                     . '<circle cx="170" cy="218" r="2" fill="none" stroke="' . $fg2 . '" stroke-width="0.5"/>'
                     // Scan progress
                     . '<text x="200" y="130" font-family="monospace" font-size="6" fill="' . $fg2 . '">Scan 3/8</text>'
                     . '<rect x="200" y="134" width="50" height="4" rx="1" fill="none" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     . '<rect x="200" y="134" width="19" height="4" rx="1" fill="' . $fg3 . '"/>'
                     // Coordinate axes (bottom right)
                     . '<line x1="230" y1="260" x2="260" y2="260" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     . '<line x1="230" y1="260" x2="230" y2="235" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     . '<line x1="230" y1="260" x2="218" y2="270" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     . '<text x="262" y="263" font-family="monospace" font-size="5" fill="' . $fg3 . '">X</text>'
                     . '<text x="228" y="233" font-family="monospace" font-size="5" fill="' . $fg3 . '">Y</text>'
                     . '<text x="213" y="275" font-family="monospace" font-size="5" fill="' . $fg3 . '">Z</text>'
                     . '</svg>';
                break;

            // 3D Printing — Printed models — benchy, calibration cube, gear, vase
            case 'printing3d-models':
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="280" height="280">'
                     // Calibration cube (upper left) with XYZ labels
                     . '<path d="M30,60 L70,60 L85,45 L45,45Z" fill="none" stroke="' . $fg1 . '" stroke-width="1"/>'
                     . '<path d="M70,60 L85,45 L85,85 L70,100Z" fill="none" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<rect x="30" y="60" width="40" height="40" fill="none" stroke="' . $fg1 . '" stroke-width="1"/>'
                     . '<line x1="45" y1="45" x2="45" y2="85" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<text x="42" y="85" font-family="sans-serif" font-size="10" fill="' . $fg2 . '">X</text>'
                     . '<text x="73" y="78" font-family="sans-serif" font-size="10" fill="' . $fg2 . '">Y</text>'
                     . '<text x="52" y="52" font-family="sans-serif" font-size="10" fill="' . $fg3 . '">Z</text>'
                     // Layer lines on cube
                     . '<line x1="30" y1="70" x2="70" y2="70" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="30" y1="80" x2="70" y2="80" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="30" y1="90" x2="70" y2="90" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     // Benchy boat (center)
                     . '<path d="M110,150 Q120,170 160,170 Q175,170 180,155" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     // Hull
                     . '<path d="M110,150 L105,170 Q108,180 140,180 Q172,180 176,170 L180,155" fill="none" stroke="' . $fg1 . '" stroke-width="0.9"/>'
                     // Cabin
                     . '<rect x="140" y="135" width="25" height="18" rx="1" fill="none" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<rect x="145" y="140" width="8" height="8" rx="0.5" fill="none" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     // Chimney
                     . '<rect x="155" y="125" width="8" height="12" rx="1" fill="none" stroke="' . $fg1 . '" stroke-width="0.9"/>'
                     // Water line
                     . '<line x1="105" y1="175" x2="178" y2="175" stroke="' . $fg3 . '" stroke-width="0.3" stroke-dasharray="2,2"/>'
                     // Gear / cog (upper right)
                     . '<circle cx="220" cy="60" r="25" fill="none" stroke="' . $fg1 . '" stroke-width="1"/>'
                     . '<circle cx="220" cy="60" r="16" fill="none" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     . '<circle cx="220" cy="60" r="6" fill="none" stroke="' . $fg1 . '" stroke-width="0.8"/>'
                     // Gear teeth
                     . '<line x1="220" y1="33" x2="220" y2="27" stroke="' . $fg1 . '" stroke-width="3.5"/>'
                     . '<line x1="220" y1="87" x2="220" y2="93" stroke="' . $fg1 . '" stroke-width="3.5"/>'
                     . '<line x1="193" y1="60" x2="187" y2="60" stroke="' . $fg1 . '" stroke-width="3.5"/>'
                     . '<line x1="247" y1="60" x2="253" y2="60" stroke="' . $fg1 . '" stroke-width="3.5"/>'
                     . '<line x1="202" y1="42" x2="198" y2="38" stroke="' . $fg1 . '" stroke-width="3"/>'
                     . '<line x1="238" y1="78" x2="242" y2="82" stroke="' . $fg1 . '" stroke-width="3"/>'
                     . '<line x1="238" y1="42" x2="242" y2="38" stroke="' . $fg1 . '" stroke-width="3"/>'
                     . '<line x1="202" y1="78" x2="198" y2="82" stroke="' . $fg1 . '" stroke-width="3"/>'
                     // Vase / spiral mode (lower left)
                     . '<path d="M30,260 Q25,230 30,210 Q35,195 50,190 Q65,195 70,210 Q75,230 70,260" fill="none" stroke="' . $fg1 . '" stroke-width="1"/>'
                     // Spiral lines on vase
                     . '<path d="M32,250 Q50,246 68,250" fill="none" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<path d="M30,240 Q50,236 70,240" fill="none" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<path d="M31,230 Q50,226 69,230" fill="none" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<path d="M33,220 Q50,216 67,220" fill="none" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<path d="M36,210 Q50,207 64,210" fill="none" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<path d="M42,200 Q50,198 58,200" fill="none" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     // Figurine silhouette (lower right)
                     . '<circle cx="210" cy="185" r="10" fill="none" stroke="' . $fg2 . '" stroke-width="0.9"/>'
                     . '<line x1="210" y1="195" x2="210" y2="230" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<line x1="210" y1="205" x2="195" y2="215" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     . '<line x1="210" y1="205" x2="225" y2="215" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     . '<line x1="210" y1="230" x2="198" y2="255" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     . '<line x1="210" y1="230" x2="222" y2="255" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     // Support material marks on figurine
                     . '<line x1="195" y1="215" x2="193" y2="220" stroke="' . $fg3 . '" stroke-width="0.4" stroke-dasharray="1,1"/>'
                     . '<line x1="225" y1="215" x2="227" y2="220" stroke="' . $fg3 . '" stroke-width="0.4" stroke-dasharray="1,1"/>'
                     // Print time label
                     . '<text x="120" y="205" font-family="monospace" font-size="5" fill="' . $fg3 . '">4h 22min</text>'
                     // Dimensions
                     . '<text x="35" y="275" font-family="monospace" font-size="5" fill="' . $fg3 . '">∅40mm</text>'
                     . '</svg>';
                break;

            // Automotive — gears, pistons, speedometer, engine parts
            case 'automotive':
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="260" height="260">'
                     // Large gear
                     . '<circle cx="70" cy="70" r="35" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<circle cx="70" cy="70" r="25" fill="none" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<circle cx="70" cy="70" r="8" fill="none" stroke="' . $fg1 . '" stroke-width="1"/>'
                     // Gear teeth (8 teeth)
                     . '<line x1="70" y1="32" x2="70" y2="26" stroke="' . $fg1 . '" stroke-width="3"/>'
                     . '<line x1="70" y1="108" x2="70" y2="114" stroke="' . $fg1 . '" stroke-width="3"/>'
                     . '<line x1="32" y1="70" x2="26" y2="70" stroke="' . $fg1 . '" stroke-width="3"/>'
                     . '<line x1="108" y1="70" x2="114" y2="70" stroke="' . $fg1 . '" stroke-width="3"/>'
                     . '<line x1="45" y1="45" x2="40" y2="40" stroke="' . $fg1 . '" stroke-width="3"/>'
                     . '<line x1="95" y1="95" x2="100" y2="100" stroke="' . $fg1 . '" stroke-width="3"/>'
                     . '<line x1="95" y1="45" x2="100" y2="40" stroke="' . $fg1 . '" stroke-width="3"/>'
                     . '<line x1="45" y1="95" x2="40" y2="100" stroke="' . $fg1 . '" stroke-width="3"/>'
                     // Small meshing gear
                     . '<circle cx="128" cy="70" r="18" fill="none" stroke="' . $fg2 . '" stroke-width="0.9"/>'
                     . '<circle cx="128" cy="70" r="10" fill="none" stroke="' . $fg3 . '" stroke-width="0.6"/>'
                     . '<circle cx="128" cy="70" r="4" fill="' . $fg2 . '"/>'
                     // Speedometer (bottom left)
                     . '<path d="M20,210 A60,60 0 0,1 140,210" fill="none" stroke="' . $fg1 . '" stroke-width="1.3"/>'
                     . '<path d="M30,210 A50,50 0 0,1 130,210" fill="none" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     . '<path d="M40,210 A40,40 0 0,1 120,210" fill="none" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     // Speed notches
                     . '<line x1="25" y1="195" x2="30" y2="200" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<line x1="40" y1="165" x2="46" y2="170" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<line x1="80" y1="152" x2="80" y2="158" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<line x1="120" y1="165" x2="114" y2="170" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<line x1="135" y1="195" x2="130" y2="200" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     // Needle
                     . '<line x1="80" y1="210" x2="115" y2="175" stroke="' . $fg1 . '" stroke-width="1.5"/>'
                     . '<circle cx="80" cy="210" r="4" fill="' . $fg1 . '"/>'
                     // Piston (right side)
                     . '<rect x="180" y="140" width="40" height="70" rx="3" fill="none" stroke="' . $fg2 . '" stroke-width="1"/>'
                     . '<line x1="180" y1="155" x2="220" y2="155" stroke="' . $fg1 . '" stroke-width="0.7"/>'
                     . '<line x1="180" y1="165" x2="220" y2="165" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     . '<line x1="180" y1="175" x2="220" y2="175" stroke="' . $fg1 . '" stroke-width="0.7"/>'
                     . '<rect x="192" y="210" width="16" height="30" rx="2" fill="none" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     // Connecting rod
                     . '<line x1="200" y1="140" x2="200" y2="120" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<circle cx="200" cy="120" r="5" fill="none" stroke="' . $fg1 . '" stroke-width="0.8"/>'
                     // Wrench
                     . '<path d="M175,30 L185,20 L195,30 L195,35 L190,35 L190,70 L180,70 L180,35 L175,35Z" fill="none" stroke="' . $fg3 . '" stroke-width="0.7"/>'
                     . '</svg>';
                break;

            // Medical / Health — DNA helix, heartbeat, molecules, caduceus elements
            case 'medical':
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="280" height="260">'
                     // DNA double helix
                     . '<path d="M40,10 Q60,40 40,70 Q20,100 40,130 Q60,160 40,190 Q20,220 40,250" fill="none" stroke="' . $fg1 . '" stroke-width="1.3"/>'
                     . '<path d="M70,10 Q50,40 70,70 Q90,100 70,130 Q50,160 70,190 Q90,220 70,250" fill="none" stroke="' . $fg1 . '" stroke-width="1.3"/>'
                     // Rungs between helices
                     . '<line x1="45" y1="25" x2="65" y2="25" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<line x1="38" y1="55" x2="72" y2="55" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<line x1="45" y1="85" x2="65" y2="85" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<line x1="38" y1="115" x2="72" y2="115" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<line x1="45" y1="145" x2="65" y2="145" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<line x1="38" y1="175" x2="72" y2="175" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<line x1="45" y1="205" x2="65" y2="205" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<line x1="38" y1="235" x2="72" y2="235" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     // Base pair dots
                     . '<circle cx="55" cy="25" r="1.5" fill="' . $fg1 . '"/>'
                     . '<circle cx="55" cy="85" r="1.5" fill="' . $fg1 . '"/>'
                     . '<circle cx="55" cy="145" r="1.5" fill="' . $fg1 . '"/>'
                     . '<circle cx="55" cy="205" r="1.5" fill="' . $fg1 . '"/>'
                     // ECG heartbeat line
                     . '<path d="M100,130 L130,130 L135,130 L140,110 L148,155 L155,95 L162,145 L168,115 L175,130 L200,130 L230,130" fill="none" stroke="' . $fg1 . '" stroke-width="1.5"/>'
                     . '<path d="M100,130 L230,130" fill="none" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     // Heart symbol
                     . '<path d="M160,55 C160,42 148,35 140,42 C132,35 120,42 120,55 C120,72 140,85 140,85 C140,85 160,72 160,55Z" fill="none" stroke="' . $fg2 . '" stroke-width="1"/>'
                     . '<path d="M140,78 L140,100" fill="none" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     // Molecule (lower right)
                     . '<circle cx="200" cy="200" r="10" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<circle cx="200" cy="200" r="3" fill="' . $fg2 . '"/>'
                     . '<line x1="208" y1="194" x2="235" y2="178" stroke="' . $fg2 . '" stroke-width="0.9"/>'
                     . '<circle cx="240" cy="175" r="7" fill="none" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<line x1="194" y1="209" x2="175" y2="235" stroke="' . $fg2 . '" stroke-width="0.9"/>'
                     . '<circle cx="172" cy="240" r="7" fill="none" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<line x1="210" y1="200" x2="240" y2="200" stroke="' . $fg3 . '" stroke-width="0.7"/>'
                     . '<circle cx="246" cy="200" r="5" fill="none" stroke="' . $fg3 . '" stroke-width="0.6"/>'
                     // Cross symbol (top right)
                     . '<rect x="220" y="20" width="30" height="10" rx="2" fill="none" stroke="' . $fg1 . '" stroke-width="1"/>'
                     . '<rect x="230" y="10" width="10" height="30" rx="2" fill="none" stroke="' . $fg1 . '" stroke-width="1"/>'
                     // Pill capsule
                     . '<rect x="110" y="185" width="40" height="16" rx="8" fill="none" stroke="' . $fg3 . '" stroke-width="0.7"/>'
                     . '<line x1="130" y1="185" x2="130" y2="201" stroke="' . $fg3 . '" stroke-width="0.7"/>'
                     . '</svg>';
                break;

            // Gastro / Food — utensils, chef hat, plates, ingredients
            case 'gastro':
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="280" height="260">'
                     // Chef hat
                     . '<path d="M50,80 Q50,40 80,40 Q100,20 120,40 Q150,40 150,80" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<rect x="55" y="80" width="90" height="20" rx="2" fill="none" stroke="' . $fg1 . '" stroke-width="1"/>'
                     . '<line x1="70" y1="82" x2="70" y2="98" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<line x1="100" y1="82" x2="100" y2="98" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<line x1="130" y1="82" x2="130" y2="98" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     // Fork (left)
                     . '<line x1="20" y1="130" x2="20" y2="240" stroke="' . $fg1 . '" stroke-width="1.3"/>'
                     . '<line x1="20" y1="130" x2="12" y2="130" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<line x1="12" y1="130" x2="12" y2="160" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<line x1="20" y1="130" x2="20" y2="160" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<line x1="20" y1="130" x2="28" y2="130" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<line x1="28" y1="130" x2="28" y2="160" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     // Knife (right of fork)
                     . '<line x1="45" y1="130" x2="45" y2="240" stroke="' . $fg1 . '" stroke-width="1.3"/>'
                     . '<path d="M45,130 Q55,135 55,160 L45,165" fill="none" stroke="' . $fg2 . '" stroke-width="0.9"/>'
                     // Plate (center)
                     . '<ellipse cx="140" cy="190" rx="50" ry="35" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<ellipse cx="140" cy="190" rx="35" ry="24" fill="none" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     . '<ellipse cx="140" cy="190" rx="15" ry="10" fill="none" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     // Steam above plate
                     . '<path d="M125,148 Q122,138 128,130" fill="none" stroke="' . $fg3 . '" stroke-width="0.6"/>'
                     . '<path d="M140,145 Q137,132 143,125" fill="none" stroke="' . $fg3 . '" stroke-width="0.7"/>'
                     . '<path d="M155,148 Q152,138 158,130" fill="none" stroke="' . $fg3 . '" stroke-width="0.6"/>'
                     // Spoon
                     . '<line x1="230" y1="180" x2="230" y2="250" stroke="' . $fg2 . '" stroke-width="1"/>'
                     . '<ellipse cx="230" cy="172" rx="10" ry="14" fill="none" stroke="' . $fg2 . '" stroke-width="1"/>'
                     // Whisk (upper right)
                     . '<path d="M220,30 Q215,60 220,90" fill="none" stroke="' . $fg3 . '" stroke-width="0.6"/>'
                     . '<path d="M230,30 Q225,60 230,90" fill="none" stroke="' . $fg3 . '" stroke-width="0.6"/>'
                     . '<path d="M240,30 Q235,60 240,90" fill="none" stroke="' . $fg3 . '" stroke-width="0.6"/>'
                     . '<path d="M225,20 Q230,10 235,20" fill="none" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<line x1="230" y1="90" x2="230" y2="110" stroke="' . $fg2 . '" stroke-width="1.2"/>'
                     // Coffee cup
                     . '<rect x="70" y="190" width="20" height="18" rx="2" fill="none" stroke="' . $fg3 . '" stroke-width="0.7"/>'
                     . '<path d="M90,195 Q98,195 98,200 Q98,205 90,205" fill="none" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     . '</svg>';
                break;

            // Fashion / Clothing — scissors, thread, fabric pattern, hanger, button
            case 'fashion':
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="260" height="260">'
                     // Scissors
                     . '<circle cx="40" cy="50" r="12" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<circle cx="40" cy="90" r="12" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<line x1="50" y1="55" x2="100" y2="68" stroke="' . $fg1 . '" stroke-width="1.5"/>'
                     . '<line x1="50" y1="85" x2="100" y2="72" stroke="' . $fg1 . '" stroke-width="1.5"/>'
                     . '<circle cx="48" cy="70" r="2" fill="' . $fg1 . '"/>'
                     // Hanger
                     . '<path d="M165,30 L200,70 L130,70Z" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<path d="M165,30 Q165,15 175,15 Q185,15 185,25" fill="none" stroke="' . $fg2 . '" stroke-width="1"/>'
                     . '<line x1="165" y1="70" x2="165" y2="75" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     // Thread spool
                     . '<rect x="20" y="150" width="30" height="40" rx="3" fill="none" stroke="' . $fg2 . '" stroke-width="1"/>'
                     . '<ellipse cx="35" cy="155" rx="15" ry="5" fill="none" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<ellipse cx="35" cy="185" rx="15" ry="5" fill="none" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<line x1="25" y1="160" x2="25" y2="180" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<line x1="30" y1="158" x2="30" y2="182" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<line x1="40" y1="158" x2="40" y2="182" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<line x1="45" y1="160" x2="45" y2="180" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     // Thread line from spool
                     . '<path d="M50,170 Q80,150 100,170 Q120,190 140,170" fill="none" stroke="' . $fg2 . '" stroke-width="0.7" stroke-dasharray="3,2"/>'
                     // Needle
                     . '<line x1="140" y1="170" x2="160" y2="155" stroke="' . $fg1 . '" stroke-width="0.9"/>'
                     . '<ellipse cx="162" cy="153" rx="2" ry="3" fill="none" stroke="' . $fg1 . '" stroke-width="0.6"/>'
                     // Buttons
                     . '<circle cx="210" cy="120" r="12" fill="none" stroke="' . $fg1 . '" stroke-width="1"/>'
                     . '<circle cx="210" cy="120" r="8" fill="none" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     . '<circle cx="206" cy="116" r="1.5" fill="' . $fg1 . '"/>'
                     . '<circle cx="214" cy="116" r="1.5" fill="' . $fg1 . '"/>'
                     . '<circle cx="206" cy="124" r="1.5" fill="' . $fg1 . '"/>'
                     . '<circle cx="214" cy="124" r="1.5" fill="' . $fg1 . '"/>'
                     . '<circle cx="240" cy="145" r="8" fill="none" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<circle cx="237" cy="143" r="1" fill="' . $fg2 . '"/>'
                     . '<circle cx="243" cy="143" r="1" fill="' . $fg2 . '"/>'
                     . '<circle cx="237" cy="149" r="1" fill="' . $fg2 . '"/>'
                     . '<circle cx="243" cy="149" r="1" fill="' . $fg2 . '"/>'
                     // Fabric swatch pattern (lower right) — crosshatch
                     . '<rect x="170" y="190" width="60" height="50" rx="3" fill="none" stroke="' . $fg2 . '" stroke-width="0.9"/>'
                     . '<line x1="170" y1="200" x2="230" y2="200" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="170" y1="210" x2="230" y2="210" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="170" y1="220" x2="230" y2="220" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="170" y1="230" x2="230" y2="230" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="185" y1="190" x2="185" y2="240" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="200" y1="190" x2="200" y2="240" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="215" y1="190" x2="215" y2="240" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     // Mannequin silhouette (subtle)
                     . '<path d="M110,200 Q110,215 100,230 Q90,245 100,250 L120,250 Q130,245 120,230 Q110,215 110,200" fill="none" stroke="' . $fg3 . '" stroke-width="0.6"/>'
                     . '<circle cx="110" cy="195" r="5" fill="none" stroke="' . $fg3 . '" stroke-width="0.6"/>'
                     . '</svg>';
                break;

            // Construction / Architecture — blueprints, bricks, crane, compass
            case 'construction':
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="280" height="260">'
                     // Blueprint grid
                     . '<line x1="0" y1="40" x2="280" y2="40" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="0" y1="80" x2="280" y2="80" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="0" y1="120" x2="280" y2="120" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="0" y1="160" x2="280" y2="160" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="0" y1="200" x2="280" y2="200" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="0" y1="240" x2="280" y2="240" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="40" y1="0" x2="40" y2="260" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="80" y1="0" x2="80" y2="260" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="120" y1="0" x2="120" y2="260" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="160" y1="0" x2="160" y2="260" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="200" y1="0" x2="200" y2="260" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="240" y1="0" x2="240" y2="260" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     // House blueprint
                     . '<rect x="20" y="60" width="80" height="60" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<path d="M15,60 L60,25 L105,60" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<rect x="45" y="90" width="20" height="30" fill="none" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<rect x="25" y="75" width="15" height="15" fill="none" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     . '<line x1="25" y1="82" x2="40" y2="82" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<line x1="32" y1="75" x2="32" y2="90" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<rect x="70" y="75" width="15" height="15" fill="none" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     . '<line x1="70" y1="82" x2="85" y2="82" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<line x1="77" y1="75" x2="77" y2="90" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     // Dimension arrows
                     . '<line x1="20" y1="130" x2="100" y2="130" stroke="' . $fg2 . '" stroke-width="0.6"/>'
                     . '<path d="M20,127 L20,133" stroke="' . $fg2 . '" stroke-width="0.6"/>'
                     . '<path d="M100,127 L100,133" stroke="' . $fg2 . '" stroke-width="0.6"/>'
                     . '<text x="48" y="128" font-family="monospace" font-size="6" fill="' . $fg2 . '">12m</text>'
                     // Crane (right side)
                     . '<line x1="200" y1="260" x2="200" y2="40" stroke="' . $fg1 . '" stroke-width="1.5"/>'
                     . '<line x1="200" y1="40" x2="270" y2="40" stroke="' . $fg1 . '" stroke-width="1.3"/>'
                     . '<line x1="200" y1="40" x2="170" y2="40" stroke="' . $fg1 . '" stroke-width="1.3"/>'
                     . '<line x1="170" y1="40" x2="175" y2="60" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     . '<line x1="200" y1="40" x2="200" y2="60" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     // Crane diagonals
                     . '<line x1="200" y1="80" x2="230" y2="40" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     . '<line x1="200" y1="80" x2="250" y2="40" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     // Crane rope and hook
                     . '<line x1="260" y1="40" x2="260" y2="100" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     . '<path d="M255,100 Q260,112 265,100" fill="none" stroke="' . $fg1 . '" stroke-width="1"/>'
                     // Bricks (lower left)
                     . '<rect x="20" y="175" width="30" height="12" fill="none" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     . '<rect x="52" y="175" width="30" height="12" fill="none" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     . '<rect x="84" y="175" width="30" height="12" fill="none" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     . '<rect x="36" y="189" width="30" height="12" fill="none" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     . '<rect x="68" y="189" width="30" height="12" fill="none" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     . '<rect x="20" y="203" width="30" height="12" fill="none" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     . '<rect x="52" y="203" width="30" height="12" fill="none" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     // Compass/protractor
                     . '<circle cx="170" cy="200" r="18" fill="none" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<line x1="170" y1="200" x2="170" y2="182" stroke="' . $fg1 . '" stroke-width="0.9"/>'
                     . '<line x1="170" y1="200" x2="185" y2="210" stroke="' . $fg1 . '" stroke-width="0.7"/>'
                     . '<circle cx="170" cy="200" r="2" fill="' . $fg1 . '"/>'
                     . '</svg>';
                break;

            // Education / Science — formulas, atom, books, graduation cap
            case 'education':
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="280" height="260">'
                     // Atom with electron orbits
                     . '<ellipse cx="80" cy="70" rx="45" ry="18" fill="none" stroke="' . $fg1 . '" stroke-width="1" transform="rotate(-30,80,70)"/>'
                     . '<ellipse cx="80" cy="70" rx="45" ry="18" fill="none" stroke="' . $fg2 . '" stroke-width="0.8" transform="rotate(30,80,70)"/>'
                     . '<ellipse cx="80" cy="70" rx="45" ry="18" fill="none" stroke="' . $fg1 . '" stroke-width="0.9" transform="rotate(90,80,70)"/>'
                     . '<circle cx="80" cy="70" r="6" fill="' . $fg1 . '" opacity="0.5"/>'
                     . '<circle cx="80" cy="70" r="3" fill="' . $fg1 . '"/>'
                     // Electrons
                     . '<circle cx="115" cy="50" r="2.5" fill="' . $fg2 . '"/>'
                     . '<circle cx="55" cy="95" r="2.5" fill="' . $fg2 . '"/>'
                     . '<circle cx="80" cy="25" r="2.5" fill="' . $fg2 . '"/>'
                     // Formulas
                     . '<text x="160" y="40" font-family="serif" font-style="italic" font-size="10" fill="' . $fg2 . '">E = mc</text>'
                     . '<text x="200" y="36" font-family="serif" font-size="7" fill="' . $fg2 . '">2</text>'
                     . '<text x="160" y="65" font-family="serif" font-style="italic" font-size="9" fill="' . $fg3 . '">F = ma</text>'
                     . '<text x="160" y="85" font-family="serif" font-style="italic" font-size="9" fill="' . $fg3 . '">&#x3C0;r</text>'
                     . '<text x="175" y="81" font-family="serif" font-size="6" fill="' . $fg3 . '">2</text>'
                     // Stack of books
                     . '<rect x="15" y="160" width="70" height="12" rx="1" fill="none" stroke="' . $fg1 . '" stroke-width="1"/>'
                     . '<rect x="20" y="148" width="60" height="12" rx="1" fill="none" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<rect x="10" y="172" width="80" height="14" rx="1" fill="none" stroke="' . $fg1 . '" stroke-width="1"/>'
                     . '<line x1="18" y1="172" x2="18" y2="186" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<line x1="80" y1="172" x2="80" y2="186" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     // Open book
                     . '<path d="M25,210 Q50,200 50,210 L50,250 Q50,240 25,250Z" fill="none" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<path d="M75,210 Q50,200 50,210 L50,250 Q50,240 75,250Z" fill="none" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<line x1="30" y1="218" x2="48" y2="215" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="30" y1="226" x2="48" y2="223" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="30" y1="234" x2="48" y2="231" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="52" y1="215" x2="70" y2="218" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="52" y1="223" x2="70" y2="226" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     // Graduation cap
                     . '<path d="M165,140 L220,155 L165,170 L110,155Z" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<line x1="165" y1="170" x2="165" y2="195" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<path d="M135,162 L135,185 Q165,200 195,185 L195,162" fill="none" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<circle cx="165" cy="197" r="2" fill="' . $fg2 . '"/>'
                     // Ruler
                     . '<rect x="140" y="215" width="120" height="14" rx="1" fill="none" stroke="' . $fg3 . '" stroke-width="0.6"/>'
                     . '<line x1="155" y1="215" x2="155" y2="222" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<line x1="170" y1="215" x2="170" y2="225" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<line x1="185" y1="215" x2="185" y2="222" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<line x1="200" y1="215" x2="200" y2="225" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<line x1="215" y1="215" x2="215" y2="222" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<line x1="230" y1="215" x2="230" y2="225" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<line x1="245" y1="215" x2="245" y2="222" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     // Pencil
                     . '<line x1="230" y1="140" x2="260" y2="170" stroke="' . $fg2 . '" stroke-width="2"/>'
                     . '<path d="M260,170 L265,175 L258,175Z" fill="' . $fg1 . '"/>'
                     . '</svg>';
                break;

            // Fitness / Sport — dumbbells, track, heartrate, sneaker
            case 'fitness':
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="280" height="260">'
                     // Dumbbell
                     . '<rect x="30" y="40" width="15" height="50" rx="3" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<rect x="20" y="48" width="10" height="34" rx="2" fill="none" stroke="' . $fg1 . '" stroke-width="1"/>'
                     . '<rect x="115" y="40" width="15" height="50" rx="3" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<rect x="130" y="48" width="10" height="34" rx="2" fill="none" stroke="' . $fg1 . '" stroke-width="1"/>'
                     . '<rect x="45" y="58" width="70" height="14" rx="4" fill="none" stroke="' . $fg2 . '" stroke-width="0.9"/>'
                     // Barbell grip texture
                     . '<line x1="60" y1="60" x2="60" y2="70" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<line x1="70" y1="60" x2="70" y2="70" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<line x1="80" y1="60" x2="80" y2="70" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<line x1="90" y1="60" x2="90" y2="70" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<line x1="100" y1="60" x2="100" y2="70" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     // Running track (center)
                     . '<path d="M20,160 L160,160 A50,50 0 0,1 160,210 L20,210 A50,50 0 0,1 20,160Z" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<path d="M20,168 L156,168 A42,42 0 0,1 156,202 L20,202 A42,42 0 0,1 20,168Z" fill="none" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     . '<path d="M20,176 L152,176 A34,34 0 0,1 152,194 L20,194 A34,34 0 0,1 20,176Z" fill="none" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     // Lane markings
                     . '<line x1="40" y1="160" x2="40" y2="210" stroke="' . $fg3 . '" stroke-width="0.4" stroke-dasharray="3,3"/>'
                     . '<line x1="70" y1="160" x2="70" y2="210" stroke="' . $fg3 . '" stroke-width="0.4" stroke-dasharray="3,3"/>'
                     . '<line x1="100" y1="160" x2="100" y2="210" stroke="' . $fg3 . '" stroke-width="0.4" stroke-dasharray="3,3"/>'
                     . '<line x1="130" y1="160" x2="130" y2="210" stroke="' . $fg3 . '" stroke-width="0.4" stroke-dasharray="3,3"/>'
                     // Heart rate
                     . '<path d="M180,140 L195,140 L200,125 L208,160 L215,120 L222,150 L228,135 L235,140 L260,140" fill="none" stroke="' . $fg1 . '" stroke-width="1.3"/>'
                     // Stopwatch
                     . '<circle cx="220" cy="60" r="22" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<circle cx="220" cy="60" r="18" fill="none" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     . '<line x1="220" y1="60" x2="220" y2="46" stroke="' . $fg1 . '" stroke-width="1"/>'
                     . '<line x1="220" y1="60" x2="230" y2="55" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     . '<rect x="215" y="33" width="10" height="6" rx="2" fill="none" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<circle cx="220" cy="60" r="2" fill="' . $fg1 . '"/>'
                     // Sneaker (bottom right), simplified
                     . '<path d="M190,230 Q195,215 220,215 L250,215 Q265,215 265,225 L265,235 Q265,245 250,245 L200,245 Q190,245 190,235Z" fill="none" stroke="' . $fg2 . '" stroke-width="0.9"/>'
                     . '<line x1="220" y1="215" x2="220" y2="245" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<path d="M195,225 L215,220" fill="none" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<path d="M195,235 L215,228" fill="none" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '</svg>';
                break;

            // Music — notes, staff lines, guitar, vinyl record, sound waves
            case 'music':
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="280" height="260">'
                     // Staff lines
                     . '<line x1="10" y1="40" x2="180" y2="40" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     . '<line x1="10" y1="50" x2="180" y2="50" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     . '<line x1="10" y1="60" x2="180" y2="60" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     . '<line x1="10" y1="70" x2="180" y2="70" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     . '<line x1="10" y1="80" x2="180" y2="80" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     // Treble clef (simplified)
                     . '<path d="M25,80 Q20,65 25,50 Q30,35 25,25 Q28,35 35,45 Q42,55 35,70 Q30,78 25,80" fill="none" stroke="' . $fg1 . '" stroke-width="1.3"/>'
                     . '<line x1="25" y1="25" x2="25" y2="88" stroke="' . $fg1 . '" stroke-width="0.8"/>'
                     // Notes on staff
                     . '<ellipse cx="60" cy="60" rx="5" ry="3.5" fill="' . $fg1 . '" transform="rotate(-15,60,60)"/>'
                     . '<line x1="65" y1="60" x2="65" y2="35" stroke="' . $fg1 . '" stroke-width="1"/>'
                     . '<ellipse cx="90" cy="50" rx="5" ry="3.5" fill="' . $fg1 . '" transform="rotate(-15,90,50)"/>'
                     . '<line x1="95" y1="50" x2="95" y2="25" stroke="' . $fg1 . '" stroke-width="1"/>'
                     . '<ellipse cx="120" cy="70" rx="5" ry="3.5" fill="' . $fg2 . '" transform="rotate(-15,120,70)"/>'
                     . '<line x1="125" y1="70" x2="125" y2="45" stroke="' . $fg2 . '" stroke-width="0.9"/>'
                     // Beam between two eighth notes
                     . '<ellipse cx="145" cy="55" rx="4" ry="3" fill="' . $fg1 . '" transform="rotate(-15,145,55)"/>'
                     . '<line x1="149" y1="55" x2="149" y2="30" stroke="' . $fg1 . '" stroke-width="0.9"/>'
                     . '<ellipse cx="160" cy="65" rx="4" ry="3" fill="' . $fg1 . '" transform="rotate(-15,160,65)"/>'
                     . '<line x1="164" y1="65" x2="164" y2="35" stroke="' . $fg1 . '" stroke-width="0.9"/>'
                     . '<line x1="149" y1="30" x2="164" y2="35" stroke="' . $fg1 . '" stroke-width="1.5"/>'
                     // Vinyl record (center)
                     . '<circle cx="100" cy="170" r="45" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<circle cx="100" cy="170" r="38" fill="none" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<circle cx="100" cy="170" r="30" fill="none" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<circle cx="100" cy="170" r="22" fill="none" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<circle cx="100" cy="170" r="15" fill="none" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<circle cx="100" cy="170" r="4" fill="' . $fg1 . '"/>'
                     // Tone arm
                     . '<line x1="140" y1="140" x2="155" y2="115" stroke="' . $fg2 . '" stroke-width="1"/>'
                     . '<line x1="155" y1="115" x2="165" y2="105" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<circle cx="165" cy="105" r="3" fill="none" stroke="' . $fg2 . '" stroke-width="0.6"/>'
                     // Guitar body (right)
                     . '<path d="M220,100 Q240,110 245,140 Q248,165 235,180 Q220,195 205,180 Q192,165 195,140 Q198,110 220,100Z" fill="none" stroke="' . $fg2 . '" stroke-width="1"/>'
                     . '<circle cx="220" cy="150" r="10" fill="none" stroke="' . $fg3 . '" stroke-width="0.6"/>'
                     . '<line x1="220" y1="100" x2="220" y2="55" stroke="' . $fg2 . '" stroke-width="1.2"/>'
                     . '<rect x="215" y="52" width="10" height="8" rx="1" fill="none" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     // Strings
                     . '<line x1="217" y1="100" x2="217" y2="185" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="220" y1="100" x2="220" y2="185" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     . '<line x1="223" y1="100" x2="223" y2="185" stroke="' . $fg3 . '" stroke-width="0.3"/>'
                     // Sound waves (bottom)
                     . '<path d="M30,230 Q30,220 35,215 Q40,220 40,230 Q40,240 35,245 Q30,240 30,230Z" fill="none" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     . '<path d="M25,230 Q25,215 35,208 Q45,215 45,230 Q45,245 35,252 Q25,245 25,230Z" fill="none" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     . '<path d="M20,230 Q20,210 35,200 Q50,210 50,230 Q50,250 35,260 Q20,250 20,230Z" fill="none" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     // Headphones
                     . '<path d="M210,210 Q210,195 225,190 Q240,195 240,210" fill="none" stroke="' . $fg2 . '" stroke-width="1.2"/>'
                     . '<rect x="205" y="210" width="10" height="18" rx="3" fill="none" stroke="' . $fg1 . '" stroke-width="1"/>'
                     . '<rect x="235" y="210" width="10" height="18" rx="3" fill="none" stroke="' . $fg1 . '" stroke-width="1"/>'
                     . '</svg>';
                break;

            // Tech / IT — code brackets, server rack, data flow, terminal
            case 'tech':
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="280" height="260">'
                     // Code brackets
                     . '<text x="15" y="30" font-family="monospace" font-size="14" fill="' . $fg1 . '">{</text>'
                     . '<text x="15" y="50" font-family="monospace" font-size="9" fill="' . $fg3 . '">  if (</text>'
                     . '<text x="15" y="65" font-family="monospace" font-size="9" fill="' . $fg2 . '">    true</text>'
                     . '<text x="15" y="80" font-family="monospace" font-size="9" fill="' . $fg3 . '">  ) {</text>'
                     . '<text x="15" y="95" font-family="monospace" font-size="9" fill="' . $fg2 . '">    run();</text>'
                     . '<text x="15" y="110" font-family="monospace" font-size="9" fill="' . $fg3 . '">  }</text>'
                     . '<text x="15" y="128" font-family="monospace" font-size="14" fill="' . $fg1 . '">}</text>'
                     // Terminal window
                     . '<rect x="120" y="15" width="140" height="90" rx="5" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<line x1="120" y1="30" x2="260" y2="30" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<circle cx="132" cy="22" r="3" fill="' . $fg2 . '"/>'
                     . '<circle cx="142" cy="22" r="3" fill="' . $fg3 . '"/>'
                     . '<circle cx="152" cy="22" r="3" fill="' . $fg3 . '"/>'
                     . '<text x="130" y="48" font-family="monospace" font-size="7" fill="' . $fg2 . '">$ npm build</text>'
                     . '<text x="130" y="60" font-family="monospace" font-size="7" fill="' . $fg3 . '">compiling...</text>'
                     . '<text x="130" y="72" font-family="monospace" font-size="7" fill="' . $fg1 . '">&#x2713; done 2.4s</text>'
                     . '<rect x="130" y="80" width="40" height="2" fill="' . $fg1 . '" opacity="0.6"/>'
                     // Server rack
                     . '<rect x="20" y="150" width="80" height="100" rx="3" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<line x1="20" y1="175" x2="100" y2="175" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     . '<line x1="20" y1="200" x2="100" y2="200" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     . '<line x1="20" y1="225" x2="100" y2="225" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     // Server LEDs
                     . '<circle cx="35" cy="162" r="2" fill="' . $fg1 . '"/>'
                     . '<circle cx="45" cy="162" r="2" fill="' . $fg2 . '"/>'
                     . '<circle cx="35" cy="187" r="2" fill="' . $fg1 . '"/>'
                     . '<circle cx="45" cy="187" r="2" fill="' . $fg3 . '"/>'
                     . '<circle cx="35" cy="212" r="2" fill="' . $fg1 . '"/>'
                     . '<circle cx="45" cy="212" r="2" fill="' . $fg1 . '"/>'
                     // Server ventilation
                     . '<line x1="65" y1="155" x2="90" y2="155" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<line x1="65" y1="159" x2="90" y2="159" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<line x1="65" y1="163" x2="90" y2="163" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<line x1="65" y1="167" x2="90" y2="167" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     // Data flow arrows (connecting server to cloud)
                     . '<path d="M100,165 Q120,155 140,155 Q160,155 180,165" fill="none" stroke="' . $fg2 . '" stroke-width="0.8" stroke-dasharray="4,3"/>'
                     . '<path d="M100,190 Q130,180 160,185 Q180,188 195,180" fill="none" stroke="' . $fg3 . '" stroke-width="0.6" stroke-dasharray="3,3"/>'
                     // Cloud
                     . '<path d="M180,175 Q175,155 195,150 Q210,140 225,150 Q245,145 250,160 Q260,165 255,178 Q252,190 235,190 L190,190 Q175,190 175,178Z" fill="none" stroke="' . $fg1 . '" stroke-width="1.1"/>'
                     // Binary
                     . '<text x="150" y="225" font-family="monospace" font-size="8" fill="' . $fg3 . '">01101001</text>'
                     . '<text x="150" y="238" font-family="monospace" font-size="8" fill="' . $fg3 . '">10010110</text>'
                     . '<text x="150" y="251" font-family="monospace" font-size="8" fill="' . $fg3 . '">01010011</text>'
                     // Git branch
                     . '<circle cx="250" cy="220" r="3" fill="' . $fg1 . '"/>'
                     . '<circle cx="250" cy="240" r="3" fill="' . $fg2 . '"/>'
                     . '<circle cx="265" cy="230" r="3" fill="' . $fg2 . '"/>'
                     . '<line x1="250" y1="223" x2="250" y2="237" stroke="' . $fg1 . '" stroke-width="0.8"/>'
                     . '<path d="M250,230 L262,230" fill="none" stroke="' . $fg2 . '" stroke-width="0.6"/>'
                     . '</svg>';
                break;

            // Nature / Eco — leaves, trees, water drops, sun, mountains
            case 'nature':
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="280" height="260">'
                     // Large leaf
                     . '<path d="M60,50 Q90,20 120,50 Q90,80 60,50Z" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<line x1="60" y1="50" x2="120" y2="50" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     . '<line x1="78" y1="50" x2="72" y2="38" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<line x1="90" y1="50" x2="85" y2="35" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<line x1="102" y1="50" x2="98" y2="38" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<line x1="78" y1="50" x2="72" y2="62" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<line x1="90" y1="50" x2="85" y2="65" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     . '<line x1="102" y1="50" x2="98" y2="62" stroke="' . $fg3 . '" stroke-width="0.4"/>'
                     // Stem
                     . '<path d="M120,50 Q130,60 125,80" fill="none" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     // Tree
                     . '<line x1="210" y1="120" x2="210" y2="180" stroke="' . $fg1 . '" stroke-width="2"/>'
                     . '<circle cx="210" cy="90" r="35" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<circle cx="195" cy="100" r="18" fill="none" stroke="' . $fg2 . '" stroke-width="0.6"/>'
                     . '<circle cx="225" cy="100" r="18" fill="none" stroke="' . $fg2 . '" stroke-width="0.6"/>'
                     . '<circle cx="210" cy="80" r="16" fill="none" stroke="' . $fg2 . '" stroke-width="0.6"/>'
                     // Branches
                     . '<path d="M210,145 Q195,135 185,140" fill="none" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     . '<path d="M210,145 Q225,135 235,140" fill="none" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     // Mountains
                     . '<path d="M0,220 L40,170 L65,200 L95,155 L130,220" fill="none" stroke="' . $fg1 . '" stroke-width="1.3"/>'
                     . '<path d="M80,180 L95,155 L110,180" fill="none" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     // Sun
                     . '<circle cx="50" cy="130" r="15" fill="none" stroke="' . $fg1 . '" stroke-width="1.2"/>'
                     . '<line x1="50" y1="110" x2="50" y2="105" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<line x1="50" y1="150" x2="50" y2="155" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<line x1="30" y1="130" x2="25" y2="130" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<line x1="70" y1="130" x2="75" y2="130" stroke="' . $fg2 . '" stroke-width="0.8"/>'
                     . '<line x1="39" y1="119" x2="35" y2="115" stroke="' . $fg3 . '" stroke-width="0.6"/>'
                     . '<line x1="61" y1="119" x2="65" y2="115" stroke="' . $fg3 . '" stroke-width="0.6"/>'
                     . '<line x1="39" y1="141" x2="35" y2="145" stroke="' . $fg3 . '" stroke-width="0.6"/>'
                     . '<line x1="61" y1="141" x2="65" y2="145" stroke="' . $fg3 . '" stroke-width="0.6"/>'
                     // Water drops
                     . '<path d="M155,210 Q160,195 165,210 Q165,220 160,222 Q155,220 155,210Z" fill="none" stroke="' . $fg2 . '" stroke-width="0.9"/>'
                     . '<path d="M175,225 Q178,218 181,225 Q181,230 178,232 Q175,230 175,225Z" fill="none" stroke="' . $fg3 . '" stroke-width="0.6"/>'
                     . '<path d="M145,235 Q148,228 151,235 Q151,240 148,242 Q145,240 145,235Z" fill="none" stroke="' . $fg3 . '" stroke-width="0.6"/>'
                     // Flowers (small accent)
                     . '<circle cx="240" cy="210" r="3" fill="' . $fg1 . '"/>'
                     . '<circle cx="237" cy="205" r="4" fill="none" stroke="' . $fg2 . '" stroke-width="0.5"/>'
                     . '<circle cx="243" cy="205" r="4" fill="none" stroke="' . $fg2 . '" stroke-width="0.5"/>'
                     . '<circle cx="245" cy="212" r="4" fill="none" stroke="' . $fg2 . '" stroke-width="0.5"/>'
                     . '<circle cx="235" cy="212" r="4" fill="none" stroke="' . $fg2 . '" stroke-width="0.5"/>'
                     . '<line x1="240" y1="218" x2="240" y2="245" stroke="' . $fg2 . '" stroke-width="0.7"/>'
                     . '<path d="M240,230 Q248,225 252,228" fill="none" stroke="' . $fg3 . '" stroke-width="0.5"/>'
                     . '</svg>';
                break;

            default:
                return "background:{$bg}";
        }

        $encoded = 'data:image/svg+xml,' . rawurlencode( $svg );

        return "background-color:{$bg};background-image:url({$encoded});background-repeat:repeat;background-size:auto";
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
