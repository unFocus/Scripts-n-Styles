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
		// Keep track of current tab.
		add_action( 'wp_ajax_update-current-sns-tab', array( __CLASS__, 'update_current_sns_tab' ) );
		// TinyMCE requests a css file.
		add_action( 'wp_ajax_sns-tinymce-styles-ajax', array( __CLASS__, 'sns_tinymce_styles_ajax' ) );
		// Ajax Saves.
		add_action( 'wp_ajax_sns-classes-ajax', array( __CLASS__, 'sns_classes_ajax' ) );
		add_action( 'wp_ajax_sns-dropdown-ajax', array( __CLASS__, 'sns_dropdown_ajax' ) );
	}
	function sns_classes_ajax() {
		check_ajax_referer( Scripts_n_Styles::$file );
		if ( ! current_user_can( 'unfiltered_html' ) || ! current_user_can( 'edit_posts' ) ) die( 'Insufficient Privileges' );

		header('Content-Type: application/json; charset=' . get_option('blog_charset'));
		$post_id 		= isset( $_REQUEST[ 'post_id' ] ) ? (int)$_REQUEST[ 'post_id' ] : 0;
		$classes_body 	= isset( $_REQUEST[ 'uFp_classes_body' ] ) ? $_REQUEST[ 'uFp_classes_body' ] : '';
		$classes_post 	= isset( $_REQUEST[ 'uFp_classes_post' ] ) ? $_REQUEST[ 'uFp_classes_post' ] : '';
		
		$styles = get_post_meta( $post_id, 'uFp_styles', true );
		
		$styles[ 'classes_body' ] = $classes_body;
		$styles[ 'classes_post' ] = $classes_post;
		
		update_post_meta( $post_id, 'uFp_styles', $styles );
		
		echo json_encode( array(
			//"styles" => $styles,
			"classes_post" => $classes_post,
			"classes_body" => $classes_body
		) );
		
		die();
		break;
	}
	function sns_dropdown_ajax() {
		check_ajax_referer( Scripts_n_Styles::$file );
		if ( ! current_user_can( 'unfiltered_html' ) || ! current_user_can( 'edit_posts' ) ) return;

		header('Content-Type: application/json; charset=' . get_option('blog_charset'));
		$post_id = isset( $_REQUEST[ 'post_id' ] ) ? (int)$_REQUEST[ 'post_id' ] : 0;
		
		$styles = get_post_meta( $post_id, 'uFp_styles', true );
		$classes_mce = $styles[ 'classes_mce' ];
		
		if ( ! isset( $classes_mce ) )
			$classes_mce = array();
		
		// Logic: Label, Element and Name are required, Type and Wrap are optional.
		if ( ! empty( $_REQUEST[ 'uFp_classes_mce_label' ] )
			&& ! empty( $_REQUEST[ 'uFp_classes_mce_element' ] )
			&& ! empty( $_REQUEST[ 'uFp_classes_mce_name' ] )
		) {
			$label = $_REQUEST[ 'uFp_classes_mce_label' ];
			$element = $_REQUEST[ 'uFp_classes_mce_element' ];
			$name = sanitize_title_with_dashes( $_REQUEST[ 'uFp_classes_mce_name' ] );
			
			if ( isset( $_REQUEST[ 'uFp_classes_mce_type' ] ) && 'block' == $_REQUEST[ 'uFp_classes_mce_type' ] )
				$type = 'block';
			else if ( isset( $_REQUEST[ 'uFp_classes_mce_type' ] ) && 'inline' == $_REQUEST[ 'uFp_classes_mce_type' ] )
				$type = 'inline';
			else
				$type = 'selector';
			
			$wrap = ( isset( $_REQUEST[ 'uFp_classes_mce_wrap' ] ) && 'block' == $type ) ? true: false;
			
			$mce_class = array();
			$mce_class[ 'type' ] = $type;
			$mce_class[ 'element' ] = $element;
			$mce_class[ 'name' ] = $name;
			$mce_class[ 'wrap' ] = $wrap;
			
			$classes_mce[ $label ] = $mce_class;
		}
		$styles[ 'classes_mce' ] = $classes_mce;
		
		update_post_meta( $post_id, 'uFp_styles', $styles );
		
		echo json_encode( array(
			//"styles" => $styles,
			"classes_mce" => (array)$classes_mce
		) );
		
		die();
		break;
	}
	function sns_tinymce_styles_ajax() {
		check_ajax_referer( 'sns-tinymce-styles-ajax' );
		
		$postid = isset( $_REQUEST[ 'postid' ] ) ? (int)$_REQUEST[ 'postid' ] : 0;
		
		if ( 0 == $postid )
			die( 'Bad Post ID' );
		
		$options = get_option( 'SnS_options' );
		$styles = get_post_meta( $postid, 'uFp_styles', true );
		
		/*header('Content-Type: text/css; charset=' . get_option('blog_charset'));
		header("Cache-Control: no-cache");
		header("Pragma: no-cache");
		session_cache_limiter( 'nocache' );*/
		
		if ( ! empty( $options ) && ! empty( $options[ 'styles' ] ) ) 
			echo $options[ 'styles' ];
		
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