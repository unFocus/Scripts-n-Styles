<?php
/**
 * Scripts n Styles Admin Class
 * 
 * Allows WordPress admin users the ability to add custom CSS
 * and JavaScript directly to individual Post, Pages or custom
 * post types.
 */

class SnS_Admin
{
    /**#@+
     * Constants
     */
	const MENU_SLUG = 'Scripts-n-Styles';
	const VERSION = '3.alpha';
    /**#@-*/
	
    /**
	 * Initializing method.
     * @static
     */
	static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'admin_meta_box' ) );
		add_action( 'admin_menu', array( __CLASS__, 'settings_page' ) );
		add_action( 'admin_init', array( __CLASS__, 'ajax_handlers' ) );
		
		$plugin_file = plugin_basename( Scripts_n_Styles::$file ); 
		add_filter( "plugin_action_links_$plugin_file", array( __CLASS__, 'plugin_action_links') );
		
		register_activation_hook( Scripts_n_Styles::$file, array( __CLASS__, 'upgrade' ) );
	}
	
	function ajax_handlers() {
		add_action( 'wp_ajax_update-current-sns-tab', array( __CLASS__, 'update_current_sns_tab' ) );
		add_action( 'wp_ajax_sns-tinymce-styles-ajax', array( __CLASS__, 'sns_tinymce_styles_ajax' ) );
	}
	function sns_tinymce_styles_ajax() {
		check_ajax_referer( 'sns-tinymce-styles-ajax' );
		
		$postid = isset( $_REQUEST[ 'postid' ] ) ? (int)$_REQUEST[ 'postid' ] : 0;
		
		if ( 0 == $postid )
			die( 'Bad Post ID' );
		
		$options = get_option( 'SnS_options' );
		if ( ! empty( $options ) && ! empty( $options[ 'styles' ] ) ) 
			echo $options[ 'styles' ];
		
		$styles = get_post_meta( $postid, 'uFp_styles', true );
		if ( ! empty( $styles ) && ! empty( $styles[ 'styles' ] ) ) 
			echo $styles[ 'styles' ];
		
		die();
		break;
	}
	function update_current_sns_tab() {
		check_ajax_referer( Scripts_n_Styles::$file );
		
		$active_tab = isset( $_POST[ 'active_tab' ] ) ? (int)$_POST[ 'active_tab' ] : 0;
		$page = isset( $_POST[ 'page' ] ) ? $_POST[ 'page' ] : '';
		
		if ( !preg_match( '/^[a-z_-]+$/', $page ) )
			die( 'Bad Page' );
		if ( ! $user = wp_get_current_user() )
			die( 'Bad User' );
		
		$success = update_user_option( $user->ID, "update-current-sns-tab_$page", $active_tab, true);
		die( $success );
		break;
	}
	
    /**
	 * Utility Method: Sets defaults if not previously set. Sets stored 'version' to VERSION.
     */
	static function upgrade() {
		$options = get_option( 'SnS_options' );
		$options[ 'version' ] = self::VERSION;
		update_option( 'SnS_options', $options );
	}
	
    /**
	 * Utility Method: Compares VERSION to stored 'version' value.
     */
	static function upgrade_check() {
		if ( ! isset( $options[ 'version' ] ) || version_compare( self::VERSION, $options[ 'version' ], '>' ) )
			self::upgrade();
	}
	
    /**
	 * Adds link to the Settings Page in the WordPress "Plugin Action Links" array.
	 * @param array $actions
	 * @return array
     */
	static function plugin_action_links( $actions ) {
		$actions[ 'settings' ] = '<a href="' . menu_page_url( self::MENU_SLUG, false ) . '"/>Settings</a>';
		return $actions;
	}
	
    /**
	 * Settings Page
	 * Adds Admin Menu Item via WordPress' "Administration Menus" API. Also hook actions to register options via WordPress' Settings API.
     */
	static function admin_meta_box() {
		include_once( 'class.SnS_Admin_Meta_Box.php' );
		SnS_Admin_Meta_Box::init();
	}
	
    /**
	 * Settings Page
	 * Adds Admin Menu Item via WordPress' "Administration Menus" API. Also hook actions to register options via WordPress' Settings API.
     */
	static function settings_page() {
		/* NOTE: Even when Scripts n Styles is not restricted by 'manage_options', Editors still can't submit the option page */
		include_once( 'class.SnS_Settings_Page.php' );
		SnS_Settings_Page::init();
	}
}

?>