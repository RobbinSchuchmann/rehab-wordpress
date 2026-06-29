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

	const data = await editorFrame.evaluate( () => {
		const cards = document.querySelectorAll( '.rehab-testimonial' );
		const out = [];
		cards.forEach( ( card, idx ) => {
			const r = card.getBoundingClientRect();
			const cs = getComputedStyle( card );
			out.push( {
				idx,
				rect: { x: r.x, y: r.y, w: r.width, h: r.height },
				gridColumn: cs.gridColumnStart + ' / ' + cs.gridColumnEnd,
				gridRow: cs.gridRowStart + ' / ' + cs.gridRowEnd,
				position: cs.position,
				marginTop: cs.marginTop,
				display: cs.display,
				parent: card.parentElement.className.slice( 0, 80 ),
				parentDisplay: getComputedStyle( card.parentElement ).display,
				parentGridCols: getComputedStyle( card.parentElement ).gridTemplateColumns,
			} );
		} );
		return out;
	} );
	console.log( JSON.stringify( data, null, 2 ) );

	await browser.close();
} )().catch( ( e ) => { console.error( e ); process.exit( 1 ); } );
