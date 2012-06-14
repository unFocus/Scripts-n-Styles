<?php
/*
Plugin Name: Scripts n Styles
Plugin URI: http://www.unfocus.com/projects/scripts-n-styles/
Description: Allows WordPress admin users the ability to add custom CSS and JavaScript directly to individual Post, Pages or custom post types.
Author: unFocus Projects
Author URI: http://www.unfocus.com/
Version: 3.2b3
License: GPLv3 or later
Text Domain: scripts-n-styles
Network: true
*/

/*  The Scripts n Styles WordPress Plugin
	Copyright (c) 2010-2012  Kenneth Newman  <http://www.unfocus.com/>
	Copyright (c) 2012  Kevin Newman  <http://www.unfocus.com/>
	Copyright (c) 2012  adcSTUDIO LLC <http://www.adcstudio.com/>

	Scripts n Styles is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 3
	of the License, or (at your option) any later version.
	
	Scripts n Styles is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program. If not, see <http://www.gnu.org/licenses/>.
	
	This file incorporates work covered by other licenses and permissions.
*/

/**
 * Scripts n Styles
 * 
 * Allows WordPress admin users the ability to add custom CSS
 * and JavaScript directly to individual Post, Pages or custom
 * post types.
 * 
 * NOTE: No user except the "Super Admin" can use this plugin in MultiSite. I'll add features for MultiSite later, perhaps the ones below...
 * The "Super Admin" user has exclusive 'unfiltered_html' capabilities in MultiSite. Also, options.php checks for is_super_admin() 
 * so the 'manage_options' capability for blog admins is insufficient to pass the check to manage options directly. 
 * 
 * The Tentative plan is for Super Admins to create Snippets or Shortcodes approved for use by users with certain capabilities 
 * ('unfiltered_html' and/or 'manage_options'). The 'unfiltered_html' capability can be granted via another plugin. This plugin will
 * not deal with granting any capabilities.
 * 
 * @package Scripts_n_Styles
 * @link http://www.unfocus.com/projects/scripts-n-styles/ Plugin URI
 * @author unFocus Projects
 * @link http://www.unfocus.com/ Author URI
 * @version 3.2b3
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Copyright (c) 2010 - 2012, Kenneth Newman
 * @copyright Copyright (c) 2012, Kevin Newman
 * @copyright Copyright (c) 2012, adcSTUDIO LLC
 * 
 * @todo Create ability to add and register scripts and styles for enqueueing (via Options page).
 * @todo Create selection on Option page of which to pick registered scripts to make available on edit screens.
 * @todo Create shortcode registration on Options page to make those snippets available on edit screens.
 * @todo Add Error messaging.
 * @todo Clean up tiny_mce_before_init in SnS_Admin_Meta_Box.
 */

class Scripts_n_Styles
{
	/**#@+
	 * @static
	 */
	const VERSION = '3.2b3';
	static $file = __FILE__;
	static $cm_themes = array( 'default', 'ambiance', 'blackboard', 'cobalt', 'eclipse', 'elegant', 'lesser-dark', 'monokai', 'neat', 'night', 'rubyblue', 'xq-dark' );
	/**#@-*/
	
	/**
	 * Initializing method. Checks if is_admin() and registers action hooks for admin if true. Sets filters and actions for Theme side functions.
	 * @static
	 */
	static function init() {
		if ( is_admin() && ! ( defined('DISALLOW_UNFILTERED_HTML') && DISALLOW_UNFILTERED_HTML ) ) {
			/*	NOTE: Setting the DISALLOW_UNFILTERED_HTML constant to
				true in the wp-config.php would effectively disable this
				plugin's admin because no user would have the capability.
			*/
			include_once( 'includes/class.SnS_Admin.php' );
			SnS_Admin::init();
		}
		
		include_once( 'includes/class-sns-widgets.php' );
		SnS_Widgets::init();
		
		add_action( 'plugins_loaded', array( __CLASS__, 'upgrade_check' ) );
		
		add_filter( 'body_class', array( __CLASS__, 'body_classes' ) );
		add_filter( 'post_class', array( __CLASS__, 'post_classes' ) );
		
		add_action( 'wp_head', array( __CLASS__, 'styles' ), 11 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ), 11 );
		add_action( 'wp_head', array( __CLASS__, 'scripts_in_head' ), 11 );
		add_action( 'wp_footer', array( __CLASS__, 'scripts' ), 11 );
		
