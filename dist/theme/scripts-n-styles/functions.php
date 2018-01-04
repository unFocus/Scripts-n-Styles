<?php
/**
 * Functions file.
 *
 * @package Scripts-N-Styles
 * @subpackage Theme
 */

namespace unFocus\SnS\Theme;

if ( version_compare( $GLOBALS['wp_version'], '4.7-alpha', '<' ) ) {

	// translators: Update Software message.
	$message = sprintf( __( 'Scripts n Styles Theme requires at least WordPress version 4.7. You are running version %s. Please upgrade and try again.', 'scripts-n-styles' ), $GLOBALS['wp_version'] );

	add_action( 'after_switch_theme', function() {
		switch_theme( WP_DEFAULT_THEME );
		unset( $_GET['activated'] );
		add_action( 'admin_notices', function() {
			printf( '<div class="error"><p>%s</p></div>', esc_html( $message ) );
		} );
	} );

	add_action( 'load-customize.php', function() {
		wp_die(
			esc_html( $message ),
			'',
			array(
				'back_link' => true,
			)
		);
	} );

	add_action( 'template_redirect', function() {
		if ( isset( $_GET['preview'] ) ) {
			wp_die( esc_html( $message ) );
		}
	} );

	return;
}

add_action( 'after_setup_theme', function() {
	add_theme_support( 'admin-bar', [
		'callback' => function() {
			// @codingStandardsIgnoreStart
			?><style id="admin-bar-style">
			#wpadminbar {
				opacity: 0.25;
				-webkit-transform: translateY(-50%);
					-ms-transform: translateY(-50%);
						transform: translateY(-50%);
				-webkit-transition: opacity .3s, -webkit-transform .3s;
						transition: opacity .3s, -webkit-transform .3s;
						transition: opacity .3s, transform .3s;
						transition: opacity .3s, transform .3s, -webkit-transform .3s;
			}
			#wpadminbar:hover {
				opacity: 1;
				-webkit-transform: translateY(0%);
					-ms-transform: translateY(0%);
				transform: translateY(0%);
			}
			</style>
			<?php
			// @codingStandardsIgnoreEnd
		},
	] );

	add_theme_support( 'scripts-n-styles', [
		'/less/variables.less',
		'/less/mixins.less',
		'/less/theme.less',
		'/less/content.less',
	] );
	// Creates a dynamic add_editor_style(); call.
	add_theme_support( 'sns-editor-style', [
		'/less/variables.less',
		'/less/mixins.less',
		'/less/content.less',
	] );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );

	$GLOBALS['content_width'] = 1024;
	register_nav_menu( 'primary', __( 'Navigation Menu', 'scripts-n-styles' ) );
	add_theme_support( 'custom-header' );
	add_theme_support( 'custom-background' );
	add_theme_support( 'html5', [
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	] );
	add_theme_support( 'post-formats', [
		'aside',
		'gallery',
		'link',
		'image',
		'quote',
		'status',
		'video',
		'audio',
		'chat',
	] );

	// Add theme support for Custom Logo.
	add_theme_support( 'custom-logo', [
		'height'      => 100,
		'width'       => 400,
		'flex-height' => true,
		'flex-width'  => true,
		'header-text' => [ 'site-title', 'site-description' ],
	] );

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/*
	* This theme styles the visual editor to resemble the theme style,
	* specifically font, colors, and column width.
	*/
	add_editor_style( [ 'css/editor-style.css' ] );

	// Define and register starter content to showcase the theme on new sites.
	add_theme_support( 'starter-content', [
		'widgets' => [
			// Place three core-defined widgets in the sidebar area.
			'sidebar-1' => [
				'text_business_info',
				'search',
				'text_about',
			],

			// Add the core-defined business info widget to the footer 1 area.
			'sidebar-2' => [
				'text_business_info',
			],

			// Put two core-defined widgets in the footer 2 area.
			'sidebar-3' => [
				'text_about',
				'search',
			],
		],

		// Specify the core-defined pages to create and add custom thumbnails to some of them.
		'posts' => [
			'home',
			'about' => [
				'thumbnail' => '{{image-sandwich}}',
			],
			'contact' => [
				'thumbnail' => '{{image-espresso}}',
			],
			'blog' => [
				'thumbnail' => '{{image-coffee}}',
			],
			'homepage-section' => [
				'thumbnail' => '{{image-espresso}}',
			],
		],

		// Create the custom image attachments used as post thumbnails for pages.
		'attachments' => [
			'image-espresso' => [
				'post_title' => _x( 'Espresso', 'Theme starter content', 'scripts-n-styles' ),
				'file' => 'assets/images/espresso.jpg', // URL relative to the template directory.
			],
			'image-sandwich' => [
				'post_title' => _x( 'Sandwich', 'Theme starter content', 'scripts-n-styles' ),
				'file' => 'assets/images/sandwich.jpg',
			],
			'image-coffee' => [
				'post_title' => _x( 'Coffee', 'Theme starter content', 'scripts-n-styles' ),
				'file' => 'assets/images/coffee.jpg',
			],
		],

		// Default to a static front page and assign the front and posts pages.
		'options' => [
			'show_on_front' => 'page',
			'page_on_front' => '{{home}}',
			'page_for_posts' => '{{blog}}',
		],

		// Set the front page section theme mods to the IDs of the core-registered pages.
		'theme_mods' => [
			'panel_1' => '{{homepage-section}}',
			'panel_2' => '{{about}}',
			'panel_3' => '{{blog}}',
			'panel_4' => '{{contact}}',
		],

		// Set up nav menus for each of the two areas registered in the theme.
		'nav_menus' => [
			// Assign a menu to the "top" location.
			'top' => [
				'name' => __( 'Top Menu', 'scripts-n-styles' ),
				'items' => [
					'link_home', // Note that the core "home" page is actually a link in case a static front page is not used.
					'page_about',
					'page_blog',
					'page_contact',
				],
			],

			// Assign a menu to the "social" location.
			'social' => [
				'name' => __( 'Social Links Menu', 'scripts-n-styles' ),
				'items' => [
					'link_yelp',
					'link_facebook',
					'link_twitter',
					'link_instagram',
					'link_email',
				],
			],
		],
	] );
} );

add_action( 'wp_enqueue_scripts', function() {
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	wp_enqueue_style( 'theme_style', get_stylesheet_uri() );
	wp_enqueue_style( 'html5shiv-printshiv' );
	wp_style_add_data( 'html5shiv-printshiv', 'conditional', 'lt IE 9' );
} );

add_action( 'widgets_init', function() {
	register_sidebar( [
		'name'          => __( 'Main Widget Area', 'scripts-n-styles' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<article id="%1$s" class="widget %2$s">',
		'after_widget'  => '</article>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	] );
});

