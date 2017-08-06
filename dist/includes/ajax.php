<?php
namespace unFocus\SnS;

add_action( 'wp_ajax_sns_open_theme_panels', function () {
	check_ajax_referer( OPTION_GROUP . "-options" );

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
} );

// Keep track of current tab.
add_action( 'wp_ajax_sns_update_tab', function () {
	check_ajax_referer( BASENAME );

	$active_tab = isset( $_POST[ 'active_tab' ] ) ? 's'.$_POST[ 'active_tab' ] : 's0';

	if ( ! $user = wp_get_current_user() ) exit( 'Bad User' );

	$success = update_user_option( $user->ID, 'current_sns_tab', $active_tab, true);
	exit();
} );

// TinyMCE requests a css file.
add_action( 'wp_ajax_sns_tinymce_styles', function () {
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
} );

// Ajax Saves.
add_action( 'wp_ajax_sns_classes', function () {
	check_ajax_referer( BASENAME );
	if ( ! current_user_can( 'unfiltered_html' ) || ! current_user_can( 'edit_posts' ) ) exit( 'Insufficient Privileges.' );

	if ( empty( $_REQUEST[ 'post_id' ] ) ) exit( 'Bad post ID.' );
	if ( ! isset( $_REQUEST[ 'classes_body' ], $_REQUEST[ 'classes_post' ] ) ) exit( 'Data missing.' );

	$post_id = absint( $_REQUEST[ 'post_id' ] );
	$SnS = get_post_meta( $post_id, '_SnS', true );
	$SnS = is_array( $SnS ) ? $SnS: array(); // Something changed in PHP 7/WP 4.8
	$styles = isset( $SnS['styles'] ) ? $SnS[ 'styles' ]: array();

	$styles = maybe_set( $styles, 'classes_body' );
	$styles = maybe_set( $styles, 'classes_post' );

	if ( empty( $styles ) ) {
		if ( isset( $SnS['styles'] ) )
			unset( $SnS['styles'] );
	} else {
		$SnS[ 'styles' ] = $styles;
	}
	maybe_update( $post_id, '_SnS', $SnS );

	header('Content-Type: application/json; charset=UTF-8');
	echo json_encode( array(
		"classes_post" => $_REQUEST[ 'classes_post' ]
		, "classes_body" => $_REQUEST[ 'classes_body' ]
	) );

	exit();
} );

add_action( 'wp_ajax_sns_scripts', function () {
	check_ajax_referer( BASENAME );
	if ( ! current_user_can( 'unfiltered_html' ) || ! current_user_can( 'edit_posts' ) ) exit( 'Insufficient Privileges.' );

	if ( empty( $_REQUEST[ 'post_id' ] ) ) exit( 'Bad post ID.' );
	if ( ! isset( $_REQUEST[ 'scripts' ], $_REQUEST[ 'scripts_in_head' ] ) ) exit( 'Data incorrectly sent.' );

	$post_id = absint( $_REQUEST[ 'post_id' ] );
	$SnS = get_post_meta( $post_id, '_SnS', true );
	$SnS = is_array( $SnS ) ? $SnS: array(); // Something changed in PHP 7/WP 4.8
	$scripts = isset( $SnS['scripts'] ) ? $SnS[ 'scripts' ]: array();

	$scripts = maybe_set( $scripts, 'scripts_in_head' );
	$scripts = maybe_set( $scripts, 'scripts' );

	if ( empty( $scripts ) ) {
		if ( isset( $SnS['scripts'] ) )
			unset( $SnS['scripts'] );
	} else {
		$SnS[ 'scripts' ] = $scripts;
	}
	maybe_update( $post_id, '_SnS', $SnS );

	header('Content-Type: application/json; charset=UTF-8');
	echo json_encode( array(
		"scripts" => $_REQUEST[ 'scripts' ]
		, "scripts_in_head" => $_REQUEST[ 'scripts_in_head' ]
	) );

	exit();
} );

