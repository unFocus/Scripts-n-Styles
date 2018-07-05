<?php
/**
 * Form Helpers
 *
 * Various functions for building settings pages.
 *
 * @package Scripts-N-Styles
 */

namespace unFocus\SnS;

/**
 * Outputs a textarea for setting 'scripts_in_head'.
 *
 * @param array $args A set of args.
 */
function textarea( $args ) {

	$setting   = $args['setting'];
	$label_for = $args['label_for'];
	$options   = get_option( $setting );
	$value     = isset( $options[ $label_for ] ) ? $options[ $label_for ] : '';

	$description = isset( $args['description'] ) ? $args['description'] : '';
	$wrap_class  = isset( $args['wrap_class'] ) ? $args['wrap_class'] : '';
	$style       = isset( $args['style'] ) ? $args['style'] : '';
	$class       = isset( $args['class'] ) ? $args['class'] : '';
	$rows        = isset( $args['rows'] ) ? $args['rows'] : '';
	$cols        = isset( $args['cols'] ) ? $args['cols'] : '';

	$textarea = '<textarea name="' . $setting . '[' . $label_for . ']"'
		. ' id="' . $label_for . '"'
		. ' style="' . $style . '"'
		. ' class="' . $class . '"'
		. ' rows="' . $rows . '"'
		. ' cols="' . $cols . '"'
		. '>' . esc_textarea( $value ) . '</textarea>';
	if ( $wrap_class ) {
		$textarea = '<div class="' . $wrap_class . '">' . $textarea . '</div>';
	}
	$textarea .= $description;
	echo wp_kses_post( $textarea );
}

/**
 * Outputs a radio for setting.
 *
 * @param array $args A set of args.
 */
function radio( $args ) {

	$setting   = $args['setting'];
	$label_for = $args['label_for'];
	$options   = get_option( $setting );
	$default   = isset( $args['default'] ) ? $args['default'] : '';
	$value     = isset( $options[ $label_for ] ) ? $options[ $label_for ] : $default;

	$legend      = isset( $args['legend'] ) ? $args['legend'] : '';
	$description = isset( $args['description'] ) ? $args['description'] : '';
	$layout      = isset( $args['layout'] ) && 'horizontal' === $args['layout'];
	$choices     = isset( $args['choices'] ) ? $args['choices'] : [];
	?>
	<fieldset>
	<?php if ( $legend ) { ?>
		<legend class="screen-reader-text"><span><?php echo esc_html( $legend ); ?></span></legend>
	<?php } ?>
	<p>
	<?php foreach ( $choices as $choice ) { ?>
		<label style="white-space: pre;"><input type="radio"
			<?php checked( $value, $choice ); ?>
			value="<?php echo esc_attr( $choice ); ?>"
			name="<?php echo esc_attr( $setting . '[' . $label_for . ']' ); ?>"
			><?php echo esc_html( $choice ); ?></label>
		<?php echo $layout ? ' &nbsp; ' : '<br>'; ?>
	<?php } ?>
	</p></fieldset>
	<?php echo wp_kses_post( $description ); ?>
	<?php
}

/**
 * Outputs a select element for selecting options to set scripts for including.
 *
 * @param array $args A set of args.
 */
function select( $args ) {

	$setting   = $args['setting'];
	$label_for = $args['label_for'];
	$options   = get_option( $setting );
	$selected  = isset( $options[ $label_for ] ) ? $options[ $label_for ] : [];

	$is_multiple  = ! empty( $args['multiple'] );
	$size         = isset( $args['size'] ) ? $args['size'] : 3;
	$style        = isset( $args['style'] ) ? $args['style'] : '';
	$show_current = isset( $args['show_current'] ) ? $args['show_current'] : '';
	$choices      = isset( $args['choices'] ) ? $args['choices'] : '';
	$name         = $setting . '[' . $label_for . ']' . ( $is_multiple ? '[]' : '' );
	?>
	<select id="<?php echo esc_attr( $label_for ); ?>"
		name="<?php echo esc_attr( $name ); ?>"
		<?php echo $is_multiple ? 'multiple="multiple"' : ''; ?>
		size="<?php echo (int) $size; ?>"
		style="<?php echo esc_attr( $style ); ?>">
	<?php foreach ( $choices as $choice ) { ?>
		<option value="<?php echo esc_attr( $choice ); ?>"
		<?php
		if ( $is_multiple ) {
			foreach ( $selected as $handle ) {
				selected( $handle, $choice );
			}
		} else {
			selected( $selected, $choice );
		}
		?>
		><?php echo esc_html( $choice ); ?></option>
	<?php } ?>
	</select>
	<?php
	if ( $show_current && ! empty( $selected ) ) {
		?>
		<p><?php echo wp_kses_post( $show_current ); ?>
		<?php foreach ( $selected as $handle ) { ?>
			<code><?php echo esc_html( $handle ); ?></code>
		<?php } ?>
		</p>
		<?php
	}
}

/**
 * Outputs the Admin Page and calls the Settings registered with the Settings API.
 */
