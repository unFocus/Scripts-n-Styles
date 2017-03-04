<?php
namespace unFocus\SnS;

add_action( 'wp_before_admin_bar_render', function() {
	global $wp_admin_bar;
	$wp_admin_bar->add_node( [
		'id'    => 'Scripts_n_Styles',
		'title' => 'Scripts n Styles',
		'href'  => '#',
		'meta'  => array( 'class' => 'Scripts_n_Styles' )
	] );
}, 11 );

register_theme_directory( dirname( __DIR__ ) . '/theme' );

add_action( 'wp_print_styles', function() {
	if ( current_theme_supports( 'scripts-n-styles' ) ) {
		$options = get_option( 'SnS_options' );
		$slug = get_stylesheet();

		if ( ! empty( $options[ 'themes' ][ $slug ][ 'compiled' ] ) ) {
			wp_deregister_style( 'theme_style' );
			wp_enqueue_style( 'theme_style', add_query_arg( array( 'action' => 'sns_theme_css' ), admin_url( "admin-ajax.php" ) ) );
		}
	}
} );

add_action( 'wp_ajax_sns_theme_css', '\unFocus\SnS\theme_css' );
add_action( 'wp_ajax_nopriv_sns_theme_css', '\unFocus\SnS\theme_css' );
function theme_css() {
	$options = get_option( 'SnS_options' );
	$slug = get_stylesheet();
	$compiled = $options[ 'themes' ][ $slug ][ 'compiled' ];
	header('Expires: ' . gmdate( "D, d M Y H:i:s", time() + 864000 ) . ' GMT');
	header("Cache-Control: public, max-age=864000");
	header('Content-Type: text/css; charset=UTF-8');
	echo $compiled;
	die();
}
add_action( 'widgets_init', function() {
	$options = get_option( 'SnS_options' );
	if ( isset( $options[ 'hoops_widget' ] ) && 'yes' == $options[ 'hoops_widget' ] )
		register_widget( '\unFocus\SnS\Widget' );
} );
add_action( 'plugins_loaded', function() {
	add_shortcode( 'sns_shortcode', '\unFocus\SnS\hoops_shortcode' );
	add_shortcode( 'hoops', '\unFocus\SnS\hoops_shortcode' );
} );
function hoops_shortcode( $atts, $content = null, $tag ) {
	global $post;
	extract( shortcode_atts( array( 'name' => 0, ), $atts ) );
	$output = '';

	$options = get_option( 'SnS_options' );
	$hoops = isset( $options['hoops']['shortcodes'] ) ? $options['hoops']['shortcodes'] : array();

	if ( isset( $post->ID ) ) {
		$SnS = get_post_meta( $post->ID, '_SnS', true );
		$shortcodes = isset( $SnS['shortcodes'] ) ? $SnS[ 'shortcodes' ]: array();
	}

	if ( isset( $shortcodes[ $name ] ) )
		$output .= $shortcodes[ $name ];
	else if ( isset( $hoops[ $name ] ) )
		$output .= $hoops[ $name ];

	if ( ! empty( $content ) && empty( $output ) )
		$output = $content;
	$output = do_shortcode( $output );

	return $output;
}

