<?php
/**
 * Site footer.
 *
 * @package RehabParent
 */

$brand_name      = get_bloginfo( 'name' );
$address         = get_theme_mod( 'rehab_footer_address', '' );
$phone_text      = get_theme_mod( 'rehab_phone_display', '' );
$phone_number    = get_theme_mod( 'rehab_phone_number', '' );
$phone_tel       = preg_replace( '/[^0-9+]/', '', $phone_number );
$international   = get_theme_mod( 'rehab_footer_intl_phones', '' ); // multiline "Label|+number"
$copyright       = get_theme_mod( 'rehab_footer_copyright', sprintf( '&copy; %d %s', gmdate( 'Y' ), $brand_name ) );
$social_facebook = get_theme_mod( 'rehab_social_facebook', '' );
$social_instagram= get_theme_mod( 'rehab_social_instagram', '' );
$social_linkedin = get_theme_mod( 'rehab_social_linkedin', '' );
$social_youtube  = get_theme_mod( 'rehab_social_youtube', '' );
$social_x        = get_theme_mod( 'rehab_social_x', '' );
$social_pinterest= get_theme_mod( 'rehab_social_pinterest', '' );
$social_threads  = get_theme_mod( 'rehab_social_threads', '' );

$intl_lines = array_filter(
	array_map( 'trim', explode( "\n", $international ) ),
	fn( $l ) => $l && str_contains( $l, '|' )
);
?>
</main><?php // Closes the <main id="main"> opened in header.php ?>

