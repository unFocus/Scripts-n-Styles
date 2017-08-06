<?php
namespace unFocus\SnS;

/**
 * Settings_Page
 *
 * Allows WordPress admin users the ability to add custom CSS
 * and JavaScript directly to individual Post, Pages or custom
 * post types.
 */

add_action( 'admin_menu', function(){
	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'unfiltered_html' ) ) return;

	remove_submenu_page( 'themes.php', 'theme-editor.php' );

	$hook_suffix = add_theme_page(
		__( 'Scripts n Styles', 'scripts-n-styles' ),
		__( 'Editor', 'scripts-n-styles' ),
		'edit_themes',
		ADMIN_MENU_SLUG.'_theme_editor',
		function() {
			global $action, $error, $file, $theme,
				   $stylesheet, $relative_file, $allowed_files, $has_templates, $scrollto;

			$title = __("Edit Themes");

			update_recently_edited( $file );

			if ( ! is_file( $file ) )
				$error = true;

			$content = '';
			if ( ! $error && filesize( $file ) > 0 ) {
				$f = fopen($file, 'r');
				$content = fread($f, filesize($file));

				if ( '.php' == substr( $file, strrpos( $file, '.' ) ) ) {
					$functions = wp_doc_link_parse( $content );

					$docs_select = '<select name="docs-list" id="docs-list">';
					$docs_select .= '<option value="">' . esc_attr__( 'Function Name&hellip;' ) . '</option>';
					foreach ( $functions as $function ) {
						$docs_select .= '<option value="' . esc_attr( urlencode( $function ) ) . '">' . htmlspecialchars( $function ) . '()</option>';
					}
					$docs_select .= '</select>';
				}

				$content = esc_textarea( $content );
			}

			if ( isset( $_GET['updated'] ) ) : ?>
				<div id="message" class="updated notice is-dismissible"><p><?php _e( 'File edited successfully.' ) ?></p></div>
			<?php endif;

			$description = get_file_description( $relative_file );
			$file_show = array_search( $file, array_filter( $allowed_files ) );
			if ( $description != $file_show )
				$description .= ' <span>(' . $file_show . ')</span>';
			?>
			<div class="wrap">
				<h1><?php echo esc_html( $title ); ?></h1>

				<div class="fileedit-sub">
					<div class="alignleft">
					<h2><?php echo $theme->display( 'Name' ); if ( $description ) echo ': ' . $description; ?></h2>
					</div>
					<div class="alignright">
						<form action="themes.php?page=sns_theme_editor" method="post">
							<strong><label for="theme"><?php _e('Select theme to edit:'); ?> </label></strong>
							<select name="theme" id="theme">
					<?php
					foreach ( wp_get_themes( array( 'errors' => null ) ) as $a_stylesheet => $a_theme ) {
						if ( $a_theme->errors() && 'theme_no_stylesheet' == $a_theme->errors()->get_error_code() )
							continue;

						$selected = $a_stylesheet == $stylesheet ? ' selected="selected"' : '';
						echo "\n\t" . '<option value="' . esc_attr( $a_stylesheet ) . '"' . $selected . '>' . $a_theme->display('Name') . '</option>';
					}
					?>
							</select>
							<?php submit_button( __( 'Select' ), '', 'Submit', false ); ?>
						</form>
					</div>
					<br class="clear" />
				</div>
				<?php
				if ( $theme->errors() )
					echo '<div class="error"><p><strong>' . __( 'This theme is broken.' ) . '</strong> ' . $theme->errors()->get_error_message() . '</p></div>';
					?>
				<div id="templateside">
					<?php
					if ( $allowed_files ) :
						$previous_file_type = '';

						foreach ( $allowed_files as $filename => $absolute_filename ) :
							$file_type = substr( $filename, strrpos( $filename, '.' ) );

							if ( $file_type !== $previous_file_type ) {
								$skip = false;
								if (
									in_array($file_type,['.js','.jsx','.ts', '.tsx','.coffee']) &&
									in_array($previous_file_type,['.js','.jsx','.ts', '.tsx','.coffee']) ) {
									$skip = true;
								}
								if (
									in_array($file_type,['.css','.less','.sass','.scss','.styl']) &&
									in_array($previous_file_type,['.css','.less','.sass','.scss','.styl']) ) {
									$skip = true;
								}

								if ( '' !== $previous_file_type && !$skip ) {
									echo "\t</ul>\n";
								}

								if (!$skip)
								switch ( $file_type ) {
									case '.php':
										if ( $has_templates || $theme->parent() ) :
											echo "\t<h2>" . __( 'Templates' ) . "</h2>\n";
											if ( $theme->parent() ) {
												echo '<p class="howto">' . sprintf( __( 'This child theme inherits templates from a parent theme, %s.' ),
													sprintf( '<a href="%s">%s</a>',
														self_admin_url( 'themes.php?page=sns_theme_editor&theme=' . urlencode( $theme->get_template() ) ),
														$theme->parent()->display( 'Name' )
													)
												) . "</p>\n";
											}
										endif;
										break;
									case '.css':
										echo "\t<h2>" . _x( 'Styles', 'Theme stylesheets in theme editor' ) . "</h2>\n";
										break;

									case '.js':
										echo "\t<h2>" . _x( 'Scripts', 'Theme JavaScript in theme editor', 'scripts-n-styles' ) . "</h2>\n";
										break;

									default:
										/* translators: %s: file extension */
										echo "\t<h2>" . sprintf( __( '%s files' ), $file_type ) . "</h2>\n";
										break;
								}

								if (!$skip)
								echo "\t<ul>\n";
							}

							$file_description = get_file_description( $filename );
							if ( $filename !== basename( $absolute_filename ) || $file_description !== $filename ) {
								$file_description .= '<br /><span class="nonessential">(' . $filename . ')</span>';
							}

							if ( $absolute_filename === $file ) {
								$file_description = '<span class="highlight">' . $file_description . '</span>';
							}

							$previous_file_type = $file_type;
							?>
							<li><a href="themes.php?page=sns_theme_editor&file=<?php echo urlencode( $filename ) ?>&amp;theme=<?php echo urlencode( $stylesheet ) ?>"><?php echo $file_description; ?></a></li>
							<?php
						endforeach;
						?>
					</ul>
					<?php endif; ?>
				</div>
				<?php if ( $error ) :
					echo '<div class="error"><p>' . __('Oops, no such file exists! Double check the name and try again, merci.') . '</p></div>';
				else : ?>
					<form name="template" id="template" action="themes.php?page=sns_theme_editor" method="post">
						<?php wp_nonce_field( 'edit-theme_' . $file . $stylesheet ); ?>
						<div><textarea cols="70" rows="30" name="newcontent" id="newcontent" aria-describedby="newcontent-description"><?php echo $content; ?></textarea>
							<input type="hidden" name="action" value="update" />
							<input type="hidden" name="file" value="<?php echo esc_attr( $relative_file ); ?>" />
							<input type="hidden" name="theme" value="<?php echo esc_attr( $theme->get_stylesheet() ); ?>" />
							<input type="hidden" name="scrollto" id="scrollto" value="<?php echo $scrollto; ?>" />
						</div>
						<?php if ( ! empty( $functions ) ) : ?>
							<div id="documentation" class="hide-if-no-js">
							<label for="docs-list"><?php _e('Documentation:') ?></label>
							<?php echo $docs_select; ?>
							<input type="button" class="button" value="<?php esc_attr_e( 'Look Up' ); ?>" onclick="if ( '' != jQuery('#docs-list').val() ) { window.open( 'https://api.wordpress.org/core/handbook/1.0/?function=' + escape( jQuery( '#docs-list' ).val() ) + '&amp;locale=<?php echo urlencode( get_user_locale() ) ?>&amp;version=<?php echo urlencode( get_bloginfo( 'version' ) ) ?>&amp;redirect=true'); }" />
							</div>
						<?php endif; ?>

						<div>
							<?php if ( is_child_theme() && $theme->get_stylesheet() == get_template() ) : ?>
								<p><?php if ( is_writeable( $file ) ) { ?><strong><?php _e( 'Caution:' ); ?></strong><?php } ?>
								<?php _e( 'This is a file in your current parent theme.' ); ?></p>
							<?php endif; ?>
							<?php
							if ( is_writeable( $file ) ) :
								submit_button( __( 'Update File' ), 'primary', 'submit', true );
							else : ?>
								<p><em><?php _e('You need to make this file writable before you can save your changes. See <a href="https://codex.wordpress.org/Changing_File_Permissions">the Codex</a> for more information.'); ?></em></p>
							<?php endif; ?>
						</div>
					</form>
				<?php
				endif; // $error
				?>
				<br class="clear" />
			</div>
			<script type="text/javascript">
			jQuery(document).ready(function($){
				$('#template').submit(function(){ $('#scrollto').val( $('#newcontent').scrollTop() ); });
				$('#newcontent').scrollTop( $('#scrollto').val() );
			});
			</script>
			<?php
		}
	);
	add_action( "admin_print_styles-$hook_suffix", function() {
		$options = get_option( 'SnS_options' );
		$cm_theme = isset( $options[ 'cm_theme' ] ) ? $options[ 'cm_theme' ] : 'default';
		wp_enqueue_style(   'sns-code-editor' );
		wp_enqueue_script(  'sns-code-editor' );
		wp_localize_script( 'sns-code-editor', 'codemirror_options', array( 'theme' => $cm_theme ) );
		wp_localize_script( 'sns-code-editor', 'sns_plugin_editor_options', array(
			'action' => 'sns_plugin_editor',
			'nonce' => wp_create_nonce( 'sns_plugin_editor')
		) );
	} );

	add_action( "load-$hook_suffix", function() {
		global $action, $error, $file, $theme,
			   $stylesheet, $relative_file, $allowed_files, $has_templates, $scrollto;

		if ( is_multisite() && ! is_network_admin() ) {
			wp_redirect( network_admin_url( 'themes.php?page=sns_theme_editor' ) );
			exit();
		}

		get_current_screen()->add_help_tab( array(
			'id'		=> 'overview',
			'title'		=> __('Overview'),
			'content'	=>
				'<p>' . __('You can use the Theme Editor to edit the individual CSS and PHP files which make up your theme.') . '</p>
				<p>' . __( 'Begin by choosing a theme to edit from the dropdown menu and clicking the Select button. A list then appears of the theme&#8217;s template files. Clicking once on any file name causes the file to appear in the large Editor box.' ) . '</p>
				<p>' . __('For PHP files, you can use the Documentation dropdown to select from functions recognized in that file. Look Up takes you to a web page with reference material about that particular function.') . '</p>
				<p id="newcontent-description">' . __( 'In the editing area the Tab key enters a tab character. To move below this area by pressing Tab, press the Esc key followed by the Tab key. In some cases the Esc key will need to be pressed twice before the Tab key will allow you to continue.' ) . '</p>
				<p>' . __('After typing in your edits, click Update File.') . '</p>
				<p>' . __('<strong>Advice:</strong> think very carefully about your site crashing if you are live-editing the theme currently in use.') . '</p>
				<p>' . sprintf( __('Upgrading to a newer version of the same theme will override changes made here. To avoid this, consider creating a <a href="%s">child theme</a> instead.'), __('https://codex.wordpress.org/Child_Themes') ) . '</p>' .
				( is_network_admin() ? '<p>' . __('Any edits to files from this screen will be reflected on all sites in the network.') . '</p>' : '' )
		) );

		get_current_screen()->set_help_sidebar(
			'<p><strong>' . __('For more information:') . '</strong></p>' .
			'<p>' . __('<a href="https://codex.wordpress.org/Theme_Development">Documentation on Theme Development</a>') . '</p>' .
			'<p>' . __('<a href="https://codex.wordpress.org/Using_Themes">Documentation on Using Themes</a>') . '</p>' .
			'<p>' . __('<a href="https://codex.wordpress.org/Editing_Files">Documentation on Editing Files</a>') . '</p>' .
			'<p>' . __('<a href="https://codex.wordpress.org/Template_Tags">Documentation on Template Tags</a>') . '</p>' .
			'<p>' . __('<a href="https://wordpress.org/support/">Support Forums</a>') . '</p>'
		);

		wp_reset_vars( array( 'action', 'error', 'file', 'theme' ) );

		// print_r($GLOBALS); exit;
		if ( !empty($theme) ) {
			$stylesheet = $theme;
		} else {
			$stylesheet = get_stylesheet();
		}

		$theme = wp_get_theme( $stylesheet );

		if ( ! $theme->exists() ) {
			wp_die( __( 'The requested theme does not exist.' ) );
		}

		if ( $theme->errors() && 'theme_no_stylesheet' == $theme->errors()->get_error_code() ) {
			wp_die( __( 'The requested theme does not exist.' ) . ' ' . $theme->errors()->get_error_message() );
		}

		$allowed_files = $style_files = array();
		$has_templates = false;
		$default_types = array( 'php', 'css' );

		/**
		 * Filters the list of file types allowed for editing in the Theme editor.
		 *
		 * @since 4.4.0
		 *
		 * @param array    $default_types List of file types. Default types include 'php' and 'css'.
		 * @param WP_Theme $theme         The current Theme object.
		 */
		$file_types = apply_filters( 'wp_theme_editor_filetypes', $default_types, $theme );

		// Ensure that default types are still there.
		$file_types = array_unique( array_merge( $file_types, $default_types ) );

		foreach ( $file_types as $type ) {
			switch ( $type ) {
				case 'php':
					$allowed_files += $theme->get_files( 'php', apply_filters('wp_theme_scandir_depth', 1, $type, $stylesheet ) );
					$has_templates = ! empty( $allowed_files );
					break;
				case 'css':
					$style_files = $theme->get_files( 'css', apply_filters('wp_theme_scandir_depth', 0, $type, $stylesheet ) );
					$allowed_files['style.css'] = $style_files['style.css'];
					$allowed_files += $style_files;
					break;
				default:
					$allowed_files += $theme->get_files( $type, apply_filters('wp_theme_scandir_depth', 0, $type, $stylesheet ) );
					break;
			}
		}

		if ( empty( $file ) ) {
			$relative_file = 'style.css';
			$file = $allowed_files['style.css'];
		} else {
			$relative_file = $file;
			$file = $theme->get_stylesheet_directory() . '/' . $relative_file;
		}

		validate_file_to_edit( $file, $allowed_files );
		$scrollto = isset( $_REQUEST['scrollto'] ) ? (int) $_REQUEST['scrollto'] : 0;
		switch( $action ) {
			case 'update':
				check_admin_referer( 'edit-theme_' . $file . $stylesheet );
				$newcontent = wp_unslash( $_POST['newcontent'] );
				$location = 'themes.php?page=sns_theme_editor&file=' . urlencode( $relative_file ) . '&theme=' . urlencode( $stylesheet ) . '&scrollto=' . $scrollto;
				if ( is_writeable( $file ) ) {
					// is_writable() not always reliable, check return value. see comments @ https://secure.php.net/is_writable
					$f = fopen( $file, 'w+' );
					if ( $f !== false ) {
						fwrite( $f, $newcontent );
						fclose( $f );
						$location .= '&updated=true';
						$theme->cache_delete();
					}
				}
				wp_redirect( $location );
				exit;
			break;
		}
	}, 49 );

	add_filter( 'wp_theme_scandir_depth', function( $depth, $type, $theme ) {
		return 10;
	},10,3 );

	add_filter( 'wp_theme_editor_filetypes', function( $default_types ) {
		return array_merge( $default_types, [
			'less', 'scss', 'sass', 'styl', 'react.js',
			'jsx', 'js', 'coffee', 'ts', 'tsx', 'json', 'txt', 'md',
			'xml', 'inc', 'include', 'text'
			] );
	}, 10);

}, 102 );