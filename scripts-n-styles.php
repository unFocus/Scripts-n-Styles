<?php
/*
Plugin Name: Scripts n Styles
Plugin URI: http://www.unfocus.com/projects/scripts-n-styles/
Description: Allows WordPress admin users the ability to add custom CSS and JavaScript directly to individual Post, Pages or custom post types.
Author: unFocus Projects
Author URI: http://www.unfocus.com/
Version: 1.0.2
License: GPL2
*/
/*  Copyright 2010  Ken Newman  www.unfocus.com

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

if ( !function_exists( 'add_action' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit();
}

if ( !class_exists( 'Scripts_n_Styles' ) ) {
	
	/**
	 * @package Scripts_n_Styles
	 * @version 1.0.2
	 */
	class Scripts_n_Styles
	{
		const PREFIX = 'uFp_';
		function Scripts_n_Styles() {
			if ( is_admin() ) {
				add_action( 'add_meta_boxes', array( &$this, 'add' ));
				add_action( 'save_post', array( &$this, 'save' ));
			} 
			
			add_filter('body_class', array( &$this, 'body_classes' ));
			add_filter('post_class', array( &$this, 'post_classes' ));
			
			add_action( 'wp_head',array( &$this, 'styles' ));
			add_action( 'wp_footer',array( &$this, 'scripts' ));
		}
		function add() {
			if ( current_user_can( 'manage_options' ) ) {
				$registered_post_types = get_post_types();
				$post_type_defaults = array( 'attachment', 'revision', 'nav_menu_item');
				$post_types = array_diff( $registered_post_types, $post_type_defaults );
				foreach ($post_types as $post_type ) {
					add_meta_box( self::PREFIX.'meta_box', 'Scripts n Styles', array( &$this, 'meta_box'), $post_type, 'normal', 'high' );
				}
			}
		}
		function meta_box( $post ) {
			$styles = get_post_meta( $post->ID, self::PREFIX.'styles', true );
			$scripts = get_post_meta( $post->ID, self::PREFIX.'scripts', true );
?>
<input type="hidden" name="<?php echo self::PREFIX ?>scripts_n_styles_noncename" id="<?php echo self::PREFIX ?>scripts_n_styles_noncename" value="<?php echo wp_create_nonce( self::PREFIX.'scripts_n_styles' ) ?>" />
<p style="margin-top:1.5em">
<label style="font-weight:bold;" for="<?php echo self::PREFIX ?>scripts">Scripts: </label>
<textarea class="code" name="<?php echo self::PREFIX ?>scripts" id="<?php echo self::PREFIX ?>scripts" rows="5" cols="40" style="width:98%;"><?php echo @ $scripts[ 'scripts' ]; ?></textarea>
<em>This code will be included <strong>verbatim</strong> in the &lt;script> tags at the end of your page's (or post's) &lt;body> tag.</em></p>

<p style="margin-top:1.5em">
<label style="font-weight:bold;" for="<?php echo self::PREFIX ?>scripts">Styles: </label>
<textarea class="code" name="<?php echo self::PREFIX ?>styles" id="<?php echo self::PREFIX ?>styles" rows="5" cols="40" style="width:98%;"><?php echo @ $styles[ 'styles' ] ?></textarea>
<em>This code will be included <strong>verbatim</strong> in the &lt;style> tags in the &lt;head> tag of your page (or post).</em></p>

<p style="margin-top:1.5em">
<strong>Classes: </strong></p>
<p><label style="width:15%; min-width:85px; display: inline-block;" for="<?php echo self::PREFIX ?>classes_body">body_class(): </label><input style="width:84%;" name="<?php echo self::PREFIX ?>classes_body" id="<?php echo self::PREFIX ?>classes_body" value="<?php echo @ $styles[ 'classes_body' ]; ?>" type="text" class="code" /></p>
<p><label style="width:15%; min-width:85px; display: inline-block;" for="<?php echo self::PREFIX ?>classes_post">post_class(): </label><input style="width:84%;" name="<?php echo self::PREFIX ?>classes_post" id="<?php echo self::PREFIX ?>classes_post" value="<?php echo @ $styles[ 'classes_post' ]; ?>" type="text" class="code" /></p>
<p><em>These <strong>space separated</strong> class names will be pushed into the body_class() or post_class() function (provided your theme uses these functions).</em></p>
<?php 
		}
		function save( $post_id ) {
			if ( current_user_can( 'manage_options' ) ) {
				if ( !wp_verify_nonce( @$_POST[ self::PREFIX.'scripts_n_styles_noncename' ], self::PREFIX.'scripts_n_styles' ))
					return $post_id;
				if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
					return $post_id;
				
				$scripts[ 'scripts' ] = $_POST[ self::PREFIX.'scripts' ];
				$styles[ 'styles' ] = $_POST[ self::PREFIX.'styles' ];
				$styles[ 'classes_body' ] = $_POST[ self::PREFIX.'classes_body' ];
				$styles[ 'classes_post' ] = $_POST[ self::PREFIX.'classes_post' ];
				update_post_meta( $post_id, self::PREFIX.'scripts', $scripts );
				update_post_meta( $post_id, self::PREFIX.'styles', $styles );
			}
		}
		function styles() {
			if ( is_page() || is_single() ) {
				global $post;
				$meta = get_post_meta( $post->ID, self::PREFIX.'styles', true );
				if ( !empty( $meta ) ) { ?>
<style type="text/css">
<?php echo $meta[ 'styles' ]; ?> 
</style>
<?php }
			}
		}
		function scripts() {
			if ( is_page() || is_single() ) {
				global $post;
				$meta = get_post_meta( $post->ID , self::PREFIX.'scripts', true );
				if ( !empty( $meta ) ) { ?>
<script type="text/javascript">
<?php echo $meta[ 'scripts' ]; ?> 
</script>
<?php }
			}
		}
		function body_classes( $classes ) {
			global $post;
			$meta = get_post_meta( $post->ID , self::PREFIX.'styles', true );
			if ( !empty( $meta ) && !empty( $meta['classes_body'] ) ) {
				$classes = array_merge( $classes, explode( " ", $meta['classes_body'] ) );
			}
			return $classes;
		}
		function post_classes( $classes ) {
			global $post;
			$meta = get_post_meta( $post->ID , self::PREFIX.'styles', true );
			if ( !empty( $meta ) && !empty( $meta['classes_post'] ) ) {
				$classes = array_merge( $classes, explode( " ", $meta['classes_post'] ) );
			}
			return $classes;
		}
	}
	$uFp_SnS = new Scripts_n_Styles;
}
?>