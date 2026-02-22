<?php
/**
 * Flavor Theme Customizer — kontrola wyglądu motywu i wtyczki Flavor Commerce
 *
 * Jeden kolor akcentu + tryb jasny/ciemny = pełna, harmonĳna paleta.
 *
 * @package Flavor
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* =====================================================================
 *  PHP-side color helpers (hex ↔ HSL)
 * ===================================================================== */

function flavor_hex_to_hsl( $hex ) {
    $hex = ltrim( $hex, '#' );
    if ( strlen( $hex ) === 3 ) {
        $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    }
    $r = hexdec( substr( $hex, 0, 2 ) ) / 255;
    $g = hexdec( substr( $hex, 2, 2 ) ) / 255;
    $b = hexdec( substr( $hex, 4, 2 ) ) / 255;

    $max = max( $r, $g, $b );
    $min = min( $r, $g, $b );
    $l   = ( $max + $min ) / 2;
    $h   = 0;
    $s   = 0;

    if ( $max !== $min ) {
        $d = $max - $min;
        $s = $l > 0.5 ? $d / ( 2 - $max - $min ) : $d / ( $max + $min );
        switch ( $max ) {
            case $r: $h = ( ( $g - $b ) / $d + ( $g < $b ? 6 : 0 ) ) / 6; break;
            case $g: $h = ( ( $b - $r ) / $d + 2 ) / 6; break;
            case $b: $h = ( ( $r - $g ) / $d + 4 ) / 6; break;
        }
    }
    return array(
        'h' => round( $h * 360 ),
        's' => round( $s * 100 ),
        'l' => round( $l * 100 ),
    );
}

function flavor_hsl_to_hex( $h, $s, $l ) {
    $h /= 360; $s /= 100; $l /= 100;
    if ( $s == 0 ) {
        $r = $g = $b = $l;
    } else {
        $hue2rgb = function( $p, $q, $t ) {
            if ( $t < 0 ) $t += 1;
            if ( $t > 1 ) $t -= 1;
            if ( $t < 1/6 ) return $p + ( $q - $p ) * 6 * $t;
            if ( $t < 1/2 ) return $q;
            if ( $t < 2/3 ) return $p + ( $q - $p ) * ( 2/3 - $t ) * 6;
            return $p;
        };
        $q = $l < 0.5 ? $l * ( 1 + $s ) : $l + $s - $l * $s;
        $p = 2 * $l - $q;
        $r = $hue2rgb( $p, $q, $h + 1/3 );
        $g = $hue2rgb( $p, $q, $h );
        $b = $hue2rgb( $p, $q, $h - 1/3 );
    }
    return '#' . sprintf( '%02x%02x%02x', round( $r * 255 ), round( $g * 255 ), round( $b * 255 ) );
}

function flavor_clamp( $v, $min, $max ) {
    return max( $min, min( $max, $v ) );
}

/**
 * Wygeneruj pełną paletę kolorów z akcentu + trybu
 */
