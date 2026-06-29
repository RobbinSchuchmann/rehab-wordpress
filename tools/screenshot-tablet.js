const { chromium } = require( 'playwright' );

const URL = process.argv[ 2 ] || 'http://localhost:8081/';
const OUT = process.argv[ 3 ] || '/tmp/tablet.png';

( async () => {
	const browser = await chromium.launch( { headless: true } );
	const ctx = await browser.newContext( {
		viewport: { width: 768, height: 1024 },
		isMobile: false,
		deviceScaleFactor: 2,
	} );
	const page = await ctx.newPage();
	await page.goto( URL, { waitUntil: 'load' } );
	await page.waitForTimeout( 1500 );
	await page.evaluate( async () => {
		const distance = 600;
		const total = document.body.scrollHeight;
		for ( let y = 0; y < total; y += distance ) {
			window.scrollTo( 0, y );
			await new Promise( ( r ) => setTimeout( r, 100 ) );
		}
		window.scrollTo( 0, 0 );
	} );
	await page.waitForTimeout( 1500 );
	await page.screenshot( { path: OUT, fullPage: true } );
	console.log( 'Saved', OUT );
	await browser.close();
} )().catch( ( e ) => { console.error( e ); process.exit( 1 ); } );
