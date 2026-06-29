const { chromium } = require( 'playwright' );

const URL = process.argv[ 2 ] || 'http://localhost:8081/';
const SELECTOR = process.argv[ 3 ] || '.rehab-article-feed';
const OUT = process.argv[ 4 ] || '/tmp/section.png';

( async () => {
	const browser = await chromium.launch( { headless: true } );
	const ctx = await browser.newContext( { viewport: { width: 1400, height: 900 } } );
	const page = await ctx.newPage();
	await page.goto( URL, { waitUntil: 'load' } );
	await page.waitForTimeout( 1500 );
	await page.evaluate( ( s ) => {
		const el = document.querySelector( s );
		if ( el ) el.scrollIntoView( { block: 'start' } );
	}, SELECTOR );
	await page.waitForTimeout( 2000 );
	await page.screenshot( { path: OUT, fullPage: false } );
	console.log( 'Saved', OUT );
	await browser.close();
} )().catch( ( e ) => { console.error( e ); process.exit( 1 ); } );
