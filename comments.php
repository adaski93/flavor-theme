<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php if ( post_password_required() ) return; ?>

<div id="comments" class="comments-area">

    <?php if ( have_comments() ) : ?>

        <h2 class="comments-title">
            <?php
            $flavor_comment_count = get_comments_number();
            echo esc_html( fc_n( 'theme_comment_singular', 'theme_comment_plural', $flavor_comment_count ) );
            ?>
        </h2>

        <ol class="comment-list">
            <?php
            wp_list_comments( array(
                'style'      => 'ol',
                'short_ping' => true,
                'avatar_size' => 48,
            ) );
            ?>
        </ol>

        <?php the_comments_navigation(); ?>

        <?php if ( ! comments_open() ) : ?>
            <p class="no-comments"><?php echo esc_html( fc__( 'theme_comments_closed' ) ); ?></p>
        <?php endif; ?>

    <?php endif; ?>

    <?php comment_form(); ?>

</div>
