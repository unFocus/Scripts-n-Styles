<?php
namespace unFocus\SnS;

// Add menu to admin bar
add_action( 'wp_before_admin_bar_render', function() {
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'unfiltered_html' ) ) return;
	global $wp_admin_bar;
	$wp_admin_bar->add_node( [
		'id'    => 'Scripts_n_Styles',
		'title' => 'Scripts n Styles',
		'href'  => admin_url( 'admin.php?page='.ADMIN_MENU_SLUG ),
		'meta'  => array( 'class' => 'Scripts_n_Styles' )
	] );
}, 11 );

// For things that don't belong in main or admin
add_action( 'plugins_loaded', function() {

	register_theme_directory( plugin_dir_path( _FILE_() ) . 'theme' );

	$options = get_option( 'SnS_options' );
	if ( ! isset( $options[ 'version' ] ) || version_compare( VERSION, $options[ 'version' ], '>' ) ) {
		upgrade();
	}
} );

/**
 * Sets defaults if not previously set. Sets stored 'version' to VERSION.
 */
function upgrade() {
	$options = get_option( 'SnS_options' );
	if ( ! $options ) $options = ['version' => '0'];

	$version = $options[ 'version' ];

	/*
	 * upgrade proceedure for 3.0 update
	 */
    if ( version_compare( $version, '3', '<' ) ) :
		$posts = get_posts(
			array(
				'numberposts' => -1,
				'post_type' => 'any',
				'post_status' => 'any',
				'meta_query' => array(
					'relation' => 'OR',
					array( 'key' => '_SnS_scripts' ),
					array( 'key' => '_SnS_styles' ),
					array( 'key' => 'uFp_scripts' ),
					array( 'key' => 'uFp_styles' )
				)
			)
		);

		if ( $posts ) :
		foreach( $posts as $post) {
			$styles = get_post_meta( $post->ID, '_SnS_styles', true );
			if ( empty( $styles ) )
				$styles = get_post_meta( $post->ID, 'uFp_styles', true );

			$scripts = get_post_meta( $post->ID, '_SnS_scripts', true );
			if ( empty( $scripts ) )
				$scripts = get_post_meta( $post->ID, 'uFp_scripts', true );

			$SnS = array();
			if ( ! empty( $styles ) )
				$SnS[ 'styles' ] = $styles;

			if ( ! empty( $scripts ) )
				$SnS[ 'scripts' ] = $scripts;

			if ( ! empty( $SnS ) )
				update_post_meta( $post->ID, '_SnS', $SnS );

			delete_post_meta( $post->ID, 'uFp_styles' );
			delete_post_meta( $post->ID, 'uFp_scripts' );
			delete_post_meta( $post->ID, '_SnS_styles' );
			delete_post_meta( $post->ID, '_SnS_scripts' );
		}
		endif; // if $posts

		$version = '3.0';
	endif; // 3.0 upgrade

	/*
	 * upgrade proceedure for 4.0 update
	 */
    if ( version_compare( $version, '4', '<' ) ) :
    	// There may yet be an upgrade routine.
		$version = '4.0';
	endif; // 4.0 upgrade

	$options[ 'version' ] = VERSION;
	update_option( 'SnS_options', $options );
}

