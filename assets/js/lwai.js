/**
 * LWAI Theme — Main JavaScript
 *
 * Handles: mobile navigation, search overlay, reading progress, back-to-top.
 * Replaces navigation.js — single cached file for performance.
 *
 * @package LWAI
 */

( function () {
	'use strict';

	/* ─── Mobile Navigation ─────────────────────────────────────────────── */
	var siteNav    = document.getElementById( 'site-navigation' );
	var navWrapper = document.getElementById( 'sticky-nav-wrapper' );
	var mobileToggles = document.querySelectorAll( '.mobile-menu-toggle, .mobile-menu-toggle-sticky' );

	function closeMobileNav() {
		if ( siteNav )    siteNav.classList.remove( 'nav-open' );
		if ( navWrapper ) navWrapper.classList.remove( 'nav-open' );
		mobileToggles.forEach( function ( b ) { b.setAttribute( 'aria-expanded', 'false' ); } );
	}

	mobileToggles.forEach( function ( btn ) {
		btn.addEventListener( 'click', function () {
			if ( ! siteNav ) return;
			var isOpen = siteNav.classList.toggle( 'nav-open' );
			if ( navWrapper ) navWrapper.classList.toggle( 'nav-open', isOpen );
			this.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
		} );
	} );

	/* Close nav when a menu link is tapped on mobile */
	if ( siteNav ) {
		siteNav.querySelectorAll( 'a' ).forEach( function ( link ) {
			link.addEventListener( 'click', function () {
				if ( window.innerWidth <= 767 ) { closeMobileNav(); }
			} );
		} );
	}

	/* ─── Search Overlay ─────────────────────────────────────────────────── */
	var searchToggle  = document.querySelector( '.search-toggle' );
	var searchOverlay = document.querySelector( '.search-overlay' );
	var searchClose   = document.querySelector( '.search-close' );
	var searchInput   = searchOverlay ? searchOverlay.querySelector( 'input[type="search"]' ) : null;

	if ( searchToggle && searchOverlay ) {
		searchToggle.addEventListener( 'click', function () {
			searchOverlay.classList.add( 'is-open' );
			if ( searchInput ) { searchInput.focus(); }
		} );
	}

	function closeSearch() {
		if ( searchOverlay ) { searchOverlay.classList.remove( 'is-open' ); }
	}

	if ( searchClose ) {
		searchClose.addEventListener( 'click', closeSearch );
	}

	document.addEventListener( 'keydown', function ( e ) {
		if ( e.key === 'Escape' ) { closeSearch(); }
	} );

	/* ─── Reading Progress Bar ───────────────────────────────────────────── */
	var progressBar = document.getElementById( 'reading-progress' );

	if ( progressBar ) {
		window.addEventListener( 'scroll', function () {
			var scrolled = window.scrollY;
			var total    = document.documentElement.scrollHeight - window.innerHeight;
			var pct      = total > 0 ? Math.min( 100, ( scrolled / total ) * 100 ) : 0;
			progressBar.style.width = pct + '%';
		}, { passive: true } );
	}

	/* ─── Back to Top ────────────────────────────────────────────────────── */
	var backToTop = document.getElementById( 'back-to-top' );

	if ( backToTop ) {
		window.addEventListener( 'scroll', function () {
			backToTop.style.display = window.scrollY > 300 ? 'flex' : 'none';
		}, { passive: true } );

		backToTop.addEventListener( 'click', function () {
			window.scrollTo( { top: 0, behavior: 'smooth' } );
		} );
	}

}() );
