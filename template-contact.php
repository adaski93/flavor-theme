<?php
/**
 * Template Name: Kontakt
 * Template Post Type: page
 *
 * Szablon strony kontaktowej z formularzem, danymi kontaktowymi i social media.
 * Wszystko konfigurowane z poziomu Customizera.
 *
 * @package Flavor
 * @since 1.5.0
 */

get_header();

// Dane firmy z ustawień sklepu (FC Settings → Sklep)
$company  = get_option( 'fc_store_name', get_bloginfo( 'name' ) );
$street   = get_option( 'fc_store_street', '' );
$postcode = get_option( 'fc_store_postcode', '' );
$city     = get_option( 'fc_store_city', '' );
$address  = trim( $street . ( $street && ( $postcode || $city ) ? "\n" : '' ) . trim( $postcode . ' ' . $city ) );
$phone_prefix = get_option( 'fc_store_phone_prefix', '' );
$phone_number = get_option( 'fc_store_phone', '' );
$phone    = trim( $phone_prefix . ' ' . $phone_number );
$email    = get_option( 'fc_store_email_contact', get_option( 'admin_email' ) );
$nip      = get_option( 'fc_store_tax_no', '' );
$crn      = get_option( 'fc_store_crn', '' );

// Etykiety NIP / CRN zgodne z krajem sklepu
$tax_labels = class_exists( 'FC_Shortcodes' )
    ? FC_Shortcodes::get_country_tax_labels( get_option( 'fc_store_country', 'PL' ) )
    : array( 'tax_no' => 'NIP', 'crn' => 'KRS' );

// Widoczność poszczególnych danych (Customizer)
$show_company = get_theme_mod( 'flavor_contact_show_company', true );
$show_address = get_theme_mod( 'flavor_contact_show_address', true );
$show_phone   = get_theme_mod( 'flavor_contact_show_phone', true );
$show_email   = get_theme_mod( 'flavor_contact_show_email', true );
$show_tax_no  = get_theme_mod( 'flavor_contact_show_tax_no', true );
$show_crn     = get_theme_mod( 'flavor_contact_show_crn', true );
$show_hours   = get_theme_mod( 'flavor_contact_show_hours', true );

// Dane z Customizera (nie ma ich w ustawieniach sklepu)
$hours_json = get_theme_mod( 'flavor_contact_hours', '' );
$hours_rows = json_decode( $hours_json, true );
if ( ! is_array( $hours_rows ) ) {
    $hours_rows = array();
}
// Skróty dni tygodnia
$day_names = array(
    '1' => fc__( 'day_mon' ),
    '2' => fc__( 'day_tue' ),
    '3' => fc__( 'day_wed' ),
    '4' => fc__( 'day_thu' ),
    '5' => fc__( 'day_fri' ),
    '6' => fc__( 'day_sat' ),
    '7' => fc__( 'day_sun' ),
);
// Filtruj puste wiersze
$hours_rows = array_filter( $hours_rows, function( $r ) {
    return ( ! empty( $r['day_from'] ) || ! empty( $r['day_to'] ) ) && ( ! empty( $r['from'] ) || ! empty( $r['to'] ) );
} );
$hours = ! empty( $hours_rows );

// Mapa
$map_enabled = get_theme_mod( 'flavor_contact_map_enabled', true );
$map_url     = '';
if ( $map_enabled ) {
    $map_source = get_theme_mod( 'flavor_contact_map_source', 'store' );
    if ( $map_source === 'custom' ) {
        $map_custom = trim( get_theme_mod( 'flavor_contact_map_custom', '' ) );
        if ( $map_custom ) {
            $map_url = 'https://maps.google.com/maps?' . http_build_query( array( 'q' => $map_custom, 'z' => 16, 'output' => 'embed' ) );
        }
    } elseif ( $address ) {
        $map_query = str_replace( "\n", ', ', $address );
        $map_url   = 'https://maps.google.com/maps?' . http_build_query( array( 'q' => $map_query, 'z' => 16, 'output' => 'embed' ) );
    }
}
$socials = Flavor_Pages::get_social_links();

$has_info = ( $show_company && $company ) || ( $show_address && $address ) || ( $show_phone && $phone ) || ( $show_email && $email ) || ( $show_hours && $hours ) || ( $show_tax_no && $nip ) || ( $show_crn && $crn ) || ! empty( $socials );

