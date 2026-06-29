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

	// Walk up from a card to the testimonials block, dumping each ancestor
	const tree = await editorFrame.evaluate( () => {
		const card = document.querySelector( '.rehab-testimonial' );
		if ( ! card ) return [ 'no card' ];
		const out = [];
		let el = card;
		for ( let i = 0; i < 10 && el; i++ ) {
			const cs = getComputedStyle( el );
			out.push( {
				tag: el.tagName,
				cls: el.className.toString().slice( 0, 120 ),
				display: cs.display,
				gridTpl: cs.gridTemplateColumns,
			} );
			el = el.parentElement;
		}
		return out;
	} );
	console.log( JSON.stringify( tree, null, 2 ) );

	// Also print the actual loaded stylesheets that contain "block-editor-block-list__layout"
	const stylesheets = await editorFrame.evaluate( () => {
		const matches = [];
		for ( const s of document.styleSheets ) {
			try {
				for ( const rule of s.cssRules ) {
					if ( rule.cssText && rule.cssText.includes( 'rehab-testimonials__grid' ) && rule.cssText.includes( 'block-editor-block-list__layout' ) ) {
						matches.push( rule.cssText.slice( 0, 200 ) );
					}
				}
			} catch ( _ ) {}
		}
		return matches;
	} );
	console.log( '--- matching rules ---' );
	console.log( stylesheets );

	await browser.close();
} )().catch( ( e ) => { console.error( e ); process.exit( 1 ); } );
