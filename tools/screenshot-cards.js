const { chromium } = require( 'playwright' );

( async () => {
	const browser = await chromium.launch( { headless: true } );
	const ctx = await browser.newContext( { viewport: { width: 1400, height: 1200 } } );
	const page = await ctx.newPage();
	await page.goto( 'http://localhost:8081/wp-login.php' );
	await page.fill( '#user_login', 'devadmin' );
	await page.fill( '#user_pass', 'dev123!' );
	await page.click( '#wp-submit' );
	await page.waitForLoadState( 'domcontentloaded' );
	await page.goto( 'http://localhost:8081/wp-admin/post.php?post=12242&action=edit', { waitUntil: 'domcontentloaded' } );
	await page.waitForTimeout( 18000 );

	const editorFrame = page.frames().find( ( f ) => f.url().startsWith( 'blob:' ) );
	if ( ! editorFrame ) { console.log( 'no iframe' ); await browser.close(); return; }

	await editorFrame.evaluate( () => {
		const el = document.querySelector( '.rehab-cards-grid' );
		if ( el ) el.scrollIntoView( { block: 'start', behavior: 'instant' } );
	} );
	await page.waitForTimeout( 2500 );

	const cards = editorFrame.locator( '.rehab-cards-grid' );
	if ( await cards.count() ) {
		await cards.first().screenshot( { path: '/tmp/cards-element.png' } );
		console.log( 'Saved /tmp/cards-element.png' );
	}
	await browser.close();
} )().catch( ( e ) => { console.error( e ); process.exit( 1 ); } );
