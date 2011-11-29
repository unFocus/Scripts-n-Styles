<?php
/**
 * SnS_Settings_Page
 * 
 * Allows WordPress admin users the ability to add custom CSS
 * and JavaScript directly to individual Post, Pages or custom
 * post types.
 */
		
class SnS_Settings_Page
{
    /**
     * Constants
     */
	const MENU_SLUG = 'sns_settings';
	
    /**
	 * Initializing method.
     * @static
     */
	function init() {
		$hook_suffix = add_submenu_page( SnS_Admin::$parent_slug, 'Scripts n Styles', 'Settings', 'unfiltered_html', self::MENU_SLUG, 'SnS_Form::page' );
		
		add_action( "load-$hook_suffix", array( __CLASS__, 'admin_load' ) );
		add_action( "load-$hook_suffix", 'SnS_Admin::help' );
		add_action( "load-$hook_suffix", 'SnS_Form::take_action', 49 );
		
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

	
    /**
	 * Settings Page
	 * Adds Admin Menu Item via WordPress' "Administration Menus" API. Also hook actions to register options via WordPress' Settings API.
     */
	function admin_load() {
		wp_enqueue_style( 'sns-options-styles', plugins_url('css/options-styles.css', Scripts_n_Styles::$file), array(), SnS_Admin::VERSION );
		
		register_setting(
			SnS_Admin::OPTION_GROUP,
			'SnS_options' );
		
		add_settings_section(
			'settings',
			'Scripts n Styles Settings',
			array( __CLASS__, 'settings_section' ),
			SnS_Admin::MENU_SLUG );
		
		add_settings_field(
			'menu_position',
			'<strong>Menu Position</strong>: ',
			array( 'SnS_Form', 'select' ),
			SnS_Admin::MENU_SLUG,
			'settings',
			array(
				'label_for' => 'menu_position',
				'setting' => 'SnS_options',
				'choices' => array( 'menu', 'object', 'utility', 'tools.php', 'options-general.php', 'themes.php' ),
				'size' => 6,
				'style' => 'height: auto;'
			) );
	}
	
    /**
	 * Settings Page
	 * Outputs Description text for the Global Section.
	 */
	function settings_section() {
		?>
		<div style="max-width: 55em;">
			<p>Control how and where Scripts n Styles menus and metaboxes appear. These options are here because sometimes users really care about this stuff. Feel free to adjust to your liking. :-)</p>
		</div>
		<?php
	}
}
?>