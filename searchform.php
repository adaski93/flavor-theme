<?php
/**
 * Custom search form using Flavor Commerce i18n.
 *
 * @package Flavor
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <label>
        <span class="screen-reader-text"><?php echo esc_html( fc__( 'theme_search_button' ) ); ?></span>
        <input type="search" class="search-field" placeholder="<?php echo esc_attr( fc__( 'theme_search_placeholder' ) ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
    </label>
    <input type="submit" class="search-submit" value="<?php echo esc_attr( fc__( 'theme_search_button' ) ); ?>" />
</form>
