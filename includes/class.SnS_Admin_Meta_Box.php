<?php
/**
 * SnS_Admin_Meta_Box
 * 
 * Allows WordPress admin users the ability to add custom CSS
 * and JavaScript directly to individual Post, Pages or custom
 * post types.
 */

// $hook_suffix = 'tools_page_Scripts-n-Styles'; // kept here for reference
// $plugin_file = 'scripts-n-styles/scripts-n-styles.php'; // kept here for reference
		
class SnS_Admin_Meta_Box
{
    /*
     * Constants
     */
	const NONCE_NAME = 'scripts_n_styles_noncename';
	
    /**
	 * Initializing method. Checks if is_admin() and registers action hooks for admin if true. Sets filters and actions for Theme side functions.
     * @static
     */
	static function init() {
		add_action( 'add_meta_boxes', array( __class__, 'add_meta_boxes' ) );
		add_action( 'save_post', array( __class__, 'save_post' ) );
		add_action( 'admin_init', array( __class__, 'tinymce_plugin' ) );
	}
	
    /**
	 * Handle the TinyMCE plugin.
     */
	static function tinymce_plugin() {
		$options = Scripts_n_Styles::get_options();
		if ( isset( $options[ 'new_tinymce' ] ) && 'yes' == $options[ 'new_tinymce' ] ) {
			add_filter( 'tiny_mce_before_init', array( __class__, 'remove_tinymce_plugin' ), 11 );
			add_filter( 'mce_external_plugins', array( __class__, 'add_tinymce_plugin' ), 11 );
			// seems to be the best hook.
			add_action( 'add_meta_boxes', array( __class__, 'add_switchEditors' ) );
		}
	}
	
    /**
	 * Replace switchEditors.
     */
	static function add_switchEditors( $initArray ) {
		wp_enqueue_script( 'switchEditors', plugins_url('/js/switchEditors.dev.js', Scripts_n_Styles::$file), array( 'editor' ) );
	}
	
    /**
	 * Removes the "wordpress" TinyMCE plugin.
     */
	static function remove_tinymce_plugin( $initArray ) {
		$initArray['plugins'] = preg_replace( '/,wordpress,/', ',', $initArray['plugins'] );
		return $initArray;
	}
	
    /**
	 * Adds our external "wordpress" TinyMCE plugin.
     */
	static function add_tinymce_plugin( $tinymce_plugin ) {
		$tinymce_plugin['wordpress'] = plugins_url('/js/wordpress/editor_plugin.dev.js', Scripts_n_Styles::$file);
		return $tinymce_plugin;
	}
	
    /**
	 * Utility Method: Returns the value of $allow if it is set, and if not, sets it according the current users capabilties as configured on the admin page.
	 * @return bool Whether or not current user can set options. 
	 * @uses Scripts_n_Styles::get_options()
	 * @uses Scripts_n_Styles::$allow
     */
	static function check_restriction() {
		if ( ! isset( Scripts_n_Styles::$allow ) ) {
			$options = Scripts_n_Styles::get_options();
			if ( isset( $options[ 'restrict' ] ) && 'yes' == $options[ 'restrict' ] )
				Scripts_n_Styles::$allow = current_user_can( 'manage_options' ) && current_user_can( 'unfiltered_html' );
			else
				Scripts_n_Styles::$allow = current_user_can( 'unfiltered_html' );
		}
		return Scripts_n_Styles::$allow;
	}
	
    /**
	 * Admin Action: 'add_meta_boxes'
	 * Main Meta Box function. Checks restriction options and display options, calls add_meta_box() and adds actions for adding admin CSS and JavaScript.
     */
	static function add_meta_boxes() {
		$options = Scripts_n_Styles::get_options();
		if ( isset( $options[ 'show_meta_box' ] ) && 'yes' == $options[ 'show_meta_box' ] && self::check_restriction() ) {
			$registered_post_types = get_post_types( array('show_ui' => true, 'publicly_queryable' => true) );
			foreach ($registered_post_types as $post_type ) {
				add_meta_box( Scripts_n_Styles::PREFIX.'meta_box', 'Scripts n Styles', array( __class__, 'meta_box' ), $post_type, 'normal', 'high' );
			}
			add_action( "admin_print_styles", array( __class__, 'meta_box_styles'));
			add_action( "admin_print_scripts", array( __class__, 'meta_box_scripts'));
		}
	}
	
