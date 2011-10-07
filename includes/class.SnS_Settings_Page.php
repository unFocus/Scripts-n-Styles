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
	
    /**
	 * Initializing method.
     * @static
     */
	function init() {
		if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'unfiltered_html' ) ) return;
		$menu_spot = 'object';
		$possible_spots = array(
			'menu', // Custom Top level
			'object', // Bottom of Top default section.
			'utility', // Bottom of Bottom default section.
			'management', // Tools section.
			'options', // Settings section.
			'theme', // Appearence section.
		);
		$a = array(
			'Scripts n Styles Settings',
			'Scripts n Styles',
			'unfiltered_html',
			SnS_Admin::MENU_SLUG,
			array( __CLASS__, 'admin_page' )
		);
		switch( $menu_spot ) {
			case 'utility':
				$a[] = plugins_url( 'images/menu.png', Scripts_n_Styles::$file );
				$hook_suffix = add_utility_page( $a[0], $a[1], $a[2], $a[3], $a[4], $a[5] );
				break;
			case 'object':
				$a[] = plugins_url( 'images/menu.png', Scripts_n_Styles::$file );
				$hook_suffix = add_object_page( $a[0], $a[1], $a[2], $a[3], $a[4], $a[5] );
				break;
			case 'management':
				$hook_suffix = add_management_page( $a[0], $a[1], $a[2], $a[3], $a[4] );
				break;
			case 'options':
				$hook_suffix = add_options_page( $a[0], $a[1], $a[2], $a[3], $a[4] );
				break;
			case 'theme':
				$hook_suffix = add_theme_page( $a[0], $a[1], $a[2], $a[3], $a[4] );
				break;
			default:
				$a[] = plugins_url( 'images/menu.png', Scripts_n_Styles::$file );
				$hook_suffix = add_menu_page( $a[0], $a[1], $a[2], $a[3], $a[4], $a[5] );
				break;
		}
		add_action( "load-$hook_suffix", array( __CLASS__, 'admin_load' ) );
		add_action( "load-$hook_suffix", array( __CLASS__, 'take_action'), 49 );
		
		add_contextual_help( $hook_suffix, self::contextual_help() );
	}
	
    /**
	 * Settings Page help
     */
	function contextual_help() {
		$contextual_help = '<p>In default (non MultiSite) WordPress installs, both <em>Administrators</em> and 
			<em>Editors</em> can access <em>Scripts-n-Styles</em> on individual edit screens. 
			Only <em>Administrators</em> can access this Options Page. In MultiSite WordPress installs, only 
			<em>"Super Admin"</em> users can access either
			<em>Scripts-n-Styles</em> on individual edit screens or this Options Page. If other plugins change 
			capabilities (specifically "unfiltered_html"), 
			other users can be granted access.</p>';
		return $contextual_help;
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
				array( __CLASS__, 'scripts_field' ),
				SnS_Admin::MENU_SLUG,
				'global',
				array(
					'label_for' => 'scripts',
					'setting' => 'SnS_options'
				) );
		add_settings_field(
				'styles',
				'<strong>Styles:</strong> ',
				array( __CLASS__, 'styles_field' ),
				SnS_Admin::MENU_SLUG,
				'global',
				array(
					'label_for' => 'styles',
					'setting' => 'SnS_options'
				) );
		add_settings_field(
				'scripts_in_head',
				'<strong>Scripts</strong><br />(for the <code>head</code> element): ',
				array( __CLASS__, 'scripts_in_head_field' ),
				SnS_Admin::MENU_SLUG,
				'global',
				array(
					'label_for' => 'scripts_in_head',
					'setting' => 'SnS_options'
				) );
		add_settings_field(
				'enqueue_scripts',
				'<strong>Enqueue Scripts</strong>: ',
				array( __CLASS__, 'enqueue_scripts_field' ),
				SnS_Admin::MENU_SLUG,
				'global',
				array(
					'label_for' => 'enqueue_scripts',
					'setting' => 'SnS_options'
				) );
		
		add_settings_section(
				'usage',
				'Scripts n Styles Usage',
				array( __CLASS__, 'usage_section' ),
				SnS_Admin::MENU_SLUG );
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
	 * Outputs a textarea for setting 'scripts'.
     */
	function scripts_field( $args ) {
		$options = get_option( 'SnS_options' );
		?><textarea style="min-width: 500px; width:97%;" class="code js" rows="5" cols="40" name="SnS_options[scripts]" id="scripts"><?php echo isset( $options[ 'scripts' ] ) ? $options[ 'scripts' ] : ''; ?></textarea>
		<span class="description" style="max-width: 500px; display: inline-block;">The "Scripts" will be included <strong>verbatim</strong> in <code>&lt;script></code> tags at the bottom of the <code>&lt;body></code> element of your html.</span>
		<?php
	}
	
    /**
	 * Settings Page
	 * Outputs a textarea for setting 'styles'.
     */
	function styles_field( $args ) {
		$options = get_option( 'SnS_options' );
		?><textarea style="min-width: 500px; width:97%;" class="code css" rows="5" cols="40" name="SnS_options[styles]" id="styles"><?php echo isset( $options[ 'styles' ] ) ? $options[ 'styles' ] : ''; ?></textarea>
		<span class="description" style="max-width: 500px; display: inline-block;">The "Styles" will be included <strong>verbatim</strong> in <code>&lt;style></code> tags in the <code>&lt;head></code> element of your html.</span><?php
	}
	
    /**
	 * Settings Page
	 * Outputs a textarea for setting 'scripts_in_head'.
     */
	function scripts_in_head_field( $args ) {
		$options = get_option( 'SnS_options' );
		?><textarea style="min-width: 500px; width:97%;" class="code js" rows="5" cols="40" name="SnS_options[scripts_in_head]" id="scripts_in_head"><?php echo isset( $options[ 'scripts_in_head' ] ) ? $options[ 'scripts_in_head' ] : ''; ?></textarea>
		<span class="description" style="max-width: 500px; display: inline-block;">The "Scripts (in head)" will be included <strong>verbatim</strong> in <code>&lt;script></code> tags in the <code>&lt;head></code> element of your html.</span>
		<?php
	}
	
    /**
	 * Settings Page
	 * Outputs a select element for selecting options to set $sns_enqueue_scripts.
     */
	function enqueue_scripts_field( $args ) {
		// One step closer to generic form element output.
		$a = $args[ 'label_for' ];
		$setting = $args[ 'setting' ];
		
		$options = get_option( $setting );
		if ( ! isset( $options[ $a ] ) )
			$$a = array();
		else
			$$a = $options[ $a ];
		?>
		<select name="<?php echo $setting . '[' . $a . ']' ?>[]" id="<?php echo $a ?>" size="5" multiple="multiple" style="height: auto;">
			<?php foreach ( Scripts_n_Styles::get_wp_registered() as $value ) { ?>
				<option value="<?php echo $value ?>"<?php foreach ( $$a as $handle ) selected( $handle, $value ); ?>><?php echo $value ?></option> 
			<?php } ?>
		</select>
		<?php if ( ! empty( $$a ) ) { ?>
			<p>Currently Enqueued Scripts: 
			<?php foreach ( $$a as $handle )  echo '<code>' . $handle . '</code> '; ?>
			</p>
		<?php }
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
		global $title;
		?>
		<div class="wrap">
			<style>#icon-<?php echo esc_html( $_REQUEST[ 'page' ] ); ?> { background: no-repeat center url('<?php echo plugins_url( 'images/icon32.png', Scripts_n_Styles::$file); ?>'); }</style>
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