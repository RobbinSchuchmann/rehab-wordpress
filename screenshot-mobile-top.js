const { chromium } = require( 'playwright' );

const URL = process.argv[ 2 ] || 'http://localhost:8081/';
const OUT = process.argv[ 3 ] || '/tmp/mobile-top.png';

( async () => {
	const browser = await chromium.launch( { headless: true } );
	const ctx = await browser.newContext( {
		viewport: { width: 390, height: 844 },
		isMobile: true,
		deviceScaleFactor: 2,
	} );
	const page = await ctx.newPage();
	await page.goto( URL, { waitUntil: 'load' } );
	await page.waitForTimeout( 1500 );
	await page.screenshot( { path: OUT, fullPage: false } );
	console.log( 'Saved', OUT );
	await browser.close();
} )().catch( ( e ) => { console.error( e ); process.exit( 1 ); } );
