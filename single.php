<?php get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <header class="entry-header">
            <h1 class="entry-title"><?php the_title(); ?></h1>
            <div class="entry-meta">
                <span class="posted-on">📅 <?php echo get_the_date(); ?></span>
                <span class="byline">✍ <?php the_author_posts_link(); ?></span>
                <?php if ( has_category() ) : ?>
                    <span class="cat-links">📁 <?php the_category( ', ' ); ?></span>
                <?php endif; ?>
            </div>
        </header>

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

        <?php if ( has_tag() ) : ?>
            <footer class="entry-footer">
                🏷 <?php the_tags( '', ', ' ); ?>
            </footer>
        <?php endif; ?>
    </article>

    <div class="post-navigation">
        <?php
        the_post_navigation( array(
            'prev_text' => '<span class="nav-subtitle">' . esc_html( fc__( 'theme_prev_post' ) ) . '</span> <span class="nav-title">%title</span>',
            'next_text' => '<span class="nav-subtitle">' . esc_html( fc__( 'theme_next_post' ) ) . '</span> <span class="nav-title">%title</span>',
        ) );
        ?>
    </div>

    <?php
    if ( comments_open() || get_comments_number() ) :
        comments_template();
    endif;
    ?>

<?php endwhile; ?>

<?php get_footer(); ?>
