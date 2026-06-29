const { chromium } = require( 'playwright' );

const POST_ID = process.argv[ 2 ] || '853';
const OUT = process.argv[ 3 ] || '/tmp/editor.png';

( async () => {
	const browser = await chromium.launch( { headless: true } );
	const ctx = await browser.newContext( { viewport: { width: 1400, height: 900 } } );
	const page = await ctx.newPage();
	await page.goto( 'http://localhost:8081/wp-login.php', { waitUntil: 'load' } );
	await page.fill( '#user_login', 'devadmin' );
	await page.fill( '#user_pass', 'dev123!' );
	await page.click( '#wp-submit' );
	await page.waitForURL( /wp-admin/ );
	await page.goto( `http://localhost:8081/wp-admin/post.php?post=${ POST_ID }&action=edit`, { waitUntil: 'load' } );
	await page.waitForTimeout( 4000 );
	await page.screenshot( { path: OUT, fullPage: false } );
	console.log( 'Saved', OUT );
	await browser.close();
} )().catch( ( e ) => { console.error( e ); process.exit( 1 ); } );
