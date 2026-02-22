    </div><!-- .container -->
</main><!-- .site-content -->

<footer class="site-footer" role="contentinfo">
    <div class="container">
        <div class="footer-content">
            <p>&copy; <?php echo date( 'Y' ); ?> <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>. <?php echo esc_html( fc__( 'theme_all_rights' ) ); ?></p>
            <p><?php printf( esc_html( fc__( 'theme_theme_by' ) ), '<a href="https://flavor-theme.dev">Flavor</a>' ); ?></p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