// Mapa — pozycja i ciemny motyw
$map_position = get_theme_mod( 'flavor_contact_map_position', 'below_all' );
$map_dark     = get_theme_mod( 'flavor_contact_map_dark', get_theme_mod( 'flavor_color_mode', 'light' ) === 'dark' );
$map_filter   = $map_dark ? ' filter: invert(90%) hue-rotate(180deg);' : '';

// Hero
$hero = Flavor_Pages::get_contact_hero();
$page_title = get_the_title();
?>

<?php while ( have_posts() ) : the_post(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'flavor-contact-page' ); ?>>

<?php
// ── Hero / Nagłówek — zawsze na górze ──
if ( $hero['enabled'] ) :
$hero_has_bg = ( $hero['bg_mode'] === 'custom' && $hero['image'] ) || $hero['bg_mode'] !== 'custom';
if ( $hero_has_bg ) :
    $is_pattern = $hero['bg_mode'] !== 'custom';
    $is_dark    = $is_pattern && $hero['bg_variant'] === 'dark';

    // Build section style
    if ( $is_pattern ) {
        $section_style = Flavor_About::get_hero_pattern_css(
            str_replace( 'pattern-', '', $hero['bg_mode'] ),
            $hero['bg_variant']
        );
    } else {
        $section_style = "background-image:url('" . esc_url( $hero['image'] ) . "');background-position:" . esc_attr( $hero['position'] ?? 'center center' );
    }

    // Append min-height
    $section_style .= ';min-height:' . esc_attr( $hero['height'] ?? '55vh' );

    // Build CSS classes
    $section_classes = 'flavor-about-hero flavor-about-full';
    if ( ! $hero['overlay'] )  $section_classes .= ' flavor-about-hero--no-overlay';
    if ( $is_pattern )         $section_classes .= ' flavor-about-hero--pattern';
    if ( $is_dark )            $section_classes .= ' flavor-about-hero--dark';
?>
    <section class="<?php echo esc_attr( $section_classes ); ?>" style="<?php echo esc_attr( $section_style ); ?>">
        <div class="flavor-about-hero-overlay"<?php if ( $hero['overlay'] && ! empty( $hero['overlay_color'] ) && ! $is_pattern ) : ?> style="background:<?php echo esc_attr( $hero['overlay_color'] ); ?>"<?php endif; ?>>
            <div class="flavor-about-hero-inner" style="text-align:<?php echo esc_attr( $hero['text_align'] ); ?>">
                <h1 class="flavor-about-hero-title"><?php echo esc_html( $page_title ); ?></h1>
                <p class="flavor-about-hero-subtitle"><?php echo esc_html( $hero['subtitle'] ); ?></p>
            </div>
        </div>
    </section>
