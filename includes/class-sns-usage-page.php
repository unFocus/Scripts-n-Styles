<?php
/**
 * SnS_Settings_Page
 *
 * Allows WordPress admin users the ability to add custom CSS
 * and JavaScript directly to individual Post, Pages or custom
 * post types.
 */

class SnS_Usage_Page
{
	/**
	 * Constants
	 */
	const MENU_SLUG = 'sns_usage';

	/**
	 * Initializing method.
	 * @static
	 */
	static function init() {
		$hook_suffix = add_submenu_page( SnS_Admin::$parent_slug, __( 'Scripts n Styles', 'scripts-n-styles' ), __( 'Usage', 'scripts-n-styles' ), 'unfiltered_html', self::MENU_SLUG, array( 'SnS_Form', 'page' ) );

		add_action( "load-$hook_suffix", array( __CLASS__, 'admin_load' ) );
		add_action( "load-$hook_suffix", array( 'SnS_Admin', 'help' ) );
		add_action( "admin_print_styles-$hook_suffix", array( __CLASS__, 'admin_enqueue_scripts' ) );

		// Make the page into a tab.
		if ( SnS_Admin::MENU_SLUG != SnS_Admin::$parent_slug ) {
			remove_submenu_page( SnS_Admin::$parent_slug, self::MENU_SLUG );
			add_filter( 'parent_file', array( __CLASS__, 'parent_file') );
		}
	}
	static function parent_file( $parent_file ) {
		global $plugin_page, $submenu_file;
		if ( self::MENU_SLUG == $plugin_page ) $submenu_file = SnS_Admin::MENU_SLUG;
		return $parent_file;
	}

	static function admin_enqueue_scripts() {
		wp_enqueue_style( 'sns-options' );
	}

	/**
	 * Settings Page
	 * Adds Admin Menu Item via WordPress' "Administration Menus" API. Also hook actions to register options via WordPress' Settings API.
	 */
	static function admin_load() {

		add_screen_option( 'per_page', array( 'label' => __( 'Per Page' ), 'default' => 20 ) );
		add_filter( 'set-screen-option', array( __CLASS__, 'set_screen_option' ), 10, 3 );
		// hack for core limitation: see http://core.trac.wordpress.org/ticket/18954
		set_screen_options();

		add_settings_section(
			'usage',
			__( 'Scripts n Styles Usage', 'scripts-n-styles' ),
			array( __CLASS__, 'usage_section' ),
			SnS_Admin::MENU_SLUG );
	}

	static function set_screen_option( $false, $option, $value ) {
		$screen_id = get_current_screen()->id;
		$this_option = str_replace( '-', '_', "{$screen_id}_per_page" );
		if ( $this_option != $option )
			return false;

		$value = (int) $value;
		if ( $value < 1 || $value > 999 )
			return false;

		return $value;
	}

	/**
	 * Settings Page
	 * Outputs the Usage Section.
	 */
	static function usage_section() { ?>
		<div style="max-width: 55em;">
			<p><?php _e( 'The following table shows content that utilizes Scripts n Styles.', 'scripts-n-styles' ) ?></p>
		</div>
		<?php
		require_once( 'class-sns-list-usage.php' );
		$usageTable = new SnS_List_Usage();
		$usageTable->prepare_items();
		$usageTable->display();
	}
}
?>