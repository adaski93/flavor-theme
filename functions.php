<?php
/**
 * Flavor Theme Functions
 *
 * @package Flavor
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'FLAVOR_VERSION', '1.5.3' );

/**
 * Fallback i18n helpers — used when Flavor Commerce plugin is not active.
 * When the plugin IS active it defines fc__() / fc_e() itself and these
 * wrappers are simply skipped thanks to function_exists().
 */
if ( ! function_exists( 'fc__' ) ) {
    function fc__( $key, $context = null ) { return $key; }
}
if ( ! function_exists( 'fc_e' ) ) {
    function fc_e( $key, $context = null ) { echo $key; }
}

/**
 * Customizer – sterowanie wyglądem motywu i wtyczki Flavor Commerce
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Strona ustawień wyglądu sklepu (sidebar, widgety)
 */
require get_template_directory() . '/inc/appearance.php';

/**
 * Auto-tworzenie stron (O nas, Kontakt) + Customizer page selectors
 */
require get_template_directory() . '/inc/class-flavor-pages.php';

/**
 * Strona "O nas" – Customizer + wymuszanie szablonu
 */
require get_template_directory() . '/inc/class-flavor-about.php';

/**
 * Stopka — Customizer + widgety
 */
require get_template_directory() . '/inc/class-flavor-footer.php';

/**
 * GitHub auto-updater for the theme.
 */
require get_template_directory() . '/inc/class-flavor-updater.php';
new Flavor_Theme_Updater( 'adaski93/flavor-theme' );

/**
 * Theme setup
 */
function flavor_setup() {
    // Tłumaczenia
    load_theme_textdomain( 'flavor', get_template_directory() . '/languages' );

    // Automatyczny tytuł strony
    add_theme_support( 'title-tag' );

    // Miniaturki wpisów
    add_theme_support( 'post-thumbnails' );

    // Kanał RSS
    add_theme_support( 'automatic-feed-links' );

    // Ustawienia HTML5
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ) );

    // Logo
    add_theme_support( 'custom-logo', array(
        'height'      => 96,
        'width'       => 96,
        'flex-width'  => true,
        'flex-height' => true,
    ) );

    // Wyrównania szerokie (Gutenberg)
    add_theme_support( 'align-wide' );

    // Responsive embeds
    add_theme_support( 'responsive-embeds' );

    // Paleta kolorów edytora
    add_theme_support( 'editor-color-palette', array(
        array(
            'name'  => fc__( 'cust_editor_accent', 'admin' ),
            'slug'  => 'accent',
            'color' => '#4a90d9',
        ),
        array(
            'name'  => fc__( 'cust_editor_accent_light', 'admin' ),
            'slug'  => 'accent-light',
            'color' => '#e8f0fe',
        ),
        array(
            'name'  => fc__( 'cust_editor_text', 'admin' ),
            'slug'  => 'text',
            'color' => '#2c3e50',
        ),
        array(
            'name'  => fc__( 'cust_editor_text_light', 'admin' ),
            'slug'  => 'text-light',
            'color' => '#6b7c93',
        ),
        array(
            'name'  => fc__( 'cust_editor_bg', 'admin' ),
            'slug'  => 'background',
            'color' => '#f8f9fc',
        ),
    ) );

}
add_action( 'after_setup_theme', 'flavor_setup' );

/**
 * Skrypty i style
 */
function flavor_scripts() {
    wp_enqueue_style(
        'flavor-style',
        get_stylesheet_uri(),
        array(),
        FLAVOR_VERSION
    );

    wp_enqueue_script(
        'flavor-navigation',
        get_template_directory_uri() . '/js/navigation.js',
        array(),
        FLAVOR_VERSION,
        true
    );

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'flavor_scripts' );

/**
 * Rejestracja sidebara
 */
