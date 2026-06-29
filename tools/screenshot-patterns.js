const { chromium } = require( 'playwright' );

const OUT = process.argv[ 2 ] || '/tmp/patterns.png';

( async () => {
	const browser = await chromium.launch( { headless: true } );
	const ctx = await browser.newContext( { viewport: { width: 1400, height: 900 } } );
	const page = await ctx.newPage();
	await page.goto( 'http://localhost:8081/wp-login.php', { waitUntil: 'load' } );
	await page.fill( '#user_login', 'devadmin' );
	await page.fill( '#user_pass', 'dev123!' );
	await page.click( '#wp-submit' );
	await page.waitForURL( /wp-admin/ );
	await page.goto( 'http://localhost:8081/wp-admin/post-new.php?post_type=page', { waitUntil: 'load' } );
	await page.waitForTimeout( 3500 );
	// Open Inserter
	await page.click( 'button[aria-label="Block Inserter"]' ).catch( () => {} );
	await page.waitForTimeout( 1000 );
	// Click Patterns tab
	await page.click( 'button[role="tab"]:has-text("Patterns")' ).catch( () => {} );
	await page.waitForTimeout( 1500 );
	await page.screenshot( { path: OUT, fullPage: false } );
	console.log( 'Saved', OUT );
	await browser.close();
} )().catch( ( e ) => { console.error( e ); process.exit( 1 ); } );
