<?php
namespace unFocus\SnS;

/**
 * Scripts n Styles Admin
 *
 * Allows WordPress admin users the ability to add custom CSS
 * and JavaScript directly to individual Post, Pages or custom
 * post types.
 */

add_action( 'admin_menu', array( '\unFocus\SnS\Admin_Meta_Box', 'init' ) );

add_action( 'admin_menu', function() {
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'unfiltered_html' ) ) return;

	Plugin_Editor_Page::init();
	Theme_Editor_Page::init();
	Global_Page::init();
	Hoops_Page::init();
	if ( current_theme_supports( 'scripts-n-styles' ) )
		Theme_Page::init();
	Settings_Page::init();
	Usage_Page::init();
});

add_action( 'admin_init', function() {
	load_plugin_textdomain( 'scripts-n-styles', false, dirname( BASENAME ) . '/languages/' );
} );

/**
 * Adds link to the Settings Page in the WordPress "Plugin Action Links" array.
 * @param array $actions
 * @return array
 */
add_filter( 'plugin_action_links_'.BASENAME, function( $actions ) {
	$actions[ 'settings' ] = '<a href="' . menu_page_url( Settings_Page::MENU_SLUG, false ) . '"/>' . __( 'Settings' ) . '</a>';
	return $actions;
} );