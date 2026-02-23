<?php get_header(); ?>

<?php if ( have_posts() ) : ?>

    <?php while ( have_posts() ) : the_post(); ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header">
                <?php if ( is_singular() ) : ?>
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                <?php else : ?>
                    <h2 class="entry-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h2>
                <?php endif; ?>

                <?php if ( 'post' === get_post_type() ) : ?>
                    <div class="entry-meta">
                        <span class="posted-on">
                            📅 <?php echo get_the_date(); ?>
                        </span>
                        <span class="byline">
                            ✍ <?php the_author_posts_link(); ?>
                        </span>
                        <?php if ( has_category() ) : ?>
                            <span class="cat-links">
                                📁 <?php the_category( ', ' ); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </header>

            <?php if ( has_post_thumbnail() && ! is_singular() ) : ?>
                <div class="post-thumbnail">
                    <a href="<?php the_permalink(); ?>">
                        <?php the_post_thumbnail( 'large' ); ?>
                    </a>
                </div>
            <?php elseif ( has_post_thumbnail() && is_singular() ) : ?>
                <div class="post-thumbnail">
                    <?php the_post_thumbnail( 'full' ); ?>
                </div>
            <?php endif; ?>

            <div class="entry-content">
                <?php if ( is_singular() ) : ?>
                    <?php the_content(); ?>
                    <?php
                    wp_link_pages( array(
                        'before' => '<div class="page-links">' . esc_html( fc__( 'theme_pages_label' ) ),
                        'after'  => '</div>',
                    ) );
                    ?>
                <?php else : ?>
                    <?php the_excerpt(); ?>
                    <a href="<?php the_permalink(); ?>" class="read-more">
                        <?php echo esc_html( fc__( 'theme_read_more' ) ); ?>
                    </a>
                <?php endif; ?>
            </div>

            <?php if ( is_singular() && has_tag() ) : ?>
                <footer class="entry-footer">
                    🏷 <?php the_tags( '', ', ' ); ?>
                </footer>
            <?php endif; ?>
        </article>

        <?php if ( is_singular() ) : ?>
            <?php
            if ( comments_open() || get_comments_number() ) :
                comments_template();
            endif;
            ?>
        <?php endif; ?>

    <?php endwhile; ?>

    <?php the_posts_pagination( array(
        'mid_size'  => 2,
        'prev_text' => '← ' . esc_html( fc__( 'theme_prev_page' ) ),
        'next_text' => esc_html( fc__( 'theme_next_page' ) ) . ' →',
    ) ); ?>

<?php else : ?>

    <article class="no-results">
        <header class="entry-header">
            <h1 class="entry-title"><?php echo esc_html( fc__( 'theme_nothing_found' ) ); ?></h1>
        </header>
        <div class="entry-content">
            <p><?php echo esc_html( fc__( 'theme_no_results_blog' ) ); ?></p>
            <?php get_search_form(); ?>
        </div>
    </article>

<?php endif; ?>

<?php get_footer(); ?>
