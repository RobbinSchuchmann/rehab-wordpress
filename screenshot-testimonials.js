const { chromium } = require( 'playwright' );

( async () => {
	const browser = await chromium.launch( { headless: true } );
	const ctx = await browser.newContext( { viewport: { width: 1400, height: 1000 } } );
	const page = await ctx.newPage();
	await page.goto( 'http://localhost:8081/wp-login.php' );
	await page.fill( '#user_login', 'devadmin' );
	await page.fill( '#user_pass', 'dev123!' );
	await page.click( '#wp-submit' );
	await page.waitForLoadState( 'domcontentloaded' );
	await page.goto( 'http://localhost:8081/wp-admin/post.php?post=12242&action=edit', { waitUntil: 'domcontentloaded' } );
	await page.waitForTimeout( 18000 );

	const editorFrame = page.frames().find( ( f ) => f.url().startsWith( 'blob:' ) );
	if ( ! editorFrame ) {
		console.log( 'no editor iframe' );
		await browser.close();
		return;
	}

	// Scroll testimonials into view
	await editorFrame.evaluate( () => {
		const el = document.querySelector( '.rehab-testimonials' );
		if ( el ) el.scrollIntoView( { block: 'start', behavior: 'instant' } );
	} );
	await page.waitForTimeout( 2000 );

	// Get bounding rects to compute the absolute screenshot region
	const gridRect = await editorFrame.evaluate( () => {
		const el = document.querySelector( '.rehab-testimonials__grid' );
		if ( ! el ) return null;
		const r = el.getBoundingClientRect();
		return { x: r.x, y: r.y, width: r.width, height: r.height };
	} );

	const iframeRect = await page.evaluate( () => {
		const iframe = document.querySelector( 'iframe[name="editor-canvas"]' );
		if ( ! iframe ) return null;
		const r = iframe.getBoundingClientRect();
		return { x: r.x, y: r.y, width: r.width, height: r.height };
	} );

	console.log( 'grid rect:', gridRect );
	console.log( 'iframe rect:', iframeRect );

	if ( gridRect && iframeRect ) {
		const absX = iframeRect.x + gridRect.x;
		const absY = iframeRect.y + gridRect.y;
		await page.screenshot( {
			path: '/tmp/editor-testimonials.png',
			clip: {
				x: Math.max( 0, absX - 50 ),
				y: Math.max( 0, absY - 50 ),
				width: Math.min( 1400 - Math.max( 0, absX - 50 ), gridRect.width + 100 ),
				height: Math.min( 1000, gridRect.height + 100 ),
			},
		} );
	} else {
		await page.screenshot( { path: '/tmp/editor-testimonials.png', fullPage: false } );
	}
	console.log( 'Saved /tmp/editor-testimonials.png' );

	await browser.close();
} )().catch( ( e ) => { console.error( e ); process.exit( 1 ); } );