function flavor_widgets_init() {
    register_sidebar( array(
        'name'          => esc_html( fc__( 'cust_sidebar_name', 'admin' ) ),
        'id'            => 'sidebar-1',
        'description'   => esc_html( fc__( 'cust_sidebar_add_widgets', 'admin' ) ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
}
add_action( 'widgets_init', 'flavor_widgets_init' );

/**
 * Skrócenie excerpta
 */
function flavor_excerpt_length( $length ) {
    return 30;
}
add_filter( 'excerpt_length', 'flavor_excerpt_length' );

/**
 * Zmiana tekstu "Czytaj więcej" w excerpcie
 */
function flavor_excerpt_more( $more ) {
    return '&hellip;';
}
add_filter( 'excerpt_more', 'flavor_excerpt_more' );

/**
 * Renderuj własne menu z Customizera
 */
function flavor_render_custom_menu() {
    $items = get_theme_mod( 'flavor_menu_items', array() );

    if ( is_string( $items ) ) {
        $items = json_decode( $items, true );
    }

    if ( empty( $items ) || ! is_array( $items ) ) {
        return '<ul id="primary-menu"></ul>';
    }

    // Prime post cache for all page IDs used in menu items
    $page_ids_to_prime = array();
    foreach ( $items as $item ) {
        $type = $item['type'] ?? '';
        if ( $type === 'fc_account' ) {
            $page_ids_to_prime[] = absint( get_option( 'fc_page_moje-konto' ) );
        } elseif ( $type === 'fc_cart' ) {
            $page_ids_to_prime[] = absint( get_option( 'fc_page_koszyk' ) );
        } elseif ( $type === 'fc_wishlist' ) {
            $page_ids_to_prime[] = absint( get_option( 'fc_page_wishlist' ) );
        } elseif ( $type === 'fc_compare' ) {
            $page_ids_to_prime[] = absint( get_option( 'fc_page_porownanie' ) );
        } elseif ( $type === 'fc_shop' ) {
            $page_ids_to_prime[] = absint( get_option( 'fc_page_sklep' ) );
        } elseif ( $type === 'fc_about' ) {
            $page_ids_to_prime[] = absint( get_option( 'fc_page_o-nas' ) );
        } elseif ( $type === 'fc_contact' ) {
            $page_ids_to_prime[] = absint( get_option( 'fc_page_kontakt' ) );
        } elseif ( $type === 'page' && ! empty( $item['id'] ) ) {
            $page_ids_to_prime[] = absint( $item['id'] );
        }
    }
    $page_ids_to_prime = array_filter( array_unique( $page_ids_to_prime ) );
    if ( $page_ids_to_prime ) {
        _prime_post_caches( $page_ids_to_prime, false, false );
    }

    $current_url = trailingslashit( home_url( add_query_arg( array(), false ) ) );
    $html = '<ul id="primary-menu">';

    $account_svg = '<svg class="fc-menu-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
    $cart_svg = '<svg class="fc-menu-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>';
    $wishlist_svg = '<svg class="fc-menu-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>';
    $compare_svg = '<svg class="fc-menu-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="M7 21h10"/><path d="M12 3v18"/><path d="M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"/></svg>';
    $shop_svg = '<svg class="fc-menu-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/><path d="M2 7h20"/><path d="M22 7v3a2 2 0 0 1-2 2a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12a2 2 0 0 1-2-2V7"/></svg>';

    foreach ( $items as $item ) {
        $type  = $item['type'] ?? '';
        $title = esc_html( $item['title'] ?? '' );
        $url   = '';

        if ( $type === 'fc_account' ) {
            $page_id = get_option( 'fc_page_moje-konto' );
            $url     = $page_id ? get_permalink( $page_id ) : '#';
            $is_current = ( trailingslashit( $url ) === $current_url );
            $class      = 'menu-item-fc-account' . ( $is_current ? ' current-menu-item' : '' );
            $html .= '<li class="' . $class . '"><a href="' . esc_url( $url ) . '" title="' . $title . '">' . $account_svg . '<span class="fc-menu-label">' . $title . '</span></a></li>';
            continue;
        }

        if ( $type === 'fc_about' ) {
            $page_id = get_option( 'fc_page_o-nas' );
            $url     = $page_id ? get_permalink( $page_id ) : '#';
            $is_current = ( trailingslashit( $url ) === $current_url );
            $class      = $is_current ? ' class="current-menu-item"' : '';
            $html .= '<li' . $class . '><a href="' . esc_url( $url ) . '">' . $title . '</a></li>';
            continue;
        }

        if ( $type === 'fc_contact' ) {
            $page_id = get_option( 'fc_page_kontakt' );
            $url     = $page_id ? get_permalink( $page_id ) : '#';
            $is_current = ( trailingslashit( $url ) === $current_url );
            $class      = $is_current ? ' class="current-menu-item"' : '';
            $html .= '<li' . $class . '><a href="' . esc_url( $url ) . '">' . $title . '</a></li>';
            continue;
        }

        if ( $type === 'fc_shop' ) {
            $page_id      = get_option( 'fc_page_sklep' );
            $url          = $page_id ? get_permalink( $page_id ) : '#';
            $shop_display = $item['shop_display'] ?? 'text';
            $is_current   = ( trailingslashit( $url ) === $current_url );
            $class        = 'menu-item-fc-shop' . ( $is_current ? ' current-menu-item' : '' );
            if ( $shop_display === 'icon' ) {
                $html .= '<li class="' . $class . '"><a href="' . esc_url( $url ) . '" title="' . $title . '">' . $shop_svg . '</a></li>';
            } else {
                $html .= '<li class="' . $class . '"><a href="' . esc_url( $url ) . '">' . $title . '</a></li>';
            }
            continue;
        }

        if ( $type === 'fc_wishlist' ) {
            if ( ! get_theme_mod( 'flavor_archive_wishlist', true ) ) continue;
            $page_id = get_option( 'fc_page_wishlist' );
            $url     = $page_id ? get_permalink( $page_id ) : '#';
            $count   = 0;
            if ( class_exists( 'FC_Wishlist' ) ) {
                $count = count( FC_Wishlist::get_wishlist( get_current_user_id() ) );
            }
            $is_current = ( trailingslashit( $url ) === $current_url );
            $class      = 'menu-item-fc-wishlist' . ( $is_current ? ' current-menu-item' : '' );
            $badge      = '<span class="fc-header-wishlist-count"' . ( $count ? '' : ' style="display:none"' ) . '>' . intval( $count ) . '</span>';
            $html .= '<li class="' . $class . '"><a href="' . esc_url( $url ) . '" data-wishlist-action="panel" title="' . $title . '">' . $wishlist_svg . $badge . '<span class="fc-menu-label">' . $title . '</span></a></li>';
            continue;
        }

        if ( $type === 'fc_compare' ) {
            if ( ! get_theme_mod( 'flavor_archive_compare', true ) ) continue;
            $page_id = get_option( 'fc_page_porownanie' );
            $url     = $page_id ? get_permalink( $page_id ) : site_url( '/porownanie/' );
            $count   = isset( $_SESSION['fc_compare'] ) ? count( $_SESSION['fc_compare'] ) : 0;
            $is_current = ( trailingslashit( $url ) === $current_url );
            $class      = 'menu-item-fc-compare' . ( $is_current ? ' current-menu-item' : '' );
            $badge      = '<span class="fc-header-compare-count fc-compare-count"' . ( $count ? '' : ' style="display:none"' ) . '>' . intval( $count ) . '</span>';
            $html .= '<li class="' . $class . '"><a href="' . esc_url( $url ) . '" title="' . $title . '" data-compare-action="panel">' . $compare_svg . $badge . '<span class="fc-menu-label">' . $title . '</span></a></li>';
            continue;
        }

        if ( $type === 'fc_cart' ) {
            $page_id     = get_option( 'fc_page_koszyk' );
            $url         = $page_id ? get_permalink( $page_id ) : '#';
            $count       = class_exists( 'FC_Cart' ) ? FC_Cart::get_count() : 0;
            $cart_action = $item['cart_action'] ?? 'minicart';
            $show_total  = ! empty( $item['show_total'] );
            $is_current  = ( trailingslashit( $url ) === $current_url );
            $class       = 'menu-item-fc-cart' . ( $is_current ? ' current-menu-item' : '' );
            $badge       = '<span class="fc-menu-cart-count fc-cart-count"' . ( $count ? '' : ' style="display:none"' ) . '>' . intval( $count ) . '</span>';

            $total_html = '';
            if ( $show_total ) {
                $total_val  = class_exists( 'FC_Cart' ) ? FC_Cart::get_total() : 0;
                $total_html = '<span class="fc-menu-cart-total fc-total-amount">' . ( function_exists( 'fc_format_price' ) ? fc_format_price( $total_val ) : '' ) . '</span>';
            }

            if ( $cart_action === 'minicart' ) {
                $html .= '<li class="' . $class . '"><a href="' . esc_url( $url ) . '" title="' . $title . '" data-cart-action="minicart">' . $cart_svg . $badge . $total_html . '<span class="fc-menu-label">' . $title . '</span></a></li>';
            } else {
                $html .= '<li class="' . $class . '"><a href="' . esc_url( $url ) . '" title="' . $title . '">' . $cart_svg . $badge . $total_html . '<span class="fc-menu-label">' . $title . '</span></a></li>';
            }
            continue;
        }

        if ( $type === 'page' && ! empty( $item['id'] ) ) {
            $url = get_permalink( absint( $item['id'] ) );
        } else {
            $url = $item['url'] ?? '#';
        }

        $is_current = ( trailingslashit( $url ) === $current_url );
        $class      = $is_current ? ' class="current-menu-item"' : '';

        $html .= '<li' . $class . '><a href="' . esc_url( $url ) . '">' . $title . '</a></li>';
    }

    $html .= '</ul>';
    return $html;
}

/**
 * Renderuj ikony konta i koszyka do mobilnego paska w nagłówku
 */
function flavor_render_header_icons() {
    $items = get_theme_mod( 'flavor_menu_items', array() );
    if ( is_string( $items ) ) {
        $items = json_decode( $items, true );
    }
    if ( empty( $items ) || ! is_array( $items ) ) return '';

    // Prime post cache for account/cart/wishlist pages
    $icon_page_ids = array();
    foreach ( $items as $item ) {
        $type = $item['type'] ?? '';
        if ( $type === 'fc_account' ) {
            $icon_page_ids[] = absint( get_option( 'fc_page_moje-konto' ) );
        } elseif ( $type === 'fc_cart' ) {
            $icon_page_ids[] = absint( get_option( 'fc_page_koszyk' ) );
        } elseif ( $type === 'fc_wishlist' ) {
            $icon_page_ids[] = absint( get_option( 'fc_page_wishlist' ) );
        } elseif ( $type === 'fc_compare' ) {
            $icon_page_ids[] = absint( get_option( 'fc_page_porownanie' ) );
        } elseif ( $type === 'fc_shop' ) {
            $icon_page_ids[] = absint( get_option( 'fc_page_sklep' ) );
        }
    }
    $icon_page_ids = array_filter( array_unique( $icon_page_ids ) );
    if ( $icon_page_ids ) {
        _prime_post_caches( $icon_page_ids, false, false );
    }

    $account_svg = '<svg class="fc-menu-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
    $cart_svg = '<svg class="fc-menu-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>';
    $wishlist_svg = '<svg class="fc-menu-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>';
    $compare_svg = '<svg class="fc-menu-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="M7 21h10"/><path d="M12 3v18"/><path d="M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"/></svg>';
    foreach ( $items as $item ) {
        $type = $item['type'] ?? '';

        if ( $type === 'fc_account' ) {
            $page_id = get_option( 'fc_page_moje-konto' );
            $url     = $page_id ? get_permalink( $page_id ) : '#';
            $html .= '<a href="' . esc_url( $url ) . '" class="header-icon header-icon-account" title="' . esc_attr( $item['title'] ?? fc__( 'page_my_account' ) ) . '">' . $account_svg . '</a>';
        }

        if ( $type === 'fc_wishlist' && get_theme_mod( 'flavor_archive_wishlist', true ) ) {
            $page_id = get_option( 'fc_page_wishlist' );
            $url     = $page_id ? get_permalink( $page_id ) : '#';
            $count   = 0;
            if ( class_exists( 'FC_Wishlist' ) ) {
                $count = count( FC_Wishlist::get_wishlist( get_current_user_id() ) );
            }
            $badge = '<span class="fc-header-wishlist-count"' . ( $count ? '' : ' style="display:none"' ) . '>' . intval( $count ) . '</span>';
            $html .= '<a href="' . esc_url( $url ) . '" data-wishlist-action="panel" class="header-icon header-icon-wishlist" title="' . esc_attr( $item['title'] ?? fc__( 'page_wishlist' ) ) . '">' . $wishlist_svg . $badge . '</a>';
        }

        if ( $type === 'fc_cart' ) {
            $page_id     = get_option( 'fc_page_koszyk' );
            $url         = $page_id ? get_permalink( $page_id ) : '#';
            $count       = class_exists( 'FC_Cart' ) ? FC_Cart::get_count() : 0;
            $cart_action = $item['cart_action'] ?? 'minicart';
            $action_attr = ( $cart_action === 'minicart' ) ? ' data-cart-action="minicart"' : '';
            $badge       = '<span class="fc-menu-cart-count fc-cart-count"' . ( $count ? '' : ' style="display:none"' ) . '>' . intval( $count ) . '</span>';
            $html .= '<a href="' . esc_url( $url ) . '" class="header-icon header-icon-cart"' . $action_attr . ' title="' . esc_attr( $item['title'] ?? fc__( 'page_cart' ) ) . '">' . $cart_svg . $badge . '</a>';
        }

        if ( $type === 'fc_compare' && get_theme_mod( 'flavor_archive_compare', true ) ) {
            $page_id = get_option( 'fc_page_porownanie' );
            $url     = $page_id ? get_permalink( $page_id ) : site_url( '/porownanie/' );
            $count   = isset( $_SESSION['fc_compare'] ) ? count( $_SESSION['fc_compare'] ) : 0;
            $badge = '<span class="fc-header-compare-count fc-compare-count"' . ( $count ? '' : ' style="display:none"' ) . '>' . intval( $count ) . '</span>';
            $html .= '<a href="' . esc_url( $url ) . '" class="header-icon header-icon-compare" data-compare-action="panel" title="' . esc_attr( $item['title'] ?? fc__( 'theme_compare' ) ) . '">' . $compare_svg . $badge . '</a>';
        }
    }
    return $html;
}

/**
 * Klasa body
 */
function flavor_body_classes( $classes ) {
    if ( ! is_singular() ) {
        $classes[] = 'hfeed';
    }
    if ( ! is_active_sidebar( 'sidebar-1' ) ) {
        $classes[] = 'no-sidebar';
    }
    return $classes;
}
add_filter( 'body_class', 'flavor_body_classes' );

/**
 * Pingback header
 */
function flavor_pingback_header() {
    if ( is_singular() && pings_open() ) {
        printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
    }
}
add_action( 'wp_head', 'flavor_pingback_header' );