<footer class="rehab-site-footer" role="contentinfo">
	<div class="rehab-site-footer__top">
		<div class="rehab-container">
			<div class="rehab-site-footer__row">

				<div class="rehab-site-footer__col rehab-site-footer__col--brand">
					<?php if ( has_custom_logo() ) : ?>
						<div class="rehab-site-footer__logo">
							<?php the_custom_logo(); ?>
						</div>
					<?php else : ?>
						<a class="rehab-site-footer__brand-text" href="<?php echo esc_url( home_url( '/' ) ); ?>">
							<?php echo esc_html( $brand_name ); ?>
						</a>
					<?php endif; ?>

					<address class="rehab-site-footer__address">
						<?php echo nl2br( esc_html( $address ) ); ?>
					</address>

					<p class="rehab-site-footer__phone-primary">
						<a href="tel:<?php echo esc_attr( $phone_tel ); ?>">
							<?php echo esc_html( $phone_text ); ?>
						</a>
					</p>

					<?php if ( $intl_lines ) : ?>
						<ul class="rehab-site-footer__phones">
							<?php foreach ( $intl_lines as $line ) :
								[ $label, $num ] = array_map( 'trim', explode( '|', $line, 2 ) );
								$tel = preg_replace( '/[^0-9+]/', '', $num );
							?>
								<li>
									<span class="rehab-site-footer__phone-label"><?php echo esc_html( $label ); ?>:</span>
									<a href="tel:<?php echo esc_attr( $tel ); ?>"><?php echo esc_html( $num ); ?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<ul class="rehab-site-footer__socials">
						<?php if ( $social_facebook ) : ?>
							<li><a href="<?php echo esc_url( $social_facebook ); ?>" target="_blank" rel="noopener" aria-label="Facebook">
								<svg viewBox="0 0 320 512" width="20" height="20" fill="currentColor"><path d="m279.14 288 14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"/></svg>
							</a></li>
						<?php endif; ?>
						<?php if ( $social_instagram ) : ?>
							<li><a href="<?php echo esc_url( $social_instagram ); ?>" target="_blank" rel="noopener" aria-label="Instagram">
								<svg viewBox="0 0 448 512" width="20" height="20" fill="currentColor"><path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8z"/></svg>
							</a></li>
						<?php endif; ?>
						<?php if ( $social_linkedin ) : ?>
							<li><a href="<?php echo esc_url( $social_linkedin ); ?>" target="_blank" rel="noopener" aria-label="LinkedIn">
								<svg viewBox="0 0 448 512" width="20" height="20" fill="currentColor"><path d="M100.28 448H7.4V148.9h92.88zm-46.44-340a53.79 53.79 0 1 1 53.79-53.79A53.81 53.81 0 0 1 53.84 108zm394.16 340h-92.68V302.4c0-34.7-.7-79.3-48.32-79.3-48.32 0-55.74 37.7-55.74 76.7V448h-92.68V148.9h89V189h1.3a97.5 97.5 0 0 1 87.7-48.3c93.85 0 111.13 61.78 111.13 142.1z"/></svg>
							</a></li>
						<?php endif; ?>
						<?php if ( $social_youtube ) : ?>
							<li><a href="<?php echo esc_url( $social_youtube ); ?>" target="_blank" rel="noopener" aria-label="YouTube">
								<svg viewBox="0 0 576 512" width="20" height="20" fill="currentColor"><path d="M549.65 124.08a68.7 68.7 0 0 0-48.39-48.61C459.07 64 288 64 288 64S116.93 64 74.74 75.47a68.7 68.7 0 0 0-48.39 48.61C14.94 166.46 14.94 256 14.94 256s0 89.54 11.41 131.92a68.7 68.7 0 0 0 48.39 48.61C116.93 448 288 448 288 448s171.07 0 213.26-11.47a68.7 68.7 0 0 0 48.39-48.61C561.06 345.54 561.06 256 561.06 256s0-89.54-11.41-131.92zM232.45 336.43V175.57L361.85 256z"/></svg>
							</a></li>
						<?php endif; ?>
						<?php if ( $social_x ) : ?>
							<li><a href="<?php echo esc_url( $social_x ); ?>" target="_blank" rel="noopener" aria-label="X">
								<svg viewBox="0 0 512 512" width="20" height="20" fill="currentColor"><path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/></svg>
							</a></li>
						<?php endif; ?>
						<?php if ( $social_pinterest ) : ?>
							<li><a href="<?php echo esc_url( $social_pinterest ); ?>" target="_blank" rel="noopener" aria-label="Pinterest">
								<svg viewBox="0 0 496 512" width="20" height="20" fill="currentColor"><path d="M496 256c0 137-111 248-248 248-25.6 0-50.2-3.9-73.4-11.1 10.1-16.5 25.2-43.5 30.8-65 3-11.6 15.4-59 15.4-59 8.1 15.4 31.7 28.5 56.8 28.5 74.8 0 128.7-68.8 128.7-154.3 0-81.9-66.9-143.2-152.9-143.2-107 0-163.9 71.8-163.9 150.1 0 36.4 19.4 81.7 50.3 96.1 4.7 2.2 7.2 1.2 8.3-3.3.8-3.4 5-20.3 6.9-28.1.6-2.5.3-4.7-1.7-7.1-10.1-12.5-18.3-35.3-18.3-56.6 0-54.7 41.4-107.6 112-107.6 60.9 0 103.6 41.5 103.6 100.9 0 67.1-33.9 113.6-78 113.6-24.3 0-42.6-20.1-36.7-44.8 7-29.5 20.5-61.3 20.5-82.6 0-19-10.2-34.9-31.4-34.9-24.9 0-44.9 25.7-44.9 60.2 0 22 7.4 36.8 7.4 36.8s-24.5 103.8-29 123.2c-5 21.4-3 51.6-.9 71.2C65.4 450.9 0 361.1 0 256 0 119 111 8 248 8s248 111 248 248z"/></svg>
							</a></li>
						<?php endif; ?>
						<?php if ( $social_threads ) : ?>
							<li><a href="<?php echo esc_url( $social_threads ); ?>" target="_blank" rel="noopener" aria-label="Threads">
								<svg viewBox="0 0 448 512" width="20" height="20" fill="currentColor"><path d="M331.5 235.7c2.2 .9 4.2 1.9 6.2 2.8 29.2 14.1 50.6 35.2 61.8 61.4 15.7 36.5 17.2 95.8-30.3 143.2-36.2 36.2-80.3 52.5-142.6 53h-.3c-70.2-.5-124.1-24.1-160.4-70.2-32.3-40.9-49-98.1-49.5-169.9V256v-.2c.5-71.9 17.2-129 49.5-169.9C56.3 39.8 110.4 16.2 180.6 15.7h.3c70.3 .5 124.9 24 162.3 69.9 18.4 22.7 32.4 50.3 41.7 82.3l-40.6 12.1c-7.5-25.5-18.7-47.6-33.3-65.5-29.2-35.8-73.3-54.2-130.8-54.6-57 .5-100.1 18.8-128.2 54.4C84.7 124.6 70 169.5 69.5 250.7c0 70.5 13.7 119.3 41 153.7 28.1 35.6 71.2 53.9 128.2 54.4 51.4-.4 85.4-12.6 113.7-40.9 32.3-32.2 31.7-71.8 21.4-95.9-6.1-14.2-17.1-26-31.9-34.9-3.7 26.9-11.8 48.3-24.7 64.8-17.1 21.8-41.4 33.6-72.7 35.3-23.6 1.3-46.3-4.4-63.9-16.1-20.8-13.8-33-34.8-34.3-59.3-2.5-48.3 35.7-83 95.2-86.4 21.1-1.2 40.9-.3 59.2 2.8 0-2.5 .1-5 .1-7.6 0-26.3-7.7-41.1-23.5-49.8-12.5-6.9-29.5-10.6-49.9-10.7h-.5c-26.4 0-43.5 7.5-54.5 23.9l-34.8-23.9c19.5-29.2 53.7-44.5 92.9-44.5h.8c65.3 .4 104.5 41.2 110.9 112.4l-.2 .1z"/></svg>
							</a></li>
						<?php endif; ?>
					</ul>
				</div>

				<?php
				$menu_locations = [
					'footer_col_1' => __( 'Footer Column 1', 'rehab-parent' ),
					'footer_col_2' => __( 'Footer Column 2', 'rehab-parent' ),
					'footer_col_3' => __( 'Footer Column 3', 'rehab-parent' ),
				];
				foreach ( $menu_locations as $location => $label ) :
					if ( ! has_nav_menu( $location ) ) {
						continue;
					}
					?>
					<nav class="rehab-site-footer__col rehab-site-footer__col--menu" aria-label="<?php echo esc_attr( $label ); ?>">
						<?php
						wp_nav_menu(
							[
								'theme_location' => $location,
								'menu_class'     => 'rehab-site-footer__menu',
								'container'      => false,
								'fallback_cb'    => '__return_empty_string',
								'depth'          => 1,
							]
						);
						?>
					</nav>
				<?php endforeach; ?>

			</div>
		</div>
	</div>

	<div class="rehab-site-footer__bottom">
		<div class="rehab-container">
			<div class="rehab-site-footer__bottom-row">
				<p class="rehab-site-footer__copyright"><?php echo wp_kses_post( $copyright ); ?></p>
				<?php if ( has_nav_menu( 'footer_legal' ) ) : ?>
					<nav class="rehab-site-footer__legal" aria-label="<?php esc_attr_e( 'Legal', 'rehab-parent' ); ?>">
						<?php
						wp_nav_menu(
							[
								'theme_location' => 'footer_legal',
								'menu_class'     => 'rehab-site-footer__legal-menu',
								'container'      => false,
								'fallback_cb'    => '__return_empty_string',
								'depth'          => 1,
							]
						);
						?>
					</nav>
				<?php endif; ?>
			</div>
		</div>
	</div>
