<?php get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <?php if ( ! has_shortcode( get_the_content(), 'fc_shop' ) && ! has_shortcode( get_the_content(), 'fc_wishlist' ) && ! has_shortcode( get_the_content(), 'fc_compare' ) && ! has_shortcode( get_the_content(), 'fc_retry_payment' ) && ! has_shortcode( get_the_content(), 'fc_thank_you' ) && ! ( has_shortcode( get_the_content(), 'fc_checkout' ) && get_theme_mod( 'flavor_checkout_hide_header', false ) ) ) : ?>
        <header class="entry-header">
            <h1 class="entry-title"><?php the_title(); ?></h1>
        </header>
        <?php endif; ?>

        <?php if ( has_post_thumbnail() ) : ?>
            <div class="post-thumbnail">
                <?php the_post_thumbnail( 'full' ); ?>
            </div>
        <?php endif; ?>

        <div class="entry-content">
            <?php the_content(); ?>
            <?php
            wp_link_pages( array(
                'before' => '<div class="page-links">' . esc_html( fc__( 'theme_pages_label' ) ),
                'after'  => '</div>',
            ) );
            ?>
        </div>
    </article>

    <?php
    if ( comments_open() || get_comments_number() ) :
        comments_template();
    endif;
    ?>

<?php endwhile; ?>

<?php get_footer(); ?>