function flavor_generate_palette( $accent_hex, $mode = 'light' ) {
    $a      = flavor_hex_to_hsl( $accent_hex );
    $isDark = ( $mode === 'dark' );

    return array(
        'accent'       => $accent_hex,
        'accent_hover' => flavor_hsl_to_hex( $a['h'], flavor_clamp( $a['s'] + 5, 0, 100 ), flavor_clamp( $a['l'] - 10, 5, 90 ) ),
        'accent_light' => $isDark
            ? flavor_hsl_to_hex( $a['h'], flavor_clamp( round( $a['s'] * 0.4 ), 5, 30 ), 18 )
            : flavor_hsl_to_hex( $a['h'], flavor_clamp( round( $a['s'] * 0.5 ), 10, 50 ), 95 ),
        'text'         => $isDark ? flavor_hsl_to_hex( $a['h'], 8, 88 ) : flavor_hsl_to_hex( $a['h'], flavor_clamp( round( $a['s'] * 0.35 ), 10, 30 ), 20 ),
        'text_light'   => $isDark ? flavor_hsl_to_hex( $a['h'], 8, 60 ) : flavor_hsl_to_hex( $a['h'], flavor_clamp( round( $a['s'] * 0.25 ), 8, 20 ), 50 ),
        'bg'           => $isDark
            ? flavor_hsl_to_hex( $a['h'], flavor_clamp( round( $a['s'] * 0.2 ), 5, 15 ), 9 )
            : flavor_hsl_to_hex( $a['h'], flavor_clamp( round( $a['s'] * 0.1 ), 3, 12 ), 97 ),
        'surface'      => $isDark
            ? flavor_hsl_to_hex( $a['h'], flavor_clamp( round( $a['s'] * 0.2 ), 5, 15 ), 14 )
            : '#ffffff',
        'border'       => $isDark
            ? flavor_hsl_to_hex( $a['h'], flavor_clamp( round( $a['s'] * 0.15 ), 5, 12 ), 22 )
            : flavor_hsl_to_hex( $a['h'], flavor_clamp( round( $a['s'] * 0.15 ), 8, 20 ), 88 ),
        'header_bg'    => $isDark
            ? flavor_hsl_to_hex( $a['h'], flavor_clamp( round( $a['s'] * 0.2 ), 5, 15 ), 12 )
            : '#ffffff',
        'footer_bg'    => $isDark
            ? flavor_hsl_to_hex( $a['h'], flavor_clamp( round( $a['s'] * 0.15 ), 5, 12 ), 7 )
            : flavor_hsl_to_hex( $a['h'], flavor_clamp( round( $a['s'] * 0.12 ), 5, 15 ), 94 ),
        'success'      => $isDark ? '#2ecc71' : '#27ae60',
        'danger'       => $isDark ? '#e74c3c' : '#e74c3c',
        'warning'      => $isDark ? '#f1c40f' : '#f39c12',
    );
}


/* =====================================================================
 *  Ukryj wbudowane panele Menu i Widżety w Customizerze
 * ===================================================================== */
add_filter( 'customize_loaded_components', function ( $components ) {
    $components = array_diff( $components, array( 'nav_menus', 'widgets' ) );
    return $components;
} );

/* =====================================================================
 *  Rejestracja sekcji, ustawień i kontrolek Customizera
 * ===================================================================== */

