<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header" role="banner" data-sticky-desktop="<?php echo get_theme_mod( 'flavor_sticky_header_desktop', true ) ? '1' : '0'; ?>" data-sticky-tablet="<?php echo get_theme_mod( 'flavor_sticky_header_tablet', true ) ? '1' : '0'; ?>" data-sticky-mobile="<?php echo get_theme_mod( 'flavor_sticky_header_mobile', true ) ? '1' : '0'; ?>">
    <div class="container">
        <div class="site-branding">
            <?php
            $flavor_dark_logo_id = get_theme_mod( 'flavor_logo_dark' );
            $flavor_color_mode   = get_theme_mod( 'flavor_color_mode', 'light' );
            ?>
            <?php if ( has_custom_logo() ) : ?>
                <div class="site-logo site-logo-light"<?php if ( $flavor_dark_logo_id && $flavor_color_mode === 'dark' ) echo ' style="display:none"'; ?>><?php the_custom_logo(); ?></div>
            <?php endif; ?>
            <?php if ( $flavor_dark_logo_id ) :
                $flavor_dark_logo_url = wp_get_attachment_image_url( $flavor_dark_logo_id, 'full' );
                if ( $flavor_dark_logo_url ) : ?>
                <div class="site-logo site-logo-dark"<?php if ( $flavor_color_mode !== 'dark' ) echo ' style="display:none"'; ?>>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="custom-logo-link">
                        <img src="<?php echo esc_url( $flavor_dark_logo_url ); ?>" class="custom-logo" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
                    </a>
                </div>
                <?php endif; ?>
            <?php endif; ?>
            <div>
                <?php if ( ! get_theme_mod( 'flavor_hide_site_title', true ) || is_customize_preview() ) : ?>
                <h1 class="site-title"<?php if ( get_theme_mod( 'flavor_hide_site_title', true ) ) echo ' style="display:none"'; ?>>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
                </h1>
                <?php endif; ?>
                <?php
                $flavor_description = get_bloginfo( 'description', 'display' );
                if ( ( $flavor_description || is_customize_preview() ) && ( ! get_theme_mod( 'flavor_hide_site_desc', true ) || is_customize_preview() ) ) : ?>
                    <p class="site-description"<?php if ( get_theme_mod( 'flavor_hide_site_desc', true ) ) echo ' style="display:none"'; ?>><?php echo $flavor_description; ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="header-actions">
            <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                ☰
            </button>
            <div class="header-icons">
                <?php echo flavor_render_header_icons(); ?>
            </div>
        </div>

        <nav class="main-navigation" role="navigation" aria-label="<?php echo esc_attr( fc__( 'theme_main_menu' ) ); ?>">
            <?php echo flavor_render_custom_menu(); ?>
        </nav>
    </div>
</header>

<main class="site-content" role="main">
    <div class="container">
