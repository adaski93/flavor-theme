<?php
/**
 * Template Name: O nas
 * Template Post Type: page
 *
 * Szablon strony „O nas" z sekcjami hero, zespół, wartości, statystyki,
 * opinie klientów i oś czasu — konfigurowane z Customizera.
 *
 * @package Flavor
 * @since 1.5.3
 */

get_header();

$sections_order = Flavor_About::get_sections_order();
$hero           = Flavor_About::get_hero();
$team           = Flavor_About::get_team();
$values         = Flavor_About::get_values();
$stats          = Flavor_About::get_stats();
$testimonials   = Flavor_About::get_testimonials();
$timeline       = Flavor_About::get_timeline();
$page_title     = get_the_title();
?>

<?php while ( have_posts() ) : the_post(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'flavor-about-page' ); ?>>

<?php foreach ( $sections_order as $section ) :

    // ── Hero Banner ──
    if ( $section === 'hero' && $hero['image'] ) : ?>
    <section class="flavor-about-hero flavor-about-full" style="background-image:url('<?php echo esc_url( $hero['image'] ); ?>')">
        <div class="flavor-about-hero-overlay">
            <div class="flavor-about-hero-inner">
                <h1 class="flavor-about-hero-title"><?php echo esc_html( $page_title ); ?></h1>
                <?php if ( $hero['subtitle'] ) : ?>
                    <p class="flavor-about-hero-subtitle"><?php echo esc_html( $hero['subtitle'] ); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php // ── Page Content (from WordPress editor) ──
    elseif ( $section === 'content' && get_the_content() ) : ?>
    <section class="flavor-about-content">
        <div class="entry-content">
            <?php the_content(); ?>
        </div>
    </section>

    <?php // ── Values / Mission ──
    elseif ( $section === 'values' && ! empty( $values['items'] ) ) : ?>
    <section class="flavor-about-values">
        <?php if ( $values['title'] ) : ?>
            <h2 class="flavor-about-section-title"><?php echo esc_html( $values['title'] ); ?></h2>
        <?php endif; ?>
        <div class="flavor-about-values-grid">
            <?php foreach ( $values['items'] as $val ) : ?>
            <div class="flavor-about-value-card">
                <div class="flavor-about-value-icon">
                    <?php echo Flavor_About::get_value_icon( $val['icon'] ?? 'star' ); ?>
                </div>
                <div class="flavor-about-value-body">
                    <?php if ( ! empty( $val['title'] ) ) : ?>
                        <h3 class="flavor-about-value-title"><?php echo esc_html( $val['title'] ); ?></h3>
                    <?php endif; ?>
                    <?php if ( ! empty( $val['desc'] ) ) : ?>
                        <p class="flavor-about-value-desc"><?php echo esc_html( $val['desc'] ); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <?php // ── Stats / Numbers ──
    elseif ( $section === 'stats' && ! empty( $stats ) ) : ?>
    <section class="flavor-about-stats flavor-about-full">
        <div class="flavor-about-stats-grid">
            <?php foreach ( $stats as $stat ) : ?>
            <div class="flavor-about-stat">
                <span class="flavor-about-stat-number" data-target="<?php echo esc_attr( $stat['number'] ?? '0' ); ?>">0</span><?php
                if ( ! empty( $stat['suffix'] ) ) : ?><span class="flavor-about-stat-suffix"><?php echo esc_html( $stat['suffix'] ); ?></span><?php endif; ?>
                <?php if ( ! empty( $stat['label'] ) ) : ?>
                    <span class="flavor-about-stat-label"><?php echo esc_html( $stat['label'] ); ?></span>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <?php // ── Team ──
    elseif ( $section === 'team' && ! empty( $team['items'] ) ) : ?>
    <section class="flavor-about-team">
        <?php if ( $team['title'] ) : ?>
            <h2 class="flavor-about-section-title"><?php echo esc_html( $team['title'] ); ?></h2>
        <?php endif; ?>
        <div class="flavor-about-team-slider-wrap">
            <button type="button" class="flavor-about-team-nav flavor-about-team-prev" aria-label="Poprzedni">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            </button>
            <div class="flavor-about-team-slider">
                <?php foreach ( $team['items'] as $member ) : ?>
                <div class="flavor-about-team-card">
                    <?php if ( ! empty( $member['image_url'] ) ) : ?>
                    <div class="flavor-about-team-photo">
                        <img src="<?php echo esc_url( $member['image_url'] ); ?>" alt="<?php echo esc_attr( $member['name'] ?? '' ); ?>" loading="lazy">
                    </div>
                    <?php else : ?>
                    <div class="flavor-about-team-photo flavor-about-team-no-photo">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 20a6 6 0 0 0-12 0"/><circle cx="12" cy="10" r="4"/></svg>
                    </div>
                    <?php endif; ?>
                    <?php if ( ! empty( $member['name'] ) ) : ?>
                        <h3 class="flavor-about-team-name"><?php echo esc_html( $member['name'] ); ?></h3>
                    <?php endif; ?>
                    <?php if ( ! empty( $member['role'] ) ) : ?>
                        <p class="flavor-about-team-role"><?php echo esc_html( $member['role'] ); ?></p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="flavor-about-team-nav flavor-about-team-next" aria-label="Następny">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </button>
        </div>
    </section>

    <?php // ── Testimonials ──
    elseif ( $section === 'testimonials' && ! empty( $testimonials['items'] ) ) : ?>
    <section class="flavor-about-testimonials">
        <?php if ( $testimonials['title'] ) : ?>
            <h2 class="flavor-about-section-title"><?php echo esc_html( $testimonials['title'] ); ?></h2>
        <?php endif; ?>
        <div class="flavor-about-testimonials-grid">
            <?php foreach ( $testimonials['items'] as $test ) : ?>
            <blockquote class="flavor-about-testimonial">
                <div class="flavor-about-testimonial-quote">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" opacity="0.15"><path d="M11.3 2.5C6 5 3.3 8.3 3.3 13c0 3.3 2 5.5 4.5 5.5 2.2 0 3.7-1.5 3.7-3.5 0-2-1.3-3.3-3.2-3.3-.3 0-.8.1-1 .2.5-2.7 2.5-5.5 5.5-7.2L11.3 2.5zm10.2 0C16.2 5 13.5 8.3 13.5 13c0 3.3 2 5.5 4.5 5.5 2.2 0 3.7-1.5 3.7-3.5 0-2-1.3-3.3-3.2-3.3-.3 0-.8.1-1 .2.5-2.7 2.5-5.5 5.5-7.2L21.5 2.5z"/></svg>
                    <p><?php echo esc_html( $test['quote'] ?? '' ); ?></p>
                </div>
                <footer class="flavor-about-testimonial-footer">
                    <cite class="flavor-about-testimonial-author"><?php echo esc_html( $test['author'] ?? '' ); ?></cite>
                    <?php if ( ! empty( $test['role'] ) ) : ?>
                        <span class="flavor-about-testimonial-role"><?php echo esc_html( $test['role'] ); ?></span>
                    <?php endif; ?>
                </footer>
            </blockquote>
            <?php endforeach; ?>
        </div>
    </section>

    <?php // ── Timeline ──
    elseif ( $section === 'timeline' && ! empty( $timeline['items'] ) ) : ?>
    <section class="flavor-about-timeline">
        <?php if ( $timeline['title'] ) : ?>
            <h2 class="flavor-about-section-title"><?php echo esc_html( $timeline['title'] ); ?></h2>
        <?php endif; ?>
        <div class="flavor-about-timeline-track">
            <?php foreach ( $timeline['items'] as $i => $entry ) : ?>
            <div class="flavor-about-timeline-entry <?php echo $i % 2 === 0 ? 'left' : 'right'; ?>">
                <div class="flavor-about-timeline-dot"></div>
                <div class="flavor-about-timeline-card">
                    <?php if ( ! empty( $entry['year'] ) ) : ?>
                        <span class="flavor-about-timeline-year"><?php echo esc_html( $entry['year'] ); ?></span>
                    <?php endif; ?>
                    <?php if ( ! empty( $entry['title'] ) ) : ?>
                        <h3 class="flavor-about-timeline-title"><?php echo esc_html( $entry['title'] ); ?></h3>
                    <?php endif; ?>
                    <?php if ( ! empty( $entry['desc'] ) ) : ?>
                        <p class="flavor-about-timeline-desc"><?php echo esc_html( $entry['desc'] ); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <?php endif; ?>

