<?php
/**
 * Template Name: Treatments Hub
 * Description: The /all-treatments/ directory. A lean overview that routes
 * visitors to a program fast: compact hero, sticky category jump-nav, and
 * styled category link lists, closing with the shared dark concierge band.
 * Implements the Claude Design "All Treatments" handoff. Program names and
 * URLs are reused verbatim from the live site.
 *
 * @package RehabParent
 */

get_header();

/*
 * Category directory — supplied per brand via the `rehab_treatments_hub_categories`
 * filter so this shared template carries no brand-specific programs (REH-49).
 * The active child theme supplies the list. Each category is:
 *   [ 'id' => 'cat-slug', 'eyebrow' => '…', 'heading' => '…',
 *     'bg' => 'white'|'parch',
 *     'links' => [ [ 'Label', '/site-relative-path/' ], … ] ]
 * Links use site-relative paths resolved through home_url() so they work on
 * every environment (dev, Cloudways, per-brand domain).
 */
$rehab_tx_categories = array_values( (array) apply_filters( 'rehab_treatments_hub_categories', [] ) );

$rehab_tx_arrow = '<svg class="rehab-tx-arrow" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>';

while ( have_posts() ) :
	the_post();
	?>
	<div class="rehab-treatments-hub">

		<!-- Compact hero: breadcrumb + title + lede -->
		<section class="rehab-tx-hero">
			<div class="rehab-container">
				<nav class="rehab-tx-hero__breadcrumb" aria-label="Breadcrumb">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a><span class="sep">/</span><span aria-current="page"><?php the_title(); ?></span>
				</nav>
				<h1 class="rehab-heading rehab-heading--xl rehab-tx-hero__h1"><?php the_title(); ?></h1>
				<p class="rehab-tx-hero__lede">Every inpatient program, in one place. Browse by area of care &mdash; each is delivered by the same multi-disciplinary clinical team.</p>
			</div>
		</section>

		<!-- Sticky category jump-nav -->
		<?php if ( $rehab_tx_categories ) : ?>
			<nav class="rehab-tx-nav" aria-label="Treatment categories">
				<div class="rehab-container rehab-tx-nav__inner">
					<?php foreach ( $rehab_tx_categories as $cat ) : ?>
						<a class="rehab-tx-chip" href="#<?php echo esc_attr( $cat['id'] ); ?>"><?php echo esc_html( wp_strip_all_tags( html_entity_decode( preg_replace( '/^\d+\s*—\s*/u', '', $cat['eyebrow'] ) ) ) ); ?></a>
					<?php endforeach; ?>
				</div>
			</nav>
		<?php endif; ?>

		<!-- Category sections -->
		<?php foreach ( $rehab_tx_categories as $cat ) : ?>
			<section class="rehab-tx-section rehab-tx-section--<?php echo esc_attr( $cat['bg'] ); ?>" id="<?php echo esc_attr( $cat['id'] ); ?>">
				<div class="rehab-container">
					<div class="rehab-tx-head">
						<span class="rehab-eyebrow"><?php echo wp_kses_post( $cat['eyebrow'] ); ?></span>
						<h2 class="rehab-heading rehab-heading--lg"><?php echo wp_kses_post( $cat['heading'] ); ?></h2>
					</div>
					<div class="rehab-tx-list">
						<?php foreach ( $cat['links'] as $link ) : ?>
							<a class="rehab-tx-link" href="<?php echo esc_url( home_url( $link[1] ) ); ?>"><?php echo wp_kses_post( $link[0] ); ?> <?php echo $rehab_tx_arrow; // phpcs:ignore WordPress.Security.EscapeOutput — static inline SVG. ?></a>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
		<?php endforeach; ?>

		<?php
		// Closing dark concierge band — the single conversion moment shared with
		// every other page. Rendered through the existing rehab/cta-band block so
		// styling stays in lock-step with the rest of the site.
		$rehab_tx_phone = get_theme_mod( 'rehab_phone_display', '' );
		$rehab_tx_cta   = [
			'background'   => 'dark',
			'eyebrow'      => 'Not sure where to start',
			'heading'      => "We'll help you find the right program",
			'lede'         => 'A short, confidential call with our admissions team &mdash; for yourself or someone you love. We listen, answer every question, and never sell.',
			'primaryText'  => 'Get a free assessment',
			'primaryUrl'   => home_url( '/contact/' ),
			'phoneText'    => $rehab_tx_phone,
			'phoneHref'    => 'tel:' . preg_replace( '/[^0-9+]/', '', $rehab_tx_phone ),
			'helper'       => 'Free, confidential, and no-obligation.',
		];
		echo do_blocks( '<!-- wp:rehab/cta-band ' . wp_json_encode( $rehab_tx_cta ) . ' /-->' );
		?>
	</div>
	<?php
endwhile;

get_footer();
