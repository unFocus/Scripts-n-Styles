<?php
namespace unFocus\SnS;

add_action( 'plugins_loaded', function() {

	register_theme_directory( plugin_dir_path( _FILE_() ) . 'theme' );

	$options = get_option( 'SnS_options' );
	if ( ! isset( $options[ 'version' ] ) || version_compare( VERSION, $options[ 'version' ], '>' ) ) {
		upgrade();
	}
} );

/**
 * Sets defaults if not previously set. Sets stored 'version' to VERSION.
 */
function upgrade() {
	$options = get_option( 'SnS_options' );
	if ( ! $options ) $options = ['version' => '0'];

	$version = $options[ 'version' ];

	/*
	 * upgrade proceedure for 3.0 update
	 */
    if ( version_compare( $version, '3', '<' ) ) :
		$posts = get_posts(
			array(
				'numberposts' => -1,
				'post_type' => 'any',
				'post_status' => 'any',
				'meta_query' => array(
					'relation' => 'OR',
					array( 'key' => '_SnS_scripts' ),
					array( 'key' => '_SnS_styles' ),
					array( 'key' => 'uFp_scripts' ),
					array( 'key' => 'uFp_styles' )
				)
			)
		);

		if ( $posts ) :
		foreach( $posts as $post) {
			$styles = get_post_meta( $post->ID, '_SnS_styles', true );
			if ( empty( $styles ) )
				$styles = get_post_meta( $post->ID, 'uFp_styles', true );

			$scripts = get_post_meta( $post->ID, '_SnS_scripts', true );
			if ( empty( $scripts ) )
				$scripts = get_post_meta( $post->ID, 'uFp_scripts', true );

			$SnS = array();
			if ( ! empty( $styles ) )
				$SnS[ 'styles' ] = $styles;

			if ( ! empty( $scripts ) )
				$SnS[ 'scripts' ] = $scripts;

			if ( ! empty( $SnS ) )
				update_post_meta( $post->ID, '_SnS', $SnS );

			delete_post_meta( $post->ID, 'uFp_styles' );
			delete_post_meta( $post->ID, 'uFp_scripts' );
			delete_post_meta( $post->ID, '_SnS_styles' );
			delete_post_meta( $post->ID, '_SnS_scripts' );
		}
		endif; // if $posts

		$version = '3.0';
	endif; // 3.0 upgrade

	/*
	 * upgrade proceedure for 4.0 update
	 */
    if ( version_compare( $version, '4.0.0', '<' ) ) :
    	// Convert Hoops widget to Text.
		$sidebars_widgets = get_option('sidebars_widgets');
		$widget_sns_hoops = get_option('widget_sns_hoops');
		$widget_text = get_option('widget_text');

		foreach ( $sidebars_widgets as $name => $sidebar ) {
			if ( ! is_array( $sidebar ) ) continue; // ignore metadata in array
			foreach ( $sidebar as $key => $widget_name ) {
				// widget_name is widget array name ("sns_hoops"), a "-", and it's index in the widget array
				if ( stripos($widget_name, "sns_hoops-") !== false ) {
					$sns_index = substr($widget_name, strlen("sns_hoops-"));
					$sns_widget = $widget_sns_hoops[$sns_index];

					$sns_widget['visual'] = true; // Upgrade
					$sns_widget['filter'] = true; // New version is always filter

					$widget_text[] = $sns_widget; // Add widget to text widget array

					$text_index = max(array_keys($widget_text)); // Get text array index

					$sidebars_widgets[$name][$key] = "text-".$text_index; // update sidebars array with new names
				}
			}
		}
		update_option( 'widget_text', $widget_text );
		update_option( 'sidebars_widgets', $sidebars_widgets );
		delete_option( 'widget_sns_hoops' );
		
		$version = '4.0.0';
	endif; // 4.0 upgrade

	$options[ 'version' ] = VERSION;
	update_option( 'SnS_options', $options );
}
