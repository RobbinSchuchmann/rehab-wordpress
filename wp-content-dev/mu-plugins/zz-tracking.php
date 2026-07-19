<?php
/**
 * Analytics & marketing tracking injection (REH-15).
 *
 * The v3 migration dropped the old site's "Insert Headers & Footers" plugin,
 * which is where every analytics / marketing script lived. Nothing restored it,
 * so the new stack ships with NO tracking. This mu-plugin puts it back — but in
 * a way that is safe for the shared codebase and the public dev/staging boxes.
 *
 * Two guards make it inert unless deliberately switched on for a live site:
 *
 *   1. Option-gated. Nothing hardcoded. Every ID is read from a per-site option
 *      (mirroring zz-mail-from.php). Unset option = that snippet is skipped, so
 *      a fresh checkout / a brand with no tracking configured injects nothing.
 *
 *   2. Host-gated. Even if the options are set, injection is refused on any
 *      non-production host — localhost, the raw dev IP, and *.cloudwaysapps.com
 *      staging URLs. Only a real brand domain ever emits tracking. This is
 *      defense-in-depth: the options should only be set on the prod server, but
 *      if they leak onto staging the box still stays clean.
 *
 * Enable on the production server via WP-CLI (Diamond values shown):
 *
 *   # GTM is the umbrella container — GA4 (G-4443JC04QD) fires THROUGH it, so
 *   # GTM alone restores Google Analytics. Do not also set rehab_ga4_id unless
 *   # the container does NOT already contain the GA4 tag (avoids double-counting).
 *   wp option update rehab_gtm_id 'GTM-WBCJPZ4'
 *
 *   # Internet Dominators lead pixel — CONFIRM the subscription (account 76381674)
 *   # is still active before enabling, else it is a dead / billable script.
 *   wp option update rehab_lead_pixel_account '76381674'
 *
 *   # Elfsight WhatsApp chat widget.
 *   wp option update rehab_whatsapp_widget_id '5edf62f4-6471-425b-948e-327b551420d2'
 *
 *   # Google Search Console verification meta. Skip if GSC is verified via DNS
 *   # or already emitted by RankMath (Rank Math > General > Webmaster Tools).
 *   wp option update rehab_gsc_verification 'vnp3yerkU4JpzIWYQwWJ55Y8Xh7sO-SSK5jE1m3uwIw'
 *
 *   # OPTIONAL direct GA4 gtag — only if GTM does NOT carry the GA4 tag.
 *   # wp option update rehab_ga4_id 'G-4443JC04QD'
 *
 * @package RehabParent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * True only on a real production host. Refuses localhost, the raw dev IP, any
 * bare-IPv4 host, and *.cloudwaysapps.com staging URLs. Never fires in wp-admin,
 * the REST API, cron, or CLI.
 *
 * @return bool
 */
