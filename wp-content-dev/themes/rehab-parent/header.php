<?php
/**
 * Site header — fixed top bar with menu toggle, logo, phone CTA, off-canvas mega menu.
 *
 * @package RehabParent
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="rehab-skip-link rehab-visually-hidden" href="#main"><?php esc_html_e( 'Skip to content', 'rehab-parent' ); ?></a>

<?php
$phone_text   = get_theme_mod( 'rehab_phone_display', '+66 96 582 3832' );
$phone_number = get_theme_mod( 'rehab_phone_number', '+66965823832' );
$phone_tel    = preg_replace( '/[^0-9+]/', '', $phone_number );

// Site-wide utility bar (top). Items filterable per-brand via 'rehab_utility_bar_items'.
$utility_left    = apply_filters( 'rehab_utility_bar_items', [
	'Confidential intake, 24/7',
	'Licensed by Thai Ministry of Public Health',
	'Only 12 clients at a time',
] );
$whatsapp_number = get_theme_mod( 'rehab_whatsapp_number', '66965823832' );
?>

<div class="rehab-utility-bar">
	<div class="rehab-utility-bar__inner">
		<div class="rehab-utility-bar__left">
			<?php foreach ( $utility_left as $item ) : ?>
				<span><span class="rehab-utility-bar__dot" aria-hidden="true">◆</span><?php echo esc_html( $item ); ?></span>
			<?php endforeach; ?>
		</div>
		<div class="rehab-utility-bar__right">
			<a href="tel:<?php echo esc_attr( $phone_tel ); ?>"><?php esc_html_e( 'UK / EU / Intl call back', 'rehab-parent' ); ?></a>
			<a href="https://wa.me/<?php echo esc_attr( $whatsapp_number ); ?>"><?php esc_html_e( 'WhatsApp', 'rehab-parent' ); ?></a>
		</div>
	</div>
</div>

<header class="rehab-navbar" id="rehab-navbar" data-rehab-navbar>
	<div class="rehab-navbar__cell rehab-navbar__cell--start">
		<button
			type="button"
			class="rehab-navbar__toggle"
			aria-label="<?php esc_attr_e( 'Toggle menu', 'rehab-parent' ); ?>"
			aria-expanded="false"
			aria-controls="rehab-mega-menu"
			data-rehab-menu-toggle
		>
			<span class="rehab-navbar__toggle-icon" aria-hidden="true">
				<svg class="rehab-navbar__toggle-icon-open" viewBox="0 0 18 8" xmlns="http://www.w3.org/2000/svg">
					<path d="M.562 1.568h16.875A.567.567 0 0 0 18 .998a.566.566 0 0 0-.563-.57H.563A.566.566 0 0 0 0 .998c0 .314.252.57.562.57ZM17.438 6.13H.561A.567.567 0 0 0 0 6.7c0 .315.252.57.562.57h16.875A.566.566 0 0 0 18 6.7a.567.567 0 0 0-.563-.57Z" fill="currentColor"/>
				</svg>
				<svg class="rehab-navbar__toggle-icon-close" viewBox="0 0 50 50" xmlns="http://www.w3.org/2000/svg">
					<path d="M9.156 6.313 6.312 9.155 22.157 25 6.22 40.969l2.81 2.811L25 27.844 40.938 43.78l2.843-2.843L27.844 25 43.687 9.156l-2.843-2.844L25 22.157Z" fill="currentColor"/>
				</svg>
			</span>
			<span class="rehab-navbar__toggle-text"><?php esc_html_e( 'Menu', 'rehab-parent' ); ?></span>
		</button>
	</div>

	<div class="rehab-navbar__cell rehab-navbar__cell--center">
		<div class="rehab-navbar__logo">
			<?php
			// Child theme can drop assets/img/logo.svg to provide a brand-specific logo.
			$child_logo_path = get_stylesheet_directory() . '/assets/img/logo.svg';
			if ( file_exists( $child_logo_path ) ) :
				$svg = file_get_contents( $child_logo_path );
				?>
				<a class="custom-logo-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
					<?php echo $svg; // SVG sourced from child theme — safe ?>
				</a>
			<?php elseif ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a class="custom-logo-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<span class="rehab-navbar__brand-text"><?php bloginfo( 'name' ); ?></span>
				</a>
			<?php endif; ?>
		</div>
	</div>

	<div class="rehab-navbar__cell rehab-navbar__cell--end">
		<a href="tel:<?php echo esc_attr( $phone_tel ); ?>" class="rehab-navbar__phone">
			<span class="rehab-navbar__phone-icon" aria-hidden="true">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
					<path d="M20 15.5c-1.25 0-2.45-.2-3.57-.57-.35-.11-.74-.03-1.02.24l-2.2 2.2c-2.83-1.44-5.15-3.75-6.59-6.58l2.2-2.21c.28-.27.36-.66.25-1.01C8.7 6.45 8.5 5.25 8.5 4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1 0 9.39 7.61 17 17 17 .55 0 1-.45 1-1v-3.5c0-.55-.45-1-1-1z"/>
				</svg>
			</span>
			<span class="rehab-navbar__phone-text"><?php echo esc_html( $phone_text ); ?></span>
		</a>
	</div>
</header>

<aside
	class="rehab-mega-menu"
	id="rehab-mega-menu"
	aria-label="<?php esc_attr_e( 'Site navigation', 'rehab-parent' ); ?>"
	aria-hidden="true"
	data-rehab-mega-menu
>
	<div class="rehab-mega-menu__inner">
		<nav class="rehab-mega-menu__nav" aria-label="<?php esc_attr_e( 'Primary', 'rehab-parent' ); ?>">
			<?php
			wp_nav_menu(
				[
					'theme_location' => 'primary',
					'menu_class'     => 'rehab-mega-menu__list',
					'container'      => false,
					'fallback_cb'    => '__return_empty_string',
					'depth'          => 3,
				]
			);
			?>
		</nav>

		<aside class="rehab-mega-menu__sidebar">
			<div class="rehab-mega-menu__pitch">
				<?php
				$pitch_title = get_theme_mod( 'rehab_menu_pitch_title', __( 'In-patient luxury rehab in Thailand', 'rehab-parent' ) );
				$pitch_body  = get_theme_mod( 'rehab_menu_pitch_body', __( 'Doctor-led, evidence-based recovery in a private 5-star sanctuary. We take a personalised approach to every client.', 'rehab-parent' ) );
				?>
				<p class="rehab-mega-menu__pitch-title"><?php echo esc_html( $pitch_title ); ?></p>
				<p class="rehab-mega-menu__pitch-body"><?php echo esc_html( $pitch_body ); ?></p>
				<a href="tel:<?php echo esc_attr( $phone_tel ); ?>" class="rehab-btn rehab-btn--ghost">
					<?php echo esc_html( $phone_text ); ?>
				</a>
			</div>
		</aside>
	</div>
</aside>

<main id="main">
