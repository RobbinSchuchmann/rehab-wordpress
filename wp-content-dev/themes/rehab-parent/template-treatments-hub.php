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
 * Category directory. Each link uses a site-relative path resolved through
 * home_url() so it works on every environment (dev, Cloudways, per-brand).
 * The three former standalone headings (Alcohol detox, Process addiction,
 * Stages of change) are grouped as "Core programs" per the design.
 */
$rehab_tx_categories = [
	[
		'id'      => 'cat-core',
		'eyebrow' => 'Core programs',
		'heading' => 'Core programs',
		'bg'      => 'white',
		'links'   => [
			[ 'Alcohol detox', '/alcohol-addiction/' ],
			[ 'Process addiction rehab', '/process-addiction-treatment/' ],
			[ 'Stages of change addiction', '/stages-of-change-addiction/' ],
		],
	],
	[
		'id'      => 'cat-substance',
		'eyebrow' => '01 — Substance addiction',
		'heading' => 'Substance addiction treatment',
		'bg'      => 'parch',
		'links'   => [
			[ 'Cocaine addiction treatment', '/cocaine-addiction-treatment-rehab-thailand/' ],
			[ 'Meth &amp; ice addiction treatment', '/ice-addiction-treatment-rehab-thailand/' ],
			[ 'Heroin &amp; opiate addiction treatment', '/heroin-rehab-thailand/' ],
			[ 'Crack addiction treatment &amp; detox', '/crack-rehab-thailand/' ],
			[ 'Ecstasy (MDMA) addiction treatment', '/mdma-ecstasy-rehab-thailand/' ],
			[ 'GHB (Fishies) addiction treatment &amp; detox', '/ghb-addiction-rehab-thailand/' ],
			[ 'Ketamine addiction treatment', '/ketamine-addiction-rehab/' ],
			[ 'Marijuana (Weed) addiction treatment', '/marijuana-addiction-rehab/' ],
		],
	],
	[
		'id'      => 'cat-prescription',
		'eyebrow' => '02 — Prescription drugs',
		'heading' => 'Prescription drug rehab',
		'bg'      => 'white',
		'links'   => [
			[ 'Xanax (Alprazolam) addiction treatment', '/xanax-rehab-thailand/' ],
			[ 'OxyContin (Oxycodone) addiction treatment', '/oxycodone-rehab/' ],
			[ 'Valium (Diazepam) addiction treatment', '/valium-rehab-thailand/' ],
			[ 'Tramadol addiction treatment &amp; detox', '/tramadol-rehab-thailand/' ],
			[ 'Ritalin (Methylphenidate) addiction treatment', '/ritalin-rehab-thailand/' ],
		],
	],
	[
		'id'      => 'cat-mental',
		'eyebrow' => '03 — Mental health',
		'heading' => 'Mental health rehab',
		'bg'      => 'parch',
		'links'   => [
			[ 'Anxiety treatment and rehab', '/anxiety-rehab-thailand/' ],
			[ 'PTSD &amp; trauma treatment', '/ptsd-trauma-retreat/' ],
			[ 'Sex addiction treatment', '/sex-addiction-treatment-thailand/' ],
			[ 'Codependency treatment', '/codependency-treatment-thailand/' ],
			[ 'Insomnia &amp; sleep disorder treatment', '/insomnia-treatment-thailand/' ],
			[ 'Burnout treatment for executives', '/luxury-executive-burnout-thailand/' ],
			[ 'Depression treatment', '/depression-retreat-thailand/' ],
			[ 'Gambling addiction treatment', '/gambling-addiction-treatment-thailand/' ],
			[ 'Internet &amp; gaming addiction treatment', '/gaming-addiction-treatment-thailand/' ],
			[ 'Internet addiction treatment', '/internet-addiction-rehab-thailand/' ],
			[ 'Cryptocurrency addiction treatment', '/what-is-crypto-addiction/' ],
			[ 'Luxury traumatic reenactment treatment', '/traumatic-reenactment/' ],
			[ 'Couples treatment', '/couples-treatment-thailand/' ],
			[ 'Dialectical behaviour treatment', '/dbt-treatment/' ],
		],
	],
	[
		'id'      => 'cat-eating',
		'eyebrow' => '04 — Eating disorders',
		'heading' => 'Eating disorder rehab',
		'bg'      => 'white',
		'links'   => [
			[ 'Anorexia treatment center', '/anorexia-rehab-treatment-thailand/' ],
			[ 'Bulimia treatment center', '/bulimia-rehab-thailand/' ],
			[ 'Overeating disorders', '/treatment-for-overeating-disorders/' ],
		],
	],
];

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
		<nav class="rehab-tx-nav" aria-label="Treatment categories">
			<div class="rehab-container rehab-tx-nav__inner">
				<?php foreach ( $rehab_tx_categories as $cat ) : ?>
					<a class="rehab-tx-chip" href="#<?php echo esc_attr( $cat['id'] ); ?>"><?php echo esc_html( wp_strip_all_tags( html_entity_decode( preg_replace( '/^\d+\s*—\s*/u', '', $cat['eyebrow'] ) ) ) ); ?></a>
				<?php endforeach; ?>
			</div>
		</nav>

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
		$rehab_tx_phone = get_theme_mod( 'rehab_phone_display', '+66 96 582 3832' );
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