function get_registered_scripts() {
	return [
	'utils', 'common', 'sack', 'quicktags', 'colorpicker', 'editor', 'wp-fullscreen', 'wp-ajax-response', 'wp-pointer', 'autosave',
	'heartbeat', 'wp-auth-check', 'wp-lists', 'prototype', 'scriptaculous-root', 'scriptaculous-builder', 'scriptaculous-dragdrop',
	'scriptaculous-effects', 'scriptaculous-slider', 'scriptaculous-sound', 'scriptaculous-controls', 'scriptaculous', 'cropper',
	'jquery', 'jquery-core', 'jquery-migrate', 'jquery-ui-core', 'jquery-effects-core', 'jquery-effects-blind', 'jquery-effects-bounce',
	'jquery-effects-clip', 'jquery-effects-drop', 'jquery-effects-explode', 'jquery-effects-fade', 'jquery-effects-fold',
	'jquery-effects-highlight', 'jquery-effects-pulsate', 'jquery-effects-scale', 'jquery-effects-shake', 'jquery-effects-slide',
	'jquery-effects-transfer', 'jquery-ui-accordion', 'jquery-ui-autocomplete', 'jquery-ui-button', 'jquery-ui-datepicker',
	'jquery-ui-dialog', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-menu', 'jquery-ui-mouse', 'jquery-ui-position',
	'jquery-ui-progressbar', 'jquery-ui-resizable', 'jquery-ui-selectable', 'jquery-ui-slider', 'jquery-ui-sortable',
	'jquery-ui-spinner', 'jquery-ui-tabs', 'jquery-ui-tooltip', 'jquery-ui-widget', 'jquery-form', 'jquery-color', 'suggest',
	'schedule', 'jquery-query', 'jquery-serialize-object', 'jquery-hotkeys', 'jquery-table-hotkeys', 'jquery-touch-punch',
	'jquery-masonry', 'thickbox', 'jcrop', 'swfobject', 'plupload', 'plupload-html5', 'plupload-flash', 'plupload-silverlight',
	'plupload-html4', 'plupload-all', 'plupload-handlers', 'wp-plupload', 'swfupload', 'swfupload-swfobject', 'swfupload-queue',
	'swfupload-speed', 'swfupload-all', 'swfupload-handlers', 'comment-reply', 'json2', 'underscore', 'backbone', 'wp-util',
	'wp-backbone', 'revisions', 'imgareaselect', 'mediaelement', 'wp-mediaelement', 'password-strength-meter', 'user-profile',
	'user-suggest', 'admin-bar', 'wplink', 'wpdialogs', 'wpdialogs-popup', 'word-count', 'media-upload', 'hoverIntent', 'customize-base',
	'customize-loader', 'customize-preview', 'customize-controls', 'accordion', 'shortcode', 'media-models', 'media-views',
	'media-editor', 'mce-view', 'less.js', 'coffeescript', 'chosen', 'coffeelint', 'mustache', 'html5shiv', 'html5shiv-printshiv',
	'google-diff-match-patch', 'codemirror' ];
}


/**
 * Settings Page help
 */
function help() {
	$help  = '<p>' . __( 'In default (non MultiSite) WordPress installs, both <em>Administrators</em> and <em>Editors</em> can access <em>Scripts-n-Styles</em> on individual edit screens. Only <em>Administrators</em> can access this Options Page. In MultiSite WordPress installs, only <em>"Super Admin"</em> users can access either <em>Scripts-n-Styles</em> on individual edit screens or this Options Page. If other plugins change capabilities (specifically "unfiltered_html"), other users can be granted access.', 'scripts-n-styles' ) . '</p>';
	$help .= '<p><strong>' . __( 'Reference: jQuery Wrappers', 'scripts-n-styles' ) . '</strong></p>'
		  . '<pre><code>jQuery(document).ready(function($) {' . PHP_EOL
		  . '	// $() will work as an alias for jQuery() inside of this function' . PHP_EOL
		  . '});</code></pre>';
	$help .= '<pre><code>(function($) {' . PHP_EOL
		  . '	// $() will work as an alias for jQuery() inside of this function' . PHP_EOL
		  . '})(jQuery);</code></pre>';
	$sidebar = '<p><strong>' . __( 'For more information:', 'scripts-n-styles' ) . '</strong></p>' .
				'<p>' . __( '<a href="http://wordpress.org/extend/plugins/scripts-n-styles/faq/" target="_blank">Frequently Asked Questions</a>', 'scripts-n-styles' ) . '</p>' .
				'<p>' . __( '<a href="https://github.com/unFocus/Scripts-n-Styles" target="_blank">Source on github</a>', 'scripts-n-styles' ) . '</p>' .
				'<p>' . __( '<a href="http://wordpress.org/tags/scripts-n-styles" target="_blank">Support Forums</a>', 'scripts-n-styles' ) . '</p>';
	$screen = get_current_screen();
	if ( method_exists( $screen, 'add_help_tab' ) ) {
		$screen->add_help_tab( array(
			'title' => __( 'Scripts n Styles', 'scripts-n-styles' ),
			'id' => 'scripts-n-styles',
			'content' => $help
			)
		);
		if ( 'post' != $screen->id )
			$screen->set_help_sidebar( $sidebar );
	} else {
		add_contextual_help( $screen, $help . $sidebar );
	}
}