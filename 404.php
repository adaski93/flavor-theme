<?php get_header(); ?>

<article class="error-404 not-found">
    <div class="error-404-inner">
        <span class="error-404-icon dashicons dashicons-warning"></span>
        <h1>404</h1>
        <h2 class="entry-title"><?php echo esc_html( fc__( 'theme_404_title' ) ); ?></h2>
        <p class="error-404-desc"><?php echo esc_html( fc__( 'theme_404_desc' ) ); ?></p>
        <div class="error-404-search">
            <?php get_search_form(); ?>
        </div>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="error-404-home">
            <?php echo esc_html( fc__( 'theme_404_go_home' ) ); ?>
        </a>
    </div>
</article>

<?php get_footer(); ?>
