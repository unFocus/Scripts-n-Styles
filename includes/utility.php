<?php
namespace unFocus\SnS;

// For things that don't belong in main or admin
add_action( 'plugins_loaded', function() {

	register_theme_directory( dirname( __DIR__ ) . '/theme' );

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
    if ( version_compare( $version, '4', '<' ) ) :
    	// There may yet be an upgrade routine.
		$version = '4.0';
	endif; // 4.0 upgrade

	$options[ 'version' ] = VERSION;
	update_option( 'SnS_options', $options );
}