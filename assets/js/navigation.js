/**
 * File navigation.js.
 *
 * Handles toggling the navigation menu for small screens.
 */
( function() {
	const siteNavigation = document.getElementById( 'site-navigation' );

	if ( ! siteNavigation ) {
		return;
	}

	const button = siteNavigation.querySelector( 'button.menu-toggle' );

	if ( ! button ) {
		return;
	}

	const menu = siteNavigation.querySelector( 'ul' );

	if ( ! menu || ! menu.children.length ) {
		button.style.display = 'none';
		return;
	}

	button.addEventListener( 'click', function() {
		siteNavigation.classList.toggle( 'toggled' );

		if ( button.getAttribute( 'aria-expanded' ) === 'true' ) {
			button.setAttribute( 'aria-expanded', 'false' );
		} else {
			button.setAttribute( 'aria-expanded', 'true' );
		}
	} );
}() );
