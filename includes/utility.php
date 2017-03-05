<?php
// For things that don't belong in main or admin

register_theme_directory( dirname( __DIR__ ) . '/theme' );

/**
 * Utility Method: Compares VERSION to stored 'version' value.
 */
add_action( 'plugins_loaded', function() {
	$options = get_option( 'SnS_options' );
	if ( ! isset( $options[ 'version' ] ) || version_compare( VERSION, $options[ 'version' ], '>' ) ) {
		include_once( 'includes/class-sns-admin.php' );
		Admin::upgrade();
	}
} );
