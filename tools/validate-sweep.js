// REH-10 pre-go-live validation sweep: for each page ID, open the editor,
// detect Gutenberg "Attempt recovery"/invalid-block warnings, and if found run
// the recover flow (click recovery + wp.data resetBlocks + savePost), then
// re-check. One shared login. Reuses recover-demo.js + check-editor.js logic.
// Usage: node validate-sweep.js <ids-file> [out-json]
const { chromium } = require( 'playwright' );
const fs = require( 'fs' );

const SITE = 'http://localhost:8081';
const IDS_FILE = process.argv[ 2 ] || '/tmp/sweep-ids.txt';
const OUT = process.argv[ 3 ] || '/tmp/validate-sweep.json';
const ids = fs.readFileSync( IDS_FILE, 'utf8' ).split( '\n' ).map( s => s.trim() ).filter( s => /^\d+$/.test( s ) );

const detect = async ( page ) => {
	let warnings = 0, buttons = 0;
	for ( const f of [ page.mainFrame(), ...page.frames() ] ) {
		try {
			const r = await f.evaluate( () => {
				let w = 0, b = 0;
				// Real Gutenberg block-invalid banner uses this exact phrase.
				document.querySelectorAll( '[class*="block-list-block"], [class*="invalid"], [class*="warning"]' ).forEach( ( el ) => {
					const t = ( el.innerText || '' ).slice( 0, 400 );
					if ( /unexpected or invalid content|has been modified externally/i.test( t ) ) w++;
				} );
				// Real recovery button contains BOTH words ("Attempt recovery").
				// Excludes the title-bar button when a page title merely contains "Recovery".
				document.querySelectorAll( 'button' ).forEach( ( bt ) => {
					const t = ( bt.innerText || '' ).trim();
					if ( /attempt/i.test( t ) && /recover/i.test( t ) ) b++;
				} );
				return { w, b };
			} );
			warnings += r.w; buttons += r.b;
		} catch ( _ ) {}
	}
	return { warnings, buttons };
};

const recover = async ( page ) => {
	const allFrames = () => [ page.mainFrame(), ...page.frames() ];
	for ( let pass = 0; pass < 4; pass++ ) {
		let found = 0;
		for ( const frame of allFrames() ) {
			const btns = frame.locator( 'button:has-text("Attempt recovery")' );
			const n = await btns.count();
			for ( let i = 0; i < n; i++ ) {
				try { await btns.nth( 0 ).click( { timeout: 2500 } ); found++; await page.waitForTimeout( 200 ); } catch ( _ ) {}
			}
		}
		if ( ! found ) break;
	}
	await page.evaluate( () => {
		const w = window;
		if ( ! w.wp || ! w.wp.data || ! w.wp.blocks ) return;
		const blocks = w.wp.data.select( 'core/block-editor' ).getBlocks();
		const recreate = ( b ) => w.wp.blocks.createBlock( b.name, b.attributes, ( b.innerBlocks || [] ).map( recreate ) );
		w.wp.data.dispatch( 'core/block-editor' ).resetBlocks( blocks.map( recreate ) );
	} );
	await page.waitForTimeout( 1200 );
	await page.evaluate( async () => {
		try { await window.wp.data.dispatch( 'core/editor' ).savePost(); } catch ( _ ) {}
	} );
	await page.waitForTimeout( 4000 );
};

const openEditor = async ( page, id ) => {
	await page.goto( `${ SITE }/wp-admin/post.php?post=${ id }&action=edit`, { waitUntil: 'domcontentloaded' } );
	// Wait for the block canvas in whichever frame holds it, then settle.
	let ready = false;
	for ( let t = 0; t < 22 && ! ready; t++ ) {
		for ( const f of [ page.mainFrame(), ...page.frames() ] ) {
			try { if ( await f.locator( '.block-editor-block-list__layout, .is-root-container' ).count() ) { ready = true; break; } } catch ( _ ) {}
		}
		if ( ! ready ) await page.waitForTimeout( 1000 );
	}
	await page.waitForTimeout( 6000 );
};

( async () => {
	const browser = await chromium.launch( { headless: true } );
	const ctx = await browser.newContext( { viewport: { width: 1400, height: 1000 } } );
	const page = await ctx.newPage();
	await page.goto( `${ SITE }/wp-login.php`, { waitUntil: 'domcontentloaded' } );
	await page.fill( '#user_login', 'devadmin' );
	await page.fill( '#user_pass', 'dev123!' );
	await page.click( '#wp-submit' );
	await page.waitForLoadState( 'domcontentloaded' );

	const results = [];
	for ( let i = 0; i < ids.length; i++ ) {
		const id = ids[ i ];
		const rec = { id, before: null, after: null, fixed: false, error: null };
		try {
			await openEditor( page, id );
			const before = await detect( page );
			rec.before = before;
			if ( before.warnings > 0 || before.buttons > 0 ) {
				await recover( page );
				await page.waitForTimeout( 1000 );
				rec.after = await detect( page );
				rec.fixed = true;
			} else {
				rec.after = before;
			}
		} catch ( e ) { rec.error = e.message.slice( 0, 120 ); }
		const tag = rec.error ? `ERR ${ rec.error }` :
			( rec.fixed ? `FIXED (was w${ rec.before.warnings }/b${ rec.before.buttons } -> w${ rec.after.warnings }/b${ rec.after.buttons })` : 'clean' );
		console.log( `[${ i + 1 }/${ ids.length }] ${ id }  ${ tag }` );
		results.push( rec );
	}
	fs.writeFileSync( OUT, JSON.stringify( results, null, 2 ) );
	const flagged = results.filter( r => r.fixed );
	const stillBad = results.filter( r => r.after && ( r.after.warnings > 0 || r.after.buttons > 0 ) );
	const errs = results.filter( r => r.error );
	console.log( `\n=== SWEEP DONE: ${ results.length } pages | ${ flagged.length } had warnings (recovered) | ${ stillBad.length } still showing after recover | ${ errs.length } errors ===` );
	if ( stillBad.length ) console.log( 'STILL BAD:', stillBad.map( r => r.id ).join( ',' ) );
	await browser.close();
} )().catch( e => { console.error( e ); process.exit( 1 ); } );
