<?php
/**
 * Admin_Meta_Box
 *
 * Allows WordPress admin users the ability to add custom CSS
 * and JavaScript directly to individual Post, Pages or custom
 * post types.
 *
 * @package Scripts-N-Styles
 */

namespace unFocus\SnS;

add_action( 'current_screen', function() {

	/**
	 * Admin Action: 'current_screen'
	 * Chosen because it's limited to admin screens, and get_current_screen() is
	 * available at this point.
	 */
	if ( ! current_user_can( 'unfiltered_html' ) ) {
		return;
	}

	if ( ! in_array(
		get_current_screen()->post_type,
		get_post_types( [
			'show_ui' => true,
			'public'  => true,
		] ),
		true
	) ) {
		return;
	}

	/**
	 * Admin Action: 'admin_print_styles'
	 * Enqueues the CSS for admin styling of the Meta Box.
	 */
	add_action( 'admin_print_styles', function() {
		wp_enqueue_style( 'sns-meta-box' );
	} );

	/**
	 * Admin Action: 'admin_print_scripts'
	 * Enqueues the JavaScript for the admin Meta Box.
	 */
	add_action( 'admin_print_scripts', function() {
		$options  = get_option( 'SnS_options' );
		$cm_theme = isset( $options['cm_theme'] ) ? $options['cm_theme'] : 'default';

		wp_enqueue_code_editor( [ 'type' => 'php' ] );

		wp_enqueue_script( 'sns-meta-box' );
		wp_localize_script( 'sns-meta-box', '_SnSOptions', [
			'theme' => $cm_theme,
			'root'  => plugins_url( '/', BASENAME ),
		] );
	} );

	/**
	 * Admin Action: 'add_meta_boxes'
	 * Main Meta Box function. Checks restriction options and display options, calls
	 * add_meta_box() and adds actions for adding admin CSS and JavaScript.
	 */
	add_action( 'add_meta_boxes', function() {

		add_meta_box(
			'SnS_meta_box',
			__( 'Scripts n Styles', 'scripts-n-styles' ),
			function ( $post ) {
				$sns = get_post_meta( $post->ID, '_SnS', true );

				$styles  = isset( $sns['styles'] ) ? $sns['styles'] : [];
				$scripts = isset( $sns['scripts'] ) ? $sns['scripts'] : [];

				$position = get_user_option( 'current_sns_tab' );
				if ( ! in_array( $position, [ 's0', 's1', 's2', 's3', 's4', 's5' ], true ) ) {
					$position = 's0';
				}
				wp_nonce_field( BASENAME, NONCE_NAME );
				?>
				<ul class="wp-tab-bar">
					<li class="<?php echo esc_attr( 's0' === $position ? 'wp-tab-active' : '' ); ?>"><a href="#SnS_scripts-tab"><?php esc_html_e( 'Scripts', 'scripts-n-styles' ); ?></a></li>
					<li class="<?php echo esc_attr( 's1' === $position ? 'wp-tab-active' : '' ); ?>"><a href="#SnS_styles-tab"><?php esc_html_e( 'Styles', 'scripts-n-styles' ); ?></a></li>
					<li class="<?php echo esc_attr( 's2' === $position ? 'wp-tab-active' : '' ); ?>"><a href="#SnS_classes_body-tab"><?php esc_html_e( 'Classes', 'scripts-n-styles' ); ?></a></li>
					<li class="<?php echo esc_attr( 's3' === $position ? 'wp-tab-active' : '' ); ?>"><a href="#SnS_enqueue_scripts-tab"><?php esc_html_e( 'Include Scripts', 'scripts-n-styles' ); ?></a></li>
					<li class="<?php echo esc_attr( 's4' === $position ? 'wp-tab-active' : '' ); ?>"><a href="#SnS_shortcodes-tab"><?php esc_html_e( 'Shortcodes', 'scripts-n-styles' ); ?></a></li>
					<li class="<?php echo esc_attr( 's5' === $position ? 'wp-tab-active' : '' ); ?>" style="display:none"><a href="#SnS_post_styles-tab"><?php esc_html_e( 'Dropdown', 'scripts-n-styles' ); ?></a></li>
				</ul>

				<div class="wp-tab-panel" id="SnS_scripts-tab">
					<p><em><?php echo wp_kses_post( "This code will be included <strong>verbatim</strong> in <code>&lt;script></code> tags at the end of your page's (or post's)", 'scripts-n-styles' ); ?> ...</em></p>
					<label for="SnS_scripts_in_head" class="title"><?php echo wp_kses_post( '<strong>Scripts</strong> (for the <code>head</code> element):', 'scripts-n-styles' ); ?> </label>
					<div class="script">
					<textarea class="codemirror js" name="SnS_scripts_in_head" id="SnS_scripts_in_head" rows="5" cols="40" style="width: 98%;"><?php echo esc_textarea( isset( $scripts['scripts_in_head'] ) ? $scripts['scripts_in_head'] : '' ); ?></textarea>
					</div>
					<p><em>... <code>&lt;/head></code> <?php esc_html_e( 'tag', 'scripts-n-styles' ); ?>.</em></p>
					<label for="SnS_scripts" class="title"><strong>Scripts</strong>: </label>
					<div class="script">
					<textarea class="codemirror js" name="SnS_scripts" id="SnS_scripts" rows="5" cols="40" style="width: 98%;"><?php echo esc_textarea( isset( $scripts['scripts'] ) ? $scripts['scripts'] : '' ); ?></textarea>
					</div>
					<p><em>... <code>&lt;/body></code> <?php esc_html_e( 'tag', 'scripts-n-styles' ); ?>.</em></p>
				</div>

				<div class="wp-tab-panel" id="SnS_styles-tab">
					<label for="SnS_styles" class="title"><?php echo wp_kses_post( '<strong>Styles</strong>:', 'scripts-n-styles' ); ?> </label>
					<div class="style">
					<textarea class="codemirror css" name="SnS_styles" id="SnS_styles" rows="5" cols="40" style="width: 98%;"><?php echo esc_textarea( isset( $styles['styles'] ) ? $styles['styles'] : '' ); ?></textarea>
					</div>
					<p><em><?php echo wp_kses_post( 'This code will be included <strong>verbatim</strong> in <code>&lt;style></code> tags in the <code>&lt;head></code> tag of your page (or post).', 'scripts-n-styles' ); ?></em></p>
				</div>

				<div class="wp-tab-panel" id="SnS_classes_body-tab">
					<strong class="title"><?php esc_html_e( 'Classes', 'scripts-n-styles' ); ?></strong>
					<div id="sns-classes">
						<p>
							<label for="SnS_classes_body"><?php echo wp_kses_post( '<strong>Body Classes</strong>:', 'scripts-n-styles' ); ?> </label>
							<input name="SnS_classes_body" id="SnS_classes_body" type="text" class="code" style="width: 99%;"
								value="<?php echo esc_attr( isset( $styles['classes_body'] ) ? $styles['classes_body'] : '' ); ?>" />
						</p>
						<p>
							<label for="SnS_classes_post"><strong>Post Classes</strong>: </label>
							<input name="SnS_classes_post" id="SnS_classes_post" type="text" class="code" style="width: 99%;"
								value="<?php echo esc_attr( isset( $styles['classes_post'] ) ? $styles['classes_post'] : '' ); ?>" />
						</p>
						<p><em><?php echo wp_kses_post( 'These <strong>space separated</strong> class names will be added to the <code>body_class()</code> or <code>post_class()</code> function (provided your theme uses these functions).', 'scripts-n-styles' ); ?></em></p>
					</div>
				</div>
				<?php // Note: Styles Dropdown section only makes sense when Javascript is enabled (Otherwise, no TinyMCE). ?>
				<div class="wp-tab-panel" id="SnS_post_styles-tab" style="display: none;">
					<strong class="title"><?php esc_html_e( 'Post Styles', 'scripts-n-styles' ); ?></strong>
					<div id="mce-dropdown-names">
						<h4><?php esc_html_e( 'The Styles Dropdown', 'scripts-n-styles' ); ?></h4>
						<div id="add-mce-dropdown-names">
							<p><?php esc_html_e( 'Add (or update) a class for the "Styles" drop-down:', 'scripts-n-styles' ); ?></p>
							<p class="sns-mce-title">
								<label for="SnS_classes_mce_title"><?php esc_html_e( 'Title:', 'scripts-n-styles' ); ?></label>
								<input name="SnS_classes_mce_title" id="SnS_classes_mce_title"
									value="" type="text" class="code" style="width: 80px;" />
							</p>
							<p class="sns-mce-type">
								<label for="SnS_classes_mce_type"><?php esc_html_e( 'Type:', 'scripts-n-styles' ); ?></label>
								<select name="SnS_classes_mce_type" id="SnS_classes_mce_type" style="width: 80px;">
									<option value="inline"><?php echo esc_html_x( 'Inline', 'css type', 'scripts-n-styles' ); ?></option>
									<option value="block"><?php echo esc_html_x( 'Block', 'css type', 'scripts-n-styles' ); ?></option>
									<option value="selector"><?php echo esc_html_x( 'Selector:', 'css type', 'scripts-n-styles' ); ?></option>
								</select>
							</p>
							<p class="sns-mce-element">
								<label for="SnS_classes_mce_element"><?php esc_html_e( 'Element:', 'scripts-n-styles' ); ?></label>
								<input name="SnS_classes_mce_element" id="SnS_classes_mce_element"
									value="" type="text" class="code" style="width: 80px;" />
							</p>
							<p class="sns-mce-classes">
								<label for="SnS_classes_mce_classes"><?php esc_html_e( 'Classes:', 'scripts-n-styles' ); ?></label>
								<input name="SnS_classes_mce_classes" id="SnS_classes_mce_classes"
									value="" type="text" class="code" style="width: 80px;" />
							</p>
							<p class="sns-mce-wrapper" style="display: none;">
								<label for="SnS_classes_mce_wrapper"><?php esc_html_e( 'Wrapper:', 'scripts-n-styles' ); ?></label>
								<input name="SnS_classes_mce_wrapper" id="SnS_classes_mce_wrapper" type="checkbox" value="true" />
							</p>
						</div>

						<div id="delete-mce-dropdown-names" style="display: none;">
							<p id="instructions-mce-dropdown-names"><?php esc_html_e( 'Classes currently in the dropdown:', 'scripts-n-styles' ); ?></p>
						</div>
					</div>
				</div>

				<div class="wp-tab-panel" id="SnS_enqueue_scripts-tab">
					<strong class="title">Include Scripts</strong>
					<select name="SnS_enqueue_scripts[]" id="SnS_enqueue_scripts" size="5" multiple="multiple" style="height: auto; float: left; margin: 6px 10px 8px 0;">
						<?php
						if ( ! empty( $scripts['enqueue_scripts'] ) && is_array( $scripts['enqueue_scripts'] ) ) {
							foreach ( get_registered_scripts() as $value ) {
								?>
								<option value="<?php echo esc_attr( $value ); ?>"
									<?php
									foreach ( $scripts['enqueue_scripts'] as $handle ) {
										selected( $handle, $value );
									}
									?>
									>
									<?php echo esc_html( $value ); ?>
								</option>
								<?php
							}
						} else {
							foreach ( get_registered_scripts() as $value ) {
								?>
								<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $value ); ?></option>
								<?php
							}
						}
						?>
					</select>
					<?php if ( ! empty( $scripts['enqueue_scripts'] ) && is_array( $scripts['enqueue_scripts'] ) ) { ?>
						<p><?php esc_html_e( 'Currently Enqueued Scripts:', 'scripts-n-styles' ); ?>
						<?php
						foreach ( $scripts['enqueue_scripts'] as $handle ) {
							echo '<code>' . esc_html( $handle ) . '</code> ';
						}
						?>
						</p>
					<?php } ?>
					<p><em><?php esc_html_e( 'The chosen scripts will be enqueued and placed before your codes if your code is dependant on certain scripts (like jQuery).', 'scripts-n-styles' ); ?></em></p>
				</div>

				<div class="wp-tab-panel" id="SnS_shortcodes-tab">
					<strong class="title">Shortcodes</strong>
					<div id="sns-add-shortcode">
						<?php
						$meta_name  = 'SnS_shortcodes';
						$sns        = get_post_meta( $post->ID, '_SnS', true );
						$shortcodes = isset( $sns['shortcodes'] ) ? $sns['shortcodes'] : [];
						?>
						<label for="<?php echo esc_attr( $meta_name ); ?>">Name: </label>
						<input id="<?php echo esc_attr( $meta_name ); ?>" name="<?php echo esc_attr( $meta_name ) . '[new][name]'; ?>" type="text" />
						<textarea id="<?php echo esc_attr( $meta_name ); ?>_new" class="codemirror htmlmixed" name="<?php echo esc_attr( $meta_name . '[new][value]' ); ?>" rows="5" cols="40" style="width: 98%;"></textarea>
					</div>
					<div id="sns-shortcodes">
						<h4>Existing Codes: </h4>
						<div id="sns-shortcodes-wrap">
						<?php if ( ! empty( $shortcodes ) ) { ?>
							<?php foreach ( $shortcodes as $key => $value ) { ?>
								<div class="sns-shortcode widget"><div class="inside">
								<p>[hoops name="<?php echo esc_attr( $key ); ?>"]</p>
								<textarea class="codemirror htmlmixed" data-sns-shortcode-key="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $meta_name ) . '[existing][' . esc_attr( $key ) . ']'; ?>" rows="5" cols="40" style="width: 98%;"><?php echo esc_textarea( $value ); ?></textarea>
								</div></div>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
				<?php
			},
			get_current_screen()->post_type,
			'normal',
			'high'
		);

		add_filter( 'contextual_help', __NAMESPACE__ . '\help' );

	} );

	/**
	 * Adds the "Format" dropdown.
	 */
	add_filter( 'mce_buttons_2', function( $buttons ) {
		global $post;
		$sns    = get_post_meta( $post->ID, '_SnS', true );
		$styles = isset( $sns['styles'] ) ? $sns['styles'] : [];

		if ( ! empty( $styles['classes_mce'] ) ) {
			array_unshift( $buttons, 'styleselect' );
		}
		return $buttons;
	} );

	/**
	 * Populates the "Format" dropdown.
	 */
	add_filter( 'tiny_mce_before_init', function( $init ) {
		global $post;
		$sns    = get_post_meta( $post->ID, '_SnS', true );
		$styles = isset( $sns['styles'] ) ? $sns['styles'] : [];

		// Add div as a format option, should probably use a string replace thing here.
		// Better yet, a setting for adding these. Postpone for now.
		// $init['theme_advanced_blockformats'] = "p,address,pre,h1,h2,h3,h4,h5,h6,div"; .
		if ( ( ! empty( $styles['classes_body'] ) || ! empty( $styles['classes_post'] ) ) && ! isset( $init['body_class'] ) ) {
			$init['body_class'] = '';
		}

		// Add body_class (and/or maybe post_class) values... somewhat problematic.
		if ( ! empty( $styles['classes_body'] ) ) {
			$init['body_class'] .= ' ' . $styles['classes_body'];
		}
		if ( ! empty( $styles['classes_post'] ) ) {
			$init['body_class'] .= ' ' . $styles['classes_post'];
		}

		// In case Themes or plugins have added style_formats, not tested.
		if ( isset( $init['style_formats'] ) ) {
			$style_formats = json_decode( $init['style_formats'], true );
		} else {
			$style_formats = [];
		}

		if ( ! empty( $styles['classes_mce'] ) ) {
			foreach ( $styles['classes_mce'] as $format ) {
				$style_formats[] = $format;
			}
		}

		if ( ! empty( $style_formats ) ) {
			$init['style_formats'] = wp_json_encode( $style_formats );
		}

		return $init;
	} );

	/**
	 * Admin Action: 'replace_editor'
	 * Adds a styles sheet to TinyMCE via ajax that contains the current styles data.
	 */
	add_filter( 'replace_editor', function( $return ) {
		$url = admin_url( 'admin-ajax.php' );
		$url = wp_nonce_url( $url, 'sns_tinymce_styles' );
		$url = add_query_arg( 'post_id', get_the_ID(), $url );
		$url = add_query_arg( 'action', 'sns_tinymce_styles', $url );
		add_theme_support( 'editor-styles' );
		add_editor_style( $url );

		return $return;
	} );

	/**
	 * Admin Action: 'save_post'
	 * Saves the values entered in the Meta Box when a post is saved (on the Edit Screen only, excluding autosaves) if the user has permission.
	 *
	 * @param int $post_id ID value of the WordPress post.
	 */
	add_action( 'save_post', function( $post_id ) {
		if ( ! isset( $_POST[ NONCE_NAME ] ) || ! wp_verify_nonce( sanitize_key( $_POST[ NONCE_NAME ] ), BASENAME )
			|| ! current_user_can( 'unfiltered_html' )
			|| wp_is_post_revision( $post_id ) // is needed for get_post_meta compatibility.
			|| ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		) {
			return;
		}

		/*
		NOTE: There is no current_user_can( 'edit_post' ) check here, because as far as I
		can tell, in /wp-admin/post.php the calls edit_post(), write_post(), post_preview(),
		wp_untrash_post(), etc., the check is already done prior to the 'save_post' action,
		which is where this function is called. Other calls are from other pages so the
		NONCE covers those cases, and that leaves autosave, which is also checked here.
		*/

		$sns = get_post_meta( $post_id, '_SnS', true );
		// http://php.net/manual/en/migration71.incompatible.php#migration71.incompatible.empty-string-index-operator for explaination.
		$sns = is_array( $sns ) ? $sns : [];

		$scripts = isset( $sns['scripts'] ) ? $sns['scripts'] : [];
		$styles  = isset( $sns['styles'] ) ? $sns['styles'] : [];

		$scripts = maybe_set_metabox( $scripts, 'scripts_in_head' );
		$scripts = maybe_set_metabox( $scripts, 'scripts' );
		$scripts = maybe_set_metabox( $scripts, 'enqueue_scripts' );
		$styles  = maybe_set_metabox( $styles, 'styles' );
		$styles  = maybe_set_metabox( $styles, 'classes_body' );
		$styles  = maybe_set_metabox( $styles, 'classes_post' );

		$shortcodes = [];

		$sns_shortcodes = isset( $_REQUEST['SnS_shortcodes'] ) ? $_REQUEST['SnS_shortcodes'] : [];

		$existing_shortcodes = isset( $sns_shortcodes['existing'] ) ? $sns_shortcodes['existing'] : [];
		foreach ( $existing_shortcodes as $key => $value ) {
			if ( ! empty( $value ) ) {
				$shortcodes[ $key ] = $value;
			}
		}

		$new_shortcode = isset( $sns_shortcodes['new'] ) ? $sns_shortcodes['new'] : [];
		if ( ! empty( $new_shortcode['value'] ) ) {

			$key = ( isset( $new_shortcode['name'] ) ) ? $new_shortcode['name'] : '';

			if ( '' === $key ) {
				$key = count( $shortcodes );
				while ( isset( $shortcodes[ $key ] ) ) {
					$key++;
				}
			}

			if ( isset( $shortcodes[ $key ] ) ) {
				$countr = 1;
				while ( isset( $shortcodes[ $key . '_' . $countr ] ) ) {
					$countr++;
				}
				$key .= '_' . $countr;
			}

			$shortcodes[ $key ] = $new_shortcode['value'];

		}

		// This one isn't posted, it's ajax only. Cleanup anyway.
		if ( isset( $styles['classes_mce'] ) && empty( $styles['classes_mce'] ) ) {
			unset( $styles['classes_mce'] );
		}

		if ( empty( $scripts ) ) {
			if ( isset( $sns['scripts'] ) ) {
				unset( $sns['scripts'] );
			}
		} else {
			$sns['scripts'] = $scripts;
		}

		if ( empty( $styles ) ) {
			if ( isset( $sns['styles'] ) ) {
				unset( $sns['styles'] );
			}
		} else {
			$sns['styles'] = $styles;
		}

		if ( empty( $shortcodes ) ) {
			if ( isset( $sns['shortcodes'] ) ) {
				unset( $sns['shortcodes'] );
			}
		} else {
			$sns['shortcodes'] = $shortcodes;
		}

		if ( empty( $sns ) ) {
			delete_post_meta( $post_id, '_SnS' );
		} else {
			update_post_meta( $post_id, '_SnS', $sns );
		}
	} );

	/**
	 * Filters $o and Checks if the sent data $i is empty (intended to clear). If not, updates.
	 *
	 * @param array  $o The object.
	 * @param string $i The index.
	 * @param string $p The prefix.
	 */
	function maybe_set_metabox( $o, $i, $p = 'SnS_' ) {
		if ( ! is_array( $o ) ) {
			return [];
		}
		if ( empty( $_REQUEST[ $p . $i ] ) ) {
			if ( isset( $o[ $i ] ) ) {
				unset( $o[ $i ] );
			}
		} else {
			$o[ $i ] = $_REQUEST[ $p . $i ];
		}
		return $o;
	}
});