<?php endforeach; ?>

</article>

<!-- Stats counter animation -->
<script>
(function(){
    var counters = document.querySelectorAll('.flavor-about-stat-number[data-target]');
    if (!counters.length) return;

    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (!entry.isIntersecting) return;
            var el     = entry.target;
            var target = parseFloat(el.getAttribute('data-target')) || 0;
            var isInt  = Number.isInteger(target);
            var dur    = 1500;
            var start  = performance.now();

            function step(now) {
                var progress = Math.min((now - start) / dur, 1);
                var ease     = 1 - Math.pow(1 - progress, 3);
                var current  = target * ease;
                el.textContent = isInt ? Math.round(current) : current.toFixed(1);
                if (progress < 1) requestAnimationFrame(step);
            }
            requestAnimationFrame(step);
            observer.unobserve(el);
        });
    }, { threshold: 0.3 });

    counters.forEach(function(c) { observer.observe(c); });
})();

/* ── Team slider navigation ── */
(function(){
    var slider = document.querySelector('.flavor-about-team-slider');
    var prev   = document.querySelector('.flavor-about-team-prev');
    var next   = document.querySelector('.flavor-about-team-next');
    if (!slider || !prev || !next) return;

    function getCardWidth() {
        var card = slider.querySelector('.flavor-about-team-card');
        if (!card) return 300;
        var style = getComputedStyle(slider);
        var gap   = parseFloat(style.gap) || parseFloat(style.columnGap) || 24;
        return card.offsetWidth + gap;
    }

    function updateNav() {
        prev.disabled = slider.scrollLeft <= 1;
        next.disabled = slider.scrollLeft + slider.offsetWidth >= slider.scrollWidth - 1;
    }

    prev.addEventListener('click', function() {
        slider.scrollBy({ left: -getCardWidth(), behavior: 'smooth' });
    });
    next.addEventListener('click', function() {
        slider.scrollBy({ left: getCardWidth(), behavior: 'smooth' });
    });

    slider.addEventListener('scroll', updateNav, { passive: true });
    updateNav();
    /* re-check after fonts / images loaded */
    window.addEventListener('load', updateNav);
})();
</script>

<?php endwhile; ?>

<?php get_footer(); ?>
