<?php
/*
Plugin Name: Scripts n Styles
Plugin URI: http://www.unfocus.com/projects/scripts-n-styles/
Description: Allows WordPress admin users the ability to add custom CSS and JavaScript directly to individual Post, Pages or custom post types.
Author: unFocus Projects
Author URI: http://www.unfocus.com/
Version: 1.0.3-alpha
License: GPL2
Network: true
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
		const CLASS_NAME = 'Scripts_n_Styles';
		const VERSION = '1.0.3-alpha';
		private static $allow;
		private static $allow_strict;
		private static $options;
		private static $scripts;
		private static $styles;
		private static $enqueue;
		private static $wp_registered;
		static function init() {
			if ( is_multisite() ) { 
				/*
				 ::TODO::	No user except the "Super Admin" can use this plugin in MultiSite. I'll add features for MultiSite later, perhaps the ones below...
				 			The "Super Admin" user has exclusive 'unfiltered_html' capabilities in MultiSite. Also, options.php checks for is_super_admin() 
							so the 'manage_options' capability for blog admins is insufficient to pass the check to manage options directly. 
							
							The Tentative plan is for Super Admins to create Snippets or Shortcodes approved for use by users with certain capabilities 
							('unfiltered_html' and/or 'manage_options'). The 'unfiltered_html' capability can be granted via another plugin. This plugin will
							not deal with granting any capabilities.
				 */
			}
			 
			// ::TODO:: Add Post Type Selection on Options Page? Not sure that's usefull.
			// ::TODO:: Add Conditional Tags support as alternative to Globally applying Scripts n Styles.
			// ::TODO:: Create ability to add and register scripts and styles for enqueueing (via Options page).
			// ::TODO:: Create selection on Option page of which to pick registered scripts to make available on edit screens.
			// ::TODO:: Create shortcode to embed html/javascript snippets.
			//			See http://scribu.net/wordpress/optimal-script-loading.html in which this is already figured out :-)
			// ::TODO:: Create shortcode registration on Options page to make those snippets available on edit screens.
			// ::TODO:: Create shortcode registration of html snippets on edit screens for single use.
			// ::TODO:: Figure out and add Error messaging.
			
			if ( is_admin() && ! ( defined('DISALLOW_UNFILTERED_HTML') && DISALLOW_UNFILTERED_HTML ) ) {
				/*
				 * NOTE: Setting the DISALLOW_UNFILTERED_HTML constant to
				 * true in the wp-config.php would effectively disable this
				 * plugin's admin because no user would have the capability.
				 */

				add_action( 'add_meta_boxes', array( self::CLASS_NAME, 'add_meta_boxes' ) );
				add_action( 'admin_menu', array( self::CLASS_NAME, 'admin_menu' ) );
				$plugin_file = plugin_basename(__FILE__); 
				add_filter( "plugin_action_links_$plugin_file", array( self::CLASS_NAME, 'plugin_action_links') );
				
				//register_activation_hook( __FILE__, array( self::CLASS_NAME, 'activation' ) );
				self::upgrade_check();
			} 
			
			add_filter( 'body_class', array( self::CLASS_NAME, 'body_classes' ) );
			add_filter( 'post_class', array( self::CLASS_NAME, 'post_classes' ) );
			
			add_action( 'wp_head', array( self::CLASS_NAME, 'styles' ), 11 );
			add_action( 'wp_head', array( self::CLASS_NAME, 'scripts_in_head' ), 11 );
			add_action( 'wp_footer', array( self::CLASS_NAME, 'scripts' ), 11 );
			
			//add_action( 'admin_enqueue_scripts', array( self::CLASS_NAME, 'admin_enqueue_scripts' ) );
		}
		function activation() {
			$sns_options = self::get_options();
			if ( ! isset( $sns_options[ 'show_meta_box' ] ) )
				$sns_options['show_meta_box' ] = 'yes';
			if ( ! isset( $sns_options[ 'restrict' ] ) )
				$sns_options[ 'restrict' ] = 'yes';
			$sns_options[ 'version' ] = self::VERSION;
			update_option( 'sns_options', $sns_options );
		}
		function upgrade_check() { 
			$sns_options = self::get_options();
			if ( ! isset( $sns_options[ 'version' ] ) || version_compare( self::VERSION, $sns_options[ 'version' ], '>' ) )
				self::activation();
		}
		
		function plugin_action_links( $actions ) {
			$actions[ 'settings' ] = '<a href="' . menu_page_url( self::MENU_SLUG, false ) . '"/>Settings</a>';
			return $actions;
		}
		function admin_menu() {
			/*
			 * NOTE: Even when Scripts n Styles is not restricted by 'manage_options', Editors still can't submit the option page
			 */
			if ( self::check_strict_restriction() ) { // if they can't, they won't be able to save anyway.
				$hook_suffix = add_management_page(
						'Scripts n Styles Settings',	// $page_title (string) (required) The text to be displayed in the title tags of the page when the menu is selected
						'Scripts n Styles',	// $menu_title (string) (required) The text to be used for the menu
						'unfiltered_html',	// $capability (string) (required) The capability required for this menu to be displayed to the user.
						self::MENU_SLUG,	// $menu_slug (string) (required) The slug name to refer to this menu by (should be unique for this menu).
						array( self::CLASS_NAME, 'options_page' )	// $function (callback) (optional) The function to be called to output the content for this page. 
					);
				add_action( "load-$hook_suffix", array( self::CLASS_NAME, 'init_options_page' ) );
				add_action( "admin_print_styles-$hook_suffix", array( self::CLASS_NAME, 'options_styles'));
				add_action( "admin_print_scripts-$hook_suffix", array( self::CLASS_NAME, 'options_scripts'));
			}
		}
		function init_options_page(){
			register_setting(
					self::OPTION_GROUP,	// $option_group (string) (required) A settings group name. Can be anything.
					'sns_options',	// $option_name (string) (required) The name of an option to sanitize and save.
					array( self::CLASS_NAME, 'options_validate' )	// $sanitize_callback (string) (optional) A callback function that sanitizes the option's value.
				);
			register_setting(
					self::OPTION_GROUP, 
					'sns_enqueue_scripts', 
					array( self::CLASS_NAME, 'enqueue_validate' )
				);
			add_settings_section(
					'general',	// $id (string) (required) String for use in the 'id' attribute of tags.
					'General Settings',	// $title (string) (required) Title of the section. 
					array( self::CLASS_NAME, 'general_section' ),	// $callback (string) (required) Function that fills the section with the desired content. The function should echo its output.
					self::MENU_SLUG	// $page (string) (required) The type of settings page on which to show the section (general, reading, writing, media etc.)
				);
			add_settings_field(
					'show_meta_box',	// $id (string) (required) String for use in the 'id' attribute of tags. 
					'<label><strong>Display:</strong> </label>',	// $title (string) (required) Title of the field.
					array( self::CLASS_NAME, 'show_meta_box_field' ),	// $callback (string) (required) Function that fills the field with the desired inputs as part of the larger form. Name and id of the input should match the $id given to this function. The function should echo its output.
					self::MENU_SLUG,	// $page (string) (required) The type of settings page on which to show the field (general, reading, writing, ...).
					'general'	// $section (string) (optional) The section of the settings page in which to show the box (default or a section you added with add_settings_section, look at the page in the source to see what the existing ones are.)
				);
			add_settings_field(
					'restrict', 
					'<label><strong>Restriction:</strong> </label>',
					array( self::CLASS_NAME, 'restrict_field' ),
					self::MENU_SLUG,
					'general'
				);
			add_settings_section(
					'global',
					'Global Scripts n Styles',
					array( self::CLASS_NAME, 'global_section' ),
					self::MENU_SLUG
				);
			add_settings_field(
					'scripts', 
					'<label for="scripts"><strong>Scripts:</strong> </label>',
					array( self::CLASS_NAME, 'scripts_field' ),
					self::MENU_SLUG,
					'global'
				);
			add_settings_field(
					'styles',
					'<label for="styles"><strong>Styles:</strong> </label>',
					array( self::CLASS_NAME, 'styles_field' ),
					self::MENU_SLUG,
					'global'
				);
			add_settings_field(
					'scripts_in_head',
					'<label for="scripts_in_head"><strong>Scripts</strong><br />(for the <code>head</code> element): </label>',
					array( self::CLASS_NAME, 'scripts_in_head_field' ),
					self::MENU_SLUG,
					'global'
				);
			add_settings_field(
					'enqueue_scripts',
					'<label for="enqueue_scripts"><strong>Enqueue Scripts</strong>: </label>',
					array( self::CLASS_NAME, 'enqueue_scripts_field' ),
					self::MENU_SLUG,
					'global'
				);
		}
		function options_styles() {
			wp_enqueue_style( 'options-styles', plugins_url('options-styles.css', __FILE__), array(), self::VERSION );
		}
		function options_scripts() {
			wp_enqueue_script( 'options-scripts', plugins_url('options-scripts.js', __FILE__), array( 'jquery' ), self::VERSION, true );
		}
		
		function general_section() {
			?>
			<div style="max-width: 55em;">
				<p>Notes about Capabilities: In default (non MultiSite) WordPress installs, Administrators and Editors have the 'unfiltered_html' capability. In MultiSite installs, only the super admin has this capabilty. In both types of install, Admin users have 'manage_options' but in MultiSite, you need to be a Super Admin to access the options.php file.</p>
				<p>The "Restriction" option will require users to have 'manage_options' in addition to 'unfiltered_html' capabilities in order to access Scripts n Styles. When this option is on, Editors will not have access to the Scripts n Styles box on Post and Page edit screens (unless another plugin grants them the 'unfiltered_html' capability). </p>
			</div>
			<?php
		}
		function global_section() {
			?>
			<div style="max-width: 55em;">
				<p>Code entered here will be included in <em>every page (and post) of your site</em>, including the homepage and archives. The code will appear <strong>before</strong> Scripts and Styles registered individually.</p>
			</div>
			<?php
			$hook_suffix = 'tools_page_Scripts-n-Styles'; // kept here for reference
			$plugin_file = 'scripts-n-styles/scripts-n-styles.php'; // kept here for reference
			?>
			<?php
		}
		
		function show_meta_box_field() {
			$sns_options = self::get_options();
			?><label for="show_meta_box"><strong>Show Scripts n Styles on Edit Screens</strong></label><br />
			<fieldset>
				<label>
					<input type="radio" name="sns_options[show_meta_box]" value="yes" id="show_meta_box_0" <?php echo (isset( $sns_options[ 'show_meta_box' ] ) && 'yes' == $sns_options[ 'show_meta_box' ] ) ? 'checked="checked" ' : ''; ?>/>
					<span>Yes</span></label>
				<br />
				<label>
					<input type="radio" name="sns_options[show_meta_box]" value="no" id="show_meta_box_1" <?php echo (isset( $sns_options[ 'show_meta_box' ] ) && 'no' == $sns_options[ 'show_meta_box' ] ) ? 'checked="checked" ' : ''; ?>/>
					<span>No</span></label>
			</fieldset>
			<span class="description" style="max-width: 500px; display: inline-block;">"No" will reduce clutter on edit screens. (Your codes will still load.)</span><?php
		}
		function restrict_field() {
			$sns_options = self::get_options();
			?><label for="restrict"><strong>Restict access to Scripts n Styles</strong></label><br />
			<fieldset>
				<label>
					<input type="radio" name="sns_options[restrict]" value="yes" id="restrict_0" <?php echo (isset( $sns_options[ 'restrict' ] ) && 'yes' == $sns_options[ 'restrict' ] ) ? 'checked="checked" ' : ''; ?>/>
					<span>Yes</span></label>
				<br />
				<label>
					<input type="radio" name="sns_options[restrict]" value="no" id="restrict_1" <?php echo (isset( $sns_options[ 'restrict' ] ) && 'no' == $sns_options[ 'restrict' ] ) ? 'checked="checked" ' : ''; ?>/>
					<span>No</span></label>
			</fieldset>
			<span class="description" style="max-width: 500px; display: inline-block;">Apply a 'manage_options' check in addition to the 'unfiltered_html' check.</span><?php
		}
		function scripts_field() {
			$sns_options = self::get_options();
			?><textarea style="min-width: 500px; width:97%;" class="code" rows="5" cols="40" name="sns_options[scripts]" id="scripts"><?php echo isset( $sns_options[ 'scripts' ] ) ? $sns_options[ 'scripts' ] : ''; ?></textarea><br />
			<span class="description" style="max-width: 500px; display: inline-block;">The "Scripts" will be included <strong>verbatim</strong> in <code>&lt;script></code> tags at the bottom of the <code>&lt;body></code> element of your html.</span>
			<?php
		}
		function styles_field() {
			$sns_options = self::get_options();
			?><textarea style="min-width: 500px; width:97%;" class="code" rows="5" cols="40" name="sns_options[styles]" id="styles"><?php echo isset( $sns_options[ 'styles' ] ) ? $sns_options[ 'styles' ] : ''; ?></textarea><br />
			<span class="description" style="max-width: 500px; display: inline-block;">The "Styles" will be included <strong>verbatim</strong> in <code>&lt;style></code> tags in the <code>&lt;head></code> element of your html.</span><?php
		}
		function scripts_in_head_field() {
			$sns_options = self::get_options();
			?><textarea style="min-width: 500px; width:97%;" class="code" rows="5" cols="40" name="sns_options[scripts_in_head]" id="scripts_in_head"><?php echo isset( $sns_options[ 'scripts_in_head' ] ) ? $sns_options[ 'scripts_in_head' ] : ''; ?></textarea><br />
			<span class="description" style="max-width: 500px; display: inline-block;">The "Scripts (in head)" will be included <strong>verbatim</strong> in <code>&lt;script></code> tags in the <code>&lt;head></code> element of your html.</span>
			<?php
		}
		function enqueue_scripts_field() {
			$registered_handles = self::get_wp_registered();
			$sns_enqueue_scripts = self::get_enqueue();
			?><select name="sns_enqueue_scripts[]" id="enqueue_scripts" size="5" multiple="multiple" style="height: auto;"><?php
				foreach ( $registered_handles as $handle ) echo '<option value="' . $handle . '">' . $handle . '</option>'; 
			?></select>
			<?php if ( ! empty( $sns_enqueue_scripts ) && is_array( $sns_enqueue_scripts ) ) { ?>
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
				<form action="options.php" method="post" autocomplete="off">
				<?php settings_fields( self::OPTION_GROUP ); ?>
				<?php do_settings_sections( self::MENU_SLUG ); ?>
				<?php submit_button(); ?>
				</form>
			</div>
			<?php
		}
		
		private function check_restriction() {
			if ( ! isset( self::$allow ) ) {
				$sns_options = self::get_options();
				if ( isset( $sns_options[ 'restrict' ] ) && 'yes' == $sns_options[ 'restrict' ] )
					self::$allow = current_user_can( 'manage_options' ) && current_user_can( 'unfiltered_html' );
				else
					self::$allow = current_user_can( 'unfiltered_html' );
			}
			return self::$allow;
		}
		private function check_strict_restriction() {
			// ::TODO:: Add MultiSite checks?
			if ( ! isset( self::$allow_strict ) )
				self::$allow_strict = current_user_can( 'manage_options' ) && current_user_can( 'unfiltered_html' );
			return self::$allow_strict;
		}
		
		private function get_options() {
			if ( ! isset( self::$options ) ) {
				self::$options = get_option( 'sns_options' );
			}
			return self::$options;
		}
		private function get_scripts() {
			if ( ! isset( self::$scripts ) ) {
				global $post;
				self::$scripts = get_post_meta( $post->ID, self::PREFIX.'scripts', true );
			}
			return self::$scripts;
		}
		private function get_styles() {
			if ( ! isset( self::$styles ) ) {
				global $post;
				self::$styles = get_post_meta( $post->ID, self::PREFIX.'styles', true );
			}
			return self::$styles;
		}
		private function get_enqueue() {
			if ( ! isset( self::$enqueue ) ) {
				self::$enqueue = get_option( 'sns_enqueue_scripts' );
			}
			return self::$enqueue;
		}
		private function get_wp_registered() {
			if ( ! isset( self::$wp_registered ) ) {
				global $wp_scripts;
				self::$wp_registered = array_keys( $wp_scripts->registered );
			}
			return self::$wp_registered;
		}
		
		function add_meta_boxes() {
			$sns_options = self::get_options();
			if ( isset( $sns_options[ 'show_meta_box' ] ) && 'yes' == $sns_options[ 'show_meta_box' ] && self::check_restriction() ) {
				$registered_post_types = get_post_types( array('show_ui' => true, 'publicly_queryable' => true) );
				foreach ($registered_post_types as $post_type ) {
					add_meta_box( self::PREFIX.'meta_box', 'Scripts n Styles', array( self::CLASS_NAME, 'meta_box' ), $post_type, 'normal', 'high' );
				}
				add_action( 'save_post', array( self::CLASS_NAME, 'save_post' ) );
				add_action( "admin_print_styles", array( self::CLASS_NAME, 'meta_box_styles'));
				add_action( "admin_print_scripts", array( self::CLASS_NAME, 'meta_box_scripts'));
			}
		}
		function meta_box( $post ) {
			$registered_handles = self::get_wp_registered();
			$styles = self::get_styles();
			$scripts = self::get_scripts();
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
				<textarea class="code" name="<?php echo self::PREFIX ?>scripts_in_head" id="<?php echo self::PREFIX ?>scripts_in_head" rows="5" cols="40" style="width: 98%;"><?php echo isset( $scripts[ 'scripts_in_head' ] ) ? $scripts[ 'scripts_in_head' ] : ''; ?></textarea>
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
				<label for="<?php echo self::PREFIX ?>enqueue_scripts"><strong>Include Scripts</strong>: </label><br />
				<select name="<?php echo self::PREFIX ?>enqueue_scripts[]" id="<?php echo self::PREFIX ?>enqueue_scripts" size="5" multiple="multiple" style="height: auto;">
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
		function meta_box_styles() {
			wp_enqueue_style( 'meta-box-styles', plugins_url('meta-box-styles.css', __FILE__), array(), self::VERSION );
		}
		function meta_box_scripts() {
			wp_enqueue_script( 'meta-box-scripts', plugins_url('meta-box-scripts.js', __FILE__), array( 'jquery' ), self::VERSION, true );
		}
		function save_post( $post_id ) {
			if ( self::check_restriction() 
				&&  isset( $_POST[ self::NONCE_NAME ] ) && wp_verify_nonce( $_POST[ self::NONCE_NAME ], __FILE__ ) 
				&& ! ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
				
				/* 
				 * NOTE: There is no current_user_can( 'edit_post' ) check here, because as far as I 
				 * can tell, in /wp-admin/post.php the calls edit_post(), write_post(), post_preview(), 
				 * wp_untrash_post(), etc., the check is already done prior to the 'save_post' action, 
				 * which is where this function is called. Other calls are from other pages so the 
				 * NONCE covers those cases, and that leaves autosave, which is also checked here. 
				 */
				
				$scripts = array();
				$styles = array();
				
				if ( ! empty( $_POST[ self::PREFIX.'scripts' ] ) )
					$scripts[ 'scripts' ] = $_POST[ self::PREFIX.'scripts' ];
					
				if ( ! empty( $_POST[ self::PREFIX.'styles' ] ) )
					$styles[ 'styles' ] = $_POST[ self::PREFIX.'styles' ];
					
				if ( ! empty( $_POST[ self::PREFIX.'scripts_in_head' ] ) )
					$scripts[ 'scripts_in_head' ] = $_POST[ self::PREFIX.'scripts_in_head' ];
					
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
		function scripts() {
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
		function scripts_in_head() {
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
		function body_classes( $classes ) {
			$meta = self::get_styles();
			if ( ! empty( $meta ) && ! empty( $meta[ 'classes_body' ] ) ) {
				$classes = array_merge( $classes, explode( " ", $meta[ 'classes_body' ] ) );
			}
			return $classes;
		}
		function post_classes( $classes ) {
			$meta = self::get_styles();
			if ( ! empty( $meta ) && ! empty( $meta[ 'classes_post' ] ) ) {
				$classes = array_merge( $classes, explode( " ", $meta[ 'classes_post' ] ) );
			}
			return $classes;
		}
		function enqueue_scripts() {
			// Global
			$sns_enqueue_scripts = self::get_enqueue();
			if ( is_array( $sns_enqueue_scripts ) ) {
				foreach ( $sns_enqueue_scripts as $handle )
					wp_enqueue_script( $handle );
			}
			// Individual
			$meta = self::get_scripts();
			if ( ! empty( $meta ) && is_array( $meta[ 'enqueue_scripts' ] ) ) {
				foreach ( $meta[ 'enqueue_scripts' ] as $handle )
					wp_enqueue_script( $handle );
			}
		}
		
		function options_validate( $input ) {
			// I'm not sure that users without the proper caps can get this far, but if they can...
			if ( self::check_strict_restriction() ) 
				return $input;
			return self::get_options();
		}
		function enqueue_validate( $input ) {
			if ( self::check_strict_restriction() ) 
				return $input;
			return self::get_enqueue();
		}
	}
	Scripts_n_Styles::init();
}
?>