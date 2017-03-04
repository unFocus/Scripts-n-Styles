<?

class Scripts_n_Styles
{
	/**#@+
	 * @static
	 */
	const VERSION = '4.0.0-alpha';
	static $file = null;
	static $cm_themes = array( 'default',
		'3024-day', '3024-night', 'abcdef', 'ambiance',
		'base16-dark', 'base16-light', 'bespin', 'blackboard',
		'cobalt', 'colorforth',
		'dracula', 'duotone-dark', 'duotone-light',
		'eclipse', 'elegant', 'erlang-dark',
		'hopscotch', 'icecoder', 'isotope',
		'lesser-dark', 'liquibyte',
		'material', 'mbo', 'mdn-like', 'midnight', 'monokai',
		'neat', 'neo', 'night',
		'panda-syntax', 'paraiso-dark', 'paraiso-light', 'pastel-on-dark',
		'railscasts', 'rubyblue',
		'seti', 'solarized',
		'the-matrix', 'tomorrow-night-bright', 'tomorrow-night-eighties',
		'ttcn', 'twilight',
		'vibrant-ink',
		'xq-dark', 'xq-light',
		'yeti', 'zenburn' );
	/**#@-*/

	/**
	 * Initializing method. Checks if is_admin() and registers action hooks for admin if true. Sets filters and actions for Theme side functions.
	 * @static
	 */
	static function init() {
		self::$file = dirname(__FILE__);
		if ( is_admin() && ! ( defined('DISALLOW_UNFILTERED_HTML') && DISALLOW_UNFILTERED_HTML ) ) {
			/*	NOTE: Setting the DISALLOW_UNFILTERED_HTML constant to
				true in the wp-config.php would effectively disable this
				plugin's admin because no user would have the capability.
			*/
			include_once( 'class-sns-admin.php' );
			SnS_Admin::init();
		}
		register_theme_directory( dirname( self::$file ) . '/theme' );
		add_action( 'plugins_loaded', array( __CLASS__, 'upgrade_check' ) );

		add_filter( 'body_class', array( __CLASS__, 'body_classes' ) );
		add_filter( 'post_class', array( __CLASS__, 'post_classes' ) );

		add_action( 'wp_head', array( __CLASS__, 'styles' ), 11 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ), 11 );
		add_action( 'wp_head', array( __CLASS__, 'scripts_in_head' ), 11 );
		add_action( 'wp_footer', array( __CLASS__, 'scripts' ), 11 );

