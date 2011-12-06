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
		$hook_suffix = add_submenu_page( SnS_Admin::$parent_slug, 'Scripts n Styles', 'Settings', 'unfiltered_html', self::MENU_SLUG, array( 'SnS_Form', 'page' ) );
		
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
	
	function admin_enqueue_scripts() {
		$options = get_option( 'SnS_options' );
		$cm_theme = isset( $options[ 'cm_theme' ] ) ? $options[ 'cm_theme' ] : 'default';
		
		wp_enqueue_style( 'sns-options-styles', plugins_url('css/options-styles.css', Scripts_n_Styles::$file), array( 'codemirror' ), SnS_Admin::VERSION );
		wp_enqueue_style( 'codemirror', plugins_url( 'libraries/CodeMirror2/lib/codemirror.css', Scripts_n_Styles::$file), array(), '2.18' );
		
		foreach ( array( 'cobalt', 'default', 'eclipse', 'elegant', 'monokai', 'neat', 'night', 'rubyblue' ) as $theme )
			wp_enqueue_style( "codemirror-$theme", plugins_url( "libraries/CodeMirror2/theme/$theme.css", Scripts_n_Styles::$file), array( 'codemirror' ), '2.18' );
		
		wp_enqueue_script( 'sns-settings-page-scripts', plugins_url('js/settings-page.js', Scripts_n_Styles::$file), array( 'jquery', 'codemirror-css', 'codemirror-javascript' ), SnS_Admin::VERSION, true );
		wp_localize_script( 'sns-settings-page-scripts', 'codemirror_options', array( 'theme' => $cm_theme ) );
		wp_enqueue_script( 'codemirror', plugins_url( 'libraries/CodeMirror2/lib/codemirror.js', Scripts_n_Styles::$file), array(), '2.18' );
		wp_enqueue_script( 'codemirror-css', plugins_url( 'libraries/CodeMirror2/mode/css/css.js', Scripts_n_Styles::$file), array( 'codemirror' ), '2.18' );
		wp_enqueue_script( 'codemirror-javascript', plugins_url( 'libraries/CodeMirror2/mode/javascript/javascript.js', Scripts_n_Styles::$file), array( 'codemirror' ), '2.18' );

		wp_enqueue_script( 'codemirror-xml', plugins_url( 'libraries/CodeMirror2/mode/xml/xml.js', Scripts_n_Styles::$file), array( 'codemirror' ), '2.18' );
		wp_enqueue_script( 'codemirror-clike', plugins_url( 'libraries/CodeMirror2/mode/clike/clike.js', Scripts_n_Styles::$file), array( 'codemirror' ), '2.18' );
		wp_enqueue_script( 'codemirror-php', plugins_url( 'libraries/CodeMirror2/mode/php/php.js', Scripts_n_Styles::$file), array( 'codemirror-xml', 'codemirror-css', 'codemirror-javascript', 'codemirror-clike' ), '2.18' );
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
		
		add_settings_field(
			'cm_theme',
			'<strong>CodeMirror Theme</strong>: ',
			array( 'SnS_Form', 'select' ),
			SnS_Admin::MENU_SLUG,
			'settings',
			array(
				'label_for' => 'cm_theme',
				'setting' => 'SnS_options',
				'choices' => array( 'cobalt', 'default', 'eclipse', 'elegant', 'monokai', 'neat', 'night', 'rubyblue' ),
				'size' => 8,
				'style' => 'height: auto;'
			) );
		
		add_settings_section(
			'demo',
			'Code Mirror Demo',
			array( __CLASS__, 'demo_section' ),
			SnS_Admin::MENU_SLUG );
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
	
    /**
	 * Settings Page
	 * Outputs Description text for the Global Section.
	 */
	function demo_section() {
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