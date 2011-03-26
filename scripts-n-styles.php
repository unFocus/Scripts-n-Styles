<?php
/*
Plugin Name: Scripts n Styles
Plugin URI: http://www.unfocus.com/projects/scripts-n-styles/
Description: Allows WordPress admin users the ability to add custom CSS and JavaScript directly to individual Post, Pages or custom post types.
Author: unFocus Projects
Author URI: http://www.unfocus.com/
Version: 1.0.3-beta
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
 * @version 1.0.3-beta
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
 * @todo Add ability to push class names into the TinyMCE editor Style Dropdown.
 * @todo Replace Multi-Select element with something better.
 * @todo Clean up Usage Table, paginate, don't display when empty.
 * @todo Fix wpautop js.
 */

class Scripts_n_Styles
{
    /**
     * Constant
	 * Post meta data, and meta box feild names are prefixed with this to prevent collisions.
     */
	const PREFIX = 'uFp_';
	const OPTION_PREFIX = 'SnS_';
	
    /**#@+
     * @static
     */
	static $allow;
	static $allow_strict;
	static $enqueue;
	static $file = __FILE__;
	static $hook_suffix; // 'tools_page_Scripts-n-Styles'; kept here for reference
	static $options;
	static $plugin_file; // 'scripts-n-styles/scripts-n-styles.php'; kept here for reference
	static $scripts;
	static $styles;
	static $wp_registered;
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
		
		add_filter( 'body_class', array( __class__, 'body_classes' ) );
		add_filter( 'post_class', array( __class__, 'post_classes' ) );
		
		add_action( 'wp_head', array( __class__, 'styles' ), 11 );
		add_action( 'wp_enqueue_scripts', array( __class__, 'enqueue_scripts' ), 11 );
		add_action( 'wp_head', array( __class__, 'scripts_in_head' ), 11 );
		add_action( 'wp_footer', array( __class__, 'scripts' ), 11 );
	}
	
    /**
	 * Utility Method: Returns the value of $options if it is set, and if not, sets it via a call to the database.
	 * @return array $options is the self::OPTION_PREFIX.'options' settings collection. 
	 * @uses self::$options
     */
	static function get_options() {
		if ( ! isset( self::$options ) ) {
			self::$options = get_option( self::OPTION_PREFIX.'options' );
		}
		return self::$options;
	}
	
    /**
	 * Utility Method: Returns the value of $scripts if it is set, and if not, sets it via a call to the database.
	 * @return array $scripts is the 'ufp_script' meta data entry.
	 * @uses self::$scripts
     */
	static function get_scripts() {
		if ( ! isset( self::$scripts ) ) {
			global $post;
			self::$scripts = get_post_meta( $post->ID, self::PREFIX.'scripts', true );
		}
		return self::$scripts;
	}
	
    /**
	 * Utility Method: Returns the value of $styles if it is set, and if not, sets it via a call to the database.
	 * @return array $styles is the 'ufp_styles' meta data entry.
	 * @uses self::$styles
     */
	static function get_styles() {
		if ( ! isset( self::$styles ) ) {
			global $post;
			self::$styles = get_post_meta( $post->ID, self::PREFIX.'styles', true );
		}
		return self::$styles;
	}
	
    /**
	 * Utility Method: Returns the value of $enqueue if it is set, and if not, sets it via a call to the database.
	 * @return array $enqueue is the self::OPTION_PREFIX.'enqueue_scripts' settings collection.
	 * @uses self::$enqueue
     */
	static function get_enqueue() {
		if ( ! isset( self::$enqueue ) ) {
			self::$enqueue = get_option( self::OPTION_PREFIX.'enqueue_scripts' );
			if ( ! is_array( self::$enqueue ) ) self::$enqueue = array();
		}
		return self::$enqueue;
	}
	
    /**
	 * Utility Method: Returns the $enqueue array if it is set, and if not, sets it via a call to the database.
	 * @return array $enqueue is the self::OPTION_PREFIX.'enqueue_scripts' setting.
	 * @uses self::$wp_registered
	 * @global array $wp_scripts
     */
	static function get_wp_registered() {
		if ( ! isset( self::$wp_registered ) ) {
			self::$wp_registered = array(
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
		return self::$wp_registered;
	}
	
    /**
	 * Theme Action: 'wp_head()'
	 * Outputs the globally and individually set Styles in the Theme's head element.
	 * @uses self::get_options()
	 * @uses self::get_styles()
     */
	static function styles() {
		// Global
		$option = self::get_options();
		if ( ! empty( $option ) && ! empty( $option[ 'styles' ] ) ) {
			?><style type="text/css"><?php
			echo $option[ 'styles' ];
			?></style><?php
		}
		// Individual
		if ( is_singular() ) {
			$meta = self::get_styles();
			if ( ! empty( $meta ) && ! empty( $meta[ 'styles' ] ) ) {
				?><style type="text/css"><?php
				echo $meta[ 'styles' ];
				?></style><?php
			}
		}
	}
	
    /**
	 * Theme Action: 'wp_footer()'
	 * Outputs the globally and individually set Scripts at the end of the Theme's body element.
	 * @uses self::get_options()
	 * @uses self::get_scripts()
     */
	static function scripts() {
		// Global
		$option = self::get_options();
		if ( ! empty( $option ) && ! empty( $option[ 'scripts' ] ) ) {
			?><script type="text/javascript"><?php
			echo $option[ 'scripts' ];
			?></script><?php
		}
		// Individual
		if ( is_singular() ) {
			$meta = self::get_scripts();
			if ( ! empty( $meta ) && ! empty( $meta[ 'scripts' ] ) ) {
				?><script type="text/javascript"><?php
				echo $meta[ 'scripts' ];
				?></script><?php
			}
		}
	}
	
    /**
	 * Theme Action: 'wp_head()'
	 * Outputs the globally and individually set Scripts in the Theme's head element.
	 * @uses self::get_options()
	 * @uses self::get_scripts()
     */
	static function scripts_in_head() {
		// Global
		$option = self::get_options();
		if ( ! empty( $option ) && ! empty($option[ 'scripts_in_head' ]) ) {
			?><script type="text/javascript"><?php
			echo $option[ 'scripts_in_head' ];
			?></script><?php
		}
		// Individual
		if ( is_singular() ) {
			$meta = self::get_scripts();
			if ( ! empty( $meta ) && ! empty( $meta[ 'scripts_in_head' ] ) ) {
				?><script type="text/javascript"><?php
				echo $meta[ 'scripts_in_head' ];
				?></script><?php
			}
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
		$meta = self::get_styles();
		if ( ! empty( $meta ) && ! empty( $meta[ 'classes_body' ] ) ) {
			$classes = array_merge( $classes, explode( " ", $meta[ 'classes_body' ] ) );
		}
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
		$meta = self::get_styles();
		if ( ! empty( $meta ) && ! empty( $meta[ 'classes_post' ] ) ) {
			$classes = array_merge( $classes, explode( " ", $meta[ 'classes_post' ] ) );
		}
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
		$sns_enqueue_scripts = self::get_enqueue();
		if ( is_array( $sns_enqueue_scripts ) ) {
			foreach ( $sns_enqueue_scripts as $handle )
				wp_enqueue_script( $handle );
		}
		// Individual
		$meta = self::get_scripts();
		if ( ! empty( $meta[ 'enqueue_scripts' ] ) && is_array( $meta[ 'enqueue_scripts' ] ) ) {
			foreach ( $meta[ 'enqueue_scripts' ] as $handle )
				wp_enqueue_script( $handle );
		}
	}
	
}

Scripts_n_Styles::init();

?>