add_action( 'wp_enqueue_scripts', '\unFocus\SnS\register_scripts' );
add_action( 'admin_enqueue_scripts', '\unFocus\SnS\register_scripts' );
function register_scripts() {
	$dir = plugins_url( '/', __DIR__ );

	$vendor = $dir . 'vendor/';
	wp_register_script( 'clean-css', $vendor . 'cleancss-browser.js', array(), '3.4.21-min' );
	wp_register_script( 'less.js', $vendor . 'less.min.js', array(), '2.7.1-min' );
	wp_register_script( 'coffeescript', $vendor . 'coffee-script.js', array(), '1.12.1-min' );
	wp_register_script( 'chosen', $vendor . 'chosen/chosen.jquery.min.js', array( 'jquery' ), '1.0.0', true );
	wp_register_style(  'chosen', $vendor . 'chosen/chosen.min.css', array(), '1.0.0' );
	//wp_register_script( 'coffeelint', $vendor . 'coffeelint.js', array(), '0.5.6' );
	//wp_register_script( 'mustache', $vendor . 'chosen/jquery.mustache.min.js', array( 'jquery' ), '0.7.2', true );
	wp_register_script( 'html5shiv', $vendor . 'html5shiv.js', array(), '3.7.3' );
	wp_register_script( 'html5shiv-printshiv', $vendor . 'html5shiv-printshiv.js', array(), '3.7.3' );

	//wp_register_script( 'google-diff-match-patch', $vendor . 'codemirror/diff_match_patch.js', array() );
	wp_register_script( 'codemirror', $vendor . 'codemirror/codemirror.min.js', array( /*'google-diff-match-patch'*/ ), '5.21.0' );
	wp_register_style(  'codemirror', $vendor . 'codemirror/codemirror.min.css', array(), '5.21.0' );

	$js = $dir . 'js/';
	wp_register_script( 'sns-global-page', $js . 'global-page.js', array( 'jquery', 'codemirror', 'less.js', 'coffeescript', 'chosen' ), VERSION, true );
	wp_register_script( 'sns-theme-page', $js . 'theme-page.js', array( 'jquery', 'codemirror', 'less.js', 'clean-css' ), VERSION, true );
	wp_register_script( 'sns-hoops-page', $js . 'hoops-page.js', array( 'jquery', 'codemirror' ), VERSION, true );
	wp_register_script( 'sns-settings-page', $js . 'settings-page.js', array( 'jquery', 'codemirror' ), VERSION, true );
	wp_register_script( 'sns-meta-box', $js . 'meta-box.js', array( 'editor', 'jquery-ui-tabs', 'codemirror', 'chosen' ), VERSION, true );
	wp_register_script( 'sns-code-editor',  $js . 'code-editor.js',  array( 'editor', 'jquery-ui-tabs', 'codemirror' ), VERSION, true );

	$css = $dir . 'css/';
	wp_register_style(  'sns-options', $css . 'options-styles.css', array( 'codemirror' ), VERSION );
	wp_register_style(  'sns-meta-box', $css . 'meta-box.css', array( 'codemirror' ), VERSION );
	wp_register_style(  'sns-code-editor', $css . 'code-editor.css', array( 'codemirror' ), VERSION );
}

/**
 * Theme Action: 'wp_head()'
 * Outputs the globally and individually set Styles in the Theme's head element.
 */
add_action( 'wp_head', function() {
	// Global
	$options = get_option( 'SnS_options' );
	if ( ! empty( $options ) && ! empty( $options[ 'styles' ] ) ) {
		?><style type="text/css" id="sns_global_styles"><?php
		echo $options[ 'styles' ];
		?></style><?php
	}
	if ( ! empty( $options ) && ! empty( $options[ 'compiled' ] ) ) {
		?><style type="text/css" id="sns_global_less_compiled"><?php
		echo $options[ 'compiled' ];
		?></style><?php
	}

	if ( ! is_singular() ) return;
	// Individual
	global $post;
	$SnS = get_post_meta( $post->ID, '_SnS', true );
	$styles = isset( $SnS['styles'] ) ? $SnS[ 'styles' ]: array();
	if ( ! empty( $styles ) && ! empty( $styles[ 'styles' ] ) ) {
		?><style type="text/css" id="sns_styles"><?php
		echo $styles[ 'styles' ];
		?></style><?php
	}
}, 11 );


/**
 * Theme Action: 'wp_footer()'
 * Outputs the globally and individually set Scripts at the end of the Theme's body element.
 */
