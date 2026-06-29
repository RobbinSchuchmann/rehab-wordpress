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
	if ( ! editorFrame ) { console.log( 'no iframe' ); await browser.close(); return; }

	const info = await editorFrame.evaluate( () => {
		const out = [];
		document.querySelectorAll( '.rehab-testimonials, [class*="rehab-testimonials"]' ).forEach( ( el ) => {
			const computed = getComputedStyle( el );
			out.push( {
				className: el.className.slice( 0, 200 ),
				display: computed.display,
				gridTemplateColumns: computed.gridTemplateColumns,
				width: computed.width,
				maxWidth: computed.maxWidth,
				outerWidth: el.getBoundingClientRect().width,
			} );
		} );
		document.querySelectorAll( '.rehab-testimonial' ).forEach( ( el ) => {
			const computed = getComputedStyle( el );
			out.push( {
				type: 'card',
				className: el.className.slice( 0, 200 ),
				display: computed.display,
				width: computed.width,
				outerWidth: el.getBoundingClientRect().width,
			} );
		} );
		return out;
	} );
	console.log( JSON.stringify( info, null, 2 ) );

	// Also print iframe canvas width
	const canvasWidth = await editorFrame.evaluate( () => document.documentElement.clientWidth );
	console.log( 'iframe canvas width:', canvasWidth );

	await browser.close();
} )().catch( ( e ) => { console.error( e ); process.exit( 1 ); } );
