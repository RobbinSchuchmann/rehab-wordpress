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

	// In each frame, dump every chunk of warning text and the parent block
	for ( const f of [ page.mainFrame(), ...page.frames() ] ) {
		const url = f.url() || 'main';
		try {
			const matches = await f.evaluate( () => {
				const found = [];
				document.querySelectorAll( '[class*="block-list-block"], [class*="invalid"], [class*="warning"]' ).forEach( ( el ) => {
					const text = ( el.innerText || '' ).slice( 0, 300 );
					if ( /invalid|recover|deprecat|unexpected/i.test( text ) ) {
						const blockType = el.getAttribute( 'data-type' ) || el.querySelector( '[data-type]' )?.getAttribute( 'data-type' ) || '?';
						found.push( { blockType, text: text.replace( /\s+/g, ' ' ).trim() } );
					}
				} );
				// Also check for buttons
				const buttons = [];
				document.querySelectorAll( 'button' ).forEach( ( b ) => {
					const t = ( b.innerText || '' ).trim();
					if ( /recover|resolve|attempt/i.test( t ) ) buttons.push( t );
				} );
				return { warnings: found, buttons };
			} );
			if ( matches.warnings.length || matches.buttons.length ) {
				console.log( `--- ${ url } ---` );
				if ( matches.warnings.length ) console.log( '  warnings:', JSON.stringify( matches.warnings, null, 2 ) );
				if ( matches.buttons.length ) console.log( '  buttons:', matches.buttons );
			}
		} catch ( _ ) {}
	}

	await page.screenshot( { path: '/tmp/editor-full.png', fullPage: true } );
	console.log( 'Saved /tmp/editor-full.png' );
	await browser.close();
} )().catch( ( e ) => { console.error( e ); process.exit( 1 ); } );