</footer>

<?php
// Sticky bottom-of-screen mobile CTA. Hidden on viewports ≥720px via CSS.
// The homepage ships its own scroll-triggered CTA (`.drt-mobile-sticky`), so
// this generic bar is suppressed there via CSS to avoid two stacked bars.
//
// One CTA — "Talk with admissions" — matching the desktop primary CTA. It opens
// a chooser (Call / form / WhatsApp) so people pick how to reach us instead of
// being bounced to a separate contact page. (REH-54)
$sticky_phone_tel = preg_replace( '/[^0-9+]/', '', get_theme_mod( 'rehab_phone_number', '' ) );
// Fallback for "Email / fill in a form" when the current page has no on-page
// assessment form — the chooser JS prefers an in-page #assessment form and only
// follows this URL otherwise. Override per-brand via the customizer.
$assessment_url  = get_theme_mod( 'rehab_assessment_url', '/contact-us/' );
$whatsapp_number = preg_replace( '/[^0-9]/', '', get_theme_mod( 'rehab_whatsapp_number', '66965823832' ) );

$icon_phone = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg>';
$icon_mail  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 6-10 7L2 6"/></svg>';
$icon_wa    = '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.6 6.3A8 8 0 0 0 4 12a7.8 7.8 0 0 0 1.1 4L4 20l4.1-1.1a8 8 0 0 0 9.5-12.6Zm-5.6 12.4a7.1 7.1 0 0 1-3.6-1l-.3-.1L5.7 18l.6-2.4-.2-.3a6.6 6.6 0 1 1 5.9 3.4Zm3.7-4.9c-.2 0-1.2-.6-1.4-.7s-.3 0-.5.2-.5.6-.6.7-.3.1-.5 0a5.4 5.4 0 0 1-2.7-2.4c-.2-.3 0-.5.1-.6l.4-.4.1-.3v-.3l-.7-1.7c-.2-.4-.3-.4-.5-.4h-.4a.8.8 0 0 0-.6.3 2.5 2.5 0 0 0-.7 1.8 4.3 4.3 0 0 0 .9 2.3 9.7 9.7 0 0 0 3.7 3.3 3.5 3.5 0 0 0 1.5.4 2.4 2.4 0 0 0 1.7-1 2 2 0 0 0 .1-1.2Z"/></svg>';
$icon_lock  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>';
$svg_kses = [ 'svg' => [ 'viewbox' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'aria-hidden' => true ], 'path' => [ 'd' => true ], 'rect' => [ 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true ] ];
?>
<div class="rehab-sticky-cta" aria-label="<?php esc_attr_e( 'Quick contact actions', 'rehab-parent' ); ?>">
	<button type="button" class="rehab-btn rehab-btn--luxury rehab-sticky-cta__open" data-rehab-sheet-open aria-haspopup="dialog" aria-expanded="false" aria-controls="rehab-contact-sheet"><?php esc_html_e( 'Talk with admissions', 'rehab-parent' ); ?></button>
