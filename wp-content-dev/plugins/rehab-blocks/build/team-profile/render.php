<?php
/**
 * Server-side render for `rehab/team-profile`.
 *
 * Two-column layout: role + name + portrait + optional pull-quote + bio on the
 * left, a sticky "[Name] is part of our team" enquiry form on the right. The
 * form posts to rehab/v1/contact via view.js.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

$a          = $attributes;
$first      = '' !== $a['firstName'] ? $a['firstName'] : trim( strtok( (string) $a['name'], ' ' ) );
$quote_src  = '' !== $a['quoteSrc'] ? $a['quoteSrc'] : $a['name'];
$paras      = array_filter( array_map( 'trim', preg_split( "/\n\s*\n/", (string) $a['bio'] ) ) );
$bio_title  = '' !== $a['bioTitle'] ? $a['bioTitle'] : ( '' !== $first ? 'About ' . $first : 'About' );

$wrapper = get_block_wrapper_attributes( [
	'class' => 'rehab-team-profile rehab-bg-' . sanitize_html_class( $a['background'] ?: 'white' ),
] );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="rehab-container">
		<nav class="rehab-team-profile__crumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'rehab-blocks' ); ?>">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'rehab-blocks' ); ?></a>
			<span class="rehab-team-profile__crumb-sep" aria-hidden="true">/</span>
			<a href="<?php echo esc_url( home_url( '/team/' ) ); ?>"><?php esc_html_e( 'Team', 'rehab-blocks' ); ?></a>
			<span class="rehab-team-profile__crumb-sep" aria-hidden="true">/</span>
			<span class="rehab-team-profile__crumb-current"><?php echo esc_html( wp_strip_all_tags( (string) $a['name'] ) ); ?></span>
		</nav>

		<div class="rehab-team-profile__layout">
			<div class="rehab-team-profile__main">
				<?php if ( '' !== $a['role'] ) : ?>
					<span class="rehab-team-profile__role"><?php echo wp_kses_post( $a['role'] ); ?></span>
				<?php endif; ?>
				<h1 class="rehab-team-profile__name"><?php echo wp_kses_post( $a['name'] ); ?></h1>

				<div class="rehab-team-profile__portrait">
					<?php if ( '' !== $a['photoUrl'] ) : ?>
						<img src="<?php echo esc_url( $a['photoUrl'] ); ?>" alt="<?php echo esc_attr( $a['photoAlt'] ?: $a['name'] ); ?>" loading="eager" decoding="async" />
					<?php else : ?>
						<div class="rehab-team-profile__portrait-placeholder"><span><?php echo esc_html( $a['name'] ); ?></span></div>
					<?php endif; ?>
				</div>

				<?php if ( '' !== $a['quote'] ) : ?>
					<blockquote class="rehab-team-profile__quote">
						<p><?php echo wp_kses_post( $a['quote'] ); ?></p>
						<?php if ( '' !== $quote_src ) : ?>
							<cite><?php echo esc_html( $quote_src ); ?></cite>
						<?php endif; ?>
					</blockquote>
				<?php endif; ?>

				<?php if ( $paras ) : ?>
					<div class="rehab-team-profile__bio">
						<h2><?php echo esc_html( $bio_title ); ?></h2>
						<?php foreach ( $paras as $p ) : ?>
							<p><?php echo wp_kses_post( $p ); ?></p>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
