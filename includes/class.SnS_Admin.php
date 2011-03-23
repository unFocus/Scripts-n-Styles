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
	const VERSION = '1.0.3-alpha';
    /**#@-*/
	
    /**
	 * Initializing method.
     * @static
     */
	static function init() {
		add_action( 'admin_menu', array( __class__, 'admin_meta_box' ) );
		add_action( 'admin_menu', array( __class__, 'settings_page' ) );
		
		$plugin_file = plugin_basename( Scripts_n_Styles::$file ); 
		add_filter( "plugin_action_links_$plugin_file", array( __class__, 'plugin_action_links') );
		
		self::upgrade_check();
		//register_activation_hook( Scripts_n_Styles::$file, array( __class__, 'activation' ) );
	}
	
    /**
	 * Utility Method: Sets default 'restrict' and 'show_meta_box' if not previously set. Sets stored 'version' to VERSION.
     */
	static function upgrade() {
		$sns_options = Scripts_n_Styles::get_options();
		if ( ! isset( $sns_options[ 'show_meta_box' ] ) )
			$sns_options['show_meta_box' ] = 'yes';
		if ( ! isset( $sns_options[ 'restrict' ] ) )
			$sns_options[ 'restrict' ] = 'yes';
		$sns_options[ 'version' ] = self::VERSION;
		update_option( 'sns_options', $sns_options );
	}
	
    /**
	 * Utility Method: Compares VERSION to stored 'version' value.
     */
	static function upgrade_check() {
		$sns_options = Scripts_n_Styles::get_options();
		if ( ! isset( $sns_options[ 'version' ] ) || version_compare( self::VERSION, $sns_options[ 'version' ], '>' ) )
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
		/* NOTE: Even when Scripts n Styles is not restricted by 'manage_options', Editors still can't submit the option page */
		
		/*add_action( 'add_meta_boxes', array( __class__, 'add_meta_boxes' ) );
		add_action( 'save_post', array( __class__, 'save_post' ) );*/
		
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