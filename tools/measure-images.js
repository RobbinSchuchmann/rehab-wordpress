const { chromium } = require( 'playwright' );

const URLS = [
	'http://localhost:8081/',
	'http://localhost:8081/treatments/cocaine-addiction/',
	'http://localhost:8081/treatments/ice-addiction-treatment-rehab-thailand/',
	'http://localhost:8081/treatments/all-treatments/',
	'http://localhost:8081/why-us/',
];

( async () => {
	const browser = await chromium.launch( { headless: true } );
	const ctx = await browser.newContext( { viewport: { width: 1400, height: 1000 } } );
	const page = await ctx.newPage();
	const seen = {};
	for ( const url of URLS ) {
		await page.goto( url, { waitUntil: 'load' } );
		await page.waitForTimeout( 800 );
		await page.evaluate( async () => {
			const distance = 800;
			const total = document.body.scrollHeight;
			for ( let y = 0; y < total; y += distance ) {
				window.scrollTo( 0, y );
				await new Promise( ( r ) => setTimeout( r, 100 ) );
			}
		} );
		await page.waitForTimeout( 800 );
		const dims = await page.evaluate( () =>
			[ ...document.images ].map( ( i ) => ( {
				src: i.src,
				w: i.naturalWidth,
				h: i.naturalHeight,
			} ) )
		);
		for ( const d of dims ) {
			if ( ! d.w || ! d.h ) continue;
			const key = d.src.replace( /^https?:\/\/[^/]+/, '' );
			seen[ key ] = `${ d.w }x${ d.h }`;
		}
	}
	console.log( JSON.stringify( seen, null, 2 ) );
	await browser.close();
} )().catch( ( e ) => { console.error( e ); process.exit( 1 ); } );