</div>

<div class="rehab-contact-sheet" id="rehab-contact-sheet" hidden>
	<div class="rehab-contact-sheet__backdrop" data-rehab-sheet-close></div>
	<div class="rehab-contact-sheet__panel" role="dialog" aria-modal="true" aria-labelledby="rehab-contact-sheet__title">
		<button type="button" class="rehab-contact-sheet__close" data-rehab-sheet-close aria-label="<?php esc_attr_e( 'Close', 'rehab-parent' ); ?>">&times;</button>
		<h2 class="rehab-contact-sheet__title" id="rehab-contact-sheet__title"><?php esc_html_e( 'How would you like to connect?', 'rehab-parent' ); ?></h2>
		<div class="rehab-contact-sheet__options">
			<?php if ( $sticky_phone_tel ) : ?>
			<a class="rehab-contact-sheet__option" href="tel:<?php echo esc_attr( $sticky_phone_tel ); ?>">
				<span class="rehab-contact-sheet__icon"><?php echo wp_kses( $icon_phone, $svg_kses ); ?></span>
				<span class="rehab-contact-sheet__label"><span class="rehab-contact-sheet__label-main"><?php esc_html_e( 'Call now', 'rehab-parent' ); ?></span><span class="rehab-contact-sheet__label-sub"><?php esc_html_e( 'Speak with us directly', 'rehab-parent' ); ?></span></span>
			</a>
			<?php endif; ?>
			<a class="rehab-contact-sheet__option" href="<?php echo esc_url( $assessment_url ); ?>" data-rehab-sheet-form>
				<span class="rehab-contact-sheet__icon"><?php echo wp_kses( $icon_mail, $svg_kses ); ?></span>
				<span class="rehab-contact-sheet__label"><span class="rehab-contact-sheet__label-main"><?php esc_html_e( 'Email / fill in a form', 'rehab-parent' ); ?></span><span class="rehab-contact-sheet__label-sub"><?php esc_html_e( 'Request a free assessment', 'rehab-parent' ); ?></span></span>
			</a>
			<?php if ( $whatsapp_number ) : ?>
			<a class="rehab-contact-sheet__option" href="https://wa.me/<?php echo esc_attr( $whatsapp_number ); ?>" target="_blank" rel="noopener">
				<span class="rehab-contact-sheet__icon"><?php echo wp_kses( $icon_wa, $svg_kses ); ?></span>
				<span class="rehab-contact-sheet__label"><span class="rehab-contact-sheet__label-main"><?php esc_html_e( 'WhatsApp', 'rehab-parent' ); ?></span><span class="rehab-contact-sheet__label-sub"><?php esc_html_e( 'Message us anytime', 'rehab-parent' ); ?></span></span>
			</a>
			<?php endif; ?>
		</div>
		<p class="rehab-contact-sheet__assurance"><?php echo wp_kses( $icon_lock, $svg_kses ); ?><?php esc_html_e( 'Your conversation is 100% confidential and secure.', 'rehab-parent' ); ?></p>
	</div>
</div>

<?php wp_footer(); ?>
</body>
</html>
