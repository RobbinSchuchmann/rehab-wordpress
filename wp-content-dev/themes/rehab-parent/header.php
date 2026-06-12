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

<?php
// Standard nav (approved June 2026 design): brand left, inline links centre,
// phone + CTA right. Burger appears on mobile only and opens the mega menu.
$nav_cta_text = get_theme_mod( 'rehab_nav_cta_text', __( 'Talk with admissions', 'rehab-parent' ) );
$nav_cta_url  = get_theme_mod( 'rehab_nav_cta_url', '/contact-us/' );
?>
<header class="rehab-navbar" id="rehab-navbar" data-rehab-navbar>
	<div class="rehab-navbar__brand">
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

	<nav class="rehab-navbar__links" aria-label="<?php esc_attr_e( 'Primary', 'rehab-parent' ); ?>">
		<?php
		wp_nav_menu(
			[
				'theme_location' => 'primary',
				'menu_class'     => 'rehab-navbar__menu',
				'container'      => false,
				'fallback_cb'    => '__return_empty_string',
				'depth'          => 2,
			]
		);
		?>
	</nav>

	<div class="rehab-navbar__actions">
		<a href="tel:<?php echo esc_attr( $phone_tel ); ?>" class="rehab-navbar__phone">
			<span class="rehab-navbar__phone-icon" aria-hidden="true">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/>
				</svg>
			</span>
			<span class="rehab-navbar__phone-text"><?php echo esc_html( $phone_text ); ?></span>
		</a>
		<a href="<?php echo esc_url( $nav_cta_url ); ?>" class="rehab-btn rehab-btn--luxury rehab-btn--sm rehab-navbar__cta"><?php echo esc_html( $nav_cta_text ); ?></a>
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
		</button>
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
