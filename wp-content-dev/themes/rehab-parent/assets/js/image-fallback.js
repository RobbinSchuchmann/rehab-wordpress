/**
 * Hide broken (404) <img> elements gracefully so the layout doesn't show the
 * default "broken image" UA glyph. We can't tell from the server which images
 * exist, so this script catches the `error` event in the bubbling phase.
 */
( function () {
	'use strict';

	function markBroken( img ) {
		if ( img.dataset.broken === 'true' ) return;
		img.dataset.broken = 'true';
	}

	// Capture-phase listener — `error` doesn't bubble.
	document.addEventListener(
		'error',
		function ( event ) {
			var target = event.target;
			if ( target && target.tagName === 'IMG' ) {
				markBroken( target );
			}
		},
		true
	);

	// Catch images that already failed before the listener attached.
	function checkExisting() {
		document.querySelectorAll( 'img' ).forEach( function ( img ) {
			if ( img.complete && img.naturalWidth === 0 && img.src ) {
				markBroken( img );
			}
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', checkExisting );
	} else {
		checkExisting();
	}
} )();
