<?php
/**
 * Scripts n Styles Admin Class
 * 
 * Allows WordPress admin users the ability to add custom CSS
 * and JavaScript directly to individual Post, Pages or custom
 * post types.
 */
 
require_once( 'class.SnS_Admin_Meta_Box.php' );
require_once( 'class.SnS_Settings_Page.php' );
require_once( 'class.SnS_AJAX.php' );

class SnS_Admin
{
    /**#@+
     * Constants
     */
	const MENU_SLUG = 'Scripts-n-Styles';
	const VERSION = '3.beta.2';
    /**#@-*/
	
    /**
	 * Initializing method.
     * @static
     */
	static function init() {
		add_action( 'admin_menu', array( 'SnS_Admin_Meta_Box', 'init' ) );
		
		add_action( 'admin_menu', array( 'SnS_Settings_Page', 'init' ) );
		
		add_action( 'admin_init', array( 'SnS_AJAX', 'init' ) );
		
		$plugin_file = plugin_basename( Scripts_n_Styles::$file ); 
		add_filter( "plugin_action_links_$plugin_file", array( __CLASS__, 'plugin_action_links') );
		
		register_activation_hook( Scripts_n_Styles::$file, array( __CLASS__, 'upgrade' ) );
	}
	
	
    /**
	 * Utility Method: Sets defaults if not previously set. Sets stored 'version' to VERSION.
     */
	static function upgrade() {
		$options = get_option( 'SnS_options' );
		if ( ! $options ) $options = array();
		$options[ 'version' ] = self::VERSION;
		update_option( 'SnS_options', $options );

		/*
		 * upgrade proceedure
		 */
		$posts = get_posts(
			array(
				'numberposts' => -1,
				'post_type' => 'any',
				'post_status' => 'any',
				'meta_query' => array(
					'relation' => 'OR',
					array( 'key' => 'uFp_scripts' ),
					array( 'key' => 'uFp_styles' )
				)
			)
		);
		
		foreach( $posts as $post) {
			$styles = get_post_meta( $post->ID, 'uFp_styles', true );
			if ( ! empty( $styles ) )
				update_post_meta( $post->ID, '_SnS_styles', $styles );
			delete_post_meta( $post->ID, 'uFp_styles' );
			
			$scripts = get_post_meta( $post->ID, 'uFp_scripts', true );
			if ( ! empty( $scripts ) )
				update_post_meta( $post->ID, '_SnS_scripts', $scripts );
			delete_post_meta( $post->ID, 'uFp_scripts' );
		}

	}
	
    /**
	 * Utility Method: Compares VERSION to stored 'version' value.
     */
	static function upgrade_check() {
		$options = get_option( 'SnS_options' );
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
	
}

?>