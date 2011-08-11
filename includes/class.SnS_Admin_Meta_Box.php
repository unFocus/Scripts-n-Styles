<?php
/**
 * SnS_Admin_Meta_Box
 * 
 * Allows WordPress admin users the ability to add custom CSS
 * and JavaScript directly to individual Post, Pages or custom
 * post types.
 */
		
class SnS_Admin_Meta_Box
{
    /*
     * Constants
     */
	const NONCE_NAME = 'scripts_n_styles_noncename';
	
	static $post_types;
	
    /**
	 * Initializing method. Checks if is_admin() and registers action hooks for admin if true. Sets filters and actions for Theme side functions.
     * @static
     */
	static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'save_post', array( __CLASS__, 'save_post' ) );
		
		add_filter( 'mce_buttons_2', array( __CLASS__, 'mce_buttons_2' ) );
		add_filter( 'tiny_mce_before_init', array( __CLASS__, 'tiny_mce_before_init' ) );
		add_filter( 'mce_css', array( __CLASS__, 'mce_css' ) );
	}
	
	function mce_buttons_2( $buttons ) {
		global $post;
		$styles = get_post_meta( $post->ID, 'uFp_styles', true );
		
		if ( ! empty( $styles[ 'classes_mce' ] ) )
			array_unshift( $buttons, 'styleselect' );
		
		return $buttons;
	}
	function tiny_mce_before_init( $initArray ) {
		global $post;
		$styles = get_post_meta( $post->ID, 'uFp_styles', true );
		
		// Add div as a format option, should probably use a string replace thing here.
		$initArray['theme_advanced_blockformats'] = "p,address,pre,h1,h2,h3,h4,h5,h6,div";
		
		// Add body_class (and/or maybe post_class) values... problematic.
		if ( ! empty( $styles[ 'classes_body' ] ) )
			$initArray['body_class'] .= ' ' . $styles[ 'classes_body' ];
		if ( ! empty( $styles[ 'classes_post' ] ) )
			$initArray['body_class'] .= ' ' . $styles[ 'classes_post' ];
		
		// In case Themes or plugins have added style_formats
		if ( isset( $initArray['style_formats'] ) ) $style_formats = json_decode( $initArray['style_formats'], true );
		else $style_formats = array();
			
		$formats = array();
		
		if ( ! empty( $styles[ 'classes_mce' ] ) )
			foreach ( $styles[ 'classes_mce' ] as $label => $mce_class ) {
				$class = array(
					'title' => $label,
					$mce_class[ 'type' ] => $mce_class[ 'element' ],
					'classes' => $mce_class[ 'name' ]
				);
				if ( $mce_class[ 'wrap' ] ) $class[ 'wrapper' ] = true;
				$formats[] = $class;
			}
		
		$initArray['style_formats'] = json_encode( array_merge( $style_formats, $formats ) );
		
		return $initArray;
	
	}
	
    /**
	 * Admin Action: 'mce_css'
	 * Adds a styles sheet to TinyMCE via ajax that contains the current styles data.
     */
	static function mce_css( $mce_css ) {
		global $post;
		$mce_css .= ',' . wp_nonce_url( admin_url( "admin-ajax.php?action=sns-tinymce-styles-ajax&post_id={$post->ID}" ), 'sns-tinymce-styles-ajax' );
		return $mce_css;
	}

    /**
	 * Admin Action: 'add_meta_boxes'
	 * Main Meta Box function. Checks restriction options and display options, calls add_meta_box() and adds actions for adding admin CSS and JavaScript.
     */
	static function add_meta_boxes() {
		if ( current_user_can( 'unfiltered_html' ) ) {
			self::$post_types = get_post_types( array('show_ui' => true, 'public' => true) ); // updated for http://core.trac.wordpress.org/changeset/18234
			foreach ( self::$post_types as $post_type ) {
				add_meta_box( 'uFp_meta_box', 'Scripts n Styles', array( __CLASS__, 'meta_box' ), $post_type, 'normal', 'high' );
			}
			add_filter( 'default_hidden_meta_boxes', array( __CLASS__,  'default_hidden_meta_boxes' )  );
			add_action( "admin_print_styles", array( __CLASS__, 'meta_box_styles'));
			add_action( "admin_print_scripts", array( __CLASS__, 'meta_box_scripts'));
			add_filter( 'contextual_help', array( __CLASS__, 'contextual_help_filter' ), 10, 3 );
		}
	}
	
	static function default_hidden_meta_boxes( $hidden ) {
		$hidden[] = 'uFp_meta_box';
    	return $hidden;
	}
	
	function contextual_help_filter( $text, $screen_id, $screen ) {
		if ( in_array( $screen->post_type, self::$post_types ) )
			$text .= '<p>In default (non MultiSite) WordPress installs, both <em>Administrators</em> and 
				<em>Editors</em> can access <em>Scripts-n-Styles</em> on individual edit screens. 
				Only <em>Administrators</em> can access this Options Page. In MultiSite WordPress installs, only <em>"Super Admin"</em> users can access either
				<em>Scripts-n-Styles</em> on individual edit screens or this Options Page. If other plugins change capabilities (specifically "unfiltered_html"), 
				other users can be granted access.</p>';
		return $text;
	}
	
    /**
	 * Admin Action: 'add_meta_boxes'
	 * Outputs the Meta Box. Only called on callback from add_meta_box() during the add_meta_boxes action.
	 * @param unknown_type WordPress Post object.
     */
	static function meta_box( $post ) {
		$registered_handles = Scripts_n_Styles::get_wp_registered();
		$styles = get_post_meta( $post->ID, 'uFp_styles', true );
		$scripts = get_post_meta( $post->ID, 'uFp_scripts', true );
		
		$screen = get_current_screen();
		$position = get_user_option( "update-current-sns-tab_{$screen->id}" );
		?>
			<?php wp_nonce_field( Scripts_n_Styles::$file, self::NONCE_NAME ); ?>
			<ul class="wp-tab-bar">
				<li<?php echo ( 0 == $position ) ? ' class="wp-tab-active"': ''; ?>><a href="#uFp_scripts-tab">Scripts</a></li>
				<li<?php echo ( 1 == $position ) ? ' class="wp-tab-active"': ''; ?>><a href="#uFp_styles-tab">Styles</a></li>
				<li<?php echo ( 2 == $position ) ? ' class="wp-tab-active"': ''; ?>><a href="#uFp_classes_body-tab">Classes</a></li>
				<li<?php echo ( 3 == $position ) ? ' class="wp-tab-active"': ''; ?>><a href="#uFp_enqueue_scripts-tab">Include Scripts</a></li>
			</ul>
			
			<div class="wp-tab-panel" id="uFp_scripts-tab">
				<p><em>This code will be included <strong>verbatim</strong> in <code>&lt;script></code> tags at the end of your page's (or post's) ...</em></p>
				<label for="uFp_scripts_in_head" class="title"><strong>Scripts</strong> (for the <code>head</code> element): </label>
				<textarea class="codemirror js" name="uFp_scripts_in_head" id="uFp_scripts_in_head" rows="5" cols="40" style="width: 98%;"><?php echo isset( $scripts[ 'scripts_in_head' ] ) ? $scripts[ 'scripts_in_head' ] : ''; ?></textarea>
				<p><em>... <code>&lt;/head></code> tag.</em></p>
				<label for="uFp_scripts" class="title"><strong>Scripts</strong>: </label>
				<textarea class="codemirror js" name="uFp_scripts" id="uFp_scripts" rows="5" cols="40" style="width: 98%;"><?php echo isset( $scripts[ 'scripts' ] ) ? $scripts[ 'scripts' ] : ''; ?></textarea>
				<p><em>... <code>&lt;/body></code> tag.</em></p>
			</div>
			
			<div class="wp-tab-panel" id="uFp_styles-tab">
				<label for="uFp_styles" class="title"><strong>Styles</strong>: </label>
				<textarea class="codemirror css" name="uFp_styles" id="uFp_styles" rows="5" cols="40" style="width: 98%;"><?php echo isset( $styles[ 'styles' ] ) ? $styles[ 'styles' ] : ''; ?></textarea>
				<p><em>This code will be included <strong>verbatim</strong> in <code>&lt;style></code> tags in the <code>&lt;head></code> tag of your page (or post).</em></p>
			</div>
			
			<div class="wp-tab-panel" id="uFp_classes_body-tab">
				<strong class="title">Classes</strong>
				<div id="sns-classes">
					<p>
						<label for="uFp_classes_body"><strong>Body Classes</strong>: </label>
						<input name="uFp_classes_body" id="uFp_classes_body" type="text" class="code" style="width: 99%;"
							value="<?php echo isset( $styles[ 'classes_body' ] ) ? $styles[ 'classes_body' ] : ''; ?>" />
						<small>Standard: <code><?php self::current_classes( 'body', $post->ID ); ?></code></small>
					</p>
					<p>
						<label for="uFp_classes_post"><strong>Post Classes</strong>: </label>
						<input name="uFp_classes_post" id="uFp_classes_post" type="text" class="code" style="width: 99%;"
							value="<?php echo isset( $styles[ 'classes_post' ] ) ? $styles[ 'classes_post' ] : ''; ?>" />
						<small>Standard: <code><?php self::current_classes( 'post', $post->ID ); ?></code></small>
					</p>
					<p><em>These <strong>space separated</strong> class names will be added to the <code>body_class()</code> or
						<code>post_class()</code> function (provided your theme uses these functions).</em></p>
				</div>
				
				<?php 
				/*
				 * Note: Styles Dropdown section only makes sense when Javascript is enabled. (Otherwise, no TinyMCE.)
				 */
				?>
				<div id="mce-dropdown-names" style="display: none;">
					<h4>The Styles Dropdown</h4>
					<div id="add-mce-dropdown-names">
						<p>Add (or update) a class for the "Styles" drop-down:</p>
						<label for="uFp_classes_mce_label">Label:</label>
						<input name="uFp_classes_mce_label" id="uFp_classes_mce_label"
							value="" type="text" class="code" style="width: 80px;" />
						<br />
						<label for="uFp_classes_mce_type">Type:</label>
						<select name="uFp_classes_mce_type" id="uFp_classes_mce_type" style="width: 80px;">
							<option value="inline">Inline</option>
							<option value="block">Block</option>
							<option value="selector">Selector</option>
						</select>
						<br />
						<label for="uFp_classes_mce_element">Element:</label>
						<input name="uFp_classes_mce_element" id="uFp_classes_mce_element"
							value="" type="text" class="code" style="width: 80px;" />
						<br />
						<label for="uFp_classes_mce_name">Class:</label>
						<input name="uFp_classes_mce_name" id="uFp_classes_mce_name"
							value="" type="text" class="code" style="width: 80px;" />
						<br />
						<label for="uFp_classes_mce_wrap">Wrap:</label>
						<input name="uFp_classes_mce_wrap" id="uFp_classes_mce_wrap" type="checkbox" />
						</p>
					</div>
					
					<div id="delete-mce-dropdown-names" style="display: none;">
						<p id="instructions-mce-dropdown-names">Classes currently in the dropdown:</p>
						
						<?php foreach( $styles[ 'classes_mce' ] as $label => $mce_class ) { ?>
						<p>
						<input type="checkbox"
							name="uFp_classes_mce_delete[<?php echo $label ?>]"
							value="delete"
							id="<?php echo $label ?>" />
						<label for="uFp_classes_mce_delete[<?php echo $label ?>]">
						"<?php echo $label ?>"
						<code>
						<?php echo '&lt;' . $mce_class[ 'element' ] . ' class="' . $mce_class[ 'name' ] . '"&gt;'; ?>
						</code>
						<?php echo ( $mce_class[ 'wrap' ] ) ? ' (wrapper)': ''; ?>
						</label>
						</p>
						<?php } ?>
						
					</div>
				</div>
			</div>
			
			<div class="wp-tab-panel" id="uFp_enqueue_scripts-tab">
				<strong class="title">Include Scripts</strong>
				<select name="uFp_enqueue_scripts[]" id="uFp_enqueue_scripts" size="5" multiple="multiple" style="height: auto; float: left; margin: 6px 10px 8px 0;">
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
				<?php if ( ! empty( $scripts[ 'enqueue_scripts' ] ) && is_array( $scripts[ 'enqueue_scripts' ] ) ) { ?>
					<p>Currently Enqueued Scripts:
					<?php foreach ( $scripts[ 'enqueue_scripts' ] as $handle )  echo '<code>' . $handle . '</code> '; ?>
					</p>
				<?php } ?>
				<p><em>The chosen scripts will be enqueued and placed before your codes if your code is dependant on certain scripts (like jQuery).</em></p>
				<p>NOTE: Not all Scripts in the list are appropriate for use in themes. This is merely a generated list of all currently available registered scripts. It's possible some scripts could be registered only on the "front end" and therefore not listed here.</p>
			</div>
		<?php
	}
	
	function current_classes( $type, $post_id ) {
		if ( 'body' == $type ) {
			global $wp_query;
			$save = $wp_query;
			$param = ( 'page' == get_post_type( $post_id ) ) ? 'page_id': 'p';
			$wp_query = new WP_Query( "$param=$post_id" );
			echo join( ' ', get_body_class( '', $post_id ) );
			$wp_query = $save;
		} else {
			echo join( ' ', get_post_class( '', $post_id ) );
		}
	}
	
    /**
	 * Admin Action: 'admin_print_styles' Action added during 'add_meta_boxes' (which restricts output to Edit Screens).
	 * Enqueues the CSS for admin styling of the Meta Box.
     */
	static function meta_box_styles() {
		wp_enqueue_style( 'codemirror', plugins_url( 'libraries/codemirror/lib/codemirror.css', Scripts_n_Styles::$file), array(), '2.11' );
		wp_enqueue_style( 'codemirror-default', plugins_url( 'libraries/codemirror/theme/default.css', Scripts_n_Styles::$file), array( 'codemirror' ), '2.11' );
		wp_enqueue_style( 'sns-meta-box-styles', plugins_url( 'css/meta-box-styles.css', Scripts_n_Styles::$file), array( 'codemirror-default' ), SnS_Admin::VERSION );
	}
	
    /**
	 * Admin Action: 'admin_print_styles' Action added during 'add_meta_boxes' (which restricts output to Edit Screens).
	 * Enqueues the JavaScript for the admin Meta Box.
     */
	static function meta_box_scripts() {
		wp_enqueue_script(
			'codemirror',
			plugins_url( 'libraries/codemirror/lib/codemirror.js', Scripts_n_Styles::$file),
			array(),
			'2.11' );
		wp_enqueue_script(
			'codemirror-css',
			plugins_url( 'libraries/codemirror/mode/css.js', Scripts_n_Styles::$file),
			array(  'codemirror' ),
			'2.11' );
		wp_enqueue_script(
			'codemirror-javascript',
			plugins_url( 'libraries/codemirror/mode/javascript.js', Scripts_n_Styles::$file),
			array(  'codemirror' ),
			'2.11' );
		/*wp_register_script(
			'codemirror-xml',
			plugins_url( 'libraries/codemirror/mode/xml.js', Scripts_n_Styles::$file),
			array(  'codemirror' ),
			'2.11' );*/
		/*wp_register_script(
			'codemirror-htmlmixed',
			plugins_url( 'libraries/codemirror/mode/htmlmixed.js', Scripts_n_Styles::$file),
			array( 	'codemirror-xml',
					'codemirror-css',
					'codemirror-javascript'
				),
			'2.11' );*/
		/*wp_register_script(
			'codemirror-clike',
			plugins_url( 'libraries/codemirror/mode/clike.js', Scripts_n_Styles::$file),
			array(  'codemirror' ),
			'2.11' );
		wp_register_script(
			'codemirror-php',
			plugins_url( 'libraries/codemirror/mode/php.js', Scripts_n_Styles::$file),
			array( 	'codemirror-xml',
					'codemirror-css',
					'codemirror-javascript',
					'codemirror-clike'
				),
			'2.11' );*/
		wp_enqueue_script(
			'sns-meta-box-scripts',
			plugins_url( 'js/meta-box-scripts.js', Scripts_n_Styles::$file),
			array( 	'jquery-ui-tabs',
					'codemirror-javascript',
					'codemirror-css'//,
					//'codemirror-htmlmixed',
					//'codemirror-php'
				),
			SnS_Admin::VERSION, true );
	}
	
    /**
	 * Admin Action: 'save_post'
	 * Saves the values entered in the Meta Box when a post is saved (on the Edit Screen only, excluding autosaves) if the user has permission.
	 * @param int $post_id ID value of the WordPress post.
     */
	static function save_post( $post_id ) {
		if ( isset( $_POST[ self::NONCE_NAME ] ) && wp_verify_nonce( $_POST[ self::NONCE_NAME ], Scripts_n_Styles::$file )
			&& current_user_can( 'unfiltered_html' ) 
			&& ! wp_is_post_revision( $post_id ) // is needed for get_post_meta compatibility.
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
			
			// start of MCE Dropdown code.
			$temp_styles = get_post_meta( $post_id, 'uFp_styles', true );
			if ( ! isset( $temp_styles[ 'classes_mce' ] ) )
				$classes_mce = array();
			else 
				$classes_mce = $temp_styles[ 'classes_mce' ];
				
			if ( ! empty( $_POST[ 'uFp_classes_mce_label' ] )
				&& ! empty( $_POST[ 'uFp_classes_mce_element' ] )
				&& ! empty( $_POST[ 'uFp_classes_mce_name' ] )
			) {
				$label = sanitize_title( $_POST[ 'uFp_classes_mce_label' ] );
				$element = sanitize_key( $_POST[ 'uFp_classes_mce_element' ] );
				$name = sanitize_title_with_dashes( $_POST[ 'uFp_classes_mce_name' ] );
				
				if ( isset( $_POST[ 'uFp_classes_mce_type' ] ) && 'block' == $_POST[ 'uFp_classes_mce_type' ] )
					$type = 'block';
				else if ( isset( $_POST[ 'uFp_classes_mce_type' ] ) && 'inline' == $_POST[ 'uFp_classes_mce_type' ] )
					$type = 'inline';
				else
					$type = 'selector';
				
				$wrap = ( isset( $_POST[ 'uFp_classes_mce_wrap' ] ) && 'block' == $type ) ? true: false;
				
				$mce_class = array();
				$mce_class[ 'type' ] = $type;
				$mce_class[ 'element' ] = $element;
				$mce_class[ 'name' ] = $name;
				$mce_class[ 'wrap' ] = $wrap;
				
				$classes_mce[ $label ] = $mce_class;
			}
			if ( ! empty( $classes_mce ) )
				$styles[ 'classes_mce' ] = $classes_mce;
			
			if ( isset( $_POST[ 'uFp_classes_mce_delete' ] ) && is_array( $_POST[ 'uFp_classes_mce_delete' ] ) ) 
				foreach ( $_POST[ 'uFp_classes_mce_delete' ] as $key => $value )
					unset( $styles[ 'classes_mce' ][ $key ] );
			// end of MCE Dropdown code.
			
			update_post_meta( $post_id, 'uFp_scripts', $scripts );
			update_post_meta( $post_id, 'uFp_styles', $styles );
		}
	}
}
?>