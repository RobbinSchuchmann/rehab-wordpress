/* Video reel: click-to-embed (REH-168).
 *
 * Cards with a recognised YouTube id play in place — the thumb's content is
 * swapped for a youtube-nocookie iframe. Cards with a non-YouTube URL keep
 * their default link behaviour (open the video wherever it lives). */
( () => {
	const init = () => {
		document.querySelectorAll( '[data-rehab-video-id]' ).forEach( ( card ) => {
			card.addEventListener( 'click', ( e ) => {
				e.preventDefault();
				if ( card.dataset.rehabPlaying ) return;
				card.dataset.rehabPlaying = '1';
				const id = card.dataset.rehabVideoId;
				const frame = document.createElement( 'iframe' );
				frame.className = 'rehab-video-card__frame';
				frame.src = `https://www.youtube-nocookie.com/embed/${ encodeURIComponent( id ) }?autoplay=1&playsinline=1&rel=0`;
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
