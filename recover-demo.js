// One-shot fix: opens the demo, uses Gutenberg's data API to reset all blocks
// (which forces save() to re-run with current attributes), then saves.

const { chromium } = require( 'playwright' );

const SITE = 'http://localhost:8081';
const POST_ID = parseInt( process.argv[ 2 ] || '12242', 10 );
const USER = 'devadmin';
const PASS = 'dev123!';

( async () => {
	const browser = await chromium.launch( { headless: true } );
	const ctx = await browser.newContext( { viewport: { width: 1400, height: 1000 } } );
	const page = await ctx.newPage();

	console.log( 'Logging in…' );
	await page.goto( `${ SITE }/wp-login.php`, { waitUntil: 'domcontentloaded' } );
	await page.fill( '#user_login', USER );
	await page.fill( '#user_pass', PASS );
	await page.click( '#wp-submit' );
	await page.waitForLoadState( 'domcontentloaded' );

	console.log( `Opening post ${ POST_ID }…` );
	await page.goto( `${ SITE }/wp-admin/post.php?post=${ POST_ID }&action=edit`, {
		waitUntil: 'domcontentloaded',
	} );
	await page.waitForTimeout( 18000 );

	// Pass 1: click any visible "Attempt recovery" buttons
	const allFrames = () => [ page.mainFrame(), ...page.frames() ];
	let visibleRecoveries = 0;
	for ( let pass = 0; pass < 4; pass++ ) {
		let foundThisPass = 0;
		for ( const frame of allFrames() ) {
			const buttons = frame.locator( 'button:has-text("Attempt recovery")' );
			const count = await buttons.count();
			for ( let i = 0; i < count; i++ ) {
				try {
					await buttons.nth( 0 ).click( { timeout: 2500 } );
					foundThisPass++;
					visibleRecoveries++;
					await page.waitForTimeout( 200 );
				} catch ( _ ) {}
			}
		}
		if ( foundThisPass === 0 ) break;
	}
	console.log( `Visible recoveries clicked: ${ visibleRecoveries }` );

	// Pass 2: force a global block reset via wp.data
	console.log( 'Forcing block reset via wp.data…' );
	const resetResult = await page.evaluate( () => {
		const w = window;
		if ( ! w.wp || ! w.wp.data || ! w.wp.blocks ) {
			return { ok: false, reason: 'wp.data not available on top window' };
		}
		const blocks = w.wp.data.select( 'core/block-editor' ).getBlocks();
		// Re-create blocks deeply via createBlock to force fresh save() output
		const recreate = ( b ) => w.wp.blocks.createBlock(
			b.name,
			b.attributes,
			( b.innerBlocks || [] ).map( recreate )
		);
		const fresh = blocks.map( recreate );
		w.wp.data.dispatch( 'core/block-editor' ).resetBlocks( fresh );
		return { ok: true, blockCount: blocks.length };
	} );
	console.log( '  result:', JSON.stringify( resetResult ) );
	await page.waitForTimeout( 1500 );

	// Pass 3: save the post via the editor data dispatch (savePost)
	console.log( 'Saving via savePost()…' );
	const saveResult = await page.evaluate( async () => {
		const w = window;
		if ( ! w.wp || ! w.wp.data ) return { ok: false, reason: 'no wp.data' };
		try {
			await w.wp.data.dispatch( 'core/editor' ).savePost();
			return { ok: true };
		} catch ( e ) {
			return { ok: false, reason: e.message };
		}
	} );
	console.log( '  result:', JSON.stringify( saveResult ) );
	await page.waitForTimeout( 5000 );

	console.log( 'Done.' );
	await browser.close();
} )().catch( ( e ) => {
	console.error( e );
	process.exit( 1 );
} );
