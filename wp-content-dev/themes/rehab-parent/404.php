<?php
/**
 * 404 fallback. Branded "page not found" with popular destinations.
 *
 * @package RehabParent
 */

get_header();
?>
<section class="rehab-404 rehab-bg-cream">
	<div class="rehab-container rehab-container--narrow">
		<p class="rehab-404__eyebrow">404 — page not found</p>
		<h1 class="rehab-404__title">This page seems to have wandered off.</h1>
		<p class="rehab-404__body">The page you were looking for doesn't exist or has moved. Try one of these instead:</p>
		<ul class="rehab-404__links">
			<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
			<li><a href="<?php echo esc_url( home_url( '/why-us/' ) ); ?>">Why us</a></li>
			<li><a href="<?php echo esc_url( home_url( '/all-treatments/' ) ); ?>">All treatments</a></li>
			<li><a href="<?php echo esc_url( home_url( '/cost/' ) ); ?>">Cost &amp; insurance</a></li>
			<li><a href="<?php echo esc_url( home_url( '/all-articles/' ) ); ?>">All articles</a></li>
			<li><a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">Contact us</a></li>
		</ul>
		<p class="rehab-404__cta">
			<a class="rehab-btn rehab-btn--luxury" href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">Speak with admissions</a>
		</p>
	</div>
</section>
<?php
get_footer();
