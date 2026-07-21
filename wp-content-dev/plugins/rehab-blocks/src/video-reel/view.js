/* Video reel: click-to-embed (REH-168, playback fixes REH-172).
 *
 * Cards with a recognised YouTube id play in place — the thumb's content is
 * swapped for a youtube-nocookie iframe. While playing, the `is-playing` class
 * drops the card's gradient overlay so the player's own controls (pause,
 * replay, …) actually receive the clicks. Starting a card stops any other
 * playing card and restores its poster. Non-YouTube URLs keep their default
 * link behaviour. */
( () => {
	const stop = ( card ) => {
		if ( ! card.dataset.rehabPlaying ) return;
		delete card.dataset.rehabPlaying;
		card.classList.remove( 'is-playing' );
		card.innerHTML = card.dataset.rehabRestore || '';
	};

	const init = () => {
		const cards = [ ...document.querySelectorAll( '[data-rehab-video-id]' ) ];
		cards.forEach( ( card ) => {
			card.addEventListener( 'click', ( e ) => {
				// The card is an <a href> — never let a stray click navigate away.
				e.preventDefault();
				if ( card.dataset.rehabPlaying ) return; // player owns the clicks now
				cards.forEach( ( other ) => { if ( other !== card ) stop( other ); } );
				card.dataset.rehabRestore = card.innerHTML;
				card.dataset.rehabPlaying = '1';
				card.classList.add( 'is-playing' );
				const frame = document.createElement( 'iframe' );
				frame.className = 'rehab-video-card__frame';
				frame.src = `https://www.youtube-nocookie.com/embed/${ encodeURIComponent( card.dataset.rehabVideoId ) }?autoplay=1&playsinline=1&rel=0`;
				frame.title = 'Video testimonial';
				frame.allow = 'autoplay; encrypted-media; picture-in-picture';
				frame.allowFullscreen = true;
				card.innerHTML = '';
				card.appendChild( frame );
			} );
		} );
	};

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
