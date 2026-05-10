const { chromium } = require( 'playwright' );

const URL = process.argv[ 2 ] || 'http://localhost:8081/';
const OUT = process.argv[ 3 ] || '/tmp/top.png';

( async () => {
	const browser = await chromium.launch( { headless: true } );
	const ctx = await browser.newContext( { viewport: { width: 1400, height: 800 } } );
	const page = await ctx.newPage();
	await page.goto( URL, { waitUntil: 'domcontentloaded' } );
	await page.waitForTimeout( 4000 );
	await page.screenshot( { path: OUT, fullPage: false } );
	console.log( 'Saved', OUT );
	await browser.close();
} )().catch( ( e ) => { console.error( e ); process.exit( 1 ); } );