		add_shortcode( 'sns_shortcode', array( __CLASS__, 'shortcode' ) );
		add_shortcode( 'hoops', array( __CLASS__, 'shortcode' ) );
		
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'register' ) );
		
		add_action( 'wp_print_styles', array( __CLASS__, 'theme_style' ) );
		add_action( 'wp_ajax_sns_theme_css', array( __CLASS__, 'theme_css' ) );
		add_action( 'wp_ajax_nopriv_sns_theme_css', array( __CLASS__, 'theme_css' ) );
		add_action( 'widgets_init', array( __CLASS__, 'widgets_init' ) );
	}
	
	function widgets_init() {
		register_widget( 'SnS_Hoops_Widget' );
	}
	
	function theme_style() {
		if ( current_theme_supports( 'scripts-n-styles' ) ) {
			$options = get_option( 'SnS_options' );
			$slug = get_stylesheet();
			
			if ( ! empty( $options[ 'themes' ][ $slug ][ 'compiled' ] ) ) {
				wp_deregister_style( 'theme_style' );
				wp_enqueue_style( 'theme_style', add_query_arg( array( 'action' => 'sns_theme_css' ), admin_url( "admin-ajax.php" ) ) );
			}
		}
	}
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
	
	function shortcode( $atts, $content = null, $tag ) {
		global $post;
		
		if ( isset( $post->ID ) ) $id = $post->ID;
		else $id = get_the_ID();
		if ( ! $id ) return '<pre>There was an error.</pre>';
		
		extract( shortcode_atts( array( 'name' => 0, ), $atts ) );
		$output = '';
		
		$SnS = get_post_meta( $post->ID, '_SnS', true );
		$shortcodes = isset( $SnS['shortcodes'] ) ? $SnS[ 'shortcodes' ]: array();
		if ( isset( $shortcodes[ $name ] ) )
			$output .= $shortcodes[ $name ];
		if ( isset( $content ) && empty( $output ) ) $output = $content;
		$output = do_shortcode( $output );
		
		return $output;
	}
	
	/**
	 * Utility Method
	 */
	static function get_wp_registered() {
		return array(
				// Starting with the list of Scripts registered by default on the Theme side (index page of twentyten).
				'l10n',
				'utils',
				'common',
				'sack',
				'quicktags',
				'colorpicker',
				'editor',
				'prototype',
				'wp-ajax-response',
				'autosave',
				'wp-lists',
				'scriptaculous-root',
				'scriptaculous-builder',
				'scriptaculous-dragdrop',
				'scriptaculous-effects',
				'scriptaculous-slider',
				'scriptaculous-sound',
				'scriptaculous-controls',
				'scriptaculous',
				'cropper',
				'jquery',
				'jquery-ui-core',
				'jquery-ui-position',
				'jquery-ui-widget',
				'jquery-ui-mouse',
				'jquery-ui-button',
				'jquery-ui-tabs',
				'jquery-ui-sortable',
				'jquery-ui-draggable',
				'jquery-ui-droppable',
				'jquery-ui-selectable',
				'jquery-ui-resizable',
				'jquery-ui-dialog',
				'jquery-form',
				'jquery-color',
				'suggest',
				'schedule',
				'jquery-query',
				'jquery-serialize-object',
				'jquery-hotkeys',
				'jquery-table-hotkeys',
				'thickbox',
				'jcrop',
				'swfobject',
				'swfupload',
				'swfupload-swfobject',
				'swfupload-queue',
				'swfupload-speed',
				'swfupload-all',
				'swfupload-handlers',
				'comment-reply',
				'json2',
				'imgareaselect',
				'password-strength-meter',
				'user-profile',
				'admin-bar',
				'wplink',
				'wpdialogs-popup',
				'less.js',
				'coffeescript',
				'chosen',
			);
	}
	function register() {
		$dir = plugins_url( '/', __FILE__);
		$js = $dir . 'js/';
		$css = $dir . 'css/';
		$cm_version = '2.25';
		$chosen_version = '0.9.8';
		$cm_dir = $dir . 'vendor/CodeMirror2/';
		$less_dir = $dir . 'vendor/';
		$coffee_dir = $dir . 'vendor/';
		$chosen_dir = $dir . 'vendor/chosen/';
		//$localize = array( 'theme' => $cm_theme );
		$options = get_option( 'SnS_options' );
		$cm_theme = isset( $options[ 'cm_theme' ] ) ? $options[ 'cm_theme' ] : 'default';
		
		wp_register_script( 'less.js', $less_dir . 'less.js', array(), '1.3.0-min' );
		wp_register_script( 'coffeescript', $coffee_dir . 'coffee-script.js', array(), '1.3.3-min' );
		wp_register_script( 'chosen', $chosen_dir . 'chosen.jquery.min.js', array( 'jquery' ), $chosen_version, true );
		wp_register_style(  'chosen', $chosen_dir . 'chosen.css', array(), $chosen_version );
		
		wp_register_script( 'codemirror',              $cm_dir . 'lib/codemirror.js',                 array(), $cm_version );
		wp_register_script( 'codemirror-css',          $cm_dir . 'mode/css/css.js',                   array( 'codemirror' ), $cm_version );
		wp_register_script( 'codemirror-coffeescript', $cm_dir . 'mode/coffeescript/coffeescript.js', array( 'codemirror' ), $cm_version );
		wp_register_script( 'codemirror-less',         $cm_dir . 'mode/less/less.js',                 array( 'codemirror' ), $cm_version );
		wp_register_script( 'codemirror-javascript',   $cm_dir . 'mode/javascript/javascript.js',     array( 'codemirror' ), $cm_version );
		wp_register_script( 'codemirror-xml',          $cm_dir . 'mode/xml/xml.js',                   array( 'codemirror' ), $cm_version );
		wp_register_script( 'codemirror-clike',        $cm_dir . 'mode/clike/clike.js',               array( 'codemirror' ), $cm_version );
		wp_register_script( 'codemirror-markdown',     $cm_dir . 'mode/markdown/markdown.js',         array( 'codemirror-xml' ), $cm_version );
		wp_register_script( 'codemirror-gfm',          $cm_dir . 'mode/gfm/gfm.js',                   array( 'codemirror-php', 'codemirror-htmlmixed' ), $cm_version );
		wp_register_script( 'codemirror-htmlmixed',    $cm_dir . 'mode/htmlmixed/htmlmixed.js',       array( 'codemirror-xml', 'codemirror-css', 'codemirror-javascript' ), $cm_version );
		wp_register_script( 'codemirror-php',          $cm_dir . 'mode/php/php.js',                   array( 'codemirror-xml', 'codemirror-css', 'codemirror-javascript', 'codemirror-clike' ), $cm_version );
		
		wp_register_style(  'codemirror-default',    $cm_dir . 'lib/codemirror.css', array(), $cm_version );
		foreach ( self::$cm_themes as $theme ) if ( 'default' !== $theme )
			wp_register_style( "codemirror-$theme",  $cm_dir . "theme/$theme.css",   array( 'codemirror-default' ), $cm_version );
		
		if ( 'default' == $cm_theme )
			wp_register_style( 'codemirror-theme', $cm_dir . 'lib/codemirror.css',  array(), $cm_version );
		else
			wp_register_style( 'codemirror-theme', $cm_dir . "theme/$cm_theme.css", array( 'codemirror-default' ), $cm_version );
			
		wp_register_style(  'sns-options', $css . 'options-styles.css', array(), self::VERSION );
		wp_register_script( 'sns-global-page', $js . 'global-page.js', array( 'jquery', 'codemirror-less', 'codemirror-coffeescript', 'codemirror-css', 'codemirror-javascript', 'less.js', 'coffeescript', 'chosen' ), self::VERSION, true );
		wp_register_script( 'sns-theme-page', $js . 'theme-page.js', array( 'jquery', 'codemirror-less', 'codemirror-css', 'less.js' ), self::VERSION, true );
		wp_register_script( 'sns-settings-page', $js . 'settings-page.js', array( 'jquery', 'codemirror-php' ), self::VERSION, true );
		wp_register_style(  'sns-meta-box', $css . 'meta-box.css', array( 'codemirror-theme' ), self::VERSION );
		wp_register_script( 'sns-meta-box', $js . 'meta-box.js', array( 'editor', 'jquery-ui-tabs', 'codemirror-less', 'codemirror-htmlmixed', 'chosen' ), self::VERSION, true );
		wp_register_style(  'sns-code-editor', $css . 'code-editor.css', array( 'codemirror-theme' ), self::VERSION );
		wp_register_script( 'sns-code-editor',  $js . 'code-editor.js',  array( 'editor', 'jquery-ui-tabs', 'codemirror-less', 'codemirror-coffeescript', 'codemirror-htmlmixed', 'codemirror-php', 'codemirror-markdown' ), self::VERSION, true );
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
			include_once( 'includes/class.SnS_Admin.php' );
			SnS_Admin::upgrade();
		}
	}
}

Scripts_n_Styles::init();

?>