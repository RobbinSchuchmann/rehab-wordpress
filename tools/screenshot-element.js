const { chromium } = require( 'playwright' );

const URL = process.argv[ 2 ] || 'http://localhost:8081/';
const SELECTOR = process.argv[ 3 ] || '.rehab-hero';
const OUT = process.argv[ 4 ] || '/tmp/element.png';

( async () => {
	const browser = await chromium.launch( { headless: true } );
	const ctx = await browser.newContext( { viewport: { width: 1400, height: 900 } } );
	const page = await ctx.newPage();
	await page.goto( URL, { waitUntil: 'load' } );
	await page.waitForTimeout( 1500 );
	const el = await page.$( SELECTOR );
	if ( ! el ) {
		console.error( 'Selector not found:', SELECTOR );
		process.exit( 2 );
	}
	await el.screenshot( { path: OUT } );
	console.log( 'Saved', OUT );
	await browser.close();
} )().catch( ( e ) => { console.error( e ); process.exit( 1 ); } );
