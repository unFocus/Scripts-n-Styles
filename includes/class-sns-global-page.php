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
	 * Initializing method.
	 * @static
	 */
	static function init() {
		if ( SnS_Admin::$parent_slug == SnS_Admin::MENU_SLUG ) $menu_title = __( 'Global', 'scripts-n-styles' );
		else $menu_title = __( 'Scripts n Styles', 'scripts-n-styles' );

		$hook_suffix = add_submenu_page( SnS_Admin::$parent_slug, __( 'Scripts n Styles', 'scripts-n-styles' ), $menu_title, 'unfiltered_html', SnS_Admin::MENU_SLUG, array( 'SnS_Form', 'page' ) );

		add_action( "load-$hook_suffix", array( __CLASS__, 'admin_load' ) );
		add_action( "load-$hook_suffix", array( 'SnS_Admin', 'help' ) );
		add_action( "load-$hook_suffix", array( 'SnS_Form', 'take_action' ), 49 );
		add_action( "admin_print_styles-$hook_suffix", array( __CLASS__, 'admin_enqueue_scripts' ) );
	}

	static function admin_enqueue_scripts() {
		$options = get_option( 'SnS_options' );
		$cm_theme = isset( $options[ 'cm_theme' ] ) ? $options[ 'cm_theme' ] : 'default';

		wp_enqueue_style( 'chosen' );
		wp_enqueue_style( 'sns-options' );

		wp_enqueue_script(  'sns-global-page' );
		wp_localize_script( 'sns-global-page', '_SnS_options', array( 'theme' => $cm_theme ) );
	}
	/**
	 * Settings Page
	 * Adds Admin Menu Item via WordPress' "Administration Menus" API. Also hook actions to register options via WordPress' Settings API.
	 */
	static function admin_load() {

		register_setting(
			SnS_Admin::OPTION_GROUP,
			'SnS_options' );

		add_settings_section(
			'global_styles',
			__( 'Blog Wide CSS Styles', 'scripts-n-styles' ),
			array( __CLASS__, 'global_styles_section' ),
			SnS_Admin::MENU_SLUG );

		add_settings_section(
			'global_scripts',
			__( 'Blog Wide JavaScript', 'scripts-n-styles' ),
			array( __CLASS__, 'global_scripts_section' ),
			SnS_Admin::MENU_SLUG );

		add_settings_field(
			'less',
			__( '<strong>LESS:</strong> ', 'scripts-n-styles' ),
			array( __CLASS__, 'less_fields' ),
			SnS_Admin::MENU_SLUG,
			'global_styles',
			array( 'label_for' => 'less' ) );
		add_settings_field(
			'coffee',
			__( '<strong>CoffeeScript:</strong> ', 'scripts-n-styles' ),
			array( __CLASS__, 'coffee_fields' ),
			SnS_Admin::MENU_SLUG,
			'global_scripts',
			array( 'label_for' => 'coffee' ) );
		add_settings_field(
			'styles',
			__( '<strong>CSS Styles:</strong> ', 'scripts-n-styles' ),
			array( 'SnS_Form', 'textarea' ),
			SnS_Admin::MENU_SLUG,
			'global_styles',
			array(
				'label_for' => 'styles',
				'setting' => 'SnS_options',
				'class' => 'code css',
				'wrap_class' => 'style',
				'rows' => 5,
				'cols' => 40,
				'style' => 'min-width: 500px; width:97%;',
				'description' => __( '<span class="description" style="max-width: 500px; display: inline-block;">The "Styles" will be included <strong>verbatim</strong> in <code>&lt;style></code> tags in the <code>&lt;head></code> element of your html.</span>', 'scripts-n-styles' )
			) );
		add_settings_field(
			'scripts_in_head',
			__( '<strong>Scripts</strong><br />(for the <code>head</code> element): ', 'scripts-n-styles' ),
			array( 'SnS_Form', 'textarea' ),
			SnS_Admin::MENU_SLUG,
			'global_scripts',
			array(
				'label_for' => 'scripts_in_head',
				'setting' => 'SnS_options',
				'class' => 'code js',
				'wrap_class' => 'script',
				'rows' => 5,
				'cols' => 40,
				'style' => 'min-width: 500px; width:97%;',
				'description' => __( '<span class="description" style="max-width: 500px; display: inline-block;">The "Scripts (in head)" will be included <strong>verbatim</strong> in <code>&lt;script></code> tags in the <code>&lt;head></code> element of your html.</span>', 'scripts-n-styles' )
			) );
		add_settings_field(
			'scripts',
			__( '<strong>Scripts</strong><br />(end of the <code>body</code> tag):', 'scripts-n-styles' ),
			array( 'SnS_Form', 'textarea' ),
			SnS_Admin::MENU_SLUG,
			'global_scripts',
			array(
				'label_for' => 'scripts',
				'setting' => 'SnS_options',
				'class' => 'code js',
				'wrap_class' => 'script',
				'rows' => 5,
				'cols' => 40,
				'style' => 'min-width: 500px; width:97%;',
				'description' => __( '<span class="description" style="max-width: 500px; display: inline-block;">The "Scripts" will be included <strong>verbatim</strong> in <code>&lt;script></code> tags at the bottom of the <code>&lt;body></code> element of your html.</span>', 'scripts-n-styles' )
			) );
		add_settings_field(
			'enqueue_scripts',
			__( '<strong>Enqueue Scripts</strong>: ', 'scripts-n-styles' ),
			array( 'SnS_Form', 'select' ),
			SnS_Admin::MENU_SLUG,
			'global_scripts',
			array(
				'label_for' => 'enqueue_scripts',
				'setting' => 'SnS_options',
				'choices' => Scripts_n_Styles::get_wp_registered(),
				'size' => 5,
				'style' => 'height: auto;',
				'multiple' => true,
				'show_current' => __( 'Currently Enqueued Scripts: ', 'scripts-n-styles' )
			) );
		add_filter( 'sns_options_pre_update_option', array( __CLASS__, 'enqueue_scripts'), 10, 5 );
	}
	static function enqueue_scripts( $value, $page, $action, $new, $old ) {
		if ( empty( $new['enqueue_scripts'] ) && ! empty( $old['enqueue_scripts'] ) )
			unset( $value['enqueue_scripts'] );
		return $value;
	}

	static function less_fields() {
		$options = get_option( 'SnS_options' );
		$less =  isset( $options[ 'less' ] ) ? $options[ 'less' ] : '';
		$compiled =  isset( $options[ 'compiled' ] ) ? $options[ 'compiled' ] : '';
		?>
		<div style="overflow: hidden;">
			<div style="width: 49%; float: left; overflow: hidden; margin-right: 2%;" class="less">
				<textarea id="less" name="SnS_options[less]" style="min-width: 250px; width:47%; float: left" class="code less" rows="5" cols="40"><?php echo esc_textarea( $less ) ?></textarea>
			</div>
			<div style="width: 49%; float: left; overflow: hidden;" class="style">
				<textarea id="compiled" name="SnS_options[compiled]" style="min-width: 250px; width:47%;" class="code css" rows="5" cols="40"><?php echo esc_textarea( $compiled ) ?></textarea>
				<div id="compiled_error" style="display: none" class="error settings-error below-h2"></div>
			</div>
		</div>
		<?php
	}
	static function coffee_fields() {
		$options = get_option( 'SnS_options' );
		$coffee =  isset( $options[ 'coffee' ] ) ? $options[ 'coffee' ] : '';
		$compiled =  isset( $options[ 'coffee_compiled' ] ) ? $options[ 'coffee_compiled' ] : '';
		?>
		<div style="overflow: hidden;">
			<div style="width: 49%; float: left; overflow: hidden; margin-right: 2%;" class="coffee">
				<textarea id="coffee" name="SnS_options[coffee]" style="min-width: 250px; width:47%; float: left" class="code coffee" rows="5" cols="40"><?php echo esc_textarea( $coffee ) ?></textarea>
			</div>
			<div style="width: 49%; float: left; overflow: hidden;" class="script">
				<textarea id="coffee_compiled" name="SnS_options[coffee_compiled]" style="min-width: 250px; width:47%;" class="code js" rows="5" cols="40"><?php echo esc_textarea( $compiled ) ?></textarea>
				<div id="coffee_compiled_error" style="display: none" class="error settings-error below-h2"></div>
			</div>
		</div>
		<?php
	}

	/**
	 * Settings Page
	 * Outputs Description text for the Global Section.
	 */
	static function global_scripts_section() {
		?>
		<div style="max-width: 55em;">
			<p><?php _e( 'Code entered here will be included in <em>every page (and post) of your site</em>, including the homepage and archives. The code will appear <strong>before</strong> Scripts that were registered individually.', 'scripts-n-styles' )?></p>
		</div>
		<?php
	}

	/**
	 * Settings Page
	 * Outputs Description text for the Global Section.
	 */
	static function global_styles_section() {
		?>
		<div style="max-width: 55em;">
			<p><?php _e( 'Code entered here will be included in <em>every page (and post) of your site</em>, including the homepage and archives. The code will appear <strong>before</strong> Styles that were registered individually.', 'scripts-n-styles' )?></p>
		</div>
		<?php
	}
}
?>
