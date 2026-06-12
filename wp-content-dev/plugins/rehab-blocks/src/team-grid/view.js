/**
 * Discipline filter for rehab/team-grid.
 */
( () => {
	const init = ( root ) => {
		const buttons = root.querySelectorAll( '.rehab-team-grid__filter button' );
		const cards = root.querySelectorAll( '.rehab-team-card' );
		const empty = root.querySelector( '.rehab-team-grid__empty' );
		if ( ! buttons.length ) return;
		buttons.forEach( ( btn ) => {
			btn.addEventListener( 'click', () => {
				const cat = btn.dataset.cat;
				buttons.forEach( ( b ) => b.classList.toggle( 'on', b === btn ) );
				let visible = 0;
				cards.forEach( ( card ) => {
					const show = cat === 'all' || card.dataset.cat === cat;
					card.classList.toggle( 'hide', ! show );
					if ( show ) visible++;
				} );
				empty && empty.classList.toggle( 'show', visible === 0 );
			} );
		} );
	};

	const boot = () => document.querySelectorAll( '.rehab-team-grid' ).forEach( init );

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
} )();
