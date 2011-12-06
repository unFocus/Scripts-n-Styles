<?php
/**
 * SnS_Global_Page
 * 
 * Allows WordPress admin users the ability to add custom CSS
 * and JavaScript directly to individual Post, Pages or custom
 * post types.
 */
		
class SnS_Global_Page
{
    /**
     * Constants
     */
	const OPTION_GROUP = 'scripts_n_styles';
	
    /**
	 * Initializing method.
     * @static
     */
	function init() {
		if ( SnS_Admin::$parent_slug == SnS_Admin::MENU_SLUG ) $menu_title = 'Global';
		else $menu_title = 'Scripts n Styles';
		
		$hook_suffix = add_submenu_page( SnS_Admin::$parent_slug, 'Scripts n Styles', $menu_title, 'unfiltered_html', SnS_Admin::MENU_SLUG, array( 'SnS_Form', 'page' ) );
		
		add_action( "load-$hook_suffix", array( __CLASS__, 'admin_load' ) );
		add_action( "load-$hook_suffix", array( 'SnS_Admin', 'help' ) );
		add_action( "load-$hook_suffix", array( 'SnS_Form', 'take_action'), 49 );
		add_action( "admin_print_styles-$hook_suffix", array( __CLASS__, 'admin_enqueue_scripts' ) );
	}
	
	function admin_enqueue_scripts() {
		$options = get_option( 'SnS_options' );
		$cm_theme = isset( $options[ 'cm_theme' ] ) ? $options[ 'cm_theme' ] : 'default';
		wp_enqueue_style( 'sns-options-styles', plugins_url('css/options-styles.css', Scripts_n_Styles::$file), array( 'codemirror' ), SnS_Admin::VERSION );
		wp_enqueue_style( 'codemirror', plugins_url( 'libraries/CodeMirror2/lib/codemirror.css', Scripts_n_Styles::$file), array(), '2.18' );
		wp_enqueue_style( "codemirror-$cm_theme", plugins_url( "libraries/CodeMirror2/theme/$cm_theme.css", Scripts_n_Styles::$file), array( 'codemirror' ), '2.18' );
		
		wp_enqueue_script( 'sns-global-page-scripts', plugins_url('js/global-page.js', Scripts_n_Styles::$file), array( 'jquery', 'codemirror-css', 'codemirror-javascript' ), SnS_Admin::VERSION, true );
		wp_localize_script( 'sns-global-page-scripts', 'codemirror_options', array( 'theme' => $cm_theme ) );
		wp_enqueue_script( 'codemirror', plugins_url( 'libraries/CodeMirror2/lib/codemirror.js', Scripts_n_Styles::$file), array(), '2.18' );
		wp_enqueue_script( 'codemirror-css', plugins_url( 'libraries/CodeMirror2/mode/css/css.js', Scripts_n_Styles::$file), array( 'codemirror' ), '2.18' );
		wp_enqueue_script( 'codemirror-javascript', plugins_url( 'libraries/CodeMirror2/mode/javascript/javascript.js', Scripts_n_Styles::$file), array( 'codemirror' ), '2.18' );
	}
    /**
	 * Settings Page
	 * Adds Admin Menu Item via WordPress' "Administration Menus" API. Also hook actions to register options via WordPress' Settings API.
     */
	function admin_load() {
		
		register_setting(
			self::OPTION_GROUP,
			'SnS_options' );
		
		add_settings_section(
			'global',
			'Global Scripts n Styles',
			array( __CLASS__, 'global_section' ),
			SnS_Admin::MENU_SLUG );
		
		add_settings_field(
			'scripts',
			'<strong>Scripts:</strong> ',
			array( 'SnS_Form', 'textarea' ),
			SnS_Admin::MENU_SLUG,
			'global',
			array(
				'label_for' => 'scripts',
				'setting' => 'SnS_options',
				'class' => 'code js',
				'rows' => 5,
				'cols' => 40,
				'style' => 'min-width: 500px; width:97%;',
				'description' => '<span class="description" style="max-width: 500px; display: inline-block;">The "Scripts" will be included <strong>verbatim</strong> in <code>&lt;script></code> tags at the bottom of the <code>&lt;body></code> element of your html.</span>'
			) );
		add_settings_field(
			'styles',
			'<strong>Styles:</strong> ',
			array( 'SnS_Form', 'textarea' ),
			SnS_Admin::MENU_SLUG,
			'global',
			array(
				'label_for' => 'styles',
				'setting' => 'SnS_options',
				'class' => 'code css',
				'rows' => 5,
				'cols' => 40,
				'style' => 'min-width: 500px; width:97%;',
				'description' => '<span class="description" style="max-width: 500px; display: inline-block;">The "Styles" will be included <strong>verbatim</strong> in <code>&lt;style></code> tags in the <code>&lt;head></code> element of your html.</span>'
			) );
		add_settings_field(
			'scripts_in_head',
			'<strong>Scripts</strong><br />(for the <code>head</code> element): ',
			array( 'SnS_Form', 'textarea' ),
			SnS_Admin::MENU_SLUG,
			'global',
			array(
				'label_for' => 'scripts_in_head',
				'setting' => 'SnS_options',
				'class' => 'code js',
				'rows' => 5,
				'cols' => 40,
				'style' => 'min-width: 500px; width:97%;',
				'description' => '<span class="description" style="max-width: 500px; display: inline-block;">The "Scripts (in head)" will be included <strong>verbatim</strong> in <code>&lt;script></code> tags in the <code>&lt;head></code> element of your html.</span>'
			) );
		add_settings_field(
			'enqueue_scripts',
			'<strong>Enqueue Scripts</strong>: ',
			array( 'SnS_Form', 'select' ),
			SnS_Admin::MENU_SLUG,
			'global',
			array(
				'label_for' => 'enqueue_scripts',
				'setting' => 'SnS_options',
				'choices' => Scripts_n_Styles::get_wp_registered(),
				'size' => 5,
				'style' => 'height: auto;',
				'multiple' => true,
				'show_current' => 'Currently Enqueued Scripts: '
			) );
	}
	
    /**
	 * Settings Page
	 * Outputs Description text for the Global Section.
	 */
	function global_section() {
		?>
		<div style="max-width: 55em;">
			<p>Code entered here will be included in <em>every page (and post) of your site</em>, including the homepage and archives. The code will appear <strong>before</strong> Scripts and Styles registered individually.</p>
		</div>
		<?php
	}
}
?>