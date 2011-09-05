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
	const VERSION = '3.beta';
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
		add_action( 'wp_ajax_sns-update-scripts-ajax', array( __CLASS__, 'sns_update_scripts_ajax' ) );
		add_action( 'wp_ajax_sns-update-styles-ajax', array( __CLASS__, 'sns_update_styles_ajax' ) );
		add_action( 'wp_ajax_sns-dropdown-ajax', array( __CLASS__, 'sns_dropdown_ajax' ) );
		add_action( 'wp_ajax_sns-dropdown-delete-ajax', array( __CLASS__, 'sns_dropdown_delete_ajax' ) );
	}
	function sns_update_scripts_ajax() {
		check_ajax_referer( Scripts_n_Styles::$file );
		if ( ! current_user_can( 'unfiltered_html' ) || ! current_user_can( 'edit_posts' ) ) exit( 'Insufficient Privileges.' );
		
		if ( ! isset( $_REQUEST[ 'post_id' ] ) || ! $_REQUEST[ 'post_id' ] ) exit( 'Bad post ID.' );
		if ( ! isset( $_REQUEST[ 'uFp_scripts' ] ) || ! isset( $_REQUEST[ 'uFp_scripts_in_head' ] ) ) exit( 'Data incorrectly sent.' );
		
		$post_id = $_REQUEST[ 'post_id' ];
		$scripts = get_post_meta( $post_id, '_SnS_scripts', true );
		
		if ( empty( $_REQUEST[ 'uFp_scripts_in_head' ] ) ) unset( $scripts[ 'scripts_in_head' ] );
		else $scripts[ 'scripts_in_head' ] = $_REQUEST[ 'uFp_scripts_in_head' ];
		
		if ( empty( $_REQUEST[ 'uFp_scripts' ] ) ) unset( $scripts[ 'scripts' ] );
		else $scripts[ 'scripts' ] = $_REQUEST[ 'uFp_scripts' ];
		
		update_post_meta( $post_id, '_SnS_scripts', $scripts );
		
		if ( empty( $_REQUEST[ 'uFp_scripts' ] ) ) $scripts[ 'scripts' ] = '';
		if ( empty( $_REQUEST[ 'uFp_scripts_in_head' ] ) ) $scripts[ 'scripts_in_head' ] = '';
		
		header('Content-Type: application/json; charset=' . get_option('blog_charset'));
		echo json_encode( array(
			"scripts" => $scripts[ 'scripts' ],
			"scripts_in_head" => $scripts[ 'scripts_in_head' ],
		) );
		
		exit();
	}
	function sns_update_styles_ajax() {
		check_ajax_referer( Scripts_n_Styles::$file );
		if ( ! current_user_can( 'unfiltered_html' ) || ! current_user_can( 'edit_posts' ) ) exit( 'Insufficient Privileges.' );
		
		if ( ! isset( $_REQUEST[ 'post_id' ] ) || ! $_REQUEST[ 'post_id' ] ) exit( 'Bad post ID.' );
		if ( ! isset( $_REQUEST[ 'uFp_styles' ] ) ) exit( 'Data incorrectly sent.' );
		
		$post_id = $_REQUEST[ 'post_id' ];
		$styles = get_post_meta( $post_id, '_SnS_styles', true );
		
		if ( empty( $_REQUEST[ 'uFp_styles' ] ) ) unset( $styles[ 'styles' ] );
		else $styles[ 'styles' ] = $_REQUEST[ 'uFp_styles' ];
		
		update_post_meta( $post_id, '_SnS_styles', $styles );
		
		if ( empty( $_REQUEST[ 'uFp_styles' ] ) ) $styles[ 'styles' ] = '';
		
		header('Content-Type: application/json; charset=' . get_option('blog_charset'));
		echo json_encode( array(
			"styles" => $styles[ 'styles' ],
		) );
		
		exit();
	}
	function sns_classes_ajax() {
		check_ajax_referer( Scripts_n_Styles::$file );
		if ( ! current_user_can( 'unfiltered_html' ) || ! current_user_can( 'edit_posts' ) ) exit( 'Insufficient Privileges.' );
		
		if ( ! isset( $_REQUEST[ 'post_id' ] ) || ! $_REQUEST[ 'post_id' ] ) exit( 'Bad post ID.' );
		if ( ! isset( $_REQUEST[ 'uFp_classes_body' ] ) || ! isset( $_REQUEST[ 'uFp_classes_post' ] ) ) exit( 'Data incorrectly sent.' );
		
		$post_id = $_REQUEST[ 'post_id' ];
		$styles = get_post_meta( $post_id, '_SnS_styles', true );
		
		if ( empty( $_REQUEST[ 'uFp_classes_body' ] ) ) unset( $styles[ 'classes_body' ] );
		else $styles[ 'classes_body' ] = $_REQUEST[ 'uFp_classes_body' ];
		
		if ( empty( $_REQUEST[ 'uFp_classes_post' ] ) ) unset( $styles[ 'classes_post' ] );
		else $styles[ 'classes_post' ] = $_REQUEST[ 'uFp_classes_post' ];
		
		update_post_meta( $post_id, '_SnS_styles', $styles );
		
		if ( empty( $_REQUEST[ 'uFp_classes_body' ] ) ) $styles[ 'classes_body' ] = '';
		if ( empty( $_REQUEST[ 'uFp_classes_post' ] ) ) $styles[ 'classes_post' ] = '';
		
		header('Content-Type: application/json; charset=' . get_option('blog_charset'));
		echo json_encode( array(
			"classes_post" => $styles[ 'classes_post' ],
			"classes_body" => $styles[ 'classes_body' ]
		) );
		
		exit();
	}
	function sns_dropdown_ajax() {
		check_ajax_referer( Scripts_n_Styles::$file );
		if ( ! current_user_can( 'unfiltered_html' ) || ! current_user_can( 'edit_posts' ) ) exit( 'Insufficient Privileges.' );
		
		if ( ! isset( $_REQUEST[ 'format' ] ) || empty( $_REQUEST[ 'format' ] ) ) exit( 'Missing Format.' );
		if ( empty( $_REQUEST[ 'format' ][ 'title' ] ) ) exit( 'Title is required.' );
		if ( empty( $_REQUEST[ 'format' ][ 'classes' ] ) ) exit( 'Classes is required.' );
		if (
			empty( $_REQUEST[ 'format' ][ 'inline' ] ) &&
			empty( $_REQUEST[ 'format' ][ 'block' ] ) &&
			empty( $_REQUEST[ 'format' ][ 'selector' ] )
		) exit( 'A type is required.' );
		
		if ( ! isset( $_REQUEST[ 'post_id' ] ) || ! $_REQUEST[ 'post_id' ] ) exit( 'Bad post ID.' );
		$post_id = $_REQUEST[ 'post_id' ];
		
		$styles = get_post_meta( $post_id, '_SnS_styles', true );
		
		if ( ! isset( $styles[ 'classes_mce' ] ) )
			$styles[ 'classes_mce' ] = array();
		
		// pass title as key to be able to delete.
		$styles[ 'classes_mce' ][ $_REQUEST[ 'format' ][ 'title' ] ] = $_REQUEST[ 'format' ];
		
		update_post_meta( $post_id, '_SnS_styles', $styles );
		
		header('Content-Type: application/json; charset=' . get_option('blog_charset'));
		echo json_encode( array(
			"classes_mce" => array_values( $styles[ 'classes_mce' ] )
		) );
		
		exit();
	}
	function sns_dropdown_delete_ajax() {
		check_ajax_referer( Scripts_n_Styles::$file );
		if ( ! current_user_can( 'unfiltered_html' ) || ! current_user_can( 'edit_posts' ) ) exit( 'Insufficient Privileges.' );
		
		if ( ! isset( $_REQUEST[ 'post_id' ] ) || ! $_REQUEST[ 'post_id' ] ) exit( 'Bad post ID.' );
		$post_id = $_REQUEST[ 'post_id' ];
		$styles = get_post_meta( $post_id, '_SnS_styles', true );
		
		$title = $_REQUEST[ 'uFp_delete' ];
		
		if ( isset( $styles[ 'classes_mce' ][ $title ] ) ) unset( $styles[ 'classes_mce' ][ $title ] );
		else exit ( 'No Format of that name.' );
		
		update_post_meta( $post_id, '_SnS_styles', $styles );
		
		header('Content-Type: application/json; charset=' . get_option('blog_charset'));
		echo json_encode( array(
			"classes_mce" => array_values( $styles[ 'classes_mce' ] )
		) );
		
		exit();
	}
	function sns_tinymce_styles_ajax() {
		check_ajax_referer( 'sns-tinymce-styles-ajax' );
		
		if ( ! isset( $_REQUEST[ 'post_id' ] ) || ! $_REQUEST[ 'post_id' ] ) exit( 'Bad post ID.' );
		$post_id = $_REQUEST[ 'post_id' ];
		
		$options = get_option( 'SnS_options' );
		$styles = get_post_meta( $post_id, '_SnS_styles', true );
		
		header('Content-Type: text/css; charset=' . get_option('blog_charset'));
		/*header("Cache-Control: no-cache");
		header("Pragma: no-cache");
		session_cache_limiter( 'nocache' );*/
		
		if ( ! empty( $options ) && ! empty( $options[ 'styles' ] ) ) 
			echo $options[ 'styles' ];
		
		if ( ! empty( $styles ) && ! empty( $styles[ 'styles' ] ) ) 
			echo $styles[ 'styles' ];
		
		exit();
	}
	function update_current_sns_tab() {
		check_ajax_referer( Scripts_n_Styles::$file );
		
		$active_tab = isset( $_POST[ 'active_tab' ] ) ? (int)$_POST[ 'active_tab' ] : 0;
		
		if ( ! $user = wp_get_current_user() )
			exit( 'Bad User' );
		
		$success = update_user_option( $user->ID, "current-sns-tab", $active_tab, true);
		if ( $success )
			exit( 'Current Tab Updated. New value is ' . $active_tab );
		else
			exit( 'Current Tab Not Updated. It is possible that no change was needed.' );
	}
	
    /**
	 * Utility Method: Sets defaults if not previously set. Sets stored 'version' to VERSION.
     */
	static function upgrade() {
		$options = get_option( 'SnS_options' );
		$options[ 'version' ] = self::VERSION;
		update_option( 'SnS_options', $options );

		/*
		 * upgrade proceedure
		 */
		$sns_posts = get_posts(
			array(
				'numberposts' => -1,
				'post_type' => 'any',
				'post_status' => 'any',
				'meta_query' => array(
					array( 'key' => 'uFp_scripts' ),
					array( 'key' => 'uFp_styles' )
				)
			)
		);
		
		foreach( $sns_posts as $post) {
			$styles = get_post_meta( $post->ID, 'uFp_styles', true );
			$scripts = get_post_meta( $post->ID, 'uFp_scripts', true );
			if ( ! empty( $styles ) || ! empty( $scripts ) ) {
				update_post_meta( $post->ID, '_SnS_styles', $styles );
				update_post_meta( $post->ID, '_SnS_scripts', $scripts );
			}
			delete_post_meta( $post->ID, 'uFp_styles' );
			delete_post_meta( $post->ID, 'uFp_scripts' );
		}
		
		
		/*
		$enqueue_scripts = get_option( 'sns_enqueue_scripts' );
		delete_option('SnS_options');
		delete_option('sns_enqueue_scripts');
		*/

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
	
    /**
	 * Settings Page
	 * Adds Admin Menu Item via WordPress' "Administration Menus" API. Also hook actions to register options via WordPress' Settings API.
     */
	static function admin_meta_box() {
		require_once( 'class.SnS_Admin_Meta_Box.php' );
		SnS_Admin_Meta_Box::init();
	}
	
    /**
	 * Settings Page
	 * Adds Admin Menu Item via WordPress' "Administration Menus" API. Also hook actions to register options via WordPress' Settings API.
     */
	static function settings_page() {
		/* NOTE: Even when Scripts n Styles is not restricted by 'manage_options', Editors still can't submit the option page */
		require_once( 'class.SnS_Settings_Page.php' );
		SnS_Settings_Page::init();
	}
}

?>