function take_action() {
	global $action, $option_page, $page, $new_whitelist_options;

	if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'unfiltered_html' ) || ( is_multisite() && ! is_super_admin() ) ) {
		wp_die(
			'<h1>' . esc_html__( 'Cheatin&#8217; uh?', 'scripts-n-styles' ) . '</h1>' .
			'<p>' . esc_html__( 'Sorry, you are not allowed to manage these options.', 'scripts-n-styles' ) . '</p>',
			403
		);
	}

	// Handle menu-redirected update message.
	if ( ! empty( $_REQUEST['message'] ) ) { // Input var okay.
		add_settings_error( $page, 'settings_updated', esc_html__( 'Settings saved.', 'scripts-n-styles' ), 'updated' );
	}

	if ( ! isset( $_REQUEST['action'], $_REQUEST['option_page'], $_REQUEST['page'] ) ) { // Input var okay.
		return;
	}

	wp_reset_vars( [ 'action', 'option_page', 'page' ] );

	check_admin_referer( $option_page . '-options' );

	if ( ! isset( $new_whitelist_options[ $option_page ] ) ) {
		return;
	}

	$options = $new_whitelist_options[ $option_page ];
	foreach ( (array) $options as $option ) {
		$old    = get_option( $option );
		$option = trim( $option );
		$new    = null;
		if ( isset( $_POST[ $option ] ) ) {
			$new = wp_unslash( $_POST[ $option ] );
		}
		if ( ! is_array( $new ) ) {
			$new = trim( $new );
		}
		$new   = stripslashes_deep( $new );
		$value = array_merge( $old, $new );

		// Allow modification of $value.
		$value = apply_filters( 'sns_options_pre_update_option', $value, $page, $action, $new, $old );

		update_option( $option, $value );
	}

	if ( ! count( get_settings_errors() ) ) {
		add_settings_error( $page, 'settings_updated', __( 'Settings saved.', 'scripts-n-styles' ), 'updated' );
	}

	if ( ! empty( $_REQUEST['ajaxsubmit'] ) ) {
		ob_start();
		settings_errors( $page );
		$output = ob_get_contents();
		ob_end_clean();
		exit( wp_kses_post( $output ) );
	}
}

/**
 * Outputs the Admin Page and calls the Settings registered with the Settings API in init_options_page().
 */
function page() {
	?>
	<div class="wrap">
		<?php nav(); ?>
		<form action="" method="post" autocomplete="off">
		<?php settings_fields( OPTION_GROUP ); ?>
		<?php do_settings_sections( ADMIN_MENU_SLUG ); ?>
		<?php
		if ( apply_filters( 'sns_show_submit_button', true ) ) {
			submit_button();
		}
		?>
		</form>
	</div>
	<?php
}

/**
 * Nav Tabs
 */
function nav() {
	$options = get_option( 'SnS_options' );
	$page    = ! empty( $_REQUEST['page'] ) ? sanitize_textarea_field( wp_unslash( $_REQUEST['page'] ) ) : '';
	?>
	<h2><?php esc_html_e( 'Scripts n Styles', 'scripts-n-styles' ); ?></h2>
	<?php settings_errors(); ?>
	<h3 class="nav-tab-wrapper">
		<a class="nav-tab<?php echo ( ADMIN_MENU_SLUG === $page ) ? ' nav-tab-active' : ''; ?>" href="<?php menu_page_url( ADMIN_MENU_SLUG ); ?>"><?php esc_html_e( 'Global', 'scripts-n-styles' ); ?></a>
		<a class="nav-tab<?php echo ( ADMIN_MENU_SLUG . '_hoops' === $page ) ? ' nav-tab-active' : ''; ?>" href="<?php menu_page_url( ADMIN_MENU_SLUG . '_hoops' ); ?>"><?php esc_html_e( 'Hoops', 'scripts-n-styles' ); ?></a>
		<?php if ( current_theme_supports( 'scripts-n-styles' ) ) { ?>
		<a class="nav-tab<?php echo ( ADMIN_MENU_SLUG . '_theme' === $page ) ? ' nav-tab-active' : ''; ?>" href="<?php menu_page_url( ADMIN_MENU_SLUG . '_theme' ); ?>"><?php esc_html_e( 'Theme', 'scripts-n-styles' ); ?></a>
		<?php } ?>
		<a class="nav-tab<?php echo ( ADMIN_MENU_SLUG . '_settings' === $page ) ? ' nav-tab-active' : ''; ?>" href="<?php menu_page_url( ADMIN_MENU_SLUG . '_settings' ); ?>"><?php esc_html_e( 'Settings', 'scripts-n-styles' ); ?></a>
		<a class="nav-tab<?php echo ( ADMIN_MENU_SLUG . '_usage' === $page ) ? ' nav-tab-active' : ''; ?>" href="<?php menu_page_url( ADMIN_MENU_SLUG . '_usage' ); ?>"><?php esc_html_e( 'Usage', 'scripts-n-styles' ); ?></a>
	</h3>
	<?php
}