		add_action( 'plugins_loaded', array( __CLASS__, 'add_shortcodes' ) );
		add_action( 'widgets_init', array( __CLASS__, 'add_widget' ) );

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'register' ) );

		add_action( 'wp_print_styles', array( __CLASS__, 'theme_style' ) );
		add_action( 'wp_ajax_sns_theme_css', array( __CLASS__, 'theme_css' ) );
		add_action( 'wp_ajax_nopriv_sns_theme_css', array( __CLASS__, 'theme_css' ) );
	}
	static function theme_style() {
		if ( current_theme_supports( 'scripts-n-styles' ) ) {
			$options = get_option( 'SnS_options' );
			$slug = get_stylesheet();

			if ( ! empty( $options[ 'themes' ][ $slug ][ 'compiled' ] ) ) {
				wp_deregister_style( 'theme_style' );
				wp_enqueue_style( 'theme_style', add_query_arg( array( 'action' => 'sns_theme_css' ), admin_url( "admin-ajax.php" ) ) );
			}
		}
	}
	static function theme_css() {
		$options = get_option( 'SnS_options' );
		$slug = get_stylesheet();
		$compiled = $options[ 'themes' ][ $slug ][ 'compiled' ];
		header('Expires: ' . gmdate( "D, d M Y H:i:s", time() + 864000 ) . ' GMT');
		header("Cache-Control: public, max-age=864000");
		header('Content-Type: text/css; charset=UTF-8');
		echo $compiled;
		die();
	}
	static function add_widget() {
		$options = get_option( 'SnS_options' );
		if ( isset( $options[ 'hoops_widget' ] ) && 'yes' == $options[ 'hoops_widget' ] )
			register_widget( 'SnS_Widget' );
	}
	static function add_shortcodes() {
		add_shortcode( 'sns_shortcode', array( __CLASS__, 'shortcode' ) );
		add_shortcode( 'hoops', array( __CLASS__, 'shortcode' ) );
	}
	static function shortcode( $atts, $content = null, $tag ) {
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
	static function hoops_widget( $atts, $content = null, $tag ) {
		$options = get_option( 'SnS_options' );
		$hoops = $options['hoops']['shortcodes'];

		extract( shortcode_atts( array( 'name' => 0, ), $atts ) );
		$output = '';

		$shortcodes = isset( $SnS['shortcodes'] ) ? $SnS[ 'shortcodes' ]: array();

		if ( isset( $hoops[ $name ] ) )
			$output .= $hoops[ $name ];

		if ( ! empty( $content ) && empty( $output ) )
			$output = $content;
		$output = do_shortcode( $output );

		return $output;
	}

	/**
	 * Utility Method
	 */
	static function get_wp_registered() {
		/* This is a collection of scripts that are listed as registered after running `wp_head` and `wp_footer` actions on the theme side. */
		return array(
			'utils', 'common', 'sack', 'quicktags', 'colorpicker', 'editor', 'wp-fullscreen', 'wp-ajax-response', 'wp-pointer', 'autosave',
			'heartbeat', 'wp-auth-check', 'wp-lists', 'prototype', 'scriptaculous-root', 'scriptaculous-builder', 'scriptaculous-dragdrop',
			'scriptaculous-effects', 'scriptaculous-slider', 'scriptaculous-sound', 'scriptaculous-controls', 'scriptaculous', 'cropper',
			'jquery', 'jquery-core', 'jquery-migrate', 'jquery-ui-core', 'jquery-effects-core', 'jquery-effects-blind', 'jquery-effects-bounce',
			'jquery-effects-clip', 'jquery-effects-drop', 'jquery-effects-explode', 'jquery-effects-fade', 'jquery-effects-fold',
			'jquery-effects-highlight', 'jquery-effects-pulsate', 'jquery-effects-scale', 'jquery-effects-shake', 'jquery-effects-slide',
			'jquery-effects-transfer', 'jquery-ui-accordion', 'jquery-ui-autocomplete', 'jquery-ui-button', 'jquery-ui-datepicker',
			'jquery-ui-dialog', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-menu', 'jquery-ui-mouse', 'jquery-ui-position',
			'jquery-ui-progressbar', 'jquery-ui-resizable', 'jquery-ui-selectable', 'jquery-ui-slider', 'jquery-ui-sortable',
			'jquery-ui-spinner', 'jquery-ui-tabs', 'jquery-ui-tooltip', 'jquery-ui-widget', 'jquery-form', 'jquery-color', 'suggest',
			'schedule', 'jquery-query', 'jquery-serialize-object', 'jquery-hotkeys', 'jquery-table-hotkeys', 'jquery-touch-punch',
			'jquery-masonry', 'thickbox', 'jcrop', 'swfobject', 'plupload', 'plupload-html5', 'plupload-flash', 'plupload-silverlight',
			'plupload-html4', 'plupload-all', 'plupload-handlers', 'wp-plupload', 'swfupload', 'swfupload-swfobject', 'swfupload-queue',
			'swfupload-speed', 'swfupload-all', 'swfupload-handlers', 'comment-reply', 'json2', 'underscore', 'backbone', 'wp-util',
			'wp-backbone', 'revisions', 'imgareaselect', 'mediaelement', 'wp-mediaelement', 'password-strength-meter', 'user-profile',
			'user-suggest', 'admin-bar', 'wplink', 'wpdialogs', 'wpdialogs-popup', 'word-count', 'media-upload', 'hoverIntent', 'customize-base',
			'customize-loader', 'customize-preview', 'customize-controls', 'accordion', 'shortcode', 'media-models', 'media-views',
			'media-editor', 'mce-view', 'less.js', 'coffeescript', 'chosen', 'coffeelint', 'mustache', 'html5shiv', 'html5shiv-printshiv',
			'google-diff-match-patch', 'codemirror'
		);
	}
	static function register() {
		$dir = plugins_url( '/', dirname(__FILE__) );

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
		wp_register_script( 'sns-global-page', $js . 'global-page.js', array( 'jquery', 'codemirror', 'less.js', 'coffeescript', 'chosen' ), self::VERSION, true );
		wp_register_script( 'sns-theme-page', $js . 'theme-page.js', array( 'jquery', 'codemirror', 'less.js', 'clean-css' ), self::VERSION, true );
		wp_register_script( 'sns-hoops-page', $js . 'hoops-page.js', array( 'jquery', 'codemirror' ), self::VERSION, true );
		wp_register_script( 'sns-settings-page', $js . 'settings-page.js', array( 'jquery', 'codemirror' ), self::VERSION, true );
		wp_register_script( 'sns-meta-box', $js . 'meta-box.js', array( 'editor', 'jquery-ui-tabs', 'codemirror', 'chosen' ), self::VERSION, true );
		wp_register_script( 'sns-code-editor',  $js . 'code-editor.js',  array( 'editor', 'jquery-ui-tabs', 'codemirror' ), self::VERSION, true );

		$css = $dir . 'css/';
		wp_register_style(  'sns-options', $css . 'options-styles.css', array( 'codemirror' ), self::VERSION );
		wp_register_style(  'sns-meta-box', $css . 'meta-box.css', array( 'codemirror' ), self::VERSION );
		wp_register_style(  'sns-code-editor', $css . 'code-editor.css', array( 'codemirror' ), self::VERSION );
	}

	/**
	 * Theme Action: 'wp_head()'
	 * Outputs the globally and individually set Styles in the Theme's head element.
	 */
	static function styles() {
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
	}

	/**
	 * Theme Action: 'wp_footer()'
	 * Outputs the globally and individually set Scripts at the end of the Theme's body element.
	 */
	static function scripts() {
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
	}

	/**
	 * Theme Action: 'wp_head()'
	 * Outputs the globally and individually set Scripts in the Theme's head element.
	 */
	static function scripts_in_head() {
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
	}

	/**
	 * Theme Filter: 'body_class()'
	 * Adds classes to the Theme's body tag.
	 * @uses self::get_styles()
	 * @param array $classes
	 * @return array $classes
	 */
	static function body_classes( $classes ) {
		if ( ! is_singular() || is_admin() ) return $classes;

		global $post;
		$SnS = get_post_meta( $post->ID, '_SnS', true );
		$styles = isset( $SnS['styles'] ) ? $SnS[ 'styles' ]: array();
		if ( ! empty( $styles ) && ! empty( $styles[ 'classes_body' ] ) )
			$classes = array_merge( $classes, explode( " ", $styles[ 'classes_body' ] ) );

		return $classes;
	}

	/**
	 * Theme Filter: 'post_class()'
	 * Adds classes to the Theme's post container.
	 * @param array $classes
	 * @return array $classes
	 */
	static function post_classes( $classes ) {
		if ( ! is_singular() || is_admin() ) return $classes;

		global $post;
		$SnS = get_post_meta( $post->ID, '_SnS', true );
		$styles = isset( $SnS['styles'] ) ? $SnS[ 'styles' ]: array();

		if ( ! empty( $styles ) && ! empty( $styles[ 'classes_post' ] ) )
			$classes = array_merge( $classes, explode( " ", $styles[ 'classes_post' ] ) );

		return $classes;
	}

	/**
	 * Theme Action: 'wp_enqueue_scripts'
	 * Enqueues chosen Scripts.
	 */
	static function enqueue_scripts() {
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
	}

	/**
	 * Utility Method: Compares VERSION to stored 'version' value.
	 */
	static function upgrade_check() {
		$options = get_option( 'SnS_options' );
		if ( ! isset( $options[ 'version' ] ) || version_compare( self::VERSION, $options[ 'version' ], '>' ) ) {
			include_once( 'includes/class-sns-admin.php' );
			SnS_Admin::upgrade();
		}
	}
}