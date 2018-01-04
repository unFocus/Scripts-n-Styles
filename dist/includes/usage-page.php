<?php
/**
 * The Useage page
 *
 * An admin page where users can see what posts Scripts n Styles are in use.
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
		__( 'Usage', 'scripts-n-styles' ),
		'unfiltered_html',
		ADMIN_MENU_SLUG . '_usage',
		'\unFocus\SnS\page'
	);

	add_action( "load-$hook_suffix", '\unFocus\SnS\help' );
	add_action( "admin_print_styles-$hook_suffix", function() {
		wp_enqueue_style( 'sns-options' );
	} );

	/**
	 * Settings Page
	 * Adds Admin Menu Item via WordPress' "Administration Menus" API. Also hook actions to register options via WordPress' Settings API.
	 */
	add_action( "load-$hook_suffix", function() {
		add_filter( 'sns_show_submit_button', '__return_false' );

		add_screen_option(
			'per_page', [
				'label' => __( 'Per Page', 'scripts-n-styles' ),
				'default' => 20,
			]
		);

		add_filter( 'set-screen-option', function( $false, $option, $value ) {
			$screen_id = get_current_screen()->id;
			$this_option = "{$screen_id}_per_page";
			if ( $this_option != $option ) {
				return false;
			}

			$value = (int) $value;
			if ( $value < 1 || $value > 100 ) {
				return false;
			}

			return $value;
		}, 10, 3 );

		// hack for core limitation: see http://core.trac.wordpress.org/ticket/18954 .
		set_screen_options();

		add_settings_section(
			'usage',
			__( 'Scripts n Styles Usage', 'scripts-n-styles' ),
			function() {
				?>
				<div style="max-width: 55em;">
					<p><?php esc_html_e( 'The following table shows content that utilizes Scripts n Styles.', 'scripts-n-styles' ); ?></p>
				</div>
				<?php
				require_once( 'class-list-usage.php' );
				$usage_table = new List_Usage();
				$usage_table->prepare_items();
				$usage_table->display();
			},
			ADMIN_MENU_SLUG
		);
	} );
} );
