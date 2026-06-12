// Responsive audit: detects horizontal overflow + offending elements across widths.
// Usage: node audit-responsive.js [urls-file] [out-json]
// urls-file: newline-separated URLs (default /tmp/audit-urls.txt)
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

				res = await page.evaluate( ( vw ) => {
					const docW = document.documentElement.scrollWidth;
					const overflow = docW - vw;
					const offenders = [];
					if ( overflow > 1 ) {
						const all = document.querySelectorAll( '*' );
						for ( const el of all ) {
							const r = el.getBoundingClientRect();
							// Element extends past the right edge of the viewport.
							if ( r.right > vw + 1 && r.width > 0 && r.left >= -1 ) {
								const style = getComputedStyle( el );
								if ( style.position === 'fixed' ) continue;
								offenders.push( {
									tag: el.tagName.toLowerCase(),
									cls: ( el.className && el.className.toString ? el.className.toString() : '' ).slice( 0, 80 ),
									right: Math.round( r.right ),
									width: Math.round( r.width ),
									overshoot: Math.round( r.right - vw ),
								} );
							}
						}
					}
					// Dedup-ish: keep the widest few offenders, sorted by overshoot.
					offenders.sort( ( a, b ) => b.overshoot - a.overshoot );
					return { docW, vw, overflow, offenders: offenders.slice( 0, 8 ) };
				}, width );
			} catch ( e ) {
				res = { error: e.message.slice( 0, 120 ) };
			}
			pageResult.widths[ width ] = res;
			await ctx.close();
		}
		// Console line per page.
		const flags = WIDTHS.filter( w => pageResult.widths[ w ] && pageResult.widths[ w ].overflow > 1 )
			.map( w => `${w}:+${pageResult.widths[ w ].overflow}` );
		const errs = WIDTHS.filter( w => pageResult.widths[ w ] && pageResult.widths[ w ].error );
		console.log( ( flags.length || errs.length ? '⚠ ' : '✓ ' ) + url.replace( 'http://localhost:8081', '' ) +
			( flags.length ? '  OVERFLOW ' + flags.join( ' ' ) : '' ) +
			( errs.length ? '  ERR@' + errs.join( ',' ) : '' ) );
		report.push( pageResult );
	}

	fs.writeFileSync( OUT, JSON.stringify( report, null, 2 ) );
	console.log( '\nSaved', OUT );
	await browser.close();
} )().catch( e => { console.error( e ); process.exit( 1 ); } );
