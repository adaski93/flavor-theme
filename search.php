<?php get_header(); ?>

<header class="search-header entry-header">
    <h1 class="entry-title">
        <?php printf( esc_html( fc__( 'theme_search_results' ) ), '<span>' . get_search_query() . '</span>' ); ?>
    </h1>
</header>

<?php if ( have_posts() ) : ?>

    <?php while ( have_posts() ) : the_post(); ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header">
                <h2 class="entry-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h2>
                <div class="entry-meta">
                    <span class="posted-on">📅 <?php echo get_the_date(); ?></span>
                </div>
            </header>

            <div class="entry-content">
                <?php the_excerpt(); ?>
                <a href="<?php the_permalink(); ?>" class="read-more">
                    <?php echo esc_html( fc__( 'theme_read_more' ) ); ?>
                </a>
            </div>
        </article>

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
            <p><?php echo esc_html( fc__( 'theme_no_results_search' ) ); ?></p>
            <?php get_search_form(); ?>
        </div>
    </article>

<?php endif; ?>

<?php get_footer(); ?>
