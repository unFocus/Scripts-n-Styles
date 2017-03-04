<?php
namespace unFocus\SnS;

/**
 * SnS_Settings_Page
 *
 * Allows WordPress admin users the ability to add custom CSS
 * and JavaScript directly to individual Post, Pages or custom
 * post types.
 */

class Plugin_Editor_Page
{
	/**
	 * Constants
	 */
	const MENU_SLUG = 'sns_plugin_editor';

	/**
	 * Initializing method.
	 * @static
	 */
	static function init() {
		remove_submenu_page( 'plugins.php', 'plugin-editor.php' );
		$hook_suffix = add_plugins_page(
			__( 'Scripts n Styles', 'scripts-n-styles' ),
			__( 'Editor', 'scripts-n-styles' ),
			'unfiltered_html',
			self::MENU_SLUG,
			[ __CLASS__, 'page' ]
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
		add_action( "load-$hook_suffix", [ __CLASS__, 'take_action' ], 49 );
		add_action( 'wp_ajax_sns_plugin_editor', [ __CLASS__, 'plugin_editor' ] );
	}

	static function _get_plugin_files( $plugin ) {
		// Retreived from https://core.trac.wordpress.org/browser/tags/4.7/src/wp-admin/includes/plugin.php#L193
		// https://core.trac.wordpress.org/attachment/ticket/6531/6531.4.diff
		$plugin_file = WP_PLUGIN_DIR . '/' . $plugin;
		$dir = dirname($plugin_file);
		$plugin_files = array($plugin);

		if ( is_dir($dir) && $dir != WP_PLUGIN_DIR ) {
			$plugins_dir = @ opendir( $dir );
			if ( $plugins_dir ) {
				$plugin_basedir = plugin_basename( $dir );
				while (($file = readdir( $plugins_dir ) ) !== false ) {
					if ( substr($file, 0, 1) == '.' || $file == 'node_modules' || $file == 'bower_components' ) // Skip npm/bower folders
						continue;
					if ( is_dir( "$dir/$file" ) ) {
						$subfiles = self::_get_plugin_sub_files( "$dir/$file" );
						$plugin_files = array_merge( $plugin_files, $subfiles );
					} else {

						if ( plugin_basename("$dir/$file") != $plugin )
							$plugin_files[] = plugin_basename("$dir/$file");

					}
				}
				@closedir( $plugins_dir );
			}
		}

		return $plugin_files;
	}

	static function _get_plugin_sub_files( $subdir ) {
		// https://core.trac.wordpress.org/attachment/ticket/6531/6531.4.diff
		$plugins_subdir = @opendir( $subdir );
		if ( $plugins_subdir ) {
			$plugin_basedir = plugin_basename( $subdir );
			$plugin_files = array();
			while ( ( $subfile = readdir( $plugins_subdir ) ) !== false ) {
				if ( substr( $subfile, 0, 1 ) == '.' || $subfile == 'node_modules' || $subfile == 'bower_components' ) {
					continue;
				}
				if ( is_dir( $subdir  . '/' . $subfile ) ) {
					$subfiles = self::_get_plugin_sub_files( $subdir . '/' . $subfile );
					$plugin_files = array_merge( $plugin_files, $subfiles );
				} else {
					$plugin_files[] = "$plugin_basedir/$subfile";
				}
			}
			@closedir( $plugins_subdir );
		}
		return $plugin_files;
	}

	static function plugin_editor() {
		check_ajax_referer( 'sns_plugin_editor' );
		if ( ! current_user_can( 'edit_plugins' ) ) exit( 'Insufficient Privileges.' );

		$active = false;
		$plugin = '';
		$debug = array();
		$need_update = false;
		$plugins = array_keys( get_plugins() );
		$file = $_REQUEST[ 'file' ];
		$short = substr( $file, 0, strpos( $file, '/' ) );

		if ( ! in_array( $file, $plugins ) ) {
			$need_update = true;

			if ( in_array( $_REQUEST[ 'plugin' ], $plugins ) ) {
				$plugin = $_REQUEST[ 'plugin' ];
			} else {
				foreach ( $plugins as $maybe ) {
					if ( false !== strpos( $maybe, $short ) ) {
						$plugin = $maybe;
						break;
					}
				}
			}
		} else {
			$plugin = $file;
			while ( 1 < substr_count( $plugin, "/" ) ) {
				$plugin = dirname( $plugin );
			}
		}

		$active = is_plugin_active( $plugin ) || is_plugin_active_for_network( $plugin );

		$files = get_plugin_files( $plugin );
		// $files = SnS_AJAX::_get_plugin_files( $plugin );

		add_filter( 'editable_extensions', array( '\unFocus\SnS\Admin_Code_Editor', 'extend' ) );
		$editable_extensions = array('php', 'txt', 'text', 'js', 'css', 'html', 'htm', 'xml', 'inc', 'include');
		$editable_extensions = (array) apply_filters('editable_extensions', $editable_extensions);
		$ul = '';
		foreach ( $files as $plugin_file ) {
			// Get the extension of the file
			if ( preg_match( '/\.([^.]+)$/', $plugin_file, $matches ) ) {
				$ext = strtolower( $matches[1] );
				// If extension is not in the acceptable list, skip it
				if ( ! in_array( $ext, $editable_extensions ) )
					continue;
			} else {
				// No extension found
				continue;
			}
			$ul .= '<li';
			$ul .= $file == $plugin_file ? ' class="highlight">' : '>';
			$ul .= '<a href="plugins.php?page=sns_plugin_editor&file=' . urlencode( $plugin_file ) . '&amp;plugin=' . urlencode( $plugin ) . '">';
			$ul .=  str_replace( $short . '/', '', $plugin_file );
			$ul .= '</a>';
			$ul .= '</li>';
		}

		header('Content-Type: application/json; charset=UTF-8');
		echo json_encode( array(
			"plugin" => $plugin,
			"active" => $active,
			"files" => $files,
			"need_update" => $need_update,
			"ul" => $ul,
		) );

		exit();
	}

	static function take_action() {

		if ( isset( $_REQUEST['action'] ) && 'update' === $_REQUEST['action'] ) {
			if ( !current_user_can('edit_plugins') ) {
				wp_die( __('Sorry, you are not allowed to edit plugins for this site.') );
			}
			$plugins = get_plugins();

			if ( empty( $plugins ) ) { return; }

			$file = '';
			$plugin = '';
			if ( isset( $_REQUEST['file'] ) ) {
				$file = sanitize_text_field( $_REQUEST['file'] );
			}

			if ( isset( $_REQUEST['plugin'] ) ) {
				$plugin = sanitize_text_field( $_REQUEST['plugin'] );
			}

			if ( empty( $plugin ) ) {
				if ( $file ) {
					$plugin = $file;
				} else {
					$plugin = array_keys( $plugins );
					$plugin = $plugin[0];
				}
			}

			$plugin_files = self::_get_plugin_files($plugin);

			if ( empty($file) )
				$file = $plugin_files[0];

			$file = validate_file_to_edit($file, $plugin_files);
			$real_file = WP_PLUGIN_DIR . '/' . $file;

			//

			check_admin_referer('edit-plugin_' . $file);

			$newcontent = wp_unslash( $_POST['newcontent'] );
			if ( is_writeable($real_file) ) {
				$f = fopen($real_file, 'w+');
				fwrite($f, $newcontent);
				fclose($f);

				$network_wide = is_plugin_active_for_network( $file );

				// Deactivate so we can test it.
				if ( (is_plugin_active( $plugin ) && 'scripts-n-styles/scripts-n-styles.php' != $plugin) || isset( $_POST['phperror'] ) ) {
					if ( is_plugin_active( $plugin ) ) {
						deactivate_plugins( $plugin, true );
					}

					if ( ! is_network_admin() ) {
						update_option( 'recently_activated', array( $file => time() ) + (array) get_option( 'recently_activated' ) );
					} else {
						update_site_option( 'recently_activated', array( $file => time() ) + (array) get_site_option( 'recently_activated' ) );
					}

					wp_redirect( add_query_arg( '_wpnonce', wp_create_nonce( 'edit-plugin-test_' . $file ), "plugins.php?page=sns_plugin_editor&file=$file&plugin=$plugin&liveupdate=1&scrollto=$scrollto&networkwide=" . $network_wide ) );
					exit;
				}
				wp_redirect( admin_url( "plugins.php?page=sns_plugin_editor&file=$file&plugin=$plugin&a=te&scrollto=$scrollto" ) );
			} else {
				wp_redirect( admin_url( "plugins.php?page=sns_plugin_editor&file=$file&plugin=$plugin&scrollto=$scrollto" ) );
			}
			exit;

		} elseif ( isset($_GET['liveupdate']) ) {
			check_admin_referer('edit-plugin-test_' . $file);

			$error = validate_plugin( $plugin );

			if ( is_wp_error( $error ) ) {
				wp_die( $error );
			}

			if ( ( ! empty( $_GET['networkwide'] ) && ! is_plugin_active_for_network( $file ) ) || ! is_plugin_active( $file ) ) {
				activate_plugin( $plugin, "plugins.php?page=sns_plugin_editor&file=$file&phperror=1", ! empty( $_GET['networkwide'] ) );
			} // we'll override this later if the plugin can be included without fatal error

			wp_redirect( admin_url("plugins.php?page=sns_plugin_editor&file=$file&plugin=$plugin&a=te&scrollto=$scrollto") );
			exit;
		}
	}
	static function page() {

		if ( !current_user_can('edit_plugins') ) {
			wp_die( __('Sorry, you are not allowed to edit plugins for this site.') );
		}

		$title = __("Edit Plugins");

		$plugins = get_plugins();

		if ( empty( $plugins ) ) {
			?>
			<div class="wrap">
				<h1><?php echo esc_html( $title ); ?></h1>
				<div id="message" class="error"><p><?php _e( 'You do not appear to have any plugins available at this time.' ); ?></p></div>
			</div>
			<?php
			exit;
		}

		$file = '';
		$plugin = '';
		if ( isset( $_REQUEST['file'] ) ) {
			$file = sanitize_text_field( $_REQUEST['file'] );
		}

		if ( isset( $_REQUEST['plugin'] ) ) {
			$plugin = sanitize_text_field( $_REQUEST['plugin'] );
		}

		if ( empty( $plugin ) ) {
			if ( $file ) {
				$plugin = $file;
			} else {
				$plugin = array_keys( $plugins );
				$plugin = $plugin[0];
			}
		}

		$plugin_files = self::_get_plugin_files($plugin);

		if ( empty($file) )
			$file = $plugin_files[0];

		$file = validate_file_to_edit($file, $plugin_files);
		$real_file = WP_PLUGIN_DIR . '/' . $file;
		$scrollto = isset($_REQUEST['scrollto']) ? (int) $_REQUEST['scrollto'] : 0;

		//

		// List of allowable extensions
		$editable_extensions = array('php', 'txt', 'text', 'js', 'css', 'html', 'htm', 'xml', 'inc', 'include');

		/**
		 * Filters file type extensions editable in the plugin editor.
		 *
		 * @since 2.8.0
		 *
		 * @param array $editable_extensions An array of editable plugin file extensions.
		 */
		$editable_extensions = (array) apply_filters( 'editable_extensions', $editable_extensions );

		if ( ! is_file($real_file) ) {
			wp_die(sprintf('<p>%s</p>', __('No such file exists! Double check the name and try again.')));
		} else {
			// Get the extension of the file
			if ( preg_match('/\.([^.]+)$/', $real_file, $matches) ) {
				$ext = strtolower($matches[1]);
				// If extension is not in the acceptable list, skip it
				if ( !in_array( $ext, $editable_extensions) )
					wp_die(sprintf('<p>%s</p>', __('Files of this type are not editable.')));
			}
		}

		update_recently_edited(WP_PLUGIN_DIR . '/' . $file);

		$content = file_get_contents( $real_file );

		if ( '.php' == substr( $real_file, strrpos( $real_file, '.' ) ) ) {
			$functions = wp_doc_link_parse( $content );

			if ( !empty($functions) ) {
				$docs_select = '<select name="docs-list" id="docs-list">';
				$docs_select .= '<option value="">' . __( 'Function Name&hellip;' ) . '</option>';
				foreach ( $functions as $function) {
					$docs_select .= '<option value="' . esc_attr( $function ) . '">' . esc_html( $function ) . '()</option>';
				}
				$docs_select .= '</select>';
			}
		}

		$content = esc_textarea( $content );

		if (isset($_GET['a'])) :
			?>
			<div id="message" class="updated notice is-dismissible"><p><?php _e('File edited successfully.') ?></p></div>
			<?php
		elseif (isset($_GET['phperror'])) :
			?>
			<div id="message" class="updated">
				<p><?php _e('This plugin has been deactivated because your changes resulted in a <strong>fatal error</strong>.') ?></p>
				<?php
				if ( wp_verify_nonce( $_GET['_error_nonce'], 'plugin-activation-error_' . $file ) ) {
					$iframe_url = add_query_arg( array(
						'action'   => 'error_scrape',
						'plugin'   => urlencode( $file ),
						'_wpnonce' => urlencode( $_GET['_error_nonce'] ),
					), admin_url( 'plugins.php' ) );
					?>
					<iframe style="border:0" width="100%" height="70px" src="<?php echo esc_url( $iframe_url ); ?>"></iframe>
				<?php } ?>
			</div>
			<?php
		endif;
		?>
		<div class="wrap">
			<h1><?php echo esc_html( $title ); ?></h1>

			<div class="fileedit-sub">
				<div class="alignleft">
					<big><?php
					if ( is_plugin_active( $plugin ) ) {
						if ( is_writeable( $real_file ) ) {
							/* translators: %s: plugin file name */
							echo sprintf( __( 'Editing %s (active)' ), '<strong>' . $file . '</strong>' );
						} else {
							/* translators: %s: plugin file name */
							echo sprintf( __( 'Browsing %s (active)' ), '<strong>' . $file . '</strong>' );
						}
					} else {
						if ( is_writeable( $real_file ) ) {
							/* translators: %s: plugin file name */
							echo sprintf( __( 'Editing %s (inactive)' ), '<strong>' . $file . '</strong>' );
						} else {
							/* translators: %s: plugin file name */
							echo sprintf( __( 'Browsing %s (inactive)' ), '<strong>' . $file . '</strong>' );
						}
					}
					?></big>
				</div>
				<div class="alignright">
					<form action="plugins.php?page=sns_plugin_editor&" method="post">
						<strong><label for="plugin"><?php _e('Select plugin to edit:'); ?> </label></strong>
						<select name="plugin" id="plugin">
						<?php
							foreach ( $plugins as $plugin_key => $a_plugin ) {
								$plugin_name = $a_plugin['Name'];
								if ( $plugin_key == $plugin )
									$selected = " selected='selected'";
								else
									$selected = '';
								$plugin_name = esc_attr($plugin_name);
								$plugin_key = esc_attr($plugin_key);
								echo "\n\t<option value=\"$plugin_key\" $selected>$plugin_name</option>";
							}
						?>
						</select>
						<?php submit_button( __( 'Select' ), '', 'Submit', false ); ?>
					</form>
				</div>
				<br class="clear" />
			</div>

			<div id="templateside">
				<h2><?php _e( 'Plugin Files' ); ?></h2>

				<ul>
					<?php
					foreach ( $plugin_files as $plugin_file ) :
						// Get the extension of the file
						if ( preg_match('/\.([^.]+)$/', $plugin_file, $matches) ) {
							$ext = strtolower($matches[1]);
							// If extension is not in the acceptable list, skip it
							if ( !in_array( $ext, $editable_extensions ) )
								continue;
						} else {
							// No extension found
							continue;
						}
					?>
					<li<?php echo $file == $plugin_file ? ' class="highlight"' : ''; ?>><a href="plugins.php?page=sns_plugin_editor&file=<?php echo urlencode( $plugin_file ) ?>&amp;plugin=<?php echo urlencode( $plugin ) ?>"><?php echo $plugin_file ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<form name="template" id="template" action="plugins.php?page=sns_plugin_editor" method="post">
				<?php wp_nonce_field('edit-plugin_' . $file) ?>
					<div><textarea cols="70" rows="25" name="newcontent" id="newcontent" aria-describedby="newcontent-description"><?php echo $content; ?></textarea>
					<input type="hidden" name="action" value="update" />
					<input type="hidden" name="file" value="<?php echo esc_attr($file) ?>" />
					<input type="hidden" name="plugin" value="<?php echo esc_attr($plugin) ?>" />
					<input type="hidden" name="scrollto" id="scrollto" value="<?php echo $scrollto; ?>" />
					</div>
					<?php if ( !empty( $docs_select ) ) : ?>
						<div id="documentation" class="hide-if-no-js"><label for="docs-list"><?php _e('Documentation:') ?></label> <?php echo $docs_select ?> <input type="button" class="button" value="<?php esc_attr_e( 'Look Up' ) ?> " onclick="if ( '' != jQuery('#docs-list').val() ) { window.open( 'https://api.wordpress.org/core/handbook/1.0/?function=' + escape( jQuery( '#docs-list' ).val() ) + '&amp;locale=<?php echo urlencode( get_user_locale() ) ?>&amp;version=<?php echo urlencode( get_bloginfo( 'version' ) ) ?>&amp;redirect=true'); }" /></div>
					<?php endif; ?>
					<?php if ( is_writeable($real_file) ) : ?>
						<?php if ( in_array( $plugin, (array) get_option( 'active_plugins', array() ) ) ) { ?>
							<p><?php _e('<strong>Warning:</strong> Making changes to active plugins is not recommended. If your changes cause a fatal error, the plugin will be automatically deactivated.'); ?></p>
						<?php } ?>
						<p class="submit">
						<?php
							if ( isset($_GET['phperror']) ) {
								echo "<input type='hidden' name='phperror' value='1' />";
								submit_button( __( 'Update File and Attempt to Reactivate' ), 'primary', 'submit', false );
							} else {
								submit_button( __( 'Update File' ), 'primary', 'submit', false );
							}
						?>
						</p>
					<?php else : ?>
						<p><em><?php _e('You need to make this file writable before you can save your changes. See <a href="https://codex.wordpress.org/Changing_File_Permissions">the Codex</a> for more information.'); ?></em></p>
					<?php endif; ?>
			</form>
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
} ?>