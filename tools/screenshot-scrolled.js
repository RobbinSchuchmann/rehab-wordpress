const { chromium } = require( 'playwright' );

const URL = process.argv[ 2 ] || 'http://localhost:8081/';
const SCROLL = parseInt( process.argv[ 3 ] || '500', 10 );
const OUT = process.argv[ 4 ] || '/tmp/scrolled.png';
const W = parseInt( process.argv[ 5 ] || '1400', 10 );
const H = parseInt( process.argv[ 6 ] || '900', 10 );

( async () => {
	const browser = await chromium.launch( { headless: true } );
	const ctx = await browser.newContext( { viewport: { width: W, height: H } } );
	const page = await ctx.newPage();
	await page.goto( URL, { waitUntil: 'load' } );
	await page.waitForTimeout( 1500 );
	await page.evaluate( ( y ) => window.scrollTo( 0, y ), SCROLL );
	await page.waitForTimeout( 1000 );
	await page.screenshot( { path: OUT, fullPage: false } );
	console.log( 'Saved', OUT );
	await browser.close();
} )().catch( ( e ) => { console.error( e ); process.exit( 1 ); } );
