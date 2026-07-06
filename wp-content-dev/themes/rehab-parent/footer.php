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
						<?php foreach ( rehab_social_links() as $rehab_social ) : ?>
							<li><a href="<?php echo esc_url( $rehab_social['url'] ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $rehab_social['label'] ); ?>">
								<?php echo $rehab_social['icon']; // phpcs:ignore WordPress.Security.EscapeOutput -- static brand SVGs. ?>
							</a></li>
						<?php endforeach; ?>
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
