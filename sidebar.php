<?php
/**
 * Sidebar template
 *
 * @package Flavor
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
    return;
}
?>

<aside class="widget-area" role="complementary">
    <?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside>
