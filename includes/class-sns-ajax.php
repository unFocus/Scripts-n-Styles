<?php
class SnS_AJAX
{
	static function init() {
		// Keep track of current tab.
		add_action( 'wp_ajax_sns_update_tab', array( __CLASS__, 'update_tab' ) );
		// TinyMCE requests a css file.
		add_action( 'wp_ajax_sns_tinymce_styles', array( __CLASS__, 'tinymce_styles' ) );

		// Ajax Saves.
		add_action( 'wp_ajax_sns_classes', array( __CLASS__, 'classes' ) );
		add_action( 'wp_ajax_sns_scripts', array( __CLASS__, 'scripts' ) );
		add_action( 'wp_ajax_sns_styles', array( __CLASS__, 'styles' ) );
		add_action( 'wp_ajax_sns_dropdown', array( __CLASS__, 'dropdown' ) );
		add_action( 'wp_ajax_sns_delete_class', array( __CLASS__, 'delete_class' ) );
		add_action( 'wp_ajax_sns_shortcodes', array( __CLASS__, 'shortcodes' ) );
		add_action( 'wp_ajax_sns_open_theme_panels', array( __CLASS__, 'open_theme_panels' ) );
		add_action( 'wp_ajax_sns_plugin_editor', array( __CLASS__, 'plugin_editor' ) );
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

		add_filter( 'editable_extensions', array( 'SnS_Admin_Code_Editor', 'extend' ) );
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
			$ul .= '<a href="plugin-editor.php?file=' . urlencode( $plugin_file ) . '&amp;plugin=' . urlencode( $plugin ) . '">';
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
	static function open_theme_panels() {
		check_ajax_referer( SnS_Admin::OPTION_GROUP . "-options" );

		$name = isset( $_POST[ 'file-name' ] ) ? $_POST[ 'file-name' ] : '';
		if ( empty( $name ) ) exit( 'empty name');

		$collapsed = isset( $_POST[ 'collapsed' ] ) ? $_POST[ 'collapsed' ] : '';
		if ( empty( $collapsed ) ) exit( 'empty value');

		if ( ! $user = get_current_user_id() ) exit( 'Bad User' );

		$open_theme_panels = json_decode( get_user_option( 'sns_open_theme_panels', $user ), true );
		$open_theme_panels = is_array( $open_theme_panels ) ? $open_theme_panels : array();
		$open_theme_panels[ $name ] = $collapsed;
		$open_theme_panels = json_encode( $open_theme_panels );
		update_user_option( $user, 'sns_open_theme_panels', $open_theme_panels );

		exit();
	}
	static function update_tab() {
		check_ajax_referer( Scripts_n_Styles::$file );

		$active_tab = isset( $_POST[ 'active_tab' ] ) ? 's'.$_POST[ 'active_tab' ] : 's0';

		if ( ! $user = wp_get_current_user() ) exit( 'Bad User' );

		$success = update_user_option( $user->ID, 'current_sns_tab', $active_tab, true);
		exit();
	}
	static function tinymce_styles() {
		check_ajax_referer( 'sns_tinymce_styles' );

		if ( empty( $_REQUEST[ 'post_id' ] ) ) exit( 'Bad post ID.' );
		$post_id = absint( $_REQUEST[ 'post_id' ] );

		$options = get_option( 'SnS_options' );
		$SnS = get_post_meta( $post_id, '_SnS', true );
		$styles = isset( $SnS['styles'] ) ? $SnS[ 'styles' ]: array();

		header('Content-Type: text/css; charset=UTF-8');

		if ( ! empty( $options[ 'styles' ] ) ) echo $options[ 'styles' ];

		if ( ! empty( $styles[ 'styles' ] ) ) echo $styles[ 'styles' ];

		exit();
	}

	// AJAX handlers
	static function classes() {
		check_ajax_referer( Scripts_n_Styles::$file );
		if ( ! current_user_can( 'unfiltered_html' ) || ! current_user_can( 'edit_posts' ) ) exit( 'Insufficient Privileges.' );

		if ( empty( $_REQUEST[ 'post_id' ] ) ) exit( 'Bad post ID.' );
		if ( ! isset( $_REQUEST[ 'classes_body' ], $_REQUEST[ 'classes_post' ] ) ) exit( 'Data missing.' );

		$post_id = absint( $_REQUEST[ 'post_id' ] );
		$SnS = get_post_meta( $post_id, '_SnS', true );
		$styles = isset( $SnS['styles'] ) ? $SnS[ 'styles' ]: array();

		$styles = self::maybe_set( $styles, 'classes_body' );
		$styles = self::maybe_set( $styles, 'classes_post' );

		if ( empty( $styles ) ) {
			if ( isset( $SnS['styles'] ) )
				unset( $SnS['styles'] );
		} else {
			$SnS[ 'styles' ] = $styles;
		}
		self::maybe_update( $post_id, '_SnS', $SnS );

		header('Content-Type: application/json; charset=UTF-8');
		echo json_encode( array(
			"classes_post" => $_REQUEST[ 'classes_post' ]
			, "classes_body" => $_REQUEST[ 'classes_body' ]
		) );

		exit();
	}
	static function scripts() {
		check_ajax_referer( Scripts_n_Styles::$file );
		if ( ! current_user_can( 'unfiltered_html' ) || ! current_user_can( 'edit_posts' ) ) exit( 'Insufficient Privileges.' );

		if ( empty( $_REQUEST[ 'post_id' ] ) ) exit( 'Bad post ID.' );
		if ( ! isset( $_REQUEST[ 'scripts' ], $_REQUEST[ 'scripts_in_head' ] ) ) exit( 'Data incorrectly sent.' );

		$post_id = absint( $_REQUEST[ 'post_id' ] );
		$SnS = get_post_meta( $post_id, '_SnS', true );
		$scripts = isset( $SnS['scripts'] ) ? $SnS[ 'scripts' ]: array();

		$scripts = self::maybe_set( $scripts, 'scripts_in_head' );
		$scripts = self::maybe_set( $scripts, 'scripts' );

		if ( empty( $scripts ) ) {
			if ( isset( $SnS['scripts'] ) )
				unset( $SnS['scripts'] );
		} else {
			$SnS[ 'scripts' ] = $scripts;
		}
		self::maybe_update( $post_id, '_SnS', $SnS );

		header('Content-Type: application/json; charset=UTF-8');
		echo json_encode( array(
			"scripts" => $_REQUEST[ 'scripts' ]
			, "scripts_in_head" => $_REQUEST[ 'scripts_in_head' ]
		) );

		exit();
	}
	static function styles() {
		check_ajax_referer( Scripts_n_Styles::$file );
		if ( ! current_user_can( 'unfiltered_html' ) || ! current_user_can( 'edit_posts' ) ) exit( 'Insufficient Privileges.' );

		if ( empty( $_REQUEST[ 'post_id' ] ) ) exit( 'Bad post ID.' );
		if ( ! isset( $_REQUEST[ 'styles' ] ) ) exit( 'Data incorrectly sent.' );

		$post_id = absint( $_REQUEST[ 'post_id' ] );
		$SnS = get_post_meta( $post_id, '_SnS', true );
		$styles = isset( $SnS['styles'] ) ? $SnS[ 'styles' ]: array();

		$styles = self::maybe_set( $styles, 'styles' );

		if ( empty( $styles ) ) {
			if ( isset( $SnS['styles'] ) )
				unset( $SnS['styles'] );
		} else {
			$SnS[ 'styles' ] = $styles;
		}
		self::maybe_update( $post_id, '_SnS', $SnS );

		header('Content-Type: application/json; charset=UTF-8');
		echo json_encode( array(
			"styles" => $_REQUEST[ 'styles' ],
		) );

		exit();
	}
	static function dropdown() {
		check_ajax_referer( Scripts_n_Styles::$file );
		if ( ! current_user_can( 'unfiltered_html' ) || ! current_user_can( 'edit_posts' ) ) exit( 'Insufficient Privileges.' );

		if ( empty( $_REQUEST[ 'format' ] ) ) exit( 'Missing Format.' );
		if ( empty( $_REQUEST[ 'format' ][ 'title' ] ) ) exit( 'Title is required.' );
		if ( empty( $_REQUEST[ 'format' ][ 'classes' ] ) ) exit( 'Classes is required.' );
		if (
			empty( $_REQUEST[ 'format' ][ 'inline' ] ) &&
			empty( $_REQUEST[ 'format' ][ 'block' ] ) &&
			empty( $_REQUEST[ 'format' ][ 'selector' ] )
		) exit( 'A type is required.' );

		if ( empty( $_REQUEST[ 'post_id' ] ) ) exit( 'Bad post ID.' );
		$post_id = absint( $_REQUEST[ 'post_id' ] );

		$SnS = get_post_meta( $post_id, '_SnS', true );
		$styles = isset( $SnS['styles'] ) ? $SnS[ 'styles' ]: array();

		if ( ! isset( $styles[ 'classes_mce' ] ) ) $styles[ 'classes_mce' ] = array();

		// pass title as key to be able to delete.
		$styles[ 'classes_mce' ][ $_REQUEST[ 'format' ][ 'title' ] ] = $_REQUEST[ 'format' ];

		$SnS[ 'styles' ] = $styles;
		update_post_meta( $post_id, '_SnS', $SnS );

		header('Content-Type: application/json; charset=UTF-8');
		echo json_encode( array(
			"classes_mce" => array_values( $styles[ 'classes_mce' ] )
		) );

		exit();
	}
	static function delete_class() {
		check_ajax_referer( Scripts_n_Styles::$file );
		if ( ! current_user_can( 'unfiltered_html' ) || ! current_user_can( 'edit_posts' ) ) exit( 'Insufficient Privileges.' );

		if ( empty( $_REQUEST[ 'post_id' ] ) ) exit( 'Bad post ID.' );
		$post_id = absint( $_REQUEST[ 'post_id' ] );
		$SnS = get_post_meta( $post_id, '_SnS', true );
		$styles = isset( $SnS['styles'] ) ? $SnS[ 'styles' ]: array();

		$title = $_REQUEST[ 'delete' ];

		if ( isset( $styles[ 'classes_mce' ][ $title ] ) ) unset( $styles[ 'classes_mce' ][ $title ] );
		else exit ( 'No Format of that name.' );

		if ( empty( $styles[ 'classes_mce' ] ) ) unset( $styles[ 'classes_mce' ] );

		if ( empty( $styles ) ) {
			if ( isset( $SnS['styles'] ) )
				unset( $SnS['styles'] );
		} else {
			$SnS[ 'styles' ] = $styles;
		}
		self::maybe_update( $post_id, '_SnS', $SnS );

		if ( ! isset( $styles[ 'classes_mce' ] ) ) $styles[ 'classes_mce' ] = array( 'Empty' );

		header('Content-Type: application/json; charset=UTF-8');
		echo json_encode( array(
			"classes_mce" => array_values( $styles[ 'classes_mce' ] )
		) );

		exit();
	}
	static function shortcodes() {
		check_ajax_referer( Scripts_n_Styles::$file );
		if ( ! current_user_can( 'unfiltered_html' ) || ! current_user_can( 'edit_posts' ) ) exit( 'Insufficient Privileges.' );

		if ( empty( $_REQUEST[ 'post_id' ] ) ) exit( 'Bad post ID.' );
		if ( empty( $_REQUEST[ 'subaction' ] ) ) exit( 'missing directive' );

		if ( in_array( $_REQUEST[ 'subaction' ], array( 'add', 'update', 'delete' ) ) )
			$subaction = $_REQUEST[ 'subaction' ];
		else
			exit( 'unknown directive' );

		$post_id = absint( $_REQUEST[ 'post_id' ] );
		$SnS = get_post_meta( $post_id, '_SnS', true );
		$shortcodes = isset( $SnS[ 'shortcodes' ] ) ? $SnS[ 'shortcodes' ]: array();
		$message = '';
		$code = 0;
		$key = '';
		$value = '';

		if ( isset( $_REQUEST[ 'name' ] ) )
			$key = $_REQUEST[ 'name' ];
		else
			exit( 'bad directive.' );

		if ( '' == $key ) {
			$key = count( $shortcodes );
			while ( isset( $shortcodes[ $key ] ) )
				$key++;
		}

		switch ( $subaction ) {
			case 'add':
				if ( empty( $_REQUEST[ 'shortcode' ] ) )
					exit( 'empty value.' );
				else
					$value = $_REQUEST[ 'shortcode' ];

				if ( isset( $shortcodes[ $key ] ) ) {
					$countr = 1;
					while ( isset( $shortcodes[ $key . '_' . $countr ] ) )
						$countr++;
					$key .= '_' . $countr;
				}

				$code = 1;
				$shortcodes[ $key ] = $value;
				break;

			case 'update':
				if ( empty( $_REQUEST[ 'shortcode' ] ) ) {
					if ( isset( $shortcodes[ $key ] ) )
						unset( $shortcodes[ $key ] );
					$code = 3;
					$message = $key;
				} else {
					$value = $_REQUEST[ 'shortcode' ];
					if ( isset( $shortcodes[ $key ] ) )
						$shortcodes[ $key ] = $value;
					else
						exit( 'wrong key.' );
					$code = 2;
					$message = 'updated ' . $key;
				}
				break;

			case 'delete':
				if ( isset( $shortcodes[ $key ] ) )
					unset( $shortcodes[ $key ] );
				else
					exit( 'bad key.' );
				$code = 3;
				$message = $key;
				break;
		}

		if ( empty( $shortcodes ) ) {
			if ( isset( $SnS[ 'shortcodes' ] ) )
				unset( $SnS[ 'shortcodes' ] );
		} else {
			$SnS[ 'shortcodes' ] = $shortcodes;
		}
		self::maybe_update( $post_id, '_SnS', $SnS );

		if ( 1 < $code ) {
			header('Content-Type: application/json; charset=UTF-8');
			echo json_encode( array(
				"message"      => $message
				, "code"       => $code
			) );
		} else {
			header('Content-Type: text/html; charset=' . get_option('blog_charset'));
			?><div class="sns-shortcode widget">
	<div class="inside">
		<p>[hoops name="<?php echo esc_attr( $key ) ?>"]</p>
		<textarea style="width: 98%;" cols="40" rows="5" name="SnS_shortcodes[existing][<?php echo esc_attr( $key ) ?>]"
			data-sns-shortcode-key="<?php echo esc_attr( $key ) ?>" class="codemirror-new htmlmixed"><?php echo esc_textarea( stripslashes( $value ) ) ?></textarea>
		<div class="sns-ajax-wrap"><a href="#" class="sns-ajax-delete-shortcode button">Delete</a> &nbsp; <a href="#" class="sns-ajax-update-shortcode button">Update</a> <span class="sns-ajax-loading"><span class="spinner" style="display: inline-block;"></span></span></div>
	</div>
</div><?php
		}
		exit();
	}

	// Differs from SnS_Admin_Meta_Box::maybe_set() in that this needs no prefix.
	static function maybe_set( $o, $i ) {
		if ( ! is_array( $o ) ) return array();
		if ( empty( $_REQUEST[ $i ] ) ) {
			if ( isset( $o[ $i ] ) ) unset( $o[ $i ] );
		} else $o[ $i ] = $_REQUEST[ $i ];
		return $o;
	}
	static function maybe_update( $id, $name, $meta ) {
		if ( empty( $meta ) ) {
			delete_post_meta( $id, $name );
		} else {
			update_post_meta( $id, $name, $meta );
		}
	}
}
?>