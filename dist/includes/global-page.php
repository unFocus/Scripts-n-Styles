<?php
/**
 * Global_Page
 *
 * Allows WordPress admin users the ability to add custom CSS
 * and JavaScript directly to individual Post, Pages or custom
 * post types.
 *
 * @package Scripts-N-Styles
 */

namespace unFocus\SnS;

add_action( 'admin_menu', function() {
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'unfiltered_html' ) ) {
		return;
	}

	$hook_suffix = add_submenu_page(
		ADMIN_MENU_SLUG,
		__( 'Scripts n Styles', 'scripts-n-styles' ),
		__( 'Global', 'scripts-n-styles' ),
		'unfiltered_html',
		ADMIN_MENU_SLUG,
		__NAMESPACE__ . '\page'
	);

	add_action( "load-$hook_suffix", __NAMESPACE__ . '\help' );
	add_action( "load-$hook_suffix", __NAMESPACE__ . '\take_action', 49 );
	add_action( "admin_print_styles-$hook_suffix", function() {
		wp_enqueue_code_editor( [ 'type' => 'php' ] );
		wp_enqueue_script( 'sns-global-page' );
		wp_localize_script( 'sns-global-page', '_SnSOptions', [
			'root' => plugins_url( '/', BASENAME ),
		] );
	} );

	/**
	 * Settings Page
	 * Adds Admin Menu Item via WordPress' "Administration Menus" API. Also hook actions to register options via WordPress' Settings API.
	 */
	add_action( "load-$hook_suffix", function() {

		register_setting(
			OPTION_GROUP,
			'SnS_options'
		);

		add_settings_section(
			'global_styles',
			__( 'Blog Wide CSS Styles', 'scripts-n-styles' ),
			function() {
				?>
				<div style="max-width: 55em;">
					<p><?php echo wp_kses_post( 'Code entered here will be included in <em>every page (and post) of your site</em>, including the homepage and archives. The code will appear <strong>before</strong> Styles that were registered individually.', 'scripts-n-styles' ); ?></p>
				</div>
				<?php
			},
			ADMIN_MENU_SLUG
		);

		add_settings_section(
			'global_scripts',
			__( 'Blog Wide JavaScript', 'scripts-n-styles' ),
			function() {
				?>
				<div style="max-width: 55em;">
					<p><?php echo wp_kses_post( 'Code entered here will be included in <em>every page (and post) of your site</em>, including the homepage and archives. The code will appear <strong>before</strong> Scripts that were registered individually.', 'scripts-n-styles' ); ?></p>
				</div>
				<?php
			},
			ADMIN_MENU_SLUG
		);

		add_settings_field(
			'less',
			__( '<strong>LESS:</strong> ', 'scripts-n-styles' ),
			function() {
				$options  = get_option( 'SnS_options' );
				$less     = isset( $options['less'] ) ? $options['less'] : '';
				$compiled = isset( $options['compiled'] ) ? $options['compiled'] : '';
				?>
				<div style="overflow: hidden;">
					<div style="width: 49%; float: left; overflow: hidden; margin-right: 2%;" class="less autoheight">
						<textarea id="less" name="SnS_options[less]" class="code less" rows="10"><?php echo esc_textarea( $less ); ?></textarea>
					</div>
					<div style="width: 49%; float: left; overflow: hidden;" class="style autoheight">
						<textarea id="compiled" name="SnS_options[compiled]" class="code css" rows="10"><?php echo esc_textarea( $compiled ); ?></textarea>
						<div id="compiled_error" style="display: none" class="error settings-error below-h2"></div>
					</div>
				</div>
				<?php
			},
			ADMIN_MENU_SLUG,
			'global_styles',
			[ 'label_for' => 'less' ]
		);
		add_settings_field(
			'coffee',
			__( '<strong>CoffeeScript:</strong> ', 'scripts-n-styles' ),
			function() {
				$options  = get_option( 'SnS_options' );
				$coffee   = isset( $options['coffee'] ) ? $options['coffee'] : '';
				$compiled = isset( $options['coffee_compiled'] ) ? $options['coffee_compiled'] : '';
				?>
				<div style="overflow: hidden;">
					<div style="width: 49%; float: left; overflow: hidden; margin-right: 2%;" class="coffee autoheight">
						<textarea id="coffee" name="SnS_options[coffee]" class="code coffee" rows="10"><?php echo esc_textarea( $coffee ); ?></textarea>
					</div>
					<div style="width: 49%; float: left; overflow: hidden;" class="script autoheight">
						<textarea id="coffee_compiled" name="SnS_options[coffee_compiled]" class="code js" rows="10" ><?php echo esc_textarea( $compiled ); ?></textarea>
						<div id="coffee_compiled_error" style="display: none" class="error settings-error below-h2"></div>
					</div>
				</div>
				<?php
			},
			ADMIN_MENU_SLUG,
			'global_scripts',
			[ 'label_for' => 'coffee' ]
		);
		add_settings_field(
			'styles',
			__( '<strong>CSS Styles:</strong> ', 'scripts-n-styles' ),
			__NAMESPACE__ . '\textarea',
			ADMIN_MENU_SLUG,
			'global_styles',
			[
				'label_for'   => 'styles',
				'setting'     => 'SnS_options',
				'class'       => 'code css autoheight',
				'wrap_class'  => 'style',
				'rows'        => 10,
				'description' => __( '<span class="description" style="max-width: 500px; display: inline-block;">The "Styles" will be included <strong>verbatim</strong> in <code>&lt;style></code> tags in the <code>&lt;head></code> element of your html.</span>', 'scripts-n-styles' ),
			]
		);
		add_settings_field(
			'scripts_in_head',
			__( '<strong>Scripts</strong><br />(for the <code>head</code> element): ', 'scripts-n-styles' ),
			__NAMESPACE__ . '\textarea',
			ADMIN_MENU_SLUG,
			'global_scripts',
			[
				'label_for'   => 'scripts_in_head',
				'setting'     => 'SnS_options',
				'class'       => 'code js autoheight',
				'wrap_class'  => 'script',
				'rows'        => 10,
				'description' => __( '<span class="description" style="max-width: 500px; display: inline-block;">The "Scripts (in head)" will be included <strong>verbatim</strong> in <code>&lt;script></code> tags in the <code>&lt;head></code> element of your html.</span>', 'scripts-n-styles' ),
			]
		);
		add_settings_field(
			'scripts',
			wp_kses_post( __( '<strong>Scripts</strong><br />(end of the <code>body</code> tag):', 'scripts-n-styles' ) ),
			__NAMESPACE__ . '\textarea',
			ADMIN_MENU_SLUG,
			'global_scripts',
			[
				'label_for'   => 'scripts',
				'setting'     => 'SnS_options',
				'class'       => 'code js autoheight',
				'wrap_class'  => 'script',
				'rows'        => 10,
				'description' => __( '<span class="description" style="max-width: 500px; display: inline-block;">The "Scripts" will be included <strong>verbatim</strong> in <code>&lt;script></code> tags at the bottom of the <code>&lt;body></code> element of your html.</span>', 'scripts-n-styles' ),
			]
		);
		add_settings_field(
			'enqueue_scripts',
			__( '<strong>Enqueue Scripts</strong>: ', 'scripts-n-styles' ),
			__NAMESPACE__ . '\select',
			ADMIN_MENU_SLUG,
			'global_scripts',
			[
				'label_for'    => 'enqueue_scripts',
				'setting'      => 'SnS_options',
				'choices'      => get_registered_scripts(),
				'size'         => 5,
				'style'        => 'height: auto;',
				'multiple'     => true,
				'show_current' => __( 'Currently Enqueued Scripts: ', 'scripts-n-styles' ),
			]
		);

		add_filter( 'sns_options_pre_update_option', function( $value, $page, $action, $new, $old ) {
			if ( empty( $new['enqueue_scripts'] ) && ! empty( $old['enqueue_scripts'] ) ) {
				unset( $value['enqueue_scripts'] );
			}
			return $value;
		}, 10, 5 );

	} );
} );
