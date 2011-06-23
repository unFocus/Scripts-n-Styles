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
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'save_post', array( __CLASS__, 'save_post' ) );
	}
	
    /**
	 * Admin Action: 'add_meta_boxes'
	 * Main Meta Box function. Checks restriction options and display options, calls add_meta_box() and adds actions for adding admin CSS and JavaScript.
     */
	static function add_meta_boxes() {
		if ( current_user_can( 'unfiltered_html' ) ) {
			$registered_post_types = get_post_types( array('show_ui' => true, 'publicly_queryable' => true) );
			foreach ($registered_post_types as $post_type ) {
				add_meta_box( 'uFp_meta_box', 'Scripts n Styles', array( __CLASS__, 'meta_box' ), $post_type, 'normal', 'high' );
				add_filter( 'default_hidden_meta_boxes', array( __CLASS__,  'default_hidden_meta_boxes' )  );
			}
			add_action( "admin_print_styles", array( __CLASS__, 'meta_box_styles'));
			add_action( "admin_print_scripts", array( __CLASS__, 'meta_box_scripts'));
		}
	}
	static function default_hidden_meta_boxes( $hidden ) {
		$hidden[] = 'uFp_meta_box';
    	return $hidden;
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
			<label for="uFp_scripts"><strong>Scripts</strong>: </label>
			<textarea class="code" name="uFp_scripts" id="uFp_scripts" rows="5" cols="40" style="width: 98%;"><?php echo isset( $scripts[ 'scripts' ] ) ? $scripts[ 'scripts' ] : ''; ?></textarea>
			<em>This code will be included <strong>verbatim</strong> in <code>&lt;script></code> tags at the end of your page's (or post's) <code>&lt;body></code> tag.</em>
		</p>
		
		<p style="margin-top: 1.5em">
			<label for="uFp_styles"><strong>Styles</strong>: </label>
			<textarea class="code" name="uFp_styles" id="uFp_styles" rows="5" cols="40" style="width: 98%;"><?php echo isset( $styles[ 'styles' ] ) ? $styles[ 'styles' ] : ''; ?></textarea>
			<em>This code will be included <strong>verbatim</strong> in <code>&lt;style></code> tags in the <code>&lt;head></code> tag of your page (or post).</em>
		</p>
		
		<p style="margin-top: 1.5em">
			<label for="uFp_scripts_in_head"><strong>Scripts</strong> (for the <code>head</code> element): </label>
			<textarea class="code" name="uFp_scripts_in_head" id="uFp_scripts_in_head" rows="5" cols="40" style="width: 98%;"><?php echo isset( $scripts[ 'scripts_in_head' ] ) ? $scripts[ 'scripts_in_head' ] : ''; ?></textarea>
			<em>This code will be included <strong>verbatim</strong> in <code>&lt;script></code> tags at the end of your page's (or post's) <code>&lt;head></code> tag.</em>
		</p>
		
		<p style="margin-top: 1.5em"><strong>Classes: </strong></p>
		<p>
			<label style="width: 15%; min-width: 85px; display: inline-block;" for="uFp_classes_body">body classes: </label>
			<input style="width: 84%;" name="uFp_classes_body" id="uFp_classes_body" value="<?php echo isset( $styles[ 'classes_body' ] ) ? $styles[ 'classes_body' ] : ''; ?>" type="text" class="code" />
		</p>
		<p>
			<label style="width: 15%; min-width: 85px; display: inline-block;" for="uFp_classes_post">post classes: </label>
			<input style="width: 84%;" name="uFp_classes_post" id="uFp_classes_post" value="<?php echo isset( $styles[ 'classes_post' ] ) ? $styles[ 'classes_post' ] : ''; ?>" type="text" class="code" />
		</p>
		<p><em>These <strong>space separated</strong> class names will be pushed into the <code>body_class()</code> or <code>post_class()</code> function (provided your theme uses these functions).</em></p>
		
		<p style="margin-top: 1.5em">
			<label for="uFp_enqueue_scripts"><strong>Include Scripts</strong>: </label><br />
			<select name="uFp_enqueue_scripts[]" id="uFp_enqueue_scripts" size="5" multiple="multiple" style="height: auto;">
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
			&& current_user_can( 'unfiltered_html' ) 
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
			
			if ( ! empty( $_POST[ 'uFp_scripts' ] ) )
				$scripts[ 'scripts' ] = $_POST[ 'uFp_scripts' ];
				
			if ( ! empty( $_POST[ 'uFp_styles' ] ) )
				$styles[ 'styles' ] = $_POST[ 'uFp_styles' ];
				
			if ( ! empty( $_POST[ 'uFp_scripts_in_head' ] ) )
				$scripts[ 'scripts_in_head' ] = $_POST[ 'uFp_scripts_in_head' ];
				
			if ( ! empty( $_POST[ 'uFp_classes_body' ] ) )
				$styles[ 'classes_body' ] = $_POST[ 'uFp_classes_body' ];
				
			if ( ! empty( $_POST[ 'uFp_classes_post' ] ) )
				$styles[ 'classes_post' ] = $_POST[ 'uFp_classes_post' ];
				
			if ( ! empty( $_POST[ 'uFp_enqueue_scripts' ] ) )
				$scripts[ 'enqueue_scripts' ] = $_POST[ 'uFp_enqueue_scripts' ];
			
			update_post_meta( $post_id, 'uFp_scripts', $scripts );
			update_post_meta( $post_id, 'uFp_styles', $styles );
		}
	}
}
?>