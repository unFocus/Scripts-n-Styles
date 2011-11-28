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
	const OPTION_GROUP = 'scripts_n_styles';
	static $hook_suffix = '';
	
    /**
	 * Initializing method.
     * @static
     */
	function init() {
		$hook_suffix = add_submenu_page( SnS_Admin::$parent_slug, 'Scripts n Styles', 'Settings', 'unfiltered_html', SnS_Admin::MENU_SLUG.'_settings', 'SnS_Settings_Page::admin_page' );
		
		add_action( "load-$hook_suffix", array( __CLASS__, 'admin_load' ) );
		add_action( "load-$hook_suffix", 'SnS_Admin::help' );
		add_action( "load-$hook_suffix", array( __CLASS__, 'take_action'), 49 );
		
		// Make the page into a tab.
		if ( SnS_Admin::MENU_SLUG != SnS_Admin::$parent_slug ) {
			remove_submenu_page( SnS_Admin::$parent_slug, SnS_Admin::MENU_SLUG.'_settings' );
			add_filter( 'parent_file', array( __CLASS__, 'parent_file') );
		}
	}	
	
	static function parent_file( $parent_file ) {
		global $plugin_page, $submenu_file;
		if ( SnS_Admin::MENU_SLUG.'_settings' == $plugin_page ) $submenu_file = SnS_Admin::MENU_SLUG;
		return $parent_file;
	}

	
    /**
	 * Settings Page
	 * Adds Admin Menu Item via WordPress' "Administration Menus" API. Also hook actions to register options via WordPress' Settings API.
     */
	function admin_load() {
		wp_enqueue_style( 'sns-options-styles', plugins_url('css/options-styles.css', Scripts_n_Styles::$file), array( 'codemirror-default' ), SnS_Admin::VERSION );
		wp_enqueue_style( 'codemirror', plugins_url( 'libraries/codemirror/lib/codemirror.css', Scripts_n_Styles::$file), array(), '2.13' );
		wp_enqueue_style( 'codemirror-default', plugins_url( 'libraries/codemirror/theme/default.css', Scripts_n_Styles::$file), array( 'codemirror' ), '2.13' );
		
		wp_enqueue_script( 'sns-options-scripts', plugins_url('js/options-scripts.js', Scripts_n_Styles::$file), array( 'jquery', 'codemirror-css', 'codemirror-javascript' ), SnS_Admin::VERSION, true );
		wp_enqueue_script( 'codemirror', plugins_url( 'libraries/codemirror/lib/codemirror.js', Scripts_n_Styles::$file), array(), '2.13' );
		wp_enqueue_script( 'codemirror-css', plugins_url( 'libraries/codemirror/mode/css.js', Scripts_n_Styles::$file), array( 'codemirror' ), '2.13' );
		wp_enqueue_script( 'codemirror-javascript', plugins_url( 'libraries/codemirror/mode/javascript.js', Scripts_n_Styles::$file), array( 'codemirror' ), '2.13' );
		
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
			array( __CLASS__, 'textarea' ),
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
			array( __CLASS__, 'textarea' ),
			SnS_Admin::MENU_SLUG,
			'global',
			array(
				'label_for' => 'styles',
				'setting' => 'SnS_options',
				'class' => 'code js',
				'rows' => 5,
				'cols' => 40,
				'style' => 'min-width: 500px; width:97%;',
				'description' => '<span class="description" style="max-width: 500px; display: inline-block;">The "Styles" will be included <strong>verbatim</strong> in <code>&lt;style></code> tags in the <code>&lt;head></code> element of your html.</span>'
			) );
		add_settings_field(
			'scripts_in_head',
			'<strong>Scripts</strong><br />(for the <code>head</code> element): ',
			array( __CLASS__, 'textarea' ),
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
			array( __CLASS__, 'select' ),
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
	
    /**
	 * Settings Page
	 * Outputs a textarea for setting 'scripts_in_head'.
     */
	function textarea( $args ) {
		extract( $args );
		$options = get_option( $setting );
		$value =  isset( $options[ $label_for ] ) ? $options[ $label_for ] : '';
		$output = '<textarea';
		$output .= ( $style ) ? ' style="' . $style . '"': '';
		$output .= ( $class ) ? ' class="' . $class . '"': '';
		$output .= ( $rows ) ? ' rows="' . $rows . '"': '';
		$output .= ( $cols ) ? ' cols="' . $cols . '"': '';
		$output .= ' name="' . $setting . '[' . $label_for . ']"';
		$output .= ' id="' . $label_for . '">';
		$output .= $value . '</textarea>';
		if ( $description ) {
			$output .= $description;
		}
		echo $output;
	}
	
    /**
	 * Settings Page
	 * Outputs a select element for selecting options to set scripts for including.
     */
	function select( $args ) {
		extract( $args );
		$options = get_option( $setting );
		$selected = isset( $options[ $label_for ] ) ? $options[ $label_for ] : array();
		
		$output = '<select';
		$output .= ' id="' . $label_for . '"';
		$output .= ' name="' . $setting . '[' . $label_for . ']';
		if ( $multiple )
			$output .= '[]" multiple="multiple"';
		else
			$output .= '"';
		$output .= ( $size ) ? ' size="' . $size . '"': '';
		$output .= ( $style ) ? ' style="' . $style . '"': '';
		$output .= '>';
		foreach ( $choices as $choice ) {
			$output .= '<option value="' . $choice . '"';
			foreach ( $selected as $handle ) $output .= selected( $handle, $choice, false );
			$output .= '>' . $choice . '</option> ';
		}
		$output .= '</select>';
		if ( ! empty( $show_current ) && ! empty( $selected ) ) {
			$output .= '<p>' . $show_current;
			foreach ( $selected as $handle ) $output .= '<code>' . $handle . '</code> ';
			$output .= '</p>';
		}
		echo $output;
	}
	
    /**
	 * Settings Page
	 * Outputs the Admin Page and calls the Settings registered with the Settings API in init_options_page().
     */
	function take_action() {
		global $action, $option_page, $page, $new_whitelist_options;
		
		if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'unfiltered_html' ) || ( is_multisite() && ! is_super_admin() ) )
			wp_die( __( 'Cheatin&#8217; uh?' ) );
		
		if ( ! isset( $_REQUEST[ 'action' ], $_REQUEST[ 'option_page' ], $_REQUEST[ 'page' ] ) )
			return;
		
		wp_reset_vars( array( 'action', 'option_page', 'page' ) );
		
		check_admin_referer(  $option_page  . '-options' );
		
		if ( ! isset( $new_whitelist_options[ $option_page ] ) )
			return;
		
		$options = $new_whitelist_options[ $option_page ];
		
		foreach ( (array) $options as $option ) {
			$option = trim($option);
			$value = null;
			if ( isset($_POST[$option]) )
				$value = $_POST[$option];
			if ( !is_array($value) )
				$value = trim($value);
			$value = stripslashes_deep($value);
			update_option($option, $value);
		}
		
		if ( ! count( get_settings_errors() ) )
			add_settings_error( $page, 'settings_updated', __( 'Settings saved.' ), 'updated' );
		
		return;
	}

    /**
	 * Settings Page
	 * Outputs the Admin Page and calls the Settings registered with the Settings API in init_options_page().
     */
	function admin_page() {
		SnS_Admin::upgrade_check();
		?>
		<div class="wrap">
			<?php SnS_Admin::nav(); ?>
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