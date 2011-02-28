<?php
/*
Plugin Name: Scripts n Styles
Plugin URI: http://www.unfocus.com/projects/scripts-n-styles/
Description: Allows WordPress admin users the ability to add custom CSS and JavaScript directly to individual Post, Pages or custom post types.
Author: unFocus Projects
Author URI: http://www.unfocus.com/
Version: 1.0.3-alpha
License: GPL2
*/
/*  Copyright 2010-2011  Ken Newman  www.unfocus.com

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
	 * @version 1.0.3
	 */
	class Scripts_n_Styles
	{
		const PREFIX = 'uFp_'; // post meta data, and meta box feild names are prefixed with this to prevent collisions.
		const OPTION_GROUP = 'scripts_n_styles';
		const MENU_SLUG = 'Scripts-n-Styles';
		const NONCE_NAME = 'scripts_n_styles_noncename';
		function Scripts_n_Styles() {
			if ( is_admin() ) {
				add_action( 'add_meta_boxes', array( &$this, 'add' ) );
				add_action( 'save_post', array( &$this, 'save' ) );
				add_action( 'admin_init', array( &$this, 'admin_init' ) );
				add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
				//register_activation_hook(__FILE__, array( &$this, 'activation' ) );
			} 
			
			add_filter( 'body_class', array( &$this, 'body_classes' ) );
			add_filter( 'post_class', array( &$this, 'post_classes' ) );
			
			add_action( 'wp_head', array( &$this, 'styles' ), 11 );
			add_action( 'wp_head', array( &$this, 'scripts_in_head' ), 11 );
			add_action( 'wp_footer', array( &$this, 'scripts' ), 11 );
			
			add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );
		}
		/*function activation() {
			$placeholder = 'I might have use for a Activation routine, but I doubt it.';
		}*/
		function admin_init(){
			register_setting(
					self::OPTION_GROUP,	// $option_group (string) (required) A settings group name. Can be anything.
					'sns_options'//,	 $option_name (string) (required) The name of an option to sanitize and save.
					// array( &$this, 'options_validate' )	$sanitize_callback (string) (optional) A callback function that sanitizes the option's value.
				);
			register_setting(
					self::OPTION_GROUP,	// $option_group (string) (required) A settings group name. Can be anything.
					'sns_enqueue_scripts'//,	 $option_name (string) (required) The name of an option to sanitize and save.
					// array( &$this, 'options_validate' )	$sanitize_callback (string) (optional) A callback function that sanitizes the option's value.
				);
			add_settings_section(
					'global',	// $id (string) (required) String for use in the 'id' attribute of tags.
					'Global Scripts n Styles',	// $title (string) (required) Title of the section. 
					array( &$this, 'global_section' ),	// $callback (string) (required) Function that fills the section with the desired content. The function should echo its output.
					self::MENU_SLUG	// $page (string) (required) The type of settings page on which to show the section (general, reading, writing, media etc.)
				);
			add_settings_field(
					'scripts',	// $id (string) (required) String for use in the 'id' attribute of tags. 
					'<label for="scripts"><strong>Scripts:</strong> </label>',	// $title (string) (required) Title of the field.
					array( &$this, 'scripts_textarea' ),	// $callback (string) (required) Function that fills the field with the desired inputs as part of the larger form. Name and id of the input should match the $id given to this function. The function should echo its output.
					self::MENU_SLUG,	// $page (string) (required) The type of settings page on which to show the field (general, reading, writing, ...).
					'global'	// $section (string) (optional) The section of the settings page in which to show the box (default or a section you added with add_settings_section, look at the page in the source to see what the existing ones are.)
				);
			add_settings_field(
					'styles',
					'<label for="styles"><strong>Styles:</strong> </label>',
					array( &$this, 'styles_textarea' ),
					self::MENU_SLUG,
					'global'
				);
			add_settings_field(
					'scripts_in_head',
					'<label for="scripts_in_head"><strong>Scripts</strong><br />(for the <code>head</code> element): </label>',
					array( &$this, 'scripts_in_head_textarea' ),
					self::MENU_SLUG,
					'global'
				);
			add_settings_field(
					'enqueue_scripts',
					'<label for="enqueue_scripts"><strong>Enqueue Scripts</strong>: </label>',
					array( &$this, 'enqueue_scripts_textarea' ),
					self::MENU_SLUG,
					'global'
				);
		}
		function admin_menu() {
			add_management_page(
					'Scripts n Styles Settings',	// $page_title (string) (required) The text to be displayed in the title tags of the page when the menu is selected
					'Scripts n Styles',	// $menu_title (string) (required) The text to be used for the menu
					'unfiltered_html',	// $capability (string) (required) The capability required for this menu to be displayed to the user.
					self::MENU_SLUG,	// $menu_slug (string) (required) The slug name to refer to this menu by (should be unique for this menu).
					array( &$this, 'options_page' )	// $function (callback) (optional) The function to be called to output the content for this page. 
				);
		}
		function global_section() {
			?>
			<p>Code entered here will be included in <em>every page (and post) of your site</em>, including the homepage and archives. The code will appear <strong>before</strong> Scripts and Styles registered for individual pages and posts, so that they can override those entered here.</p>
			<p>The "Scripts" will be included <strong>verbatim</strong> in <code>&lt;script></code> tags at the bottom of the <code>&lt;body></code> element of your html.</p>
			<p>The "Styles" will be included <strong>verbatim</strong> in <code>&lt;style></code> tags in the <code>&lt;head></code> element of your html.</p>
			<p>The "Scripts (in head)" will be included <strong>verbatim</strong> in <code>&lt;script></code> tags in the <code>&lt;head></code> element of your html.</p>
			<?php
		}
		function styles_textarea() {
			$sns_options = get_option( 'sns_options' );
			?><textarea class="code" rows="5" cols="40" name="sns_options[styles]" id="styles"><?php echo isset( $sns_options[ 'styles' ] ) ? $sns_options[ 'styles' ] : ''; ?></textarea><?php
		}	
		function scripts_textarea() {
			$sns_options = get_option( 'sns_options' );
			?><textarea class="code" rows="5" cols="40" name="sns_options[scripts]" id="scripts"><?php echo isset( $sns_options[ 'scripts' ] ) ? $sns_options[ 'scripts' ] : ''; ?></textarea><?php
		}
		function scripts_in_head_textarea() {
			$sns_options = get_option( 'sns_options' );
			?><textarea class="code" rows="5" cols="40" name="sns_options[scripts_in_head]" id="scripts_in_head"><?php echo isset( $sns_options[ 'scripts_in_head' ] ) ? $sns_options[ 'scripts_in_head' ] : ''; ?></textarea><?php
		}
		function enqueue_scripts_textarea() {
			global $wp_scripts;
			$registered_handles = array_keys( $wp_scripts->registered );
			$sns_enqueue_scripts = get_option( 'sns_enqueue_scripts' );
			$sns_options = get_option( 'sns_options' );
			?><select name="sns_enqueue_scripts[]" id="enqueue_scripts" size="5" multiple="multiple" style="height: auto; width:98%;"><?php
				foreach ( $registered_handles as $handle ) echo '<option value="' . $handle . '">' . $handle . '</option>'; 
			?></select>
			<?php if ( ! empty( $sns_enqueue_scripts[ 'enqueue_scripts' ] ) && is_array( $sns_enqueue_scripts[ 'enqueue_scripts' ] ) ) { ?>
				<p><?php
				echo 'Currently Enqueued Scripts: ';
				foreach ( $sns_enqueue_scripts as $handle )  echo '<code>' . $handle . '</code> ';
				?></p>
			<?php }
		}
		function options_page() {
			global $title;
			?>
			<div class="wrap">
				<?php screen_icon(); ?>
				<h2><?php echo esc_html($title); ?></h2>
				<form action="options.php" method="post">
				<?php settings_fields( self::OPTION_GROUP ); ?>
				<?php do_settings_sections( self::MENU_SLUG ); ?>
				<?php submit_button(); ?>
				</form>
			</div>
			<?php
		}
		function add() {
			if ( current_user_can( 'manage_options' ) && current_user_can( 'unfiltered_html' ) ) {
				$registered_post_types = get_post_types( array('show_ui' => true, 'publicly_queryable' => true) );
				foreach ($registered_post_types as $post_type ) {
					add_meta_box( self::PREFIX.'meta_box', 'Scripts n Styles', array( &$this, 'meta_box' ), $post_type, 'normal', 'high' );
				}
			}
		}
		function meta_box( $post ) {
			global $wp_scripts;
			$registered_handles = array_keys( $wp_scripts->registered );
			$styles = get_post_meta( $post->ID, self::PREFIX.'styles', true );
			$scripts = get_post_meta( $post->ID, self::PREFIX.'scripts', true );
			?>
			<input type="hidden" name="<?php echo self::NONCE_NAME ?>" id="<?php echo self::NONCE_NAME ?>" value="<?php echo wp_create_nonce( __FILE__ ) ?>" />
			<p style="margin-top: 1.5em">
				<label for="<?php echo self::PREFIX ?>scripts"><strong>Scripts</strong>: </label>
				<textarea class="code" name="<?php echo self::PREFIX ?>scripts" id="<?php echo self::PREFIX ?>scripts" rows="5" cols="40" style="width: 98%;"><?php echo isset( $scripts[ 'scripts' ] ) ? $scripts[ 'scripts' ] : ''; ?></textarea>
				<em>This code will be included <strong>verbatim</strong> in <code>&lt;script></code> tags at the end of your page's (or post's) <code>&lt;body></code> tag.</em>
			</p>
			
			<p style="margin-top: 1.5em">
				<label for="<?php echo self::PREFIX ?>styles"><strong>Styles</strong>: </label>
				<textarea class="code" name="<?php echo self::PREFIX ?>styles" id="<?php echo self::PREFIX ?>styles" rows="5" cols="40" style="width: 98%;"><?php echo isset( $styles[ 'styles' ] ) ? $styles[ 'styles' ] : ''; ?></textarea>
				<em>This code will be included <strong>verbatim</strong> in <code>&lt;style></code> tags in the <code>&lt;head></code> tag of your page (or post).</em>
			</p>
			
			<p style="margin-top: 1.5em">
				<label for="<?php echo self::PREFIX ?>scripts_in_head"><strong>Scripts</strong> (for the <code>head</code> element): </label>
				<textarea class="code" name="<?php echo self::PREFIX ?>scripts_in_head" id="<?php echo self::PREFIX ?>scripts_in_head" rows="5" cols="40" style="width: 98%;"><?php echo isset( $scripts[ 'scripts_in_head' ] ) ? $styles[ 'scripts_in_head' ] : ''; ?></textarea>
				<em>This code will be included <strong>verbatim</strong> in <code>&lt;script></code> tags at the end of your page's (or post's) <code>&lt;head></code> tag.</em>
			</p>
			
			<p style="margin-top: 1.5em"><strong>Classes: </strong></p>
			<p>
				<label style="width: 15%; min-width: 85px; display: inline-block;" for="<?php echo self::PREFIX ?>classes_body">body classes: </label>
				<input style="width: 84%;" name="<?php echo self::PREFIX ?>classes_body" id="<?php echo self::PREFIX ?>classes_body" value="<?php echo isset( $styles[ 'classes_body' ] ) ? $styles[ 'classes_body' ] : ''; ?>" type="text" class="code" />
			</p>
			<p>
				<label style="width: 15%; min-width: 85px; display: inline-block;" for="<?php echo self::PREFIX ?>classes_post">post classes: </label>
				<input style="width: 84%;" name="<?php echo self::PREFIX ?>classes_post" id="<?php echo self::PREFIX ?>classes_post" value="<?php echo isset( $styles[ 'classes_post' ] ) ? $styles[ 'classes_post' ] : ''; ?>" type="text" class="code" />
			</p>
			<p><em>These <strong>space separated</strong> class names will be pushed into the <code>body_class()</code> or <code>post_class()</code> function (provided your theme uses these functions).</em></p>
			
			<p style="margin-top: 1.5em">
				<label for="<?php echo self::PREFIX ?>enqueue_scripts"><strong>Include Scripts</strong>: </label>
				<select name="<?php echo self::PREFIX ?>enqueue_scripts[]" id="<?php echo self::PREFIX ?>enqueue_scripts" size="5" multiple="multiple" style="height: auto; width:98%;">
					<?php foreach ( $registered_handles as $handle ) echo '<option value="' . $handle . '">' . $handle . '</option>'; ?>
				</select>
			</p>
			<?php if ( ! empty( $scripts[ 'enqueue_scripts' ] ) && is_array( $scripts[ 'enqueue_scripts' ] ) ) { ?>
				<p><?php echo 'Currently Enqueued Scripts: ';
				foreach ( $scripts[ 'enqueue_scripts' ] as $handle )  echo '<code>' . $handle . '</code> ';
				?></p>
			<?php } ?>
			<p><em>The chosen scripts will be enqueued and placed before your codes if your code is dependant on certain scripts (like jQuery).</em></p>
			<p>NOTE: Not all Scripts in the list are appropriate for use in themes. This is merely a generated list of all currently available registered scripts. It's possible some scripts could be registered only on the "front end" and therefore not listed here.</p>
			<?php
		}
		function save( $post_id ) {
			if ( current_user_can( 'manage_options' ) && current_user_can( 'unfiltered_html' ) ) {
				if ( ! isset( $_POST[ self::NONCE_NAME ] ) || ! wp_verify_nonce( $_POST[ self::NONCE_NAME ], __FILE__ ))
					return $post_id;
				if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
					return $post_id;
				$scripts = array();
				$styles = array();
				if ( ! empty( $_POST[ self::PREFIX.'scripts' ] ) )
					$scripts[ 'scripts' ] = $_POST[ self::PREFIX.'scripts' ];
				if ( ! empty( $_POST[ self::PREFIX.'scripts_in_head' ] ) )
					$scripts[ 'scripts_in_head' ] = $_POST[ self::PREFIX.'scripts_in_head' ];
				if ( ! empty( $_POST[ self::PREFIX.'styles' ] ) )
					$styles[ 'styles' ] = $_POST[ self::PREFIX.'styles' ];
				if ( ! empty( $_POST[ self::PREFIX.'classes_body' ] ) )
					$styles[ 'classes_body' ] = $_POST[ self::PREFIX.'classes_body' ];
				if ( ! empty( $_POST[ self::PREFIX.'classes_post' ] ) )
					$styles[ 'classes_post' ] = $_POST[ self::PREFIX.'classes_post' ];
				if ( ! empty( $_POST[ self::PREFIX.'enqueue_scripts' ] ) )
					$scripts[ 'enqueue_scripts' ] = $_POST[ self::PREFIX.'enqueue_scripts' ];
				update_post_meta( $post_id, self::PREFIX.'scripts', $scripts );
				update_post_meta( $post_id, self::PREFIX.'styles', $styles );
			}
		}
		function styles() {
			$option = get_option( 'sns_options' );
			if ( ! empty( $option ) && ! empty( $option[ 'styles' ] ) ) {
				?><style type="text/css"><?php
				echo $option[ 'styles' ];
				?></style><?php
			}
			if ( is_singular() ) {
				global $post;
				$meta = get_post_meta( $post->ID, self::PREFIX.'styles', true );
				if ( ! empty( $meta ) && ! empty( $meta[ 'styles' ] ) ) {
					?><style type="text/css"><?php
					echo $meta[ 'styles' ];
					?></style><?php
				}
			}
		}
		function scripts() {
			$option = get_option( 'sns_options' );
			if ( ! empty( $option ) && ! empty( $option[ 'scripts' ] ) ) {
				?><script type="text/javascript"><?php
				echo $option[ 'scripts' ];
				?></script><?php
			}
			if ( is_singular() ) {
				global $post;
				$meta = get_post_meta( $post->ID, self::PREFIX.'scripts', true );
				if ( ! empty( $meta ) && ! empty( $meta[ 'scripts' ] ) ) {
					?><script type="text/javascript"><?php
					echo $meta[ 'scripts' ];
					?></script><?php
				}
			}
		}
		function scripts_in_head() {
			$option = get_option( 'sns_options' );
			if ( ! empty( $option ) && ! empty($option[ 'scripts_in_head' ]) ) {
				?><script type="text/javascript"><?php
				echo $option[ 'scripts_in_head' ];
				?></script><?php
			}
			if ( is_singular() ) {
				global $post;
				$meta = get_post_meta( $post->ID, self::PREFIX.'scripts', true );
				if ( ! empty( $meta ) && ! empty( $meta[ 'scripts_in_head' ] ) ) {
					?><script type="text/javascript"><?php
					echo $meta[ 'scripts_in_head' ];
					?></script><?php
				}
			}
		}
		function body_classes( $classes ) {
			global $post;
			$meta = get_post_meta( $post->ID , self::PREFIX.'styles', true );
			if ( ! empty( $meta ) && ! empty( $meta[ 'classes_body' ] ) ) {
				$classes = array_merge( $classes, explode( " ", $meta[ 'classes_body' ] ) );
			}
			return $classes;
		}
		function post_classes( $classes ) {
			global $post;
			$meta = get_post_meta( $post->ID , self::PREFIX.'styles', true );
			if ( ! empty( $meta ) && ! empty( $meta[ 'classes_post' ] ) ) {
				$classes = array_merge( $classes, explode( " ", $meta[ 'classes_post' ] ) );
			}
			return $classes;
		}
		function enqueue_scripts() {
			global $post;
			$meta = get_post_meta( $post->ID , self::PREFIX.'scripts', true );
			if ( ! empty( $meta ) && is_array( $meta[ 'enqueue_scripts' ] ) ) {
				foreach ( $meta[ 'enqueue_scripts' ] as $handle )
					wp_enqueue_script( $handle );
			}
			$sns_enqueue_scripts = get_option( 'sns_enqueue_scripts' );
			if ( ! empty( $sns_enqueue_scripts ) && is_array( $sns_enqueue_scripts ) ) {
				foreach ( $sns_enqueue_scripts as $handle )
					wp_enqueue_script( $handle );
			}
		}
	}
	$uFp_SnS = new Scripts_n_Styles;
}
?>