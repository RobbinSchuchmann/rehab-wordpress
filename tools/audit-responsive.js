// Responsive audit across widths. Flags three classes of layout bug:
//   1. horizontal overflow (element extends past the viewport right edge)
//   2. overlap of in-flow interactive/text elements (e.g. nav links sliding
//      under the logo) — normal-flow boxes should never overlap, so this is a
//      high-signal check; positioned elements are excluded (they overlap by
//      design: dropdowns, lightboxes, sticky bars).
//   3. zero-area buttons (a btn-class element rendered with no padding/height,
//      e.g. a CTA whose variant set colours but no padding).
// Usage: node audit-responsive.js [urls-file] [out-json]   (WIDTHS env overrides)
const { chromium } = require( 'playwright' );
const fs = require( 'fs' );

const URLS_FILE = process.argv[ 2 ] || '/tmp/audit-urls.txt';
const OUT = process.argv[ 3 ] || '/tmp/responsive-report.json';

// Widths to test: small phone, iPhone, tablet portrait, small laptop, desktop, 5K.
// Override with WIDTHS env (comma-separated) for faster full-site sweeps.
const WIDTHS = ( process.env.WIDTHS ? process.env.WIDTHS.split( ',' ).map( Number ) : [ 360, 390, 768, 1024, 1440, 2560 ] );

const urls = fs.readFileSync( URLS_FILE, 'utf8' ).split( '\n' ).map( s => s.trim() ).filter( Boolean );

