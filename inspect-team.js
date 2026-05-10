const { chromium } = require( 'playwright' );

( async () => {
	const browser = await chromium.launch( { headless: true } );
	const ctx = await browser.newContext( { viewport: { width: 1400, height: 1000 } } );
	const page = await ctx.newPage();
	await page.goto( 'http://localhost:8081/', { waitUntil: 'networkidle', timeout: 30000 } );
	await page.waitForTimeout( 5000 );

	const info = await page.evaluate( () => {
		const photos = document.querySelectorAll( '.rehab-team-member__photo' );
		return Array.from( photos ).slice( 0, 4 ).map( ( img ) => ( {
			src: img.currentSrc || img.src,
			complete: img.complete,
			naturalW: img.naturalWidth,
			naturalH: img.naturalHeight,
			displayW: img.getBoundingClientRect().width,
			displayH: img.getBoundingClientRect().height,
		} ) );
	} );
	console.log( JSON.stringify( info, null, 2 ) );

	// Scroll team into view & screenshot
	await page.evaluate( () => {
		const el = document.querySelector( '.rehab-team' );
		if ( el ) el.scrollIntoView( { block: 'start' } );
	} );
	await page.waitForTimeout( 2000 );
	await page.screenshot( { path: '/tmp/team-section.png', fullPage: false } );
	console.log( 'Saved /tmp/team-section.png' );

	await browser.close();
} )().catch( e => { console.error( e ); process.exit( 1 ); } );
