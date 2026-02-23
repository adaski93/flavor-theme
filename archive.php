<?php get_header(); ?>

<header class="archive-header entry-header">
    <?php
    the_archive_title( '<h1 class="entry-title">', '</h1>' );
    the_archive_description( '<div class="archive-description">', '</div>' );
    ?>
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
                    <span class="byline">✍ <?php the_author_posts_link(); ?></span>
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
            <p><?php echo esc_html( fc__( 'theme_no_results' ) ); ?></p>
        </div>
    </article>

<?php endif; ?>

<?php get_footer(); ?>