function flavor_customize_register( $wp_customize ) {

    // ── Panel ──
    $wp_customize->add_panel( 'flavor_panel', array(
        'title'    => fc__( 'cust_store_settings', 'admin' ),
        'priority' => 39,
    ) );

    // =================================================================
    //  Sekcja: Kolory
    // =================================================================
    $wp_customize->add_section( 'flavor_colors', array(
        'title'       => fc__( 'cust_colors', 'admin' ),
        'priority'    => 40,
        'description' => fc__( 'cust_colors_desc', 'admin' ),
    ) );

    // ── Tryb jasny / ciemny ──
    $wp_customize->add_setting( 'flavor_color_mode', array(
        'default'           => 'light',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'flavor_color_mode', array(
        'label'   => fc__( 'cust_color_mode', 'admin' ),
        'section' => 'flavor_colors',
        'type'    => 'select',
        'choices' => array(
            'light' => fc__( 'cust_mode_light', 'admin' ),
            'dark'  => fc__( 'cust_mode_dark', 'admin' ),
        ),
        'priority' => 1,
    ) );

    // ── Kolor akcentu (główny) ──
    $wp_customize->add_setting( 'flavor_color_accent', array(
        'default'           => '#4a90d9',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'flavor_color_accent', array(
        'label'    => fc__( 'cust_accent_color', 'admin' ),
        'section'  => 'flavor_colors',
        'priority' => 2,
    ) ) );

    // ── Kolory pochodne (auto-generowane, możliwe do ręcznej korekty) ──
    $derived_colors = array(
        'flavor_color_accent_hover' => array( 'label' => fc__( 'cust_accent_hover', 'admin' ),            'default' => '#3a7bc8' ),
        'flavor_color_accent_light' => array( 'label' => fc__( 'cust_accent_light', 'admin' ),            'default' => '#e8f0fe' ),
        'flavor_color_text'         => array( 'label' => fc__( 'cust_color_text', 'admin' ),                     'default' => '#2c3e50' ),
        'flavor_color_text_light'   => array( 'label' => fc__( 'cust_color_text_light', 'admin' ),             'default' => '#6b7c93' ),
        'flavor_color_bg'           => array( 'label' => fc__( 'cust_color_bg', 'admin' ),                'default' => '#f8f9fc' ),
        'flavor_color_surface'      => array( 'label' => fc__( 'cust_color_surface', 'admin' ),      'default' => '#ffffff' ),
        'flavor_color_border'       => array( 'label' => fc__( 'cust_color_border', 'admin' ),               'default' => '#e2e8f0' ),
        'flavor_color_header_bg'    => array( 'label' => fc__( 'cust_color_header_bg', 'admin' ),              'default' => '#ffffff' ),
        'flavor_color_footer_bg'    => array( 'label' => fc__( 'cust_color_footer_bg', 'admin' ),                'default' => '#f1f4f8' ),
        'flavor_color_success'      => array( 'label' => fc__( 'cust_color_success', 'admin' ),                    'default' => '#27ae60' ),
        'flavor_color_danger'       => array( 'label' => fc__( 'cust_color_danger', 'admin' ),  'default' => '#e74c3c' ),
        'flavor_color_warning'      => array( 'label' => fc__( 'cust_color_warning', 'admin' ),               'default' => '#f39c12' ),
    );

    $p = 10;
    foreach ( $derived_colors as $id => $opts ) {
        $wp_customize->add_setting( $id, array(
            'default'           => $opts['default'],
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id, array(
            'label'    => $opts['label'],
            'section'  => 'flavor_colors',
            'priority' => $p++,
        ) ) );
    }

    // =================================================================
    //  Sekcja: Typografia
    // =================================================================
    $wp_customize->add_section( 'flavor_typography', array(
        'title'    => fc__( 'cust_typography', 'admin' ),
        'priority' => 41,
    ) );

    $wp_customize->add_setting( 'flavor_font_family', array(
        'default'           => 'system',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'flavor_font_family', array(
        'label'   => fc__( 'cust_font_family', 'admin' ),
        'section' => 'flavor_typography',
        'type'    => 'select',
        'choices' => array(
            'system'     => fc__( 'cust_font_system', 'admin' ),
            'inter'      => 'Inter',
            'poppins'    => 'Poppins',
            'roboto'     => 'Roboto',
            'lato'       => 'Lato',
            'open-sans'  => 'Open Sans',
            'montserrat' => 'Montserrat',
            'nunito'     => 'Nunito',
            'raleway'    => 'Raleway',
            'dm-sans'    => 'DM Sans',
        ),
    ) );

    $wp_customize->add_setting( 'flavor_font_size', array(
        'default'           => 16,
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'flavor_font_size', array(
        'label'       => fc__( 'cust_font_size', 'admin' ),
        'section'     => 'flavor_typography',
        'type'        => 'number',
        'input_attrs' => array( 'min' => 12, 'max' => 22, 'step' => 1 ),
    ) );

    // =================================================================
    //  Sekcja: Układ
    // =================================================================
    $wp_customize->add_section( 'flavor_header', array(
        'title'    => fc__( 'cust_header', 'admin' ),
        'priority' => 39,
    ) );

    $wp_customize->add_section( 'flavor_layout', array(
        'title'    => fc__( 'cust_layout', 'admin' ),
        'priority' => 42,
    ) );

    $wp_customize->add_setting( 'flavor_container_max', array(
        'default'           => 1200,
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'flavor_container_max', array(
        'label'       => fc__( 'cust_container_max', 'admin' ),
        'section'     => 'flavor_layout',
        'type'        => 'number',
        'input_attrs' => array( 'min' => 900, 'max' => 1800, 'step' => 10 ),
    ) );

    $wp_customize->add_setting( 'flavor_border_radius', array(
        'default'           => 0,
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'flavor_border_radius', array(
        'label'       => fc__( 'cust_border_radius', 'admin' ),
        'section'     => 'flavor_layout',
        'type'        => 'number',
        'input_attrs' => array( 'min' => 0, 'max' => 30, 'step' => 1 ),
    ) );

    $wp_customize->add_setting( 'flavor_sticky_header_desktop', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'flavor_sticky_header_desktop', array(
        'label'   => fc__( 'cust_sticky_desktop', 'admin' ),
        'section' => 'flavor_header',
        'type'    => 'checkbox',
    ) );

    $wp_customize->add_setting( 'flavor_sticky_header_tablet', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'flavor_sticky_header_tablet', array(
        'label'   => fc__( 'cust_sticky_tablet', 'admin' ),
        'section' => 'flavor_header',
        'type'    => 'checkbox',
    ) );

    $wp_customize->add_setting( 'flavor_sticky_header_mobile', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'flavor_sticky_header_mobile', array(
        'label'   => fc__( 'cust_sticky_mobile', 'admin' ),
        'section' => 'flavor_header',
        'type'    => 'checkbox',
    ) );

    $wp_customize->add_setting( 'flavor_sticky_header_opacity', array(
        'default'           => 100,
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'flavor_sticky_header_opacity', array(
        'label'       => fc__( 'cust_sticky_opacity', 'admin' ),
        'description' => fc__( 'cust_sticky_opacity_desc', 'admin' ),
        'section'     => 'flavor_header',
        'type'        => 'range',
        'input_attrs' => array( 'min' => 0, 'max' => 100, 'step' => 5 ),
    ) );

    $wp_customize->add_setting( 'flavor_hide_site_title', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'flavor_hide_site_title', array(
        'label'   => fc__( 'cust_hide_site_title', 'admin' ),
        'section' => 'title_tagline',
        'type'    => 'checkbox',
    ) );

    $wp_customize->add_setting( 'flavor_hide_site_desc', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'flavor_hide_site_desc', array(
        'label'   => fc__( 'cust_hide_site_desc', 'admin' ),
        'section' => 'title_tagline',
        'type'    => 'checkbox',
    ) );

    $wp_customize->add_setting( 'flavor_logo_height', array(
        'default'           => 48,
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'flavor_logo_height', array(
        'label'       => fc__( 'cust_logo_height', 'admin' ),
        'section'     => 'title_tagline',
        'type'        => 'number',
        'input_attrs' => array( 'min' => 20, 'max' => 200, 'step' => 2 ),
    ) );

    $wp_customize->add_setting( 'flavor_logo_dark', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'flavor_logo_dark', array(
        'label'     => fc__( 'cust_logo_dark', 'admin' ),
        'description' => fc__( 'cust_logo_dark_desc', 'admin' ),
        'section'   => 'title_tagline',
        'mime_type' => 'image',
    ) ) );

    // =================================================================
    //  Sekcja: Sklep (Flavor Commerce)
    // =================================================================
    if ( defined( 'FC_VERSION' ) ) {

        $wp_customize->add_setting( 'flavor_shop_card_radius', array(
            'default'           => 12,
            'sanitize_callback' => 'absint',
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( 'flavor_shop_card_radius', array(
            'label'       => fc__( 'cust_card_radius', 'admin' ),
            'section'     => 'flavor_layout',
            'type'        => 'number',
            'input_attrs' => array( 'min' => 0, 'max' => 30, 'step' => 1 ),
        ) );

        $wp_customize->add_setting( 'flavor_shop_btn_radius', array(
            'default'           => 8,
            'sanitize_callback' => 'absint',
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( 'flavor_shop_btn_radius', array(
            'label'       => fc__( 'cust_btn_radius', 'admin' ),
            'section'     => 'flavor_layout',
            'type'        => 'number',
            'input_attrs' => array( 'min' => 0, 'max' => 30, 'step' => 1 ),
        ) );

        $wp_customize->add_setting( 'flavor_shop_input_radius', array(
            'default'           => 8,
            'sanitize_callback' => 'absint',
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( 'flavor_shop_input_radius', array(
            'label'       => fc__( 'cust_input_radius', 'admin' ),
            'section'     => 'flavor_layout',
            'type'        => 'number',
            'input_attrs' => array( 'min' => 0, 'max' => 30, 'step' => 1 ),
        ) );

        $wp_customize->add_setting( 'flavor_shop_sale_color', array(
            'default'           => '#ee2233',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'flavor_shop_sale_color', array(
            'label'   => fc__( 'cust_sale_color', 'admin' ),
            'section' => 'flavor_colors',
        ) ) );

        $wp_customize->add_setting( 'flavor_shop_badge_bg', array(
            'default'           => '#e74c3c',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'flavor_shop_badge_bg', array(
            'label'   => fc__( 'cust_badge_color', 'admin' ),
            'section' => 'flavor_colors',
        ) ) );

        $wp_customize->add_setting( 'flavor_shop_preorder_bg', array(
            'default'           => '#2980b9',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'flavor_shop_preorder_bg', array(
            'label'   => fc__( 'cust_preorder_color', 'admin' ),
            'section' => 'flavor_colors',
        ) ) );

    }
}
add_action( 'customize_register', 'flavor_customize_register', 1000 );


/* =====================================================================
 *  Mapa czcionek Google Fonts
 * ===================================================================== */

function flavor_get_font_stack( $key ) {
    $fonts = array(
        'system'     => '"Segoe UI", -apple-system, BlinkMacSystemFont, "Helvetica Neue", Arial, sans-serif',
        'inter'      => '"Inter", -apple-system, BlinkMacSystemFont, sans-serif',
        'poppins'    => '"Poppins", -apple-system, BlinkMacSystemFont, sans-serif',
        'roboto'     => '"Roboto", -apple-system, BlinkMacSystemFont, sans-serif',
        'lato'       => '"Lato", -apple-system, BlinkMacSystemFont, sans-serif',
        'open-sans'  => '"Open Sans", -apple-system, BlinkMacSystemFont, sans-serif',
        'montserrat' => '"Montserrat", -apple-system, BlinkMacSystemFont, sans-serif',
        'nunito'     => '"Nunito", -apple-system, BlinkMacSystemFont, sans-serif',
        'raleway'    => '"Raleway", -apple-system, BlinkMacSystemFont, sans-serif',
        'dm-sans'    => '"DM Sans", -apple-system, BlinkMacSystemFont, sans-serif',
    );
    return $fonts[ $key ] ?? $fonts['system'];
}

/**
 * Załaduj Google Fonts jeśli wybrano
 */
function flavor_enqueue_google_font() {
    $font_key = get_theme_mod( 'flavor_font_family', 'system' );
    if ( $font_key === 'system' ) return;

    $google_fonts = array(
        'inter'      => 'Inter:wght@300;400;500;600;700',
        'poppins'    => 'Poppins:wght@300;400;500;600;700',
        'roboto'     => 'Roboto:wght@300;400;500;700',
        'lato'       => 'Lato:wght@300;400;700',
        'open-sans'  => 'Open+Sans:wght@300;400;600;700',
        'montserrat' => 'Montserrat:wght@300;400;500;600;700',
        'nunito'     => 'Nunito:wght@300;400;600;700',
        'raleway'    => 'Raleway:wght@300;400;500;600;700',
        'dm-sans'    => 'DM+Sans:wght@400;500;700',
    );

    if ( isset( $google_fonts[ $font_key ] ) ) {
        wp_enqueue_style(
            'flavor-google-font',
            'https://fonts.googleapis.com/css2?family=' . $google_fonts[ $font_key ] . '&display=swap',
            array(),
            null
        );
    }
}
add_action( 'wp_enqueue_scripts', 'flavor_enqueue_google_font' );


/* =====================================================================
 *  Dynamiczny CSS (front-end)
 * ===================================================================== */

function flavor_customizer_css() {

    $accent = get_theme_mod( 'flavor_color_accent', '#4a90d9' );
    $mode   = get_theme_mod( 'flavor_color_mode', 'light' );

    // Wygeneruj paletę bazową
    $auto = flavor_generate_palette( $accent, $mode );

    // Pobierz wartości z Customizera (mogą być ręcznie nadpisane)
    $c = array();
    $map = array(
        'accent'       => 'flavor_color_accent',
        'accent_hover' => 'flavor_color_accent_hover',
        'accent_light' => 'flavor_color_accent_light',
        'text'         => 'flavor_color_text',
        'text_light'   => 'flavor_color_text_light',
        'bg'           => 'flavor_color_bg',
        'surface'      => 'flavor_color_surface',
        'border'       => 'flavor_color_border',
        'header_bg'    => 'flavor_color_header_bg',
        'footer_bg'    => 'flavor_color_footer_bg',
        'success'      => 'flavor_color_success',
        'danger'       => 'flavor_color_danger',
        'warning'      => 'flavor_color_warning',
    );
    foreach ( $map as $key => $setting_id ) {
        $c[ $key ] = get_theme_mod( $setting_id, $auto[ $key ] );
    }

    $font_key    = get_theme_mod( 'flavor_font_family', 'system' );
    $font_size   = intval( get_theme_mod( 'flavor_font_size', 16 ) );
    $cont_max    = intval( get_theme_mod( 'flavor_container_max', 1200 ) );
    $radius      = intval( get_theme_mod( 'flavor_border_radius', 0 ) );
    $font_stack  = flavor_get_font_stack( $font_key );

    // Sklep
    $shop_card_r  = intval( get_theme_mod( 'flavor_shop_card_radius', 12 ) );
    $shop_btn_r   = intval( get_theme_mod( 'flavor_shop_btn_radius', 8 ) );
    $shop_input_r = intval( get_theme_mod( 'flavor_shop_input_radius', 8 ) );
    $shop_img_rat = get_theme_mod( 'flavor_shop_img_ratio', '4/3' );
    $shop_img_fit = get_theme_mod( 'flavor_shop_img_fit', 'cover' );
    $shop_sale_c  = get_theme_mod( 'flavor_shop_sale_color', '#ee2233' );
    $shop_badge   = get_theme_mod( 'flavor_shop_badge_bg', '#e74c3c' );
    $shop_preorder = get_theme_mod( 'flavor_shop_preorder_bg', '#2980b9' );

    // Archiwum
    $archive_cols     = intval( get_theme_mod( 'flavor_archive_columns', 3 ) );
    $archive_card_min = intval( get_theme_mod( 'flavor_archive_card_min_width', 200 ) );

    ob_start();
    ?>
    <style id="flavor-customizer-css">
    :root {
        /* ── Kolory motywu ── */
        --color-accent: <?php echo esc_attr( $c['accent'] ); ?>;
        --color-accent-hover: <?php echo esc_attr( $c['accent_hover'] ); ?>;
        --color-accent-light: <?php echo esc_attr( $c['accent_light'] ); ?>;
        --color-text: <?php echo esc_attr( $c['text'] ); ?>;
        --color-text-light: <?php echo esc_attr( $c['text_light'] ); ?>;
        --color-bg: <?php echo esc_attr( $c['bg'] ); ?>;
        --color-surface: <?php echo esc_attr( $c['surface'] ); ?>;
        --color-border: <?php echo esc_attr( $c['border'] ); ?>;
        --color-header-bg: <?php echo esc_attr( $c['header_bg'] ); ?>;
        --color-footer-bg: <?php echo esc_attr( $c['footer_bg'] ); ?>;

        /* ── Kolory pluginu ── */
        --fc-accent: <?php echo esc_attr( $c['accent'] ); ?>;
        --fc-accent-hover: <?php echo esc_attr( $c['accent_hover'] ); ?>;
        --fc-accent-light: <?php echo esc_attr( $c['accent_light'] ); ?>;
        --fc-text: <?php echo esc_attr( $c['text'] ); ?>;
        --fc-text-light: <?php echo esc_attr( $c['text_light'] ); ?>;
        --fc-bg: <?php echo esc_attr( $c['bg'] ); ?>;
        --fc-surface: <?php echo esc_attr( $c['surface'] ); ?>;
        --fc-border: <?php echo esc_attr( $c['border'] ); ?>;
        --fc-success: <?php echo esc_attr( $c['success'] ); ?>;
        --fc-danger: <?php echo esc_attr( $c['danger'] ); ?>;
        --fc-warning: <?php echo esc_attr( $c['warning'] ); ?>;

        /* ── Typografia ── */
        --font-main: <?php echo $font_stack; ?>;
        --font-heading: <?php echo $font_stack; ?>;
        --fc-font: <?php echo $font_stack; ?>;

        /* ── Układ ── */
        --container-max: <?php echo $cont_max; ?>px;
        --radius: <?php echo $radius; ?>px;

        /* ── Sklep ── */
        --fc-card-radius: <?php echo $shop_card_r; ?>px;
        --fc-btn-radius: <?php echo $shop_btn_r; ?>px;
        --fc-input-radius: <?php echo $shop_input_r; ?>px;
        --fc-img-ratio: <?php echo esc_attr( $shop_img_rat ); ?>;
        --fc-img-fit: <?php echo esc_attr( $shop_img_fit ); ?>;
        --fc-sale-color: <?php echo esc_attr( $shop_sale_c ); ?>;
        --fc-badge-bg: <?php echo esc_attr( $shop_badge ); ?>;
        --fc-preorder-bg: <?php echo esc_attr( $shop_preorder ); ?>;

        /* ── Archiwum ── */
        --fc-grid-columns: <?php echo $archive_cols; ?>;
        --fc-card-min-width: <?php echo $archive_card_min; ?>px;

        /* ── Nagłówek ── */
        --header-sticky-opacity: <?php echo intval( get_theme_mod( 'flavor_sticky_header_opacity', 100 ) ); ?>%;
    }
    html { font-size: <?php echo $font_size; ?>px; }
    .site-logo img { max-height: <?php echo intval( get_theme_mod( 'flavor_logo_height', 48 ) ); ?>px; }
    </style>
    <?php
    echo ob_get_clean();
}
add_action( 'wp_head', 'flavor_customizer_css', 99 );


/* =====================================================================
 *  Customizer JS — Controls (sidebar) + Preview (iframe)
 * ===================================================================== */

/**
 * JS w panelu bocznym Customizera — automatyczna paleta
 */
function flavor_customize_controls_js() {
    wp_enqueue_script(
        'flavor-customizer-controls',
        get_template_directory_uri() . '/js/customizer-controls.js',
        array( 'customize-controls', 'jquery' ),
        FLAVOR_VERSION,
        true
    );

    $checkout_id = defined( 'FC_VERSION' ) ? get_option( 'fc_page_zamowienie' ) : 0;
    wp_localize_script( 'flavor-customizer-controls', 'flavorCustomizer', array(
        'checkoutUrl' => $checkout_id ? get_permalink( $checkout_id ) : '',
    ) );
}
add_action( 'customize_controls_enqueue_scripts', 'flavor_customize_controls_js' );

/**
 * JS w podglądzie (iframe) — natychmiastowa aktualizacja
 */
function flavor_customize_preview_js() {
    wp_enqueue_script(
        'flavor-customizer-preview',
        get_template_directory_uri() . '/js/customizer-preview.js',
        array( 'customize-preview', 'jquery' ),
        FLAVOR_VERSION,
        true
    );
}
add_action( 'customize_preview_init', 'flavor_customize_preview_js' );
