<?php
/**
 * REST integration
 *
 * @package Scripts-N-Styles
 */

namespace unFocus\SnS;

add_action('admin_menu', function() {
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'unfiltered_html' ) ) {
		return;
	}

	$hook_suffix = add_submenu_page(
		ADMIN_MENU_SLUG,
		__( 'Scripts n Styles', 'scripts-n-styles' ),
		__( 'REST Test', 'scripts-n-styles' ),
		'unfiltered_html',
		ADMIN_MENU_SLUG . '_rest',
		function() {
			?>
			<div id="scripts-n-styles"></div>
			<?php
		}
	);

	add_action( "admin_print_styles-$hook_suffix", function() {
		wp_enqueue_script( 'sns-rest' );
		wp_localize_script( 'sns-rest', 'snsREST', array(
			'strings' => array(
				'saved' => __( 'Settings Saved', 'scripts-n-styles' ),
				'error' => __( 'Error', 'scripts-n-styles' ),
			),
			'api'     => array(
				'root'  => esc_url_raw( rest_url() ),
				'url'   => esc_url_raw( rest_url( 'sns/1.0' ) ),
				'nonce' => wp_create_nonce( 'wp_rest' ),
			),
		) );
	} );
} );
