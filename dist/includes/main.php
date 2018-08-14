<?php
/**
 * Main plugin functions.
 *
 * Handles plugin's theme output.
 *
 * @package Scripts-N-Styles
 */

namespace unFocus\SnS;

// Add menu to admin bar.
add_action( 'wp_before_admin_bar_render', function() {
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'unfiltered_html' ) ) {
		return;
	}
	global $wp_admin_bar;
	$title  = esc_html__( 'Scripts n Styles', 'scripts-n-styles' );
	$title .= WP_DEBUG ? ' (PHP: ' . PHP_VERSION . ')' : '';
	$wp_admin_bar->add_node( [
		'id'    => 'Scripts_n_Styles',
		'title' => $title,
		'href'  => admin_url( 'admin.php?page=' . ADMIN_MENU_SLUG ),
		'meta'  => [ 'class' => 'Scripts_n_Styles' ],
	] );
}, 11 );


/**
 * Theme support of Scripts n Styles
 * If a theme registers support, replace css with a generated version.
 */
add_action( 'wp_print_styles', function() {
	if ( ! current_theme_supports( 'scripts-n-styles' ) ) {
		return;
	}

	$options = get_option( 'SnS_options' );
	$slug    = get_stylesheet();

	if ( ! empty( $options['themes'][ $slug ]['compiled'] ) ) {
		wp_deregister_style( 'theme_style' );
		wp_enqueue_style( 'theme_style', add_query_arg( [ 'action' => 'sns_theme_css' ], admin_url( 'admin-ajax.php' ) ) );
	}
} );

/**
 * Gets the generated theme css
 */
function theme_css() {
	$options  = get_option( 'SnS_options' );
	$slug     = get_stylesheet();
	$compiled = $options['themes'][ $slug ]['compiled'];
	header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + 864000 ) . ' GMT' );
	header( 'Cache-Control: public, max-age=864000' );
	header( 'Content-Type: text/css; charset=UTF-8' );
	echo $compiled; // WPCS: XSS OK.
	die();
}
add_action( 'wp_ajax_sns_theme_css', '\unFocus\SnS\theme_css' );
add_action( 'wp_ajax_nopriv_sns_theme_css', '\unFocus\SnS\theme_css' );

/**
 * The Hoops shortcode
 *
 * @param string $atts Stuff.
 * @param string $content Stuff.
 * @param string $tag Stuff.
 */
function hoops_shortcode( $atts, $content = null, $tag ) {
	global $post;
	$atts   = shortcode_atts( [ 'name' => 0 ], $atts );
	$output = '';

	$options = get_option( 'SnS_options' );
	$hoops   = isset( $options['hoops']['shortcodes'] ) ? $options['hoops']['shortcodes'] : [];

	if ( isset( $post->ID ) ) {
		$sns        = get_post_meta( $post->ID, '_SnS', true );
		$shortcodes = isset( $sns['shortcodes'] ) ? $sns['shortcodes'] : [];
	}

	if ( isset( $shortcodes[ $atts['name'] ] ) ) {
		$output .= $shortcodes[ $atts['name'] ];
	} elseif ( isset( $hoops[ $atts['name'] ] ) ) {
		$output .= $hoops[ $atts['name'] ];
	}

	if ( ! empty( $content ) && empty( $output ) ) {
		$output = $content;
	}
	$output = do_shortcode( $output );

	return $output;
}
add_action( 'plugins_loaded', function() {
	add_shortcode( 'sns_shortcode', '\unFocus\SnS\hoops_shortcode' );
	add_shortcode( 'hoops', '\unFocus\SnS\hoops_shortcode' );
} );

/**
 * Register legacy scripts
 *
 * These where registered and made available to users in the past.
 */
