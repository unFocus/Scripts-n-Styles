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
		'\unFocus\SnS\page'
	);

	add_action( "load-$hook_suffix", '\unFocus\SnS\help' );
	add_action( "load-$hook_suffix", '\unFocus\SnS\take_action', 49 );
	add_action( "admin_print_styles-$hook_suffix", function() {
		wp_enqueue_code_editor( [ 'type' => 'php' ] );
		wp_add_inline_script(
			'code-editor',
			"jQuery(function( $ ) {"
				. "var sns = wp.codeEditor.initialize( $( '#codemirror_demo' ), wp.codeEditor.defaultSettings );"
				. "$('input[name=\"SnS_options[cm_theme]\"]').change( function(){"
					. "sns.codemirror.setOption(\"theme\", $(this).val());"
				. "});"
			."});"
		);
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
						. '</script>'
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
			'\unFocus\SnS\radio',
			ADMIN_MENU_SLUG,
			'demo',
			[
				'label_for' => 'cm_theme',
				'setting' => 'SnS_options',
				'choices' => [
					'default',
					'3024-day',
					'3024-night',
					'abcdef',
					'ambiance',
					'base16-dark',
					'base16-light',
					'bespin',
					'blackboard',
					'cobalt',
					'colorforth',
					'dracula',
					'duotone-dark',
					'duotone-light',
					'eclipse',
					'elegant',
					'erlang-dark',
					'hopscotch',
					'icecoder',
					'isotope',
					'lesser-dark',
					'liquibyte',
					'material',
					'mbo',
					'mdn-like',
					'midnight',
					'monokai',
					'neat',
					'neo',
					'night',
					'panda-syntax',
					'paraiso-dark',
					'paraiso-light',
					'pastel-on-dark',
					'railscasts',
					'rubyblue',
					'seti',
					'solarized',
					'the-matrix',
					'tomorrow-night-bright',
					'tomorrow-night-eighties',
					'ttcn',
					'twilight',
					'vibrant-ink',
					'xq-dark',
					'xq-light',
					'yeti',
					'zenburn',
				],
				'default' => 'default',
				'legend' => __( 'Theme', 'scripts-n-styles' ),
				'layout' => 'horizontal',
				'description' => '',
			]
		);
		add_settings_field(
			'delete_data_uninstall',
			__( '<strong>Delete Data When Uninstalling</strong>: ', 'scripts-n-styles' ),
			'\unFocus\SnS\radio',
			ADMIN_MENU_SLUG,
			'settings',
			[
				'label_for' => 'delete_data_uninstall',
				'setting' => 'SnS_options',
				'choices' => [ 'yes', 'no' ],
				'layout' => 'horizontal',
				'default' => 'no',
				'legend' => __( 'Delete Data When Uninstalling', 'scripts-n-styles' ),
				'description' => __( '<span class="description" style="max-width: 500px; display: inline-block;">Should the plugin clean up after itself and delete all of its saved data.</span>', 'scripts-n-styles' ),
			]
		);
	} );
} );
