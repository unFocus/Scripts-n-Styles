<?php

class SnS_Theme
{
	function __construct() {
		if ( ! isset( $content_width ) )
			$content_width = 625;
		add_action( 'after_setup_theme', array( __CLASS__, 'after_setup_theme' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'wp_enqueue_scripts' ) );
		add_action( 'widgets_init', array( __CLASS__, 'widgets_init' ) );
	}
	function wp_enqueue_scripts() {
		global $wp_styles;
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
			wp_enqueue_script( 'comment-reply' );
		wp_enqueue_style( 'theme-style', get_stylesheet_uri() );
		wp_enqueue_style( 'html5shiv-printshiv' );
		$wp_styles->add_data( 'html5shiv-printshiv', 'conditional', 'lt IE 9' );
	}
	function widgets_init() {
		register_sidebar( array(
			'name'          => __( 'Main Widget Area', 'scripts-n-styles' ),
			'id'            => 'sidebar-1',
			'before_widget' => '<article id="%1$s" class="widget %2$s">',
			'after_widget'  => '</article>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );
	}
	function after_setup_theme() {
		load_theme_textdomain( 'scripts-n-styles', get_template_directory() . '/languages' );
		add_theme_support(
			'scripts-n-styles',
			array(
				'/less/variables.less',
				'/less/mixins.less',
				'/less/theme.less',
				'/less/content.less',
			)
		);
		add_theme_support( // add_editor_style();
			'sns-editor-style',
			array(
				'/less/variables.less',
				'/less/mixins.less',
				'/less/content.less',
			)
		);
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'custom-header' );
		add_theme_support( 'custom-background' );
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list' ) );
		register_nav_menu( 'primary', __( 'Navigation Menu', 'scripts-n-styles' ) );
		add_filter( 'use_default_gallery_style', '__return_false' );
	}
}
?>