add_action( 'wp_ajax_sns_styles', function () {
	check_ajax_referer( BASENAME );
	if ( ! current_user_can( 'unfiltered_html' ) || ! current_user_can( 'edit_posts' ) ) exit( 'Insufficient Privileges.' );

	if ( empty( $_REQUEST[ 'post_id' ] ) ) exit( 'Bad post ID.' );
	if ( ! isset( $_REQUEST[ 'styles' ] ) ) exit( 'Data incorrectly sent.' );

	$post_id = absint( $_REQUEST[ 'post_id' ] );
	$SnS = get_post_meta( $post_id, '_SnS', true );
	$SnS = is_array( $SnS ) ? $SnS: array(); // Something changed in PHP 7/WP 4.8
	$styles = isset( $SnS['styles'] ) ? $SnS[ 'styles' ]: array();

	$styles = maybe_set( $styles, 'styles' );

	if ( empty( $styles ) ) {
		if ( isset( $SnS['styles'] ) )
			unset( $SnS['styles'] );
	} else {
		$SnS[ 'styles' ] = $styles;
	}
	maybe_update( $post_id, '_SnS', $SnS );

	header('Content-Type: application/json; charset=UTF-8');
	echo json_encode( array(
		"styles" => $_REQUEST[ 'styles' ],
	) );

	exit();
} );

add_action( 'wp_ajax_sns_dropdown', function () {
	check_ajax_referer( BASENAME );
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
	$SnS = is_array( $SnS ) ? $SnS: array(); // Something changed in PHP 7/WP 4.8
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
} );

add_action( 'wp_ajax_sns_delete_class', function () {
	check_ajax_referer( BASENAME );
	if ( ! current_user_can( 'unfiltered_html' ) || ! current_user_can( 'edit_posts' ) ) exit( 'Insufficient Privileges.' );

	if ( empty( $_REQUEST[ 'post_id' ] ) ) exit( 'Bad post ID.' );
	$post_id = absint( $_REQUEST[ 'post_id' ] );
	$SnS = get_post_meta( $post_id, '_SnS', true );
	$SnS = is_array( $SnS ) ? $SnS: array(); // Something changed in PHP 7/WP 4.8
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
	maybe_update( $post_id, '_SnS', $SnS );

	if ( ! isset( $styles[ 'classes_mce' ] ) ) $styles[ 'classes_mce' ] = array( 'Empty' );

	header('Content-Type: application/json; charset=UTF-8');
	echo json_encode( array(
		"classes_mce" => array_values( $styles[ 'classes_mce' ] )
	) );

	exit();
} );

add_action( 'wp_ajax_sns_shortcodes', function () {
	check_ajax_referer( BASENAME );
	if ( ! current_user_can( 'unfiltered_html' ) || ! current_user_can( 'edit_posts' ) ) exit( 'Insufficient Privileges.' );

	if ( empty( $_REQUEST[ 'post_id' ] ) ) exit( 'Bad post ID.' );
	if ( empty( $_REQUEST[ 'subaction' ] ) ) exit( 'missing directive' );

	if ( in_array( $_REQUEST[ 'subaction' ], array( 'add', 'update', 'delete' ) ) )
		$subaction = $_REQUEST[ 'subaction' ];
	else
		exit( 'unknown directive' );

	$post_id = absint( $_REQUEST[ 'post_id' ] );
	$SnS = get_post_meta( $post_id, '_SnS', true );
	$SnS = is_array( $SnS ) ? $SnS: array(); // Something changed in PHP 7/WP 4.8
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
	maybe_update( $post_id, '_SnS', $SnS );

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
} );


// Differs from Admin_Meta_Box::maybe_set() in that this needs no prefix.
function maybe_set( $o, $i ) {
	if ( ! is_array( $o ) ) return array();
	if ( empty( $_REQUEST[ $i ] ) ) {
		if ( isset( $o[ $i ] ) ) unset( $o[ $i ] );
	} else $o[ $i ] = $_REQUEST[ $i ];
	return $o;
}
function maybe_update( $id, $name, $meta ) {
	if ( empty( $meta ) ) {
		delete_post_meta( $id, $name );
	} else {
		update_post_meta( $id, $name, $meta );
	}
}