    /**
	 * Admin Action: 'add_meta_boxes'
	 * Outputs the Meta Box. Only called on callback from add_meta_box() during the add_meta_boxes action.
	 * @param unknown_type WordPress Post object.
     */
	static function meta_box( $post ) {
		$registered_handles = Scripts_n_Styles::get_wp_registered();
		$styles = Scripts_n_Styles::get_styles();
		$scripts = Scripts_n_Styles::get_scripts();
		?>
		<input type="hidden" name="<?php echo self::NONCE_NAME ?>" id="<?php echo self::NONCE_NAME ?>" value="<?php echo wp_create_nonce( Scripts_n_Styles::$file ) ?>" />
		<p style="margin-top: 1.5em">
			<label for="<?php echo Scripts_n_Styles::PREFIX ?>scripts"><strong>Scripts</strong>: </label>
			<textarea class="code" name="<?php echo Scripts_n_Styles::PREFIX ?>scripts" id="<?php echo Scripts_n_Styles::PREFIX ?>scripts" rows="5" cols="40" style="width: 98%;"><?php echo isset( $scripts[ 'scripts' ] ) ? $scripts[ 'scripts' ] : ''; ?></textarea>
			<em>This code will be included <strong>verbatim</strong> in <code>&lt;script></code> tags at the end of your page's (or post's) <code>&lt;body></code> tag.</em>
		</p>
		
		<p style="margin-top: 1.5em">
			<label for="<?php echo Scripts_n_Styles::PREFIX ?>styles"><strong>Styles</strong>: </label>
			<textarea class="code" name="<?php echo Scripts_n_Styles::PREFIX ?>styles" id="<?php echo Scripts_n_Styles::PREFIX ?>styles" rows="5" cols="40" style="width: 98%;"><?php echo isset( $styles[ 'styles' ] ) ? $styles[ 'styles' ] : ''; ?></textarea>
			<em>This code will be included <strong>verbatim</strong> in <code>&lt;style></code> tags in the <code>&lt;head></code> tag of your page (or post).</em>
		</p>
		
