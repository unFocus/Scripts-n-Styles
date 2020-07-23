<?php
/**
 * Settings page
 *
 * Select a CodeMirror Theme, and set wether to delete data on uninstall.
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
		__( 'Settings', 'scripts-n-styles' ),
		'unfiltered_html',
		ADMIN_MENU_SLUG . '_settings',
		__NAMESPACE__ . '\page'
	);

	add_action( "load-$hook_suffix", __NAMESPACE__ . '\help' );
	add_action( "load-$hook_suffix", __NAMESPACE__ . '\take_action', 49 );
	add_action( "admin_print_styles-$hook_suffix", function() {
		$options  = get_option( 'SnS_options' );
		$cm_theme = isset( $options['cm_theme'] ) ? $options['cm_theme'] : 'default';

		wp_enqueue_code_editor( [ 'type' => 'php' ] );
		wp_enqueue_script( 'sns-settings-page' );
		wp_localize_script( 'sns-settings-page', '_SnSOptions', [
			'theme' => $cm_theme,
			'root'  => plugins_url( '/', BASENAME ),
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
			'settings',
			__( 'Scripts n Styles Settings', 'scripts-n-styles' ),
			function() {
				?>
				<div style="max-width: 55em;">
					<p><?php esc_html_e( 'Control how and where Scripts n Styles menus and metaboxes appear. These options are here because sometimes users really care about this stuff. Feel free to adjust to your liking. :-)', 'scripts-n-styles' ); ?></p>
				</div>
				<?php
			},
			ADMIN_MENU_SLUG
		);

		add_settings_section(
			'demo',
			__( 'Code Mirror Demo', 'scripts-n-styles' ),
			function() {
				$demo = '<?php' . PHP_EOL
						. 'function hello($who) {' . PHP_EOL
						. '	return "Hello " . $who;' . PHP_EOL
						. '}' . PHP_EOL
						. '?>' . PHP_EOL
						. '<p>The program says <?= hello("World") ?>.</p>' . PHP_EOL
						. '<script>' . PHP_EOL
						. '	alert("And here is some JS code"); // also colored' . PHP_EOL
						. '</script>';
				?>
				<div style="max-width: 55em;">
					<textarea id="codemirror_demo" name="code" style="min-width: 500px; width:97%;"
						rows="5" cols="40"><?php echo esc_textarea( $demo ); ?></textarea>
				</div>
				<?php
			},
			ADMIN_MENU_SLUG
		);

		add_settings_field(
			'cm_theme',
			__( '<strong>Theme</strong>: ', 'scripts-n-styles' ),
			__NAMESPACE__ . '\radio',
			ADMIN_MENU_SLUG,
			'demo',
			[
				'label_for'   => 'cm_theme',
				'setting'     => 'SnS_options',
				'choices'     => [
					'default',
					'3024-day',
					'3024-night',
					'abcdef',
					'ambiance',
					'ayu-dark',
					'ayu-mirage',
					'base16-dark',
					'base16-light',
					'bespin',
					'blackboard',
					'cobalt',
					'colorforth',
					'darcula',
					'dracula',
					'duotone-dark',
					'duotone-light',
					'eclipse',
					'elegant',
					'erlang-dark',
					'gruvbox-dark',
					'hopscotch',
					'icecoder',
					'idea',
					'isotope',
					'lesser-dark',
					'liquibyte',
					'lucario',
					'material-darker',
					'material-ocean',
					'material-palenight',
					'material',
					'mbo',
					'mdn-like',
					'midnight',
					'monokai',
					'moxer',
					'neat',
					'neo',
					'night',
					'nord',
					'oceanic-next',
					'panda-syntax',
					'paraiso-dark',
					'paraiso-light',
					'pastel-on-dark',
					'railscasts',
					'rubyblue',
					'seti',
					'shadowfox',
					'solarized',
					'ssms',
					'the-matrix',
					'tomorrow-night-bright',
					'tomorrow-night-eighties',
					'ttcn',
					'twilight',
					'vibrant-ink',
					'xq-dark',
					'xq-light',
					'yeti',
					'yonce',
					'zenburn',
				],
				'default'     => 'default',
				'legend'      => __( 'Theme', 'scripts-n-styles' ),
				'layout'      => 'horizontal',
				'description' => '',
			]
		);
		add_settings_field(
			'delete_data_uninstall',
			__( '<strong>Delete Data When Uninstalling</strong>: ', 'scripts-n-styles' ),
			__NAMESPACE__ . '\radio',
			ADMIN_MENU_SLUG,
			'settings',
			[
				'label_for'   => 'delete_data_uninstall',
				'setting'     => 'SnS_options',
				'choices'     => [ 'yes', 'no' ],
				'layout'      => 'horizontal',
				'default'     => 'no',
				'legend'      => __( 'Delete Data When Uninstalling', 'scripts-n-styles' ),
				'description' => __( '<span class="description" style="max-width: 500px; display: inline-block;">Should the plugin clean up after itself and delete all of its saved data.</span>', 'scripts-n-styles' ),
			]
		);
	} );
} );
