// REH-120: fold top-level core blocks (heading/paragraph/list) into the
// preceding rehab/article-row, inside the block editor, so Gutenberg
// regenerates valid static-block markup (never hand-edit attr JSON — see
// the static-block change protocol / REH-84).
//
// Transform, per run of loose blocks that follows an article-row:
//   core/heading    -> `<p><strong>{text}</strong></p>` appended to row body
//   core/paragraph  -> `<p>{content}</p>` appended (empty/&nbsp; ones just removed)
//   core/list       -> its items appended to the row's native listItems
// Special case (stages-of-change 2853): an article-row whose heading starts
// "What are the stages" with an EMPTY body takes over the body of the next
// article-row (the general intro was mislabeled under "1. Precontemplation");
// that row's own prose then arrives via the fold.
//
// Usage: node tools/merge-loose-prose.js <post_id> [--dry]
const { chromium } = require( 'playwright' );

const SITE = 'http://localhost:8081';
const POST = process.argv[ 2 ];
const DRY = process.argv.includes( '--dry' );
if ( ! /^\d+$/.test( POST || '' ) ) { console.error( 'usage: node tools/merge-loose-prose.js <post_id> [--dry]' ); process.exit( 1 ); }

( async () => {
	const browser = await chromium.launch( { headless: true } );
	const ctx = await browser.newContext( { viewport: { width: 1400, height: 1000 } } );
	const page = await ctx.newPage();
	await page.goto( `${ SITE }/wp-login.php`, { waitUntil: 'domcontentloaded' } );
	await page.fill( '#user_login', 'devadmin' );
	await page.fill( '#user_pass', 'dev123!' );
	await page.click( '#wp-submit' );
	await page.waitForLoadState( 'domcontentloaded' );

	await page.goto( `${ SITE }/wp-admin/post.php?post=${ POST }&action=edit`, { waitUntil: 'domcontentloaded' } );
	// Wait for the block canvas (any frame) to exist, then settle.
	let ready = false;
	for ( let t = 0; t < 22 && ! ready; t++ ) {
		for ( const f of [ page.mainFrame(), ...page.frames() ] ) {
			try { if ( await f.locator( '.block-editor-block-list__layout, .is-root-container' ).count() ) { ready = true; break; } } catch ( _ ) {}
		}
		if ( ! ready ) await page.waitForTimeout( 1000 );
	}
	await page.waitForTimeout( 6000 );

	const summary = await page.evaluate( ( dry ) => {
		const sel = window.wp.data.select( 'core/block-editor' );
		const dis = window.wp.data.dispatch( 'core/block-editor' );
		const str = ( v ) => ( v == null ? '' : ( typeof v === 'string' ? v : v.toString() ) );
		const isEmptyText = ( s ) => str( s ).replace( /&nbsp;| /g, ' ' ).replace( /<[^>]+>/g, '' ).trim() === '';

		const blocks = sel.getBlocks();
		const log = [];

		// --- special case: empty "What are the stages…" intro row steals the
		// general-intro body from the next article-row.
		const rows = blocks.filter( ( b ) => b.name === 'rehab/article-row' );
		const introRow = rows.find( ( b ) => /^what are the stages/i.test( str( b.attributes.heading ) ) && isEmptyText( b.attributes.body ) );
		if ( introRow ) {
			const donor = rows[ rows.indexOf( introRow ) + 1 ];
			if ( donor && ! isEmptyText( donor.attributes.body ) ) {
				if ( ! dry ) {
					dis.updateBlockAttributes( introRow.clientId, { body: str( donor.attributes.body ) } );
					dis.updateBlockAttributes( donor.clientId, { body: '' } );
				}
				log.push( `intro-move: "${ str( donor.attributes.heading ) }" body -> "${ str( introRow.attributes.heading ) }"` );
			}
		}

		// --- fold loose core blocks into the preceding article-row.
		let currentRow = null;
		let pend = null;
		const toRemove = [];
		const flush = () => {
			if ( ! currentRow || ! pend || ( ! pend.body && ! pend.items.length ) ) { pend = null; return; }
			const cur = sel.getBlockAttributes( currentRow.clientId );
			if ( ! dry ) {
				dis.updateBlockAttributes( currentRow.clientId, {
					body: str( cur.body ) + pend.body,
					listItems: [ ...( cur.listItems || [] ), ...pend.items ],
				} );
			}
			log.push( `fold -> "${ str( cur.heading ) }": +${ pend.nBody } body block(s), +${ pend.items.length } list item(s)` );
			pend = null;
		};

		for ( const b of blocks ) {
			if ( b.name === 'rehab/article-row' ) { flush(); currentRow = b; pend = { body: '', items: [], nBody: 0 }; continue; }
			if ( ! currentRow ) continue;
			if ( b.name === 'core/heading' ) {
				const t = str( b.attributes.content ).trim();
				if ( t ) { pend.body += `<p><strong>${ t }</strong></p>`; pend.nBody++; }
				toRemove.push( b.clientId );
			} else if ( b.name === 'core/paragraph' ) {
				const t = str( b.attributes.content ).trim();
				if ( ! isEmptyText( t ) ) { pend.body += `<p>${ t }</p>`; pend.nBody++; }
				toRemove.push( b.clientId );
			} else if ( b.name === 'core/list' ) {
				for ( const li of b.innerBlocks || [] ) {
					const t = str( li.attributes.content ).trim();
					if ( t ) pend.items.push( t );
				}
				toRemove.push( b.clientId );
			} else {
				// next structured block — close out the current run
				flush(); currentRow = null;
			}
		}
		flush();

		if ( ! dry && toRemove.length ) dis.removeBlocks( toRemove );
		return { log, removed: toRemove.length };
	}, DRY );

	console.log( summary.log.join( '\n' ) );
	console.log( `loose blocks ${ DRY ? 'to remove' : 'removed' }: ${ summary.removed }` );

	if ( ! DRY ) {
		await page.evaluate( async () => { await window.wp.data.dispatch( 'core/editor' ).savePost(); } );
		await page.waitForTimeout( 5000 );
		const status = await page.evaluate( () => window.wp.data.select( 'core/editor' ).didPostSaveRequestSucceed() );
		console.log( 'saved:', status );
	}
	await browser.close();
} )().catch( ( e ) => { console.error( e ); process.exit( 1 ); } );