( async () => {
	const browser = await chromium.launch( { headless: true } );
	const report = [];

	for ( const url of urls ) {
		const pageResult = { url, widths: {} };
		for ( const width of WIDTHS ) {
			const ctx = await browser.newContext( {
				viewport: { width, height: 900 },
				deviceScaleFactor: 1,
				isMobile: width <= 414,
			} );
			const page = await ctx.newPage();
			let res;
			try {
				await page.goto( url, { waitUntil: 'domcontentloaded', timeout: 30000 } );
				await page.waitForTimeout( 600 );
				// Trigger lazy assets.
				await page.evaluate( async () => {
					const total = document.body.scrollHeight;
					for ( let y = 0; y < total; y += 1000 ) { window.scrollTo( 0, y ); await new Promise( r => setTimeout( r, 60 ) ); }
					window.scrollTo( 0, 0 );
				} );
				await page.waitForTimeout( 300 );

				// Optional what-if hook: inject CSS before measuring (e.g. to
				// reproduce a regression). INJECT_CSS='<css>' in the environment.
				if ( process.env.INJECT_CSS ) {
					await page.addStyleTag( { content: process.env.INJECT_CSS } );
					await page.waitForTimeout( 200 );
				}

				res = await page.evaluate( ( vw ) => {
					const visible = ( el ) => {
						try { return ! el.checkVisibility || el.checkVisibility( { checkOpacity: true, checkVisibilityCSS: true } ); }
						catch ( e ) { return true; }
					};
					const first = ( el ) => ( el.className && el.className.toString ? el.className.toString().split( ' ' )[ 0 ] : '' );
					const label = ( el ) => el.tagName.toLowerCase() + ( first( el ) ? '.' + first( el ) : '' );

					// --- 1. Horizontal overflow ---
					const docW = document.documentElement.scrollWidth;
					const overflow = docW - vw;
					const offenders = [];
					if ( overflow > 1 ) {
						for ( const el of document.querySelectorAll( '*' ) ) {
							const r = el.getBoundingClientRect();
							if ( r.right > vw + 1 && r.width > 0 && r.left >= -1 ) {
								if ( getComputedStyle( el ).position === 'fixed' ) continue;
								offenders.push( { tag: el.tagName.toLowerCase(), cls: ( el.className && el.className.toString ? el.className.toString() : '' ).slice( 0, 80 ), right: Math.round( r.right ), width: Math.round( r.width ), overshoot: Math.round( r.right - vw ) } );
							}
						}
						offenders.sort( ( a, b ) => b.overshoot - a.overshoot );
					}

					// --- 2. Overlap among in-flow interactive/heading/brand elements ---
					// Body-copy links inside prose are excluded — their overlaps aren't
					// the bug class and they add O(n^2) noise on article pages.
					// Nearest positioned ancestor = the element's overlay/stacking
					// context. Two boxes that overlap only matter if they live in the
					// SAME context: nav links sliding under the logo (both inside the
					// fixed navbar) is a bug; a fixed sticky-CTA bar floating over page
					// copy (different contexts) is by design.
					const posCtx = ( el ) => {
						let p = el.parentElement;
						while ( p ) {
							const pos = getComputedStyle( p ).position;
							if ( pos === 'fixed' || pos === 'absolute' || pos === 'sticky' ) return p;
							p = p.parentElement;
						}
						return null;
					};
					const cand = [ ...document.querySelectorAll( 'a, button, h1, h2, h3, h4, [class*="rehab-btn"], [class*="drt-btn"], [class*="navbar__brand"], [class*="logo"]' ) ]
						.filter( ( el ) => {
							// Exclude body-copy links (prose / article / index / footer) — their
							// overlaps aren't the bug class and add O(n^2) noise. Keep nav links,
							// which live in a <ul><li><a> but are NOT body copy.
							if ( el.tagName === 'A' && el.closest( 'p, .rehab-prose__inner, .rehab-article__body, .rehab-articles-index, footer, .rehab-footer' ) ) return false;
							if ( ! visible( el ) ) return false;
							const pos = getComputedStyle( el ).position;
							if ( pos !== 'static' && pos !== 'relative' ) return false;
							const r = el.getBoundingClientRect();
							return r.width > 4 && r.height > 4 && r.right > 0 && r.left < vw;
						} )
						.slice( 0, 400 )
						.map( ( el ) => ( { el, r: el.getBoundingClientRect(), ctx: posCtx( el ) } ) );
					const overlaps = [];
					for ( let i = 0; i < cand.length; i++ ) {
						for ( let j = i + 1; j < cand.length; j++ ) {
							const A = cand[ i ], B = cand[ j ];
							if ( A.ctx !== B.ctx ) continue; // different overlay/stacking context
							if ( A.el.contains( B.el ) || B.el.contains( A.el ) ) continue;
							const ox = Math.min( A.r.right, B.r.right ) - Math.max( A.r.left, B.r.left );
							const oy = Math.min( A.r.bottom, B.r.bottom ) - Math.max( A.r.top, B.r.top );
							if ( ox <= 1 || oy <= 1 ) continue;
							const area = ox * oy;
							const minArea = Math.min( A.r.width * A.r.height, B.r.width * B.r.height );
							// Partial overlaps only: >=90% of the smaller box means one sits
							// inside the other's area (an overlay link under buttons), which
							// is layering, not a collision.
							if ( area < 120 || area < minArea * 0.2 || area > minArea * 0.9 ) continue;
							overlaps.push( { a: label( A.el ), aText: ( A.el.textContent || '' ).trim().slice( 0, 24 ), b: label( B.el ), bText: ( B.el.textContent || '' ).trim().slice( 0, 24 ), area: Math.round( area ) } );
						}
					}
					overlaps.sort( ( a, b ) => b.area - a.area );

					// --- 3. Zero-area / padding-less buttons ---
					const seen = new Set();
					const badButtons = [];
					for ( const el of document.querySelectorAll( '[class*="btn"], button' ) ) {
						if ( ! visible( el ) ) continue;
						const txt = ( el.textContent || '' ).trim();
						if ( txt.length < 3 ) continue; // icon-only buttons
						const r = el.getBoundingClientRect();
						if ( r.width < 1 || r.height < 1 ) continue;
						const s = getComputedStyle( el );
						const vpad = parseFloat( s.paddingTop ) + parseFloat( s.paddingBottom );
						const hasFill = s.backgroundColor && s.backgroundColor !== 'rgba(0, 0, 0, 0)' && s.backgroundColor !== 'transparent';
						const hasBorder = parseFloat( s.borderTopWidth ) > 0 || parseFloat( s.borderBottomWidth ) > 0;
						// Only flag elements that LOOK like a button box (fill or border).
						// Plain text-style toggles ("Read more") have neither and are
						// meant to be short — not broken.
						if ( ( hasFill || hasBorder ) && ( r.height < 24 || vpad < 6 ) ) {
							const cls = ( el.className && el.className.toString ? el.className.toString() : '' ).slice( 0, 50 );
							const key = cls + '|' + Math.round( r.height );
							if ( seen.has( key ) ) continue;
							seen.add( key );
							badButtons.push( { cls, text: txt.slice( 0, 24 ), h: Math.round( r.height ), vpad: Math.round( vpad ) } );
						}
					}

					return { docW, vw, overflow, offenders: offenders.slice( 0, 8 ), overlaps: overlaps.slice( 0, 6 ), badButtons: badButtons.slice( 0, 6 ) };
				}, width );
			} catch ( e ) {
				res = { error: e.message.slice( 0, 120 ) };
			}
			pageResult.widths[ width ] = res;
			await ctx.close();
		}
		// Console line per page.
		const at = ( pred ) => WIDTHS.filter( ( w ) => pageResult.widths[ w ] && pred( pageResult.widths[ w ] ) );
		const ovf = at( ( r ) => r.overflow > 1 ).map( ( w ) => `${ w }:+${ pageResult.widths[ w ].overflow }` );
		const olp = at( ( r ) => r.overlaps && r.overlaps.length ).map( ( w ) => `${ w }(${ pageResult.widths[ w ].overlaps.length })` );
		const btn = at( ( r ) => r.badButtons && r.badButtons.length ).map( ( w ) => `${ w }(${ pageResult.widths[ w ].badButtons.length })` );
		const errs = at( ( r ) => r.error );
		const bad = ovf.length || olp.length || btn.length || errs.length;
		console.log( ( bad ? '⚠ ' : '✓ ' ) + url.replace( 'http://localhost:8081', '' ) +
			( ovf.length ? '  OVERFLOW ' + ovf.join( ' ' ) : '' ) +
			( olp.length ? '  OVERLAP ' + olp.join( ' ' ) : '' ) +
			( btn.length ? '  BADBTN ' + btn.join( ' ' ) : '' ) +
			( errs.length ? '  ERR@' + errs.join( ',' ) : '' ) );
		report.push( pageResult );
	}

	fs.writeFileSync( OUT, JSON.stringify( report, null, 2 ) );
	console.log( '\nSaved', OUT );
	await browser.close();
} )().catch( e => { console.error( e ); process.exit( 1 ); } );