add_action( 'wp_footer', function() {
	// Global
	$options = get_option( 'SnS_options' );
	if ( ! empty( $options ) && ! empty( $options[ 'scripts' ] ) ) {
		?><script type="text/javascript" id="sns_global_scripts"><?php
		echo $options[ 'scripts' ];
		?></script><?php
	}
	if ( ! empty( $options ) && ! empty( $options[ 'coffee_compiled' ] ) ) {
		?><script type="text/javascript" id="sns_global_coffee_compiled"><?php
		echo $options[ 'coffee_compiled' ];
		?></script><?php
	}

	if ( ! is_singular() ) return;
	// Individual
	global $post;
	$SnS = get_post_meta( $post->ID, '_SnS', true );
	$scripts = isset( $SnS['scripts'] ) ? $SnS[ 'scripts' ]: array();
	if ( ! empty( $scripts ) && ! empty( $scripts[ 'scripts' ] ) ) {
		?><script type="text/javascript" id="sns_scripts"><?php
		echo $scripts[ 'scripts' ];
		?></script><?php
	}
}, 11 );


/**
 * Theme Action: 'wp_head()'
 * Outputs the globally and individually set Scripts in the Theme's head element.
 */
add_action( 'wp_head', function() {
	// Global
	$options = get_option( 'SnS_options' );
	if ( ! empty( $options ) && ! empty( $options[ 'scripts_in_head' ] ) ) {
		?><script type="text/javascript" id="sns_global_scripts_in_head"><?php
		echo $options[ 'scripts_in_head' ];
		?></script><?php
	}

	if ( ! is_singular() ) return;
	// Individual
	global $post;
	$SnS = get_post_meta( $post->ID, '_SnS', true );
	$scripts = isset( $SnS['scripts'] ) ? $SnS[ 'scripts' ]: array();
	if ( ! empty( $scripts ) && ! empty( $scripts[ 'scripts_in_head' ] ) ) {
		?><script type="text/javascript" id="sns_scripts_in_head"><?php
		echo $scripts[ 'scripts_in_head' ];
		?></script><?php
	}
}, 11 );


/**
 * Theme Filter: 'body_class()'
 * Adds classes to the Theme's body tag.
 * @uses self::get_styles()
 * @param array $classes
 * @return array $classes
 */
add_filter( 'body_class', function( $classes ) {
	if ( ! is_singular() || is_admin() ) return $classes;

	global $post;
	$SnS = get_post_meta( $post->ID, '_SnS', true );
	$styles = isset( $SnS['styles'] ) ? $SnS[ 'styles' ]: array();
	if ( ! empty( $styles ) && ! empty( $styles[ 'classes_body' ] ) )
		$classes = array_merge( $classes, explode( " ", $styles[ 'classes_body' ] ) );

	return $classes;
} );


/**
 * Theme Filter: 'post_class()'
 * Adds classes to the Theme's post container.
 * @param array $classes
 * @return array $classes
 */
add_filter( 'post_class', function( $classes ) {
	if ( ! is_singular() || is_admin() ) return $classes;

	global $post;
	$SnS = get_post_meta( $post->ID, '_SnS', true );
	$styles = isset( $SnS['styles'] ) ? $SnS[ 'styles' ]: array();

	if ( ! empty( $styles ) && ! empty( $styles[ 'classes_post' ] ) )
		$classes = array_merge( $classes, explode( " ", $styles[ 'classes_post' ] ) );

	return $classes;
} );

/**
 * Theme Action: 'wp_enqueue_scripts'
 * Enqueues chosen Scripts.
 */
add_action( 'wp_enqueue_scripts', function() {
	// Global
	$options = get_option( 'SnS_options' );
	if ( ! isset( $options[ 'enqueue_scripts' ] ) )
		$enqueue_scripts = array();
	else
		$enqueue_scripts = $options[ 'enqueue_scripts' ];

	foreach ( $enqueue_scripts as $handle )
		wp_enqueue_script( $handle );

	if ( ! is_singular() ) return;
	// Individual
	global $post;
	$SnS = get_post_meta( $post->ID, '_SnS', true );
	$scripts = isset( $SnS['scripts'] ) ? $SnS[ 'scripts' ]: array();

	if ( ! empty( $scripts[ 'enqueue_scripts' ] ) && is_array( $scripts[ 'enqueue_scripts' ] ) ) {
		foreach ( $scripts[ 'enqueue_scripts' ] as $handle )
			wp_enqueue_script( $handle );
	}
}, 11 );

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
