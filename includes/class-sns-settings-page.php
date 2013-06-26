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
	static function init() {
		$hook_suffix = add_submenu_page( SnS_Admin::$parent_slug, __( 'Scripts n Styles', 'scripts-n-styles' ), __( 'Settings' ), 'unfiltered_html', self::MENU_SLUG, array( 'SnS_Form', 'page' ) );

		add_action( "load-$hook_suffix", array( __CLASS__, 'admin_load' ) );
		add_action( "load-$hook_suffix", array( 'SnS_Admin', 'help' ) );
		add_action( "load-$hook_suffix", array( 'SnS_Form', 'take_action' ), 49 );
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
		$options = get_option( 'SnS_options' );
		$cm_theme = isset( $options[ 'cm_theme' ] ) ? $options[ 'cm_theme' ] : '';

		wp_enqueue_style( 'sns-options' );

		wp_enqueue_script(  'sns-settings-page' );
		wp_localize_script( 'sns-settings-page', 'codemirror_options', array( 'theme' => $cm_theme ) );
	}

	/**
	 * Settings Page
	 * Adds Admin Menu Item via WordPress' "Administration Menus" API. Also hook actions to register options via WordPress' Settings API.
	 */
	static function admin_load() {
		wp_enqueue_style( 'sns-options' );

		register_setting(
			SnS_Admin::OPTION_GROUP,
			'SnS_options' );

		add_settings_section(
			'settings',
			__( 'Scripts n Styles Settings', 'scripts-n-styles' ),
			array( __CLASS__, 'settings_section' ),
			SnS_Admin::MENU_SLUG );

		add_settings_field(
			'metabox',
			__( '<strong>Hide Metabox by default</strong>: ', 'scripts-n-styles' ),
			array( 'SnS_Form', 'radio' ),
			SnS_Admin::MENU_SLUG,
			'settings',
			array(
				'label_for' => 'metabox',
				'setting' => 'SnS_options',
				'choices' => array( 'yes', 'no' ),
				'layout' => 'horizontal',
				'default' => 'yes',
				'legend' => __( 'Hide Metabox by default', 'scripts-n-styles' ),
				'description' => __( '<span class="description" style="max-width: 500px; display: inline-block;">This is overridable via Screen Options on each edit screen.</span>', 'scripts-n-styles' )
			) );

		add_settings_field(
			'menu_position',
			__( '<strong>Menu Position</strong>: ', 'scripts-n-styles' ),
			array( 'SnS_Form', 'radio' ),
			SnS_Admin::MENU_SLUG,
			'settings',
			array(
				'label_for' => 'menu_position',
				'setting' => 'SnS_options',
				'choices' => array( 'menu', 'object', 'utility', 'tools.php', 'options-general.php', 'themes.php' ),
				'default' => 'tools.php',
				'legend' => __( 'Theme', 'scripts-n-styles' ),
				'layout' => 'vertical',
				'description' => __( '<span class="description" style="max-width: 500px; display: inline-block;">Some people are fussy about where the menu goes, so I made an option.</span>', 'scripts-n-styles' ),
			) );

		add_settings_section(
			'demo',
			__( 'Code Mirror Demo', 'scripts-n-styles' ),
			array( __CLASS__, 'demo_section' ),
			SnS_Admin::MENU_SLUG );

		add_settings_field(
			'cm_theme',
			__( '<strong>Theme</strong>: ', 'scripts-n-styles' ),
			array( 'SnS_Form', 'radio' ),
			SnS_Admin::MENU_SLUG,
			'demo',
			array(
				'label_for' => 'cm_theme',
				'setting' => 'SnS_options',
				'choices' => Scripts_n_Styles::$cm_themes,
				'default' => 'default',
				'legend' => __( 'Theme', 'scripts-n-styles' ),
				'layout' => 'horizontal',
				'description' => '',
			) );
		add_settings_field(
			'hoops_widget',
			__( '<strong>Hoops Widgets</strong>: ', 'scripts-n-styles' ),
			array( 'SnS_Form', 'radio' ),
			SnS_Admin::MENU_SLUG,
			'settings',
			array(
				'label_for' => 'hoops_widget',
				'setting' => 'SnS_options',
				'choices' => array( 'yes', 'no' ),
				'layout' => 'horizontal',
				'default' => 'no',
				'legend' => __( 'Shortcode Widgets', 'scripts-n-styles' ),
				'description' => __( '<span class="description" style="max-width: 500px; display: inline-block;">This enables Hoops shortcodes to be used via a "Hoops" Text Widget.</span>', 'scripts-n-styles' )
			) );
	}

	/**
	 * Settings Page
	 * Outputs Description text for the Global Section.
	 */
	static function settings_section() {
		?>
		<div style="max-width: 55em;">
			<p><?php _e( 'Control how and where Scripts n Styles menus and metaboxes appear. These options are here because sometimes users really care about this stuff. Feel free to adjust to your liking. :-)', 'scripts-n-styles' ) ?></p>
		</div>
		<?php
	}

	/**
	 * Settings Page
	 * Outputs Description text for the Global Section.
	 */
	static function demo_section() {
		?>
		<div style="max-width: 55em;">
<textarea id="codemirror_demo" name="code" style="min-width: 500px; width:97%;" rows="5" cols="40">
<?php echo esc_textarea( '<?php
function hello($who) {
	return "Hello " . $who;
}
?>
<p>The program says <?= hello("World") ?>.</p>
<script>
	alert("And here is some JS code"); // also colored
</script>' ); ?>
</textarea>
		</div>
		<?php
	}
}
?>