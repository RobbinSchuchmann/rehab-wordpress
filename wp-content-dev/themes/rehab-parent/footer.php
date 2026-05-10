<?php
/**
 * Site footer.
 *
 * @package RehabParent
 */

$brand_name      = get_bloginfo( 'name' );
$address         = get_theme_mod( 'rehab_footer_address', "8, Moo 14, Soi Mon Mai Hin Lek Fai\nHua Hin District, Prachuap Khiri Khan\nThailand 77110" );
$phone_text      = get_theme_mod( 'rehab_phone_display', '+66 96 582 3832' );
$phone_number    = get_theme_mod( 'rehab_phone_number', '+66965823832' );
$phone_tel       = preg_replace( '/[^0-9+]/', '', $phone_number );
$international   = get_theme_mod( 'rehab_footer_intl_phones', '' ); // multiline "Label|+number"
$copyright       = get_theme_mod( 'rehab_footer_copyright', sprintf( '&copy; %d %s', gmdate( 'Y' ), $brand_name ) );
$social_facebook = get_theme_mod( 'rehab_social_facebook', '' );
$social_instagram= get_theme_mod( 'rehab_social_instagram', '' );
$social_linkedin = get_theme_mod( 'rehab_social_linkedin', '' );
$social_youtube  = get_theme_mod( 'rehab_social_youtube', '' );

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

<?php wp_footer(); ?>
</body>
</html>
