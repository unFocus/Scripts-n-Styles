<?php
/*
Plugin Name: Scripts n Styles
Plugin URI: http://www.unFocus.com/
Description: Allows WordPress admin users the ability to add custom CSS and JavaScript directly to individual Post, Pages or custom post types.
Author: unFocus Projects
Author URI: http://twitter.com/wraithkenny
Version: 1.0
License: GPL2
*/

if ( !function_exists( 'add_action' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit();
}

if ( !class_exists( 'Scripts_n_Styles' ) ) {
	
	/**
	 * @package Scripts_n_Styles
	 * @version 1.0
	 */
	class Scripts_n_Styles
	{
		const PREFIX = 'uFp_';
		function Scripts_n_Styles() {
			if ( is_admin() ) {
				add_action( 'add_meta_boxes', array( &$this, 'add' ));
				add_action( 'save_post', array( &$this, 'save_styles' ));
				add_action( 'save_post', array( &$this, 'save_scripts' ));
			}
			add_action( 'wp_head',array( &$this, 'styles' ));
			add_action( 'wp_footer',array( &$this, 'scripts' ));
		}
		function add() {
			if ( current_user_can( 'manage_options' ) ) {
				$registered_post_types = get_post_types();
				$post_type_defaults = array( 'mediapage', 'attachment', 'revision', 'nav_menu_item');
				$post_types = array_diff( $registered_post_types, $post_type_defaults );
				foreach ($post_types as $post_type ) {
					add_meta_box( self::PREFIX.'styles', 'Styles', array( &$this, 'styles_meta_box'), $post_type, 'normal', 'high' );
					add_meta_box( self::PREFIX.'scripts', 'Scripts', array(&$this, 'scripts_meta_box'), $post_type, 'normal', 'high' );
				}
			}
		}
		function styles_meta_box( $post ) {
			$value = get_post_meta( $post->ID, self::PREFIX.'styles', true ); ?>
<input type="hidden" name="<?= self::PREFIX ?>styles_noncename" id="<?= self::PREFIX ?>styles_noncename" value="<?= wp_create_nonce( self::PREFIX.'styles' ) ?>" />
<textarea name="<?= self::PREFIX ?>styles" id="<?= self::PREFIX ?>styles" rows="5" cols="30" style="width:100%;"><?php echo @ $value[ 'styles' ] ?></textarea>
<?php 
		}
		function scripts_meta_box( $post ) {
			$value = get_post_meta( $post->ID, self::PREFIX.'scripts', true ); ?>
<input type="hidden" name="<?= self::PREFIX ?>scripts_noncename" id="<?= self::PREFIX ?>scripts_noncename" value="<?= wp_create_nonce( self::PREFIX.'scripts' ) ?>" />
<textarea name="<?= self::PREFIX ?>scripts" id="<?= self::PREFIX ?>scripts" rows="5" cols="30" style="width:100%;"><?php echo @ $value[ 'scripts' ]; ?></textarea>
<?php 
		}
		function save_scripts( $post_id ) {
			if ( current_user_can( 'manage_options' ) ) {
				if ( !wp_verify_nonce( @$_POST[ self::PREFIX.'scripts_noncename' ], self::PREFIX.'scripts' ))
					return $post_id;
				if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
					return $post_id;
				
				$meta[ 'scripts' ] = $_POST[ self::PREFIX.'scripts' ];
				update_post_meta( $post_id, self::PREFIX.'scripts', $meta );
			}
		}
		function save_styles( $post_id ) {
			if ( current_user_can( 'manage_options' ) ) {
				if ( !wp_verify_nonce( @$_POST[ self::PREFIX.'styles_noncename' ], self::PREFIX.'styles' ) )
					return $post_id;
				if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
					return $post_id;
				
				$meta[ 'styles' ] = $_POST[ self::PREFIX.'styles' ];
				update_post_meta( $post_id, self::PREFIX.'styles', $meta );
			}
		}
		function styles() {
			if ( is_page() || is_single() ) {
				global $post;
				$styles = get_post_meta( $post->ID, self::PREFIX.'styles', true );
				if ( !empty( $styles ) ) { ?>
<style type="text/css">
<?php echo $styles[ 'styles' ]; ?> 
</style>
<?php }
			}
		}
		function scripts() {
			if ( is_page() || is_single() ) {
				global $post;
				$scripts = get_post_meta( $post->ID , self::PREFIX.'scripts', true );
				if ( !empty( $scripts ) ) { ?>
<script type="text/javascript">
<?php echo $scripts[ 'scripts' ]; ?> 
</script>
<?php }
			}
		}
	}
	$uFp_SnS = new Scripts_n_Styles;
}
?>