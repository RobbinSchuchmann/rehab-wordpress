/**
 * Scroll-spy for the rehab/faq-page sticky category nav.
 */
( () => {
	const init = ( root ) => {
		const links = Array.from( root.querySelectorAll( '.rehab-faq-page__nav a' ) );
		const cats = Array.from( root.querySelectorAll( '.rehab-faq-page__cat' ) );
		if ( ! links.length || ! cats.length ) return;
		const activate = ( id ) => links.forEach( ( a ) => a.classList.toggle( 'on', a.getAttribute( 'href' ) === '#' + id ) );
		const io = new IntersectionObserver( ( entries ) => {
			const visible = entries.filter( ( e ) => e.isIntersecting ).sort( ( x, y ) => x.boundingClientRect.top - y.boundingClientRect.top );
			if ( visible.length ) activate( visible[ 0 ].target.id );
		}, { rootMargin: '-110px 0px -60% 0px' } );
		cats.forEach( ( c ) => io.observe( c ) );
	};

	const boot = () => document.querySelectorAll( '.rehab-faq-page' ).forEach( init );

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
} )();
