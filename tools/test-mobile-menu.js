const { chromium } = require( 'playwright' );

( async () => {
	const browser = await chromium.launch( { headless: true } );
	const ctx = await browser.newContext( {
		viewport: { width: 390, height: 844 },
		isMobile: true,
		deviceScaleFactor: 2,
	} );
	const page = await ctx.newPage();
	await page.goto( 'http://localhost:8081/', { waitUntil: 'domcontentloaded' } );
	await page.waitForTimeout( 3000 );

	// Tap hamburger menu
	await page.click( '[data-rehab-menu-toggle]' );
	await page.waitForTimeout( 1500 );

	await page.screenshot( { path: '/tmp/mobile-menu-open.png', fullPage: false } );
	console.log( 'Saved /tmp/mobile-menu-open.png' );

	await browser.close();
} )().catch( ( e ) => { console.error( e ); process.exit( 1 ); } );
