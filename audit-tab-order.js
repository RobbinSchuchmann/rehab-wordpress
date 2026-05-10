const { chromium } = require( 'playwright' );

const URL = process.argv[ 2 ] || 'http://localhost:8081/';
const COUNT = parseInt( process.argv[ 3 ] || '20', 10 );

( async () => {
	const browser = await chromium.launch( { headless: true } );
	const ctx = await browser.newContext( { viewport: { width: 1400, height: 900 } } );
	const page = await ctx.newPage();
	await page.goto( URL, { waitUntil: 'load' } );
	await page.waitForTimeout( 1000 );

	const order = [];
	for ( let i = 0; i < COUNT; i++ ) {
		await page.keyboard.press( 'Tab' );
		const info = await page.evaluate( () => {
			const el = document.activeElement;
			if ( ! el ) return null;
			return {
				tag: el.tagName,
				id: el.id || '',
				cls: ( el.className || '' ).toString().slice( 0, 60 ),
				text: ( el.innerText || el.value || el.alt || '' ).trim().slice( 0, 60 ),
				href: el.href ? el.href.replace( /^https?:\/\/[^/]+/, '' ) : '',
			};
		} );
		order.push( info );
	}
	console.log( order.map( ( o, i ) => `${ i + 1 }. ${ o.tag } [${ o.cls.split( ' ' )[ 0 ] || '' }] "${ o.text }" ${ o.href }`.trim() ).join( '\n' ) );
	await browser.close();
} )().catch( ( e ) => { console.error( e ); process.exit( 1 ); } );