function rehab_tracking_is_production() {
	if ( is_admin() || wp_doing_ajax() || wp_doing_cron() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || 'cli' === php_sapi_name() ) {
		return false;
	}

	$host = isset( $_SERVER['HTTP_HOST'] ) ? strtolower( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
	$host = preg_replace( '/:\d+$/', '', $host ); // strip any :port
	if ( '' === $host ) {
		return false;
	}

	// Non-production hosts: localhost, loopback, staging.
	if ( 'localhost' === $host || false !== strpos( $host, 'cloudwaysapps.com' ) ) {
		return false;
	}

	// Any bare IPv4 address (the dev box is reached by IP) is never production.
	if ( preg_match( '/^\d{1,3}(\.\d{1,3}){3}$/', $host ) ) {
		return false;
	}

	return true;
}

/**
 * A validated tracking option, or '' if unset / malformed.
 *
 * @param string $option  Option name.
 * @param string $pattern Anchored regex the value must match.
 * @return string
 */
function rehab_tracking_option( $option, $pattern ) {
	$value = trim( (string) get_option( $option, '' ) );
	return ( '' !== $value && preg_match( $pattern, $value ) ) ? $value : '';
}

/**
 * <head>: GTM container, optional direct GA4 gtag, GSC verification meta,
 * and the Internet Dominators lead pixel.
 */
add_action( 'wp_head', function () {
	if ( ! rehab_tracking_is_production() ) {
		return;
	}

	$gtm   = rehab_tracking_option( 'rehab_gtm_id', '/^GTM-[A-Z0-9]+$/' );
	$ga4   = rehab_tracking_option( 'rehab_ga4_id', '/^G-[A-Z0-9]+$/' );
	$gsc   = rehab_tracking_option( 'rehab_gsc_verification', '/^[A-Za-z0-9_-]+$/' );
	$pixel = rehab_tracking_option( 'rehab_lead_pixel_account', '/^[0-9]+$/' );

	// Google Search Console verification.
	if ( $gsc ) {
		printf( '<meta name="google-site-verification" content="%s" />' . "\n", esc_attr( $gsc ) );
	}

	// Google Tag Manager (umbrella container — GA4 typically fires through this).
	if ( $gtm ) {
		$id = esc_js( $gtm );
		echo "<!-- Google Tag Manager -->\n";
		echo "<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':\n";
		echo "new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],\n";
		echo "j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=\n";
		echo "'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);\n";
		echo "})(window,document,'script','dataLayer','{$id}');</script>\n";
		echo "<!-- End Google Tag Manager -->\n";
	}

	// Optional direct GA4 gtag — only when a container is NOT carrying GA4.
	if ( $ga4 ) {
		$id = esc_js( $ga4 );
		printf( '<script async src="https://www.googletagmanager.com/gtag/js?id=%s"></script>' . "\n", esc_attr( $ga4 ) );
		echo "<script>\n";
		echo "window.dataLayer = window.dataLayer || [];\n";
		echo "function gtag(){dataLayer.push(arguments);}\n";
		echo "gtag('js', new Date());\n";
		echo "gtag('config', '{$id}');\n";
		echo "</script>\n";
	}

	// Internet Dominators lead pixel (PxGrabber). Label = "account|<page url>".
	if ( $pixel ) {
		$account = esc_js( $pixel );
		echo "<!-- Lead pixel -->\n";
		echo "<script>(function(doc, tag, id){var js = doc.getElementsByTagName(tag)[0];if (doc.getElementById(id)) {return;}js = doc.createElement(tag); js.id = id;js.src = \"https://leads.internetdominators.app/px.min.js\";js.type = \"text/javascript\";doc.head.appendChild(js);js.onload = function() {pxfired();};}(document, 'script', 'px-grabber'));function pxfired() {PxGrabber.setOptions({Label: \"{$account}|\" + window.location.href,});PxGrabber.render();};</script>\n";
	}
}, 1 );

/**
 * Immediately after <body>: GTM <noscript> fallback iframe.
 */
add_action( 'wp_body_open', function () {
	if ( ! rehab_tracking_is_production() ) {
		return;
	}
	$gtm = rehab_tracking_option( 'rehab_gtm_id', '/^GTM-[A-Z0-9]+$/' );
	if ( ! $gtm ) {
		return;
	}
	printf(
		'<!-- Google Tag Manager (noscript) --><noscript><iframe src="https://www.googletagmanager.com/ns.html?id=%s" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>' . "\n",
		esc_attr( $gtm )
	);
} );

/**
 * Footer: Elfsight WhatsApp chat widget.
 */
add_action( 'wp_footer', function () {
	if ( ! rehab_tracking_is_production() ) {
		return;
	}
	$widget = rehab_tracking_option( 'rehab_whatsapp_widget_id', '/^[a-f0-9-]+$/' );
	if ( ! $widget ) {
		return;
	}
	echo "<!-- Elfsight WhatsApp Chat -->\n";
	echo '<script src="https://elfsightcdn.com/platform.js" async></script>' . "\n";
	printf( '<div class="elfsight-app-%s" data-elfsight-app-lazy></div>' . "\n", esc_attr( $widget ) );
} );