function legacy_scripts() {
	$dir    = plugins_url( '/', BASENAME );
	$vendor = $dir . 'legacy/';
	wp_register_script( 'clean-css', $vendor . 'cleancss-browser.js', [], '3.4.21-min' );
	wp_register_script( 'less.js', $vendor . 'less.min.js', [], '2.7.1-min' );
	wp_register_script( 'coffeescript', $vendor . 'coffee-script.js', [], '1.12.1-min' );
	wp_register_script( 'chosen', $vendor . 'chosen/chosen.jquery.min.js', [ 'jquery' ], '1.0.0', true );
	wp_register_style( 'chosen', $vendor . 'chosen/chosen.min.css', [], '1.0.0' );
	wp_register_script( 'html5shiv', $vendor . 'html5shiv.js', [], '3.7.3' );
	wp_register_script( 'html5shiv-printshiv', $vendor . 'html5shiv-printshiv.js', [], '3.7.3' );
}
add_action( 'wp_enqueue_scripts', '\unFocus\SnS\legacy_scripts' );
add_action( 'admin_enqueue_scripts', '\unFocus\SnS\legacy_scripts' );


/**
 * Register bundled scripts
 */
function register_scripts() {
	$dir = plugins_url( '/', BASENAME );

	$js = $dir . 'js/';
	wp_register_script( 'sns-global-page', $js . 'global-page.min.js', [], VERSION, true );
	wp_register_script( 'sns-theme-page', $js . 'theme-page.min.js', [], VERSION, true );
	wp_register_script( 'sns-hoops-page', $js . 'hoops-page.min.js', [], VERSION, true );
	wp_register_script( 'sns-meta-box', $js . 'meta-box.min.js', [ 'editor', 'jquery-ui-tabs' ], VERSION, true );

	// Use a correctly bundled version of CodeMirror.
	wp_deregister_script( 'wp-codemirror' );
	wp_register_script( 'wp-codemirror', $dir . 'vendor/codemirror/codemirror.min.js', [], '5.39.2', true );

	// // Add Themes to bundled version of CodeMirror.
	wp_deregister_style( 'wp-codemirror' );
	wp_register_style( 'wp-codemirror', $dir . 'vendor/codemirror/codemirror.min.css', [], '5.39.2' );

	$css = $dir . 'css/';
	wp_register_style( 'sns-options', $css . 'options-styles.css', [], VERSION );
	wp_register_style( 'sns-meta-box', $css . 'meta-box.css', [], VERSION );
}
add_action( 'wp_enqueue_scripts', '\unFocus\SnS\register_scripts' );
add_action( 'admin_enqueue_scripts', '\unFocus\SnS\register_scripts' );

/**
 * Theme Action: 'wp_head()'
 * Outputs the globally and individually set Styles in the Theme's head element.
 */
add_action( 'wp_head', function() {
	// Global output.
	$options = get_option( 'SnS_options' );
	if ( ! empty( $options ) && ! empty( $options['styles'] ) ) {
		echo '<style type="text/css" id="sns_global_styles">';
		echo $options['styles']; // WPCS: XSS OK.
		echo '</style>';
	}
	if ( ! empty( $options ) && ! empty( $options['compiled'] ) ) {
		echo '<style type="text/css" id="sns_global_less_compiled">';
		echo $options['compiled']; // WPCS: XSS OK.
		echo '</style>';
	}

	if ( ! is_singular() ) {
		return;
	}
	// Individual.
	global $post;
	$sns    = get_post_meta( $post->ID, '_SnS', true );
	$styles = isset( $sns['styles'] ) ? $sns['styles'] : [];
	if ( ! empty( $styles ) && ! empty( $styles['styles'] ) ) {
		echo '<style type="text/css" id="sns_styles">';
		echo $styles['styles']; // WPCS: XSS OK.
		echo '</style>';
	}
}, 11 );

/**
 * Theme Action: 'wp_footer()'
 * Outputs the globally and individually set Scripts at the end of the Theme's body element.
 */
