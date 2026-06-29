const { chromium } = require( 'playwright' );
const WIDTHS = [ 1024, 1100, 1200, 1280, 1366, 1440, 1536, 1680, 1920, 2200, 2560 ];
( async () => {
	const browser = await chromium.launch( { headless: true } );
	for ( const width of WIDTHS ) {
		const ctx = await browser.newContext( { viewport: { width, height: 800 } } );
		const page = await ctx.newPage();
		await page.goto( 'http://localhost:8081/', { waitUntil: 'domcontentloaded' } );
		await page.waitForTimeout( 800 );
		const r = await page.evaluate( () => {
			const q = ( s ) => document.querySelector( s );
			const box = ( el ) => { if ( ! el ) return null; const b = el.getBoundingClientRect(); return { l: Math.round( b.left ), r: Math.round( b.right ), w: Math.round( b.width ) }; };
			const brand = q( '.rehab-navbar__brand' );
			const menu = q( '.rehab-navbar__menu' );
			const firstLink = menu ? menu.querySelector( 'li a' ) : null;
			const links = q( '.rehab-navbar__links' );
			const actions = q( '.rehab-navbar__actions' );
			const toggleVisible = q( '.rehab-navbar__toggle' ) ? getComputedStyle( q( '.rehab-navbar__toggle' ) ).display !== 'none' : false;
			return {
				brand: box( brand ), links: box( links ), menu: box( menu ),
				firstLink: box( firstLink ), firstLinkText: firstLink ? firstLink.textContent.trim() : null,
				actions: box( actions ), toggleVisible,
			};
		} );
		let verdict = 'ok';
		if ( ! r.toggleVisible && r.firstLink && r.brand ) {
			if ( r.firstLink.l < r.brand.r ) verdict = `*** OVERLAP: "${ r.firstLinkText }" left=${ r.firstLink.l } < brand right=${ r.brand.r } (by ${ r.brand.r - r.firstLink.l }px)`;
			else if ( r.firstLink.l - r.brand.r < 16 ) verdict = `tight (gap ${ r.firstLink.l - r.brand.r }px)`;
		}
		if ( r.menu && r.actions && ! r.toggleVisible && r.menu.r > r.actions.l ) verdict += ` | menu/actions overlap (menu.r=${ r.menu.r } > actions.l=${ r.actions.l })`;
		console.log( `@${ width }  burger=${ r.toggleVisible }  brand[${ r.brand?.l }-${ r.brand?.r }]  menu[${ r.menu?.l }-${ r.menu?.r }]  actions[${ r.actions?.l }-${ r.actions?.r }]  -> ${ verdict }` );
		await ctx.close();
	}
	await browser.close();
} )().catch( e => { console.error( e ); process.exit( 1 ); } );
