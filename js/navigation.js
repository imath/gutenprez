/**
 * GutenSlides Navigation Block
 */

/* global gutenPrezStrings */
( function( wp ) {
	var el                = wp.element.createElement,
	    registerBlockType = wp.blocks.registerBlockType;

	registerBlockType( 'gutenprez/navigation', {

		// Block Title
		title: gutenPrezStrings.nav.title,

		// Block Icon
		icon: 'leftright',

		// Block Category
		category: 'layout',

		// Use only once per GutenSlide
		useOnce: true,

		edit: function() {
			var elements = [], currentItem = parseInt( gutenPrezStrings.nav.current, 10 );

			if ( ! gutenPrezStrings.nav.links.length ) {
				elements.push( el( 'p', { key: 'noitems' }, gutenPrezStrings.nav.nonav ) );
			} else if ( 3 === gutenPrezStrings.nav.links.length ) {
				elements.push( el( 'a', {
					key: 'prev',
					className: 'prev',
					href: gutenPrezStrings.nav.links[0].url,
				}, gutenPrezStrings.nav.prev ) );
				elements.push( el( 'a', {
					key: 'next',
					className: 'next',
					href: gutenPrezStrings.nav.links[2].url,
				}, gutenPrezStrings.nav.next ) );
			} else if ( currentItem === gutenPrezStrings.nav.links[0].id ) {
				elements.push( el( 'a', {
					key: 'next',
					className: 'next',
					href:    gutenPrezStrings.nav.links[1].url,
				}, gutenPrezStrings.nav.next ) );
			} else {
				elements.push( el( 'a', {
					key: 'prev',
					className: 'prev',
					href: gutenPrezStrings.nav.links[0].url,
				}, gutenPrezStrings.nav.prev ) );
			}

			return el(
				'div', {
					className: 'gutenprez-navigation',
				}, elements
			);
		},

		save: function() {
			return null;
		}
	} );

} )( window.wp || {} );