add_action( 'wp_footer', function() {
	// Global output.
	$options = get_option( 'SnS_options' );
	if ( ! empty( $options ) && ! empty( $options['scripts'] ) ) {
		?>
		<script type="text/javascript" id="sns_global_scripts">
		<?php
		echo $options['scripts']; // WPCS: XSS OK.
		?>
		</script>
		<?php
	}
	if ( ! empty( $options ) && ! empty( $options['coffee_compiled'] ) ) {
		?>
		<script type="text/javascript" id="sns_global_coffee_compiled">
		<?php
		echo $options['coffee_compiled']; // WPCS: XSS OK.
		?>
		</script>
		<?php
	}

	if ( ! is_singular() ) {
		return;
	}
	// Individual.
	global $post;
	$sns     = get_post_meta( $post->ID, '_SnS', true );
	$scripts = isset( $sns['scripts'] ) ? $sns['scripts'] : [];
	if ( ! empty( $scripts ) && ! empty( $scripts['scripts'] ) ) {
		?>
		<script type="text/javascript" id="sns_scripts">
		<?php
		echo $scripts['scripts']; // WPCS: XSS OK.
		?>
		</script>
		<?php
	}
}, 11 );

/**
 * Theme Action: 'wp_head()'
 * Outputs the globally and individually set Scripts in the Theme's head element.
 */
add_action( 'wp_head', function() {
	// Global ouput.
	$options = get_option( 'SnS_options' );
	if ( ! empty( $options ) && ! empty( $options['scripts_in_head'] ) ) {
		?>
		<script type="text/javascript" id="sns_global_scripts_in_head">
		<?php
		echo $options['scripts_in_head']; // WPCS: XSS OK.
		?>
		</script>
		<?php
	}

	if ( ! is_singular() ) {
		return;
	}
	// Individual.
	global $post;
	$sns     = get_post_meta( $post->ID, '_SnS', true );
	$scripts = isset( $sns['scripts'] ) ? $sns['scripts'] : [];
	if ( ! empty( $scripts ) && ! empty( $scripts['scripts_in_head'] ) ) {
		?>
		<script type="text/javascript" id="sns_scripts_in_head">
		<?php
		echo $scripts['scripts_in_head']; // WPCS: XSS OK.
		?>
		</script>
		<?php
	}
}, 11 );

/**
 * Theme Filter: 'body_class()'
 * Adds classes to the Theme's body tag.
 */
add_filter( 'body_class', function( $classes ) {
	if ( ! is_singular() || is_admin() ) {
		return $classes;
	}

	global $post;
	$sns    = get_post_meta( $post->ID, '_SnS', true );
	$styles = isset( $sns['styles'] ) ? $sns['styles'] : [];
	if ( ! empty( $styles ) && ! empty( $styles['classes_body'] ) ) {
		$classes = array_merge( $classes, explode( ' ', $styles['classes_body'] ) );
	}

	return $classes;
} );

/**
 * Theme Filter: 'post_class()'
 * Adds classes to the Theme's post container.
 */
add_filter( 'post_class', function( $classes ) {
	if ( ! is_singular() || is_admin() ) {
		return $classes;
	}

	global $post;
	$sns    = get_post_meta( $post->ID, '_SnS', true );
	$styles = isset( $sns['styles'] ) ? $sns['styles'] : [];

	if ( ! empty( $styles ) && ! empty( $styles['classes_post'] ) ) {
		$classes = array_merge( $classes, explode( ' ', $styles['classes_post'] ) );
	}

	return $classes;
} );

/**
 * Theme Action: 'wp_enqueue_scripts'
 * Enqueues chosen Scripts.
 */
add_action( 'wp_enqueue_scripts', function() {
	// Global output.
	$options = get_option( 'SnS_options' );
	if ( ! isset( $options['enqueue_scripts'] ) ) {
		$enqueue_scripts = [];
	} else {
		$enqueue_scripts = $options['enqueue_scripts'];
	}

	foreach ( $enqueue_scripts as $handle ) {
		wp_enqueue_script( $handle );
	}

	if ( ! is_singular() ) {
		return;
	}
	// Individual.
	global $post;
	$sns     = get_post_meta( $post->ID, '_SnS', true );
	$scripts = isset( $sns['scripts'] ) ? $sns['scripts'] : [];

	if ( ! empty( $scripts['enqueue_scripts'] ) && is_array( $scripts['enqueue_scripts'] ) ) {
		foreach ( $scripts['enqueue_scripts'] as $handle ) {
			wp_enqueue_script( $handle );
		}
	}
}, 11 );
