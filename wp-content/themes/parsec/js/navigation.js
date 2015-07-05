/**
 * navigation.js
 *
 * Handles toggling the navigation menu for small screens.
 */
( function() {
	var container, button, menu;

	container = document.getElementById( 'site-navigation' );
	if ( ! container ) {
		return;
	}

	buttonContainer = document.getElementById( 'masthead' );

	button = container.getElementsByTagName( 'i' )[0];
	if ( 'undefined' === typeof button ) {
		return;
	}

	menu = container.getElementsByTagName( 'ul' )[0];

	// Hide menu toggle button if menu is empty and return early.
	if ( 'undefined' === typeof menu ) {
		button.style.display = 'none';
		return;
	}

	menu.setAttribute( 'aria-expanded', 'false' );

	if ( -1 === menu.className.indexOf( 'nav-menu' ) ) {
		menu.className += ' nav-menu';
	}

	button.onclick = function() {
		if ( -1 !== container.className.indexOf( 'toggled' ) ) {
			container.className = container.className.replace( ' toggled', '' );
			button.setAttribute( 'aria-expanded', 'false' );
			menu.setAttribute( 'aria-expanded', 'false' );
		} else {
			container.className += ' toggled';
			button.setAttribute( 'aria-expanded', 'true' );
			menu.setAttribute( 'aria-expanded', 'true' );
		}
	};
} )();

(function( $ ) {

	$(document).ready( reHeight );
	$(window).resize( reHeight );

	function reHeight() {
		$('.event-info').each(function(){

				var highestBox = 0;
				$('.widget', this).each(function(){

						if($(this).height() > highestBox) {
							highestBox = $(this).height();
						} else if( $(this).text.height > highestBox ) {
							highestBox = $(this).text.height();
						}
				});

				$('.widget',this).height(highestBox);

		});
	}

})(jQuery);

jQuery('html, body').animate({
		scrollTop: jQuery("body:not(.home) #js-countdown").offset().top
}, 600);
