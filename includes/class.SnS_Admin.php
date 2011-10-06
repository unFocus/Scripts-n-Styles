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
	const VERSION = '3.beta.2';
    /**#@-*/
	
    /**
	 * Initializing method.
     * @static
     */
	static function init() {
		require_once( 'class.SnS_Admin_Meta_Box.php' );
		add_action( 'admin_menu', array( 'SnS_Admin_Meta_Box', 'init' ) );
		
		require_once( 'class.SnS_Settings_Page.php' );
		add_action( 'admin_menu', array( 'SnS_Settings_Page', 'init' ) );
		
		add_action( 'admin_init', array( __CLASS__, 'ajax_handlers' ) );
		
		$plugin_file = plugin_basename( Scripts_n_Styles::$file ); 
		add_filter( "plugin_action_links_$plugin_file", array( __CLASS__, 'plugin_action_links') );
		
		register_activation_hook( Scripts_n_Styles::$file, array( __CLASS__, 'upgrade' ) );
	}
	
	function ajax_handlers() {
		// Keep track of current tab.
		add_action( 'wp_ajax_sns_update_tab', array( __CLASS__, 'update_tab' ) );
		// TinyMCE requests a css file.
		add_action( 'wp_ajax_sns_tinymce_styles', array( __CLASS__, 'tinymce_styles' ) );
		
		// Ajax Saves.
		add_action( 'wp_ajax_sns_classes', array( __CLASS__, 'classes' ) );
		add_action( 'wp_ajax_sns_scripts', array( __CLASS__, 'scripts' ) );
		add_action( 'wp_ajax_sns_styles', array( __CLASS__, 'styles' ) );
		add_action( 'wp_ajax_sns_dropdown', array( __CLASS__, 'dropdown' ) );
		add_action( 'wp_ajax_sns_delete_class', array( __CLASS__, 'delete_class' ) );
	}
	
	function update_tab() {
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
	function tinymce_styles() {
		check_ajax_referer( 'sns_tinymce_styles' );
		
		if ( ! isset( $_REQUEST[ 'post_id' ] ) || ! $_REQUEST[ 'post_id' ] ) exit( 'Bad post ID.' );
		$post_id = absint( $_REQUEST[ 'post_id' ] );
		
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
	
	// AJAX handlers
	function classes() {
		check_ajax_referer( Scripts_n_Styles::$file );
		if ( ! current_user_can( 'unfiltered_html' ) || ! current_user_can( 'edit_posts' ) ) exit( 'Insufficient Privileges.' );
		
		if ( ! isset( $_REQUEST[ 'post_id' ] ) || ! $_REQUEST[ 'post_id' ] ) exit( 'Bad post ID.' );
		if ( ! isset( $_REQUEST[ 'classes_body' ] ) || ! isset( $_REQUEST[ 'classes_post' ] ) ) exit( 'Data incorrectly sent.' );
		
		$post_id = absint( $_REQUEST[ 'post_id' ] );
		$styles = get_post_meta( $post_id, '_SnS_styles', true );
		
		$styles = self::maybe_set( $styles, 'classes_body' );
		$styles = self::maybe_set( $styles, 'classes_post' );
		
		self::maybe_update( $post_id, '_SnS_styles', $styles );
		
		header('Content-Type: application/json; charset=' . get_option('blog_charset'));
		echo json_encode( array(
			"classes_post" => $_REQUEST[ 'classes_post' ],
			"classes_body" => $_REQUEST[ 'classes_body' ]
		) );
		
		exit();
	}
	function scripts() {
		check_ajax_referer( Scripts_n_Styles::$file );
		if ( ! current_user_can( 'unfiltered_html' ) || ! current_user_can( 'edit_posts' ) ) exit( 'Insufficient Privileges.' );
		
		if ( ! isset( $_REQUEST[ 'post_id' ] ) || ! $_REQUEST[ 'post_id' ] ) exit( 'Bad post ID.' );
		if ( ! isset( $_REQUEST[ 'scripts' ] ) || ! isset( $_REQUEST[ 'scripts_in_head' ] ) ) exit( 'Data incorrectly sent.' );
		
		$post_id = absint( $_REQUEST[ 'post_id' ] );
		$scripts = get_post_meta( $post_id, '_SnS_scripts', true );
		
		$scripts = self::maybe_set( $scripts, 'scripts_in_head' );
		$scripts = self::maybe_set( $scripts, 'scripts' );
		
		self::maybe_update( $post_id, '_SnS_scripts', $scripts );
		
		header('Content-Type: application/json; charset=' . get_option('blog_charset'));
		echo json_encode( array(
			"scripts" => $_REQUEST[ 'scripts' ],
			"scripts_in_head" => $_REQUEST[ 'scripts_in_head' ],
		) );
		
		exit();
	}
	function styles() {
		check_ajax_referer( Scripts_n_Styles::$file );
		if ( ! current_user_can( 'unfiltered_html' ) || ! current_user_can( 'edit_posts' ) ) exit( 'Insufficient Privileges.' );
		
		if ( ! isset( $_REQUEST[ 'post_id' ] ) || ! $_REQUEST[ 'post_id' ] ) exit( 'Bad post ID.' );
		if ( ! isset( $_REQUEST[ 'styles' ] ) ) exit( 'Data incorrectly sent.' );
		
		$post_id = absint( $_REQUEST[ 'post_id' ] );
		$styles = get_post_meta( $post_id, '_SnS_styles', true );
		
		$styles = self::maybe_set( $styles, 'styles' );
		
		self::maybe_update( $post_id, '_SnS_styles', $styles );
		
		header('Content-Type: application/json; charset=' . get_option('blog_charset'));
		echo json_encode( array(
			"styles" => $_REQUEST[ 'styles' ],
		) );
		
		exit();
	}
	function dropdown() {
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
		$post_id = absint( $_REQUEST[ 'post_id' ] );
		
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
	function delete_class() {
		check_ajax_referer( Scripts_n_Styles::$file );
		if ( ! current_user_can( 'unfiltered_html' ) || ! current_user_can( 'edit_posts' ) ) exit( 'Insufficient Privileges.' );
		
		if ( ! isset( $_REQUEST[ 'post_id' ] ) || ! $_REQUEST[ 'post_id' ] ) exit( 'Bad post ID.' );
		$post_id = absint( $_REQUEST[ 'post_id' ] );
		$styles = get_post_meta( $post_id, '_SnS_styles', true );
		
		$title = $_REQUEST[ 'delete' ];
		
		if ( isset( $styles[ 'classes_mce' ][ $title ] ) ) unset( $styles[ 'classes_mce' ][ $title ] );
		else exit ( 'No Format of that name.' );
		
		if ( empty( $styles[ 'classes_mce' ] ) ) unset( $styles[ 'classes_mce' ] );
		
		self::maybe_update( $post_id, '_SnS_styles', $styles );
		
		if ( ! isset( $styles[ 'classes_mce' ] ) ) $styles[ 'classes_mce' ] = array( 'Empty' );
		
		header('Content-Type: application/json; charset=' . get_option('blog_charset'));
		echo json_encode( array(
			"classes_mce" => array_values( $styles[ 'classes_mce' ] )
		) );
		
		exit();
	}
	
	// Differs from SnS_Admin_Meta_Box::maybe_set() in that this needs no prefix.
	function maybe_set( $o, $i ) {
		if ( empty( $_REQUEST[ $i ] ) ) {
			if ( isset( $o[ $i ] ) ) unset( $o[ $i ] );
		} else $o[ $i ] = $_REQUEST[ $i ];
		return $o;
	}
	function maybe_update( $id, $name, $meta ) {
		if ( empty( $meta ) ) delete_post_meta( $id, $name );
		else update_post_meta( $id, $name, $meta );
	}
	
    /**
	 * Utility Method: Sets defaults if not previously set. Sets stored 'version' to VERSION.
     */
	static function upgrade() {
		$options = get_option( 'SnS_options' );
		if ( ! $option ) $option = array();
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
			if ( ! empty( $styles ) ) {
				update_post_meta( $post->ID, '_SnS_styles', $styles );
			}
			delete_post_meta( $post->ID, 'uFp_styles' );
			
			$scripts = get_post_meta( $post->ID, 'uFp_scripts', true );
			if ( ! empty( $scripts ) ) {
				update_post_meta( $post->ID, '_SnS_scripts', $scripts );
			}
			delete_post_meta( $post->ID, 'uFp_scripts' );
		}
		
		
		/*
		// ::TODO:: Combine multiple option
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
		if ( ! $options || ! isset( $options[ 'version' ] ) || version_compare( self::VERSION, $options[ 'version' ], '>' ) )
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