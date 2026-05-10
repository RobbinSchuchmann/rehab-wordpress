const { chromium } = require( 'playwright' );

const URL = process.argv[ 2 ] || 'http://localhost:8081/';

( async () => {
	const browser = await chromium.launch( { headless: true } );
	const ctx = await browser.newContext( { viewport: { width: 1400, height: 900 } } );
	const page = await ctx.newPage();
	await page.goto( URL, { waitUntil: 'load' } );
	await page.waitForTimeout( 1500 );
	const tokens = await page.evaluate( () => {
		const root = getComputedStyle( document.documentElement );
		return {
			'--rehab-sage':       root.getPropertyValue( '--rehab-sage' ).trim(),
			'--rehab-sage-dark':  root.getPropertyValue( '--rehab-sage-dark' ).trim(),
			'--rehab-color-cream': root.getPropertyValue( '--rehab-color-cream' ).trim(),
		};
	} );
	const btn = await page.evaluate( () => {
		const el = document.querySelector( '.rehab-btn--luxury' );
		if ( ! el ) return null;
		const cs = getComputedStyle( el );
		return { bg: cs.backgroundColor };
	} );
	console.log( JSON.stringify( { tokens, btn }, null, 2 ) );
	await browser.close();
} )().catch( ( e ) => { console.error( e ); process.exit( 1 ); } );
