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
	const OPTION_GROUP = 'scripts_n_styles';
	static $hook_suffix = '';
	
    /**
	 * Initializing method.
     * @static
     */
	function init() {
		if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'unfiltered_html' ) ) return;
		
		self::$hook_suffix = $hook_suffix = add_submenu_page(
			SnS_Admin::MENU_SLUG,
			'Scripts n Styles Usage',
			'Usage',
			'unfiltered_html',
			SnS_Admin::MENU_SLUG.'_usage',
			array( __CLASS__, 'admin_page' )
		);

		add_action( "load-$hook_suffix", array( __CLASS__, 'admin_load' ) );
		add_action( "load-$hook_suffix", array( 'SnS_Admin', 'help' ) );
	}
	
    /**
	 * Settings Page
	 * Adds Admin Menu Item via WordPress' "Administration Menus" API. Also hook actions to register options via WordPress' Settings API.
     */
	function admin_load() {
		add_screen_option( 'per_page', array( 'label' => __( 'SnS Per Page' ), 'default' => 20 ) );
		add_filter( 'set-screen-option', array( __CLASS__, 'set_screen_option' ), 10, 3 );
		// hack for core limitation: see http://core.trac.wordpress.org/ticket/18954
		set_screen_options();
		
		register_setting(
			self::OPTION_GROUP,
			'SnS_options' );
			
		add_settings_section(
			'usage',
			'Scripts n Styles Usage',
			array( __CLASS__, 'usage_section' ),
			SnS_Admin::MENU_SLUG );
	}
	
	function set_screen_option( $false, $option, $value ) {
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
	function usage_section() {
		require_once( 'class.SnS_List_Usage.php' );
		$usageTable = new SnS_List_Usage();
		$usageTable->prepare_items();
		$usageTable->display();
	}

    /**
	 * Settings Page
	 * Outputs the Admin Page and calls the Settings registered with the Settings API in init_options_page().
     */
	function admin_page() {
		SnS_Admin::upgrade_check();
		global $title;
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2><?php echo esc_html($title); ?></h2>
			<?php settings_errors(); ?>
			<form action="" method="post" autocomplete="off">
			<?php settings_fields( self::OPTION_GROUP ); ?>
			<?php do_settings_sections( SnS_Admin::MENU_SLUG ); ?>
			<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
?>