<?php
/*
Plugin Name: Scripts n Styles
Plugin URI: http://www.unfocus.com/projects/scripts-n-styles/
Description: Allows WordPress admin users the ability to add custom CSS and JavaScript directly to individual Post, Pages or custom post types.
Author: unFocus Projects
Author URI: http://www.unfocus.com/
Version: 3.alpha
License: GPL2
Network: true
*/
/*  Copyright 2010-2011  Kenneth Newman  www.unfocus.com

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as 
	published by the Free Software Foundation.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
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
 * @version 3.alpha
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Copyright (c) 2010, Kenneth Newman
 * @todo Add Post Type Selection on Options Page? Not sure that's usefull.
 * @todo Add Conditional Tags support as alternative to Globally applying Scripts n Styles.
 * @todo Create ability to add and register scripts and styles for enqueueing (via Options page).
 * @todo Create selection on Option page of which to pick registered scripts to make available on edit screens.
 * @todo Create shortcode to embed html/javascript snippets. See http://scribu.net/wordpress/optimal-script-loading.html in which this is already figured out :-)
 * @todo Create shortcode registration on Options page to make those snippets available on edit screens.
 * @todo Create shortcode registration of html snippets on edit screens for single use.
 * @todo Figure out and add Error messaging.
 * @todo Replace Multi-Select element with something better.
 * @todo Clean up Usage Table, paginate, don't display when empty.
 * @todo "Include Scripts" will be reintroduced when registering is finished.
 * @todo Clean up tiny_mce_before_init in SnS_Admin_Meta_Box.
 */

class Scripts_n_Styles
{
    /**#@+
     * @static
     */
	static $file = __FILE__;
	static $hook_suffix; // 'tools_page_Scripts-n-Styles';
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
		
		add_filter( 'body_class', array( __CLASS__, 'body_classes' ) );
		add_filter( 'post_class', array( __CLASS__, 'post_classes' ) );
		
		add_action( 'wp_head', array( __CLASS__, 'styles' ), 11 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ), 11 );
		add_action( 'wp_head', array( __CLASS__, 'scripts_in_head' ), 11 );
		add_action( 'wp_footer', array( __CLASS__, 'scripts' ), 11 );
	}
	
    /**
	 * Utility Method: Returns the value of $scripts if it is set, and if not, sets it via a call to the database.
	 * @return array 'ufp_script' meta data entry.
     */
	static function get_scripts() {
		global $post;
		return get_post_meta( $post->ID, 'uFp_scripts', true );
	}
	
    /**
	 * Utility Method: Returns the value of $styles if it is set, and if not, sets it via a call to the database.
	 * @return array 'ufp_styles' meta data entry.
     */
	static function get_styles() {
		global $post;
		return get_post_meta( $post->ID, 'uFp_styles', true );
	}
	
    /**
	 * Utility Method
     */
	static function get_wp_registered() {
		return array(
				// Starting with the list of Scripts registered by default on the Theme side (index page of twentyten).
				// This list should be trimmed down, as some probably aren't apporpriate for Theme enqueueing.
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
				'wpdialogs-popup'
			);
	}
	
    /**
	 * Theme Action: 'wp_head()'
	 * Outputs the globally and individually set Styles in the Theme's head element.
     */
	static function styles() {
		// Global
		$options = get_option( 'SnS_options' );
		if ( ! empty( $options ) && ! empty( $options[ 'styles' ] ) ) {
			?><style type="text/css"><?php
			echo $options[ 'styles' ];
			?></style><?php
		}
		
		if ( ! is_singular() ) return;
		// Individual
		$styles = self::get_styles();
		if ( ! empty( $styles ) && ! empty( $styles[ 'styles' ] ) ) {
			?><style type="text/css"><?php
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
			?><script type="text/javascript"><?php
			echo $options[ 'scripts' ];
			?></script><?php
		}
		
		if ( ! is_singular() ) return;
		// Individual
		$scripts = self::get_scripts();
		if ( ! empty( $scripts ) && ! empty( $scripts[ 'scripts' ] ) ) {
			?><script type="text/javascript"><?php
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
		if ( ! empty( $options ) && ! empty($options[ 'scripts_in_head' ]) ) {
			?><script type="text/javascript"><?php
			echo $options[ 'scripts_in_head' ];
			?></script><?php
		}
		
		if ( ! is_singular() ) return;
		// Individual
		$scripts = self::get_scripts();
		if ( ! empty( $scripts ) && ! empty( $scripts[ 'scripts_in_head' ] ) ) {
			?><script type="text/javascript"><?php
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
		
		$styles = self::get_styles();
		if ( ! empty( $styles ) && ! empty( $styles[ 'classes_body' ] ) )
			$classes = array_merge( $classes, explode( " ", $styles[ 'classes_body' ] ) );
		
		return $classes;
	}
	
    /**
	 * Theme Filter: 'post_class()'
	 * Adds classes to the Theme's post container.
	 * @uses self::get_styles()
	 * @param array $classes 
	 * @return array $classes 
     */
	static function post_classes( $classes ) {
		if ( ! is_singular() || is_admin() ) return $classes;
		
		$styles = self::get_styles();
		if ( ! empty( $styles ) && ! empty( $styles[ 'classes_post' ] ) )
			$classes = array_merge( $classes, explode( " ", $styles[ 'classes_post' ] ) );
		
		return $classes;
	}
	
    /**
	 * Theme Action: 'wp_enqueue_scripts'
	 * Enqueues chosen Scripts.
	 * @uses self::get_enqueue()
	 * @uses self::get_scripts()
     */
	static function enqueue_scripts() {
		// Global
		$enqueue_scripts = get_option( 'SnS_enqueue_scripts' );

		if ( is_array( $enqueue_scripts ) ) {
			foreach ( $enqueue_scripts as $handle )
				wp_enqueue_script( $handle );
		}
		
		if ( ! is_singular() ) return;
		// Individual
		$scripts = self::get_scripts();
		if ( ! empty( $scripts[ 'enqueue_scripts' ] ) && is_array( $scripts[ 'enqueue_scripts' ] ) ) {
			foreach ( $scripts[ 'enqueue_scripts' ] as $handle )
				wp_enqueue_script( $handle );
		}
	}
	
}

Scripts_n_Styles::init();

?>