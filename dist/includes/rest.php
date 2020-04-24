<?php
/**
 * REST integration
 *
 * @package Scripts-N-Styles
 */

namespace unFocus\SnS;

add_action( 'rest_api_init', function() {

	register_rest_route( 'sns/0.1', '/global', [
		[
			'methods'              => 'POST',
			'permissions_callback' => function() {
				return current_user_can( 'manage_options' ) && current_user_can( 'unfiltered_html' );
			},
			// 'args' is schema.
			'args'                 => [],
			'callback'             => function( \WP_REST_Request $request ) {
				$settings = [
					'item1' => $request->get_param( 'item1' ),
					'item2' => $request->get_param( 'item2' ),
				];
				save_settings( $settings );
				return rest_ensure_response( get_sns_settings() )->set_status( 201 );
			},
		],
		[
			'methods'              => 'GET',
			'permissions_callback' => function() {
				return current_user_can( 'manage_options' ) && current_user_can( 'unfiltered_html' );
			},
			// 'args' is schema.
			'args'                 => [],
			'callback'             => function() {
				return rest_ensure_response( get_sns_settings() );
			},
		],
	]);
} );

/**
 * Get defaults.
 */
function get_sns_defaults() {
	return array();
}

/**
 * Get settings.
 */
function get_sns_settings() {
	$saved = get_option( '_SnS', array() );

	if ( ! is_array( $saved ) || ! empty( $saved ) ) {
		return get_sns_defaults();
	}
	return wp_parse_args( $saved, get_sns_defaults() );
}

/**
 * Save settings.
 *
 * @param array $settings The settings.
 */
function save_settings( array $settings ) {

	// remove any non-allowed indexes before save.
	foreach ( $settings as $i => $setting ) {
		if ( ! array_key_exists( $setting, get_sns_defaults() ) ) {
			unset( $settings[ $i ] );
		}
	}
	update_option( '_SnS', $settings );
}
