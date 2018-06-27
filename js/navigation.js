/**
 * GutenSlides Navigation Block & Custom Sidebar
 */

/* global gutenPrezStrings */
( function( wp ) {
	var el = wp.element.createElement,
		registerBlockType = wp.blocks.registerBlockType,
		Fragment = wp.element.Fragment,
		PanelBody = wp.components.PanelBody,
		PanelRow = wp.components.PanelRow,
		PluginSidebar = wp.editPost.PluginSidebar,
		PluginSidebarMoreMenuItem = wp.editPost.PluginSidebarMoreMenuItem,
		registerPlugin = wp.plugins.registerPlugin,
		postData = wp.data;

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
			var elements = [],
				currentItem = parseInt( gutenPrezStrings.nav.current, 10 );

			if ( 1 >= gutenPrezStrings.nav.links.length ) {
				elements.push( el( 'p', { key: 'noitems' }, gutenPrezStrings.nav.nonav ) );
			} else if ( 3 === gutenPrezStrings.nav.links.length ) {
				elements.push( el( 'a', {
					key: 'prev',
					className: 'prev',
					href: gutenPrezStrings.nav.links[ 0 ].url,
				}, gutenPrezStrings.nav.prev ) );
				elements.push( el( 'a', {
					key: 'next',
					className: 'next',
					href: gutenPrezStrings.nav.links[ 2 ].url,
				}, gutenPrezStrings.nav.next ) );
			} else if ( currentItem === gutenPrezStrings.nav.links[ 0 ].id ) {
				elements.push( el( 'a', {
					key: 'next',
					className: 'next',
					href: gutenPrezStrings.nav.links[ 1 ].url,
				}, gutenPrezStrings.nav.next ) );
			} else {
				elements.push( el( 'a', {
					key: 'prev',
					className: 'prev',
					href: gutenPrezStrings.nav.links[ 0 ].url,
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
		},
	} );

	function gutenprezSidebar() {
		var postID = postData.select( 'core/editor' ).getCurrentPostId(),
			editLinks = [];

		if ( ! gutenPrezStrings.plan.titles.length ) {
			editLinks.push(
				el( PanelRow,
					{
						key: 'noplan',
					},
					gutenPrezStrings.plan.noplan
				)
			);
		} else {
			gutenPrezStrings.plan.titles.forEach( function( t ) {
				editLinks.push(
					el( PanelRow,
						{
							key: 'chapter-' + t.id,
						},
						( postID === t.id ) ? t.title : el( 'a', { href: t.url }, t.title )
					)
				);
			} );
		}

		return el(
			Fragment,
			{},
			el(
				PluginSidebarMoreMenuItem,
				{
					target: 'gutenprez/plan',
				},
				gutenPrezStrings.plan.planTitle
			),
			el(
				PluginSidebar,
				{
					name: 'gutenprez/plan',
					title: gutenPrezStrings.plan.planTitle,
				},
				el(
					PanelBody,
					{},
					editLinks
				)
			)
		);
	}

	registerPlugin( 'gutenprez-sidebar', {
		icon: 'leftright',
		render: gutenprezSidebar,
	} );
}( window.wp || {} ) );