<?php endif; // hero_has_bg ?>
<?php endif; // hero enabled ?>

    <?php if ( get_the_content() ) : ?>
    <div class="entry-content" style="margin-bottom: 2rem;">
        <?php the_content(); ?>
    </div>
    <?php endif; ?>

    <?php if ( $map_url && $map_position === 'above_all' ) : ?>
    <div class="flavor-contact-map flavor-contact-map--above">
        <iframe src="<?php echo esc_url( $map_url ); ?>" width="100%" height="350" style="border:0; border-radius: var(--fc-card-radius, var(--radius));<?php echo $map_filter; ?>" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
    <?php endif; ?>

    <div class="flavor-contact-layout <?php echo $has_info ? 'has-sidebar' : 'no-sidebar'; ?>">

        <!-- ── Formularz kontaktowy ── -->
        <div class="flavor-contact-form-wrap">

            <?php if ( $map_url && $map_position === 'above_form' ) : ?>
            <div class="flavor-contact-map flavor-contact-map--inline">
                <iframe src="<?php echo esc_url( $map_url ); ?>" width="100%" height="300" style="border:0; border-radius: var(--fc-card-radius, var(--radius));<?php echo $map_filter; ?>" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            <?php endif; ?>
            <h2 class="flavor-contact-heading">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <?php fc_e( 'contact_form_title' ); ?>
            </h2>

            <form id="flavor-contact-form" class="flavor-contact-form" novalidate>
                <?php wp_nonce_field( 'flavor_contact_nonce', 'flavor_contact_nonce_field' ); ?>

                <?php if ( is_user_logged_in() ) : ?>
                    <input type="hidden" name="contact_name" value="">
                    <input type="hidden" name="contact_email" value="">
                <?php else : ?>
                <div class="flavor-form-row">
                    <div class="flavor-form-group">
                        <label for="fc-contact-name"><?php fc_e( 'contact_label_name' ); ?> <span class="required">*</span></label>
                        <input type="text" id="fc-contact-name" name="contact_name" required autocomplete="name">
                    </div>
                    <div class="flavor-form-group">
                        <label for="fc-contact-email"><?php fc_e( 'contact_label_email' ); ?> <span class="required">*</span></label>
                        <input type="email" id="fc-contact-email" name="contact_email" required autocomplete="email">
                    </div>
                </div>
                <?php endif; ?>

                <div class="flavor-form-group">
                    <label for="fc-contact-subject"><?php fc_e( 'contact_label_subject' ); ?></label>
                    <input type="text" id="fc-contact-subject" name="contact_subject">
                </div>

                <div class="flavor-form-group">
                    <label for="fc-contact-message"><?php fc_e( 'contact_label_message' ); ?> <span class="required">*</span></label>
                    <textarea id="fc-contact-message" name="contact_message" rows="6" required></textarea>
                </div>

                <!-- Honeypot -->
                <div style="position:absolute;left:-9999px" aria-hidden="true">
                    <input type="text" name="contact_website" tabindex="-1" autocomplete="off">
                </div>

                <div class="flavor-form-footer">
                    <button type="submit" class="flavor-contact-submit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        <?php fc_e( 'contact_btn_send' ); ?>
                    </button>
                </div>

                <div id="flavor-contact-status" class="flavor-contact-status" style="display:none"></div>
            </form>

            <?php if ( $map_url && $map_position === 'below_form' ) : ?>
            <div class="flavor-contact-map flavor-contact-map--inline flavor-contact-map--below">
                <iframe src="<?php echo esc_url( $map_url ); ?>" width="100%" height="300" style="border:0; border-radius: var(--fc-card-radius, var(--radius));<?php echo $map_filter; ?>" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            <?php endif; ?>
        </div>

        <?php if ( $has_info ) : ?>
        <!-- ── Dane kontaktowe / sidebar ── -->
        <aside class="flavor-contact-sidebar">

            <?php
            // Kolejność kart z Customizera
            $cards_order = array_filter( array_map( 'trim', explode( ',', get_theme_mod( 'flavor_contact_cards_order', 'company,reach,hours,social' ) ) ) );
            // Upewnij się, że wszystkie karty są uwzględnione
            foreach ( array( 'company', 'reach', 'hours', 'social' ) as $default_card ) {
                if ( ! in_array( $default_card, $cards_order, true ) ) {
                    $cards_order[] = $default_card;
                }
            }

            foreach ( $cards_order as $card_key ) :
                if ( $card_key === 'company' && ( ( $show_company && $company ) || ( $show_address && $address ) || ( $show_tax_no && $nip ) || ( $show_crn && $crn ) ) ) : ?>
            <div class="flavor-contact-card">
                <h3 class="flavor-contact-card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/><path d="M2 7h20"/><path d="M22 7v3a2 2 0 0 1-2 2a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12a2 2 0 0 1-2-2V7"/></svg>
                    <?php fc_e( 'contact_info_title' ); ?>
                </h3>
                <?php if ( $show_company && $company ) : ?>
                    <p class="flavor-contact-company"><?php echo esc_html( $company ); ?></p>
                <?php endif; ?>
                <?php if ( $show_address && $address ) : ?>
                    <p class="flavor-contact-address"><?php echo nl2br( esc_html( $address ) ); ?></p>
                <?php endif; ?>
                <?php if ( $show_tax_no && $nip ) : ?>
                    <p class="flavor-contact-nip"><?php echo esc_html( $tax_labels['tax_no'] ); ?>: <?php echo esc_html( $nip ); ?></p>
                <?php endif; ?>
                <?php if ( $show_crn && $crn ) : ?>
                    <p class="flavor-contact-crn"><?php echo esc_html( $tax_labels['crn'] ); ?>: <?php echo esc_html( $crn ); ?></p>
                <?php endif; ?>
            </div>
                <?php elseif ( $card_key === 'reach' && ( ( $show_phone && $phone ) || ( $show_email && $email ) ) ) : ?>
            <div class="flavor-contact-card">
                <h3 class="flavor-contact-card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <?php fc_e( 'contact_reach_us' ); ?>
                </h3>
                <?php if ( $show_phone && $phone ) : ?>
                <a href="tel:<?php echo esc_attr( preg_replace( '/[^+\d]/', '', $phone ) ); ?>" class="flavor-contact-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <?php echo esc_html( $phone ); ?>
                </a>
                <?php endif; ?>
                <?php if ( $show_email && $email ) : ?>
                <a href="mailto:<?php echo esc_attr( $email ); ?>" class="flavor-contact-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    <?php echo esc_html( $email ); ?>
                </a>
                <?php endif; ?>
            </div>
                <?php elseif ( $card_key === 'hours' && $show_hours && $hours ) : ?>
            <div class="flavor-contact-card">
                <h3 class="flavor-contact-card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <?php fc_e( 'contact_hours_title' ); ?>
                </h3>
                <table class="flavor-contact-hours-table">
                    <?php foreach ( $hours_rows as $row ) :
                        $df = $row['day_from'] ?? '1';
                        $dt = $row['day_to']   ?? '1';
                        $day_label = ( $df === $dt )
                            ? ( $day_names[ $df ] ?? $df )
                            : ( ( $day_names[ $df ] ?? $df ) . ' – ' . ( $day_names[ $dt ] ?? $dt ) );
                    ?>
                    <tr>
                        <td class="flavor-hours-days"><?php echo esc_html( $day_label ); ?></td>
                        <td class="flavor-hours-time">
                            <?php
                            $from = $row['from'] ?? '';
                            $to   = $row['to']   ?? '';
                            if ( $from && $to ) {
                                echo esc_html( $from . ' – ' . $to );
                            } elseif ( $from ) {
                                echo esc_html( fc__( 'contact_hours_from' ) . ' ' . $from );
                            } elseif ( $to ) {
                                echo esc_html( fc__( 'contact_hours_to' ) . ' ' . $to );
                            }
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
                <?php elseif ( $card_key === 'social' && ! empty( $socials ) ) : ?>
            <div class="flavor-contact-card">
                <h3 class="flavor-contact-card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                    <?php fc_e( 'contact_social_title' ); ?>
                </h3>
                <div class="flavor-social-links">
                    <?php foreach ( $socials as $social ) : ?>
                    <a href="<?php echo esc_url( $social['url'] ); ?>" class="flavor-social-link flavor-social-<?php echo esc_attr( $social['key'] ); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo esc_attr( $social['label'] ); ?>">
                        <?php echo Flavor_Pages::get_social_icon( $social['key'] ); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
                <?php endif;
            endforeach; ?>

        </aside>
        <?php endif; ?>

    </div><!-- .flavor-contact-layout -->

    <?php if ( $map_url && $map_position === 'below_all' ) : ?>
    <div class="flavor-contact-map">
        <iframe src="<?php echo esc_url( $map_url ); ?>" width="100%" height="350" style="border:0; border-radius: var(--fc-card-radius, var(--radius));<?php echo $map_filter; ?>" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
    <?php endif; ?>

</article>

<script>
(function(){
    var form = document.getElementById('flavor-contact-form');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        var btn    = form.querySelector('.flavor-contact-submit');
        var status = document.getElementById('flavor-contact-status');
        var orig   = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<span class="flavor-spinner"></span>';

        var fd = new FormData(form);
        fd.append('action', 'flavor_contact_form');
        fd.append('nonce', form.querySelector('[name="flavor_contact_nonce_field"]').value);

        fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
            method: 'POST',
            credentials: 'same-origin',
            body: fd
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            status.style.display = 'block';
            if (res.success) {
                status.className = 'flavor-contact-status success';
                status.textContent = res.data.message;
                form.reset();
            } else {
                status.className = 'flavor-contact-status error';
                status.textContent = res.data.message;
            }
            btn.disabled = false;
            btn.innerHTML = orig;
        })
        .catch(function() {
            status.style.display = 'block';
            status.className = 'flavor-contact-status error';
            status.textContent = '<?php echo esc_js( fc__( 'contact_error_send' ) ); ?>';
            btn.disabled = false;
            btn.innerHTML = orig;
        });
    });
})();
</script>

<?php endwhile; ?>

<?php get_footer(); ?>
