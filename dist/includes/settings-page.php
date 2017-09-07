<?php
namespace unFocus\SnS;

/**
 * Settings_Page
 *
 * Allows WordPress admin users the ability to add custom CSS
 * and JavaScript directly to individual Post, Pages or custom
 * post types.
 */

add_action( 'admin_menu', function() {
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'unfiltered_html' ) ) return;

	$hook_suffix = add_submenu_page(
		ADMIN_MENU_SLUG,
		__( 'Scripts n Styles', 'scripts-n-styles' ),
		__( 'Settings' ),
		'unfiltered_html',
		ADMIN_MENU_SLUG.'_settings',
		'\unFocus\SnS\page' );

	add_action( "load-$hook_suffix", '\unFocus\SnS\help' );
	add_action( "load-$hook_suffix", '\unFocus\SnS\take_action', 49 );
	add_action( "admin_print_styles-$hook_suffix", function() {
		$options = get_option( 'SnS_options' );
		$cm_theme = isset( $options[ 'cm_theme' ] ) ? $options[ 'cm_theme' ] : '';

		wp_enqueue_style( 'sns-options' );

		wp_enqueue_script(  'sns-settings-page' );
		wp_localize_script( 'sns-settings-page', 'codemirror_options', array( 'theme' => $cm_theme ) );
	} );

	/**
	 * Settings Page
	 * Adds Admin Menu Item via WordPress' "Administration Menus" API. Also hook actions to register options via WordPress' Settings API.
	 */
	add_action( "load-$hook_suffix", function() {
		register_setting(
			OPTION_GROUP,
			'SnS_options' );

		add_settings_section(
			'settings',
			__( 'Scripts n Styles Settings', 'scripts-n-styles' ),
			/**
			 * Settings Page
			 * Outputs Description text for the Global Section.
			 */
			function() {
				?>
				<div style="max-width: 55em;">
					<p><?php _e( 'Control how and where Scripts n Styles menus and metaboxes appear. These options are here because sometimes users really care about this stuff. Feel free to adjust to your liking. :-)', 'scripts-n-styles' ) ?></p>
				</div>
				<?php
			},
			ADMIN_MENU_SLUG );

		add_settings_section(
			'demo',
			__( 'Code Mirror Demo', 'scripts-n-styles' ),
			/**
			 * Settings Page
			 * Outputs Description text for the Global Section.
			 */
			function() {
				?>
				<div style="max-width: 55em;">
					<textarea id="codemirror_demo" name="code" style="min-width: 500px; width:97%;" rows="5" cols="40"><?php
					echo esc_textarea( '<?php' . PHP_EOL
					.'function hello($who) {' . PHP_EOL
					.'	return "Hello " . $who;' . PHP_EOL
					.'}' . PHP_EOL
					.'?>' . PHP_EOL
					.'<p>The program says <?= hello("World") ?>.</p>' . PHP_EOL
					.'<script>' . PHP_EOL
					.'	alert("And here is some JS code"); // also colored' . PHP_EOL
					.'</script>' );
					?></textarea>
				</div>
				<?php
			},
			ADMIN_MENU_SLUG );

		add_settings_field(
			'cm_theme',
			__( '<strong>Theme</strong>: ', 'scripts-n-styles' ),
			'\unFocus\SnS\radio',
			ADMIN_MENU_SLUG,
			'demo',
			array(
				'label_for' => 'cm_theme',
				'setting' => 'SnS_options',
				'choices' => [ 'default',
					'3024-day', '3024-night', 'abcdef', 'ambiance',
					'base16-dark', 'base16-light', 'bespin', 'blackboard',
					'cobalt', 'colorforth',
					'dracula', 'duotone-dark', 'duotone-light',
					'eclipse', 'elegant', 'erlang-dark',
					'hopscotch', 'icecoder', 'isotope',
					'lesser-dark', 'liquibyte',
					'material', 'mbo', 'mdn-like', 'midnight', 'monokai',
					'neat', 'neo', 'night',
					'panda-syntax', 'paraiso-dark', 'paraiso-light', 'pastel-on-dark',
					'railscasts', 'rubyblue',
					'seti', 'solarized',
					'the-matrix', 'tomorrow-night-bright', 'tomorrow-night-eighties',
					'ttcn', 'twilight',
					'vibrant-ink',
					'xq-dark', 'xq-light',
					'yeti', 'zenburn' ],
				'default' => 'default',
				'legend' => __( 'Theme', 'scripts-n-styles' ),
				'layout' => 'horizontal',
				'description' => '',
			) );
		add_settings_field(
			'hoops_widget',
			__( '<strong>Hoops Widgets</strong>: ', 'scripts-n-styles' ),
			'\unFocus\SnS\radio',
			ADMIN_MENU_SLUG,
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
		add_settings_field(
			'delete_data_uninstall',
			__( '<strong>Delete Data When Uninstalling</strong>: ', 'scripts-n-styles' ),
			'\unFocus\SnS\radio',
			ADMIN_MENU_SLUG,
			'settings',
			array(
				'label_for' => 'delete_data_uninstall',
				'setting' => 'SnS_options',
				'choices' => array( 'yes', 'no' ),
				'layout' => 'horizontal',
				'default' => 'no',
				'legend' => __( 'Delete Data When Uninstalling', 'scripts-n-styles' ),
				'description' => __( '<span class="description" style="max-width: 500px; display: inline-block;">Should the plugin clean up after itself and delete all of its saved data.</span>', 'scripts-n-styles' )
			) );
	} );

} );