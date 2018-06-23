<?php
/**
 * GutenPrez functions.
 *
 * @package GutenPrez\inc
 *
 * @since  1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Get plugin's version.
 *
 * @since  1.0.0
 *
 * @return string the plugin's version.
 */
function gutenprez_version() {
	return gutenprez()->version;
}

/**
 * Get the plugin's JS Url.
 *
 * @since  1.0.0
 *
 * @return string the plugin's JS Url.
 */
function gutenprez_js_url() {
	return gutenprez()->js_url;
}

/**
 * Get the plugin's Assets Url.
 *
 * @since  1.0.0
 *
 * @return string the plugin's Assets Url.
 */
function gutenprez_assets_url() {
	return gutenprez()->assets_url;
}

/**
 * Get the JS minified suffix.
 *
 * @since  1.0.0
 *
 * @return string the JS minified suffix.
 */
function gutenprez_min_suffix() {
	$min = '.min';

	if ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG )  {
		$min = '';
	}

	/**
	 * Filter here to edit the minified suffix.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $min The minified suffix.
	 */
	return apply_filters( 'gutenprez_min_suffix', $min );
}

/**
 * Registers Things!
 *
 * @since 1.0.0
 */
function gutenprez_register() {
	$min = gutenprez_min_suffix();
	$v   = gutenprez_version();

	/** JavaScripts **********************************************************/
	$url = gutenprez_js_url();

	$scripts = apply_filters( 'gutenprez_register_javascripts', array(
		'gutenprez-navigation' => array(
			'location' => sprintf( '%1$snavigation%2$s.js', $url, $min ),
			'deps'     => array( 'wp-blocks', 'wp-element', 'wp-edit-post' ),
		),
	), $url, $min, $v );

	foreach ( $scripts as $js_handle => $script ) {
		$in_footer = false;

		if ( isset( $script['footer'] ) ) {
			$in_footer = $script['footer'];
		}

		wp_register_script( $js_handle, $script['location'], $script['deps'], $v, $in_footer );
	}

	/** Style ****************************************************************/

	wp_register_style( 'gutenprez',
		sprintf( '%1$sblocks%2$s.css', gutenprez_assets_url(), $min ),
		array( 'wp-blocks' ),
		$v
	);

	/** Post Type ***************************************************************/
	register_post_type( 'gutenslides', array(
			'public'              => true,
			'exclude_from_search' => true,
			'show_in_nav_menus'   => false,
			'menu_icon'           => 'dashicons-analytics',
			'supports'            => array(
				'title',
				'editor',
				'author',
				'revisions',
				'page-attributes'
			),
			'delete_with_user'    => true,
			'can_export'          => true,
			'show_in_rest'        => true,
			'label'               => __( 'Gutenslides', 'gutenprez' ),
			'labels'              => array(
				'menu_name'     => _x( 'GutenPrez', 'Main Plugin menu', 'gutenprez' ),
				'singular_name' => __( 'Gutenslide',                    'gutenprez' ),
			),
			'hierarchical'        => true,
			'template'            => array(
				array( 'core/cover-image' ),
				array( 'core/list' ),
				array( 'gutenprez/navigation' ),
			),
	) );
}
add_action( 'init', 'gutenprez_register', 12 );

/**
 * l10n for GutenPrez.
 *
 * @since 1.0.0
 * @since 1.1.0 Prepare titles for the TOC.
 *
 * @return  array The GutenPrez l10n strings.
 */
function gutenprez_l10n() {
	$current_slide = get_post();
	$edit_links = array();
	$titles = array();

	if ( 'publish' === get_post_status( $current_slide ) ) {
		$gutenslides = get_pages( array(
			'post_type'   => 'gutenslides',
			'sort_column' => 'menu_order',
		) );

		$edit_links = array();
		remove_filter( 'the_title', 'wptexturize' );

		foreach ( $gutenslides as $gutenslide ) {
			$title = get_the_title( $gutenslide );
			$edit_links[ $gutenslide->ID ] = (object) array(
				'id'  => $gutenslide->ID,
				'url' => get_edit_post_link( $gutenslide->ID, 'raw' ),
			);

			$titles[ $gutenslide->ID ] = clone $edit_links[ $gutenslide->ID ];
			$titles[ $gutenslide->ID ]->title = $title;
		}

		add_filter( 'the_title', 'wptexturize' );

		$count = count( $edit_links );
		if ( 2 <= count( $edit_links ) && isset(  $edit_links[ $current_slide->ID ] ) ) {
			$keys        = array_keys( $edit_links );
			$current_key = array_search( $current_slide->ID, $keys, true );

			if ( 0 === $current_key ) {
				$edit_links = array_slice( $edit_links, $current_key, 2, true );
			} elseif ( $count - 1 === $current_key ) {
				$edit_links = array_slice( $edit_links, $current_key - 1, 2, true );
			} else {
				$edit_links = array_slice( $edit_links, $current_key - 1, 3, true );
			}
		}
	}

	return array(
		'nav' => array(
			'title'   => _x( 'Navigation', 'Nav Block Title',    'gutenprez' ),
			'prev'    => _x( 'Précédent',  'Nav Block Previous', 'gutenprez' ),
			'next'    => _x( 'Suivant',    'Nav Block Next',     'gutenprez' ),
			'links'   => array_values( $edit_links ),
			'current' => $current_slide->ID,
			'nonav'   => _x( 'Aucune navigation disponible.', 'Nav Block No available nav', 'gutenprez' ),
		),
		'plan' => array(
			'planTitle' => _x( 'Plan de la présentation', 'Nav Sidebar TOC title', 'gutenprez' ),
			'noplan'    => _x( 'Aucun plan disponible.', 'Nav Sidebar No available TOC', 'gutenprez' ),
			'titles'    => array_values( $titles ),
		)
	);
}

/**
 * Enqueues the Gutenberg blocks script and style.
 *
 * @since 1.0.0
 */
function gutenprez_editor() {
	$current_slide = get_post();

	if ( 'gutenslides' !== get_post_type( $current_slide ) ) {
		return;
	}

	// Enqueue style
	wp_enqueue_style( 'gutenprez' );

	// List of blocks for Gutenslides.
	$blocks = array( 'gutenprez-navigation' );

	foreach ( $blocks as $block ) {
		wp_enqueue_script( $block );
	}

	$handle = reset( $blocks );
	wp_localize_script( $handle, 'gutenPrezStrings', gutenprez_l10n() );
}
add_action( 'enqueue_block_editor_assets', 'gutenprez_editor' );

/**
 * Loads translation.
 *
 * @since 1.0.0
 */
function gutenprez_load_textdomain() {
	$g = gutenprez();

	load_plugin_textdomain( $g->domain, false, trailingslashit( basename( $g->dir ) ) . 'languages' );
}
add_action( 'plugins_loaded', 'gutenprez_load_textdomain', 9 );
