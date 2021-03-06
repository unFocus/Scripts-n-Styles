<?php
/**
 * Utility functions
 *
 * Mostly just handles upgrade procedures, but also registers the theme.
 *
 * @package Scripts-N-Styles
 */

namespace unFocus\SnS;

add_action( 'plugins_loaded', function() {

	register_theme_directory( plugin_dir_path( SNS_FILE ) . 'theme' );

	$options = get_option( 'SnS_options' );
	if ( ! isset( $options['version'] ) || version_compare( VERSION, $options['version'], '>' ) ) {
		upgrade();
	}
} );

/**
 * Sets defaults if not previously set. Sets stored 'version' to VERSION.
 */
function upgrade() {
	$options = get_option( 'SnS_options' );
	if ( ! $options ) {
		$options = [ 'version' => '0' ];
	}

	$version = $options['version'];

	/*
	 * upgrade proceedure for 4.0 update
	 */
	if ( version_compare( $version, '4.0.0', '<' ) ) :
		// Convert Hoops widget to Text.
		$sidebars_widgets = get_option( 'sidebars_widgets' );
		$widget_sns_hoops = get_option( 'widget_sns_hoops' );
		$widget_text      = get_option( 'widget_text' );

		foreach ( $sidebars_widgets as $name => $sidebar ) {
			if ( ! is_array( $sidebar ) ) {
				continue; // ignore metadata in array.
			}
			foreach ( $sidebar as $key => $widget_name ) {
				// widget_name is widget array name ("sns_hoops"), a "-", and it's index in the widget array.
				if ( stripos( $widget_name, 'sns_hoops-' ) !== false ) {
					$sns_index  = substr( $widget_name, strlen( 'sns_hoops-' ) );
					$sns_widget = $widget_sns_hoops[ $sns_index ];

					$sns_widget['visual'] = true; // Upgrade.
					$sns_widget['filter'] = true; // New version is always filter.

					$widget_text[] = $sns_widget; // Add widget to text widget array.

					$text_index = max( array_keys( $widget_text ) ); // Get text array index.

					$sidebars_widgets[ $name ][ $key ] = 'text-' . $text_index; // update sidebars array with new names.
				}
			}
		}
		update_option( 'widget_text', $widget_text );
		update_option( 'sidebars_widgets', $sidebars_widgets );
		delete_option( 'widget_sns_hoops' );

		$version = '4.0.0';
	endif; // 4.0 upgrade

	$options['version'] = VERSION;
	update_option( 'SnS_options', $options );
}
