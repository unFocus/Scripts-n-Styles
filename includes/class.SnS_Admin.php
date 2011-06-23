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
	const VERSION = '1.0.3-beta';
    /**#@-*/
	
    /**
	 * Initializing method.
     * @static
     */
	static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'admin_meta_box' ) );
		add_action( 'admin_menu', array( __CLASS__, 'settings_page' ) );
		
		$plugin_file = plugin_basename( Scripts_n_Styles::$file ); 
		add_filter( "plugin_action_links_$plugin_file", array( __CLASS__, 'plugin_action_links') );
		
		register_activation_hook( Scripts_n_Styles::$file, array( __CLASS__, 'upgrade' ) );
	}
	
    /**
	 * Utility Method: Sets default 'restrict' and 'show_meta_box' if not previously set. Sets stored 'version' to VERSION.
     */
	static function upgrade() {
		$options = Scripts_n_Styles::get_options();
		if ( ! isset( $options[ 'show_meta_box' ] ) )
			$options['show_meta_box' ] = 'yes';
		if ( ! isset( $options[ 'restrict' ] ) )
			$options[ 'restrict' ] = 'yes';
		if ( ! isset( $options[ 'show_usage' ] ) )
			$options[ 'show_usage' ] = 'no';
		$options[ 'version' ] = self::VERSION;
		update_option( Scripts_n_Styles::OPTION_PREFIX.'options', $options );
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