		<p style="margin-top: 1.5em">
			<label for="<?php echo Scripts_n_Styles::PREFIX ?>scripts_in_head"><strong>Scripts</strong> (for the <code>head</code> element): </label>
			<textarea class="code" name="<?php echo Scripts_n_Styles::PREFIX ?>scripts_in_head" id="<?php echo Scripts_n_Styles::PREFIX ?>scripts_in_head" rows="5" cols="40" style="width: 98%;"><?php echo isset( $scripts[ 'scripts_in_head' ] ) ? $scripts[ 'scripts_in_head' ] : ''; ?></textarea>
			<em>This code will be included <strong>verbatim</strong> in <code>&lt;script></code> tags at the end of your page's (or post's) <code>&lt;head></code> tag.</em>
		</p>
		
		<p style="margin-top: 1.5em"><strong>Classes: </strong></p>
		<p>
			<label style="width: 15%; min-width: 85px; display: inline-block;" for="<?php echo Scripts_n_Styles::PREFIX ?>classes_body">body classes: </label>
			<input style="width: 84%;" name="<?php echo Scripts_n_Styles::PREFIX ?>classes_body" id="<?php echo Scripts_n_Styles::PREFIX ?>classes_body" value="<?php echo isset( $styles[ 'classes_body' ] ) ? $styles[ 'classes_body' ] : ''; ?>" type="text" class="code" />
		</p>
		<p>
			<label style="width: 15%; min-width: 85px; display: inline-block;" for="<?php echo Scripts_n_Styles::PREFIX ?>classes_post">post classes: </label>
			<input style="width: 84%;" name="<?php echo Scripts_n_Styles::PREFIX ?>classes_post" id="<?php echo Scripts_n_Styles::PREFIX ?>classes_post" value="<?php echo isset( $styles[ 'classes_post' ] ) ? $styles[ 'classes_post' ] : ''; ?>" type="text" class="code" />
		</p>
		<p><em>These <strong>space separated</strong> class names will be pushed into the <code>body_class()</code> or <code>post_class()</code> function (provided your theme uses these functions).</em></p>
		
		<p style="margin-top: 1.5em">
			<label for="<?php echo Scripts_n_Styles::PREFIX ?>enqueue_scripts"><strong>Include Scripts</strong>: </label><br />
			<select name="<?php echo Scripts_n_Styles::PREFIX ?>enqueue_scripts[]" id="<?php echo Scripts_n_Styles::PREFIX ?>enqueue_scripts" size="5" multiple="multiple" style="height: auto;">
				<?php // This is a bit intense here...
				if ( ! empty( $scripts[ 'enqueue_scripts' ] ) && is_array( $scripts[ 'enqueue_scripts' ] ) ) {
					foreach ( $registered_handles as $value ) { ?>
						<option value="<?php echo $value ?>"<?php foreach ( $scripts[ 'enqueue_scripts' ] as $handle ) selected( $handle, $value ); ?>><?php echo $value ?></option> 
					<?php }
				} else {
					foreach ( $registered_handles as $value ) { ?>
						<option value="<?php echo $value ?>"><?php echo $value ?></option> 
					<?php }
				} ?>
			</select>
		</p>
		<?php if ( ! empty( $scripts[ 'enqueue_scripts' ] ) && is_array( $scripts[ 'enqueue_scripts' ] ) ) { ?>
			<p>Currently Enqueued Scripts:
			<?php foreach ( $scripts[ 'enqueue_scripts' ] as $handle )  echo '<code>' . $handle . '</code> '; ?>
			</p>
		<?php } ?>
		<p><em>The chosen scripts will be enqueued and placed before your codes if your code is dependant on certain scripts (like jQuery).</em></p>
		<p>NOTE: Not all Scripts in the list are appropriate for use in themes. This is merely a generated list of all currently available registered scripts. It's possible some scripts could be registered only on the "front end" and therefore not listed here.</p>
		<?php
	}
	
    /**
	 * Admin Action: 'admin_print_styles' Action added during 'add_meta_boxes' (which restricts output to Edit Screens).
	 * Enqueues the CSS for admin styling of the Meta Box.
     */
	static function meta_box_styles() {
		wp_enqueue_style( 'sns-meta-box-styles', plugins_url('css/meta-box-styles.css', Scripts_n_Styles::$file), array(), SnS_Admin::VERSION );
	}
	
    /**
	 * Admin Action: 'admin_print_styles' Action added during 'add_meta_boxes' (which restricts output to Edit Screens).
	 * Enqueues the JavaScript for the admin Meta Box.
     */
	static function meta_box_scripts() {
		wp_enqueue_script( 'sns-meta-box-scripts', plugins_url('/meta-box-scripts.js', Scripts_n_Styles::$file), array( 'jquery' ), SnS_Admin::VERSION, true );
		//wp_enqueue_script( 'unwpautop', plugins_url( 'js/unwpautop.js', Scripts_n_Styles::$file ), 'jquery' );
	}
	
    /**
	 * Admin Action: 'save_post'
	 * Saves the values entered in the Meta Box when a post is saved (on the Edit Screen only, excluding autosaves) if the user has permission.
	 * @param int $post_id ID value of the WordPress post.
     */
	static function save_post( $post_id ) {
		if ( isset( $_POST[ self::NONCE_NAME ] ) && wp_verify_nonce( $_POST[ self::NONCE_NAME ], Scripts_n_Styles::$file )
			&& self::check_restriction() 
			&& ! ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
			
			/* 
				NOTE: There is no current_user_can( 'edit_post' ) check here, because as far as I 
				can tell, in /wp-admin/post.php the calls edit_post(), write_post(), post_preview(), 
				wp_untrash_post(), etc., the check is already done prior to the 'save_post' action, 
				which is where this function is called. Other calls are from other pages so the 
				NONCE covers those cases, and that leaves autosave, which is also checked here. 
			*/
			
			$scripts = array();
			$styles = array();
			
			if ( ! empty( $_POST[ Scripts_n_Styles::PREFIX.'scripts' ] ) )
				$scripts[ 'scripts' ] = $_POST[ Scripts_n_Styles::PREFIX.'scripts' ];
				
			if ( ! empty( $_POST[ Scripts_n_Styles::PREFIX.'styles' ] ) )
				$styles[ 'styles' ] = $_POST[ Scripts_n_Styles::PREFIX.'styles' ];
				
			if ( ! empty( $_POST[ Scripts_n_Styles::PREFIX.'scripts_in_head' ] ) )
				$scripts[ 'scripts_in_head' ] = $_POST[ Scripts_n_Styles::PREFIX.'scripts_in_head' ];
				
			if ( ! empty( $_POST[ Scripts_n_Styles::PREFIX.'classes_body' ] ) )
				$styles[ 'classes_body' ] = $_POST[ Scripts_n_Styles::PREFIX.'classes_body' ];
				
			if ( ! empty( $_POST[ Scripts_n_Styles::PREFIX.'classes_post' ] ) )
				$styles[ 'classes_post' ] = $_POST[ Scripts_n_Styles::PREFIX.'classes_post' ];
				
			if ( ! empty( $_POST[ Scripts_n_Styles::PREFIX.'enqueue_scripts' ] ) )
				$scripts[ 'enqueue_scripts' ] = $_POST[ Scripts_n_Styles::PREFIX.'enqueue_scripts' ];
			
			update_post_meta( $post_id, Scripts_n_Styles::PREFIX.'scripts', $scripts );
			update_post_meta( $post_id, Scripts_n_Styles::PREFIX.'styles', $styles );
		}
	}
}
?>