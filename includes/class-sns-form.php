<?php
namespace unFocus\SnS;

/**
 * Settings_Page
 *
 * Allows WordPress admin users the ability to add custom CSS
 * and JavaScript directly to individual Post, Pages or custom
 * post types.
 */

class Form
{
	/**
	 * Settings Page
	 * Outputs a textarea for setting 'scripts_in_head'.
	 */
	static function textarea( $args ) {
		extract( $args );
		$options = get_option( $setting );
		$value =  isset( $options[ $label_for ] ) ? $options[ $label_for ] : '';
		$output = '';
		if ( isset( $wrap_class ) ) $output .= '<div class="'. $wrap_class . '">';
		$output .= '<textarea';
		$output .= ( $style ) ? ' style="' . $style . '"': '';
		$output .= ( $class ) ? ' class="' . $class . '"': '';
		$output .= ( $rows ) ? ' rows="' . $rows . '"': '';
		$output .= ( $cols ) ? ' cols="' . $cols . '"': '';
		$output .= ' name="' . $setting . '[' . $label_for . ']"';
		$output .= ' id="' . $label_for . '">';
		$output .= esc_textarea( $value ) . '</textarea>';
		if ( isset( $wrap_class ) ) $output .= '</div>';
		if ( $description ) {
			$output .= $description;
		}
		echo $output;
	}

	static function radio( $args ) {
		extract( $args );
		$options = get_option( $setting );
		$default =  isset( $default ) ? $default : '';
		$value =  isset( $options[ $label_for ] ) ? $options[ $label_for ] : $default;
		$output = '<fieldset>';
		if ( $legend ) {
			$output .= '<legend class="screen-reader-text"><span>';
			$output .= $legend;
			$output .= '</span></legend>';
		}
		$output .= '<p>';
		foreach ( $choices as $choice ) {
			$output .= '<label style="white-space: pre;">';
			$output .= '<input type="radio"';
			$output .= checked( $value, $choice, false );
			$output .= ' value="' . $choice . '" name="' . $setting . '[' . $label_for . ']"> ' . $choice;
			$output .= '</label>';
			$output .= ( ! isset( $layout ) || 'horizontal' != $layout ) ? '<br>' : ' &nbsp; ';
		}
		$output .= '</p></fieldset>';
		if ( $description ) {
			$output .= $description;
		}
		echo $output;
	}

	/**
	 * Settings Page
	 * Outputs a select element for selecting options to set scripts for including.
	 */
	static function select( $args ) {
		extract( $args );
		$options = get_option( $setting );
		$selected = isset( $options[ $label_for ] ) ? $options[ $label_for ] : array();

		$output = '<select';
		$output .= ' id="' . $label_for . '"';
		$output .= ' name="' . $setting . '[' . $label_for . ']';
		if ( isset( $multiple ) && $multiple )
			$output .= '[]" multiple="multiple"';
		else
			$output .= '"';
		$output .= ( $size ) ? ' size="' . $size . '"': '';
		$output .= ( $style ) ? ' style="' . $style . '"': '';
		$output .= '>';
		foreach ( $choices as $choice ) {
			$output .= '<option value="' . $choice . '"';
			if ( isset( $multiple ) && $multiple )
				foreach ( $selected as $handle ) $output .= selected( $handle, $choice, false );
			else
				$output .= selected( $selected, $choice, false );
			$output .= '>' . $choice . '</option> ';
		}
		$output .= '</select>';
		if ( ! empty( $show_current ) && ! empty( $selected ) ) {
			$output .= '<p>' . $show_current;
			foreach ( $selected as $handle ) $output .= '<code>' . $handle . '</code> ';
			$output .= '</p>';
		}
		echo $output;
	}

	/**
	 * Settings Page
	 * Outputs the Admin Page and calls the Settings registered with the Settings API.
	 */
	static function take_action() {
		global $action, $option_page, $page, $new_whitelist_options;

		if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'unfiltered_html' ) || ( is_multisite() && ! is_super_admin() ) )
			wp_die( __( 'Cheatin&#8217; uh?' ) );

		// Handle menu-redirected update message.
		if ( isset( $_REQUEST[ 'message' ] ) && $_REQUEST[ 'message' ] )
			add_settings_error( $page, 'settings_updated', __( 'Settings saved.' ), 'updated' );

		if ( ! isset( $_REQUEST[ 'action' ], $_REQUEST[ 'option_page' ], $_REQUEST[ 'page' ] ) )
			return;

		wp_reset_vars( array( 'action', 'option_page', 'page' ) );

		check_admin_referer(  $option_page  . '-options' );

		if ( ! isset( $new_whitelist_options[ $option_page ] ) )
			return;

		$options = $new_whitelist_options[ $option_page ];
		foreach ( (array) $options as $option ) {
			$old = get_option( $option );
			$option = trim( $option );
			$new = null;
			if ( isset($_POST[ $option ]) )
				$new = $_POST[ $option ];
			if ( !is_array( $new ) )
				$new = trim( $new );
			$new = stripslashes_deep( $new );
			$value = array_merge( $old, $new );

			// Allow modification of $value
			$value = apply_filters( 'sns_options_pre_update_option', $value, $page, $action, $new, $old );

			update_option( $option, $value );
		}

		if ( ! count( get_settings_errors() ) )
			add_settings_error( $page, 'settings_updated', __( 'Settings saved.' ), 'updated' );

		if ( isset( $_REQUEST[ 'ajaxsubmit' ] ) && $_REQUEST[ 'ajaxsubmit' ] ) {
			ob_start();
			settings_errors( $page );
			$output = ob_get_contents();
			ob_end_clean();
			exit( $output );
		}

		return;
	}

	/**
	 * Settings Page
	 * Outputs the Admin Page and calls the Settings registered with the Settings API in init_options_page().
	 */
	static function page() {
		?>
		<div class="wrap">
			<?php nav(); ?>
			<form action="" method="post" autocomplete="off">
			<?php settings_fields( OPTION_GROUP ); ?>
			<?php do_settings_sections( ADMIN_MENU_SLUG ); ?>
			<?php if ( apply_filters( 'sns_show_submit_button', true ) ) submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
/**
 * Nav Tabs
 */
function nav() {
	$options = get_option( 'SnS_options' );
	$page = $_REQUEST[ 'page' ];
	?>
	<?php screen_icon(); ?>
	<h2>Scripts n Styles</h2>
	<?php settings_errors(); ?>
	<?php screen_icon( 'none' ); ?>
	<h3 class="nav-tab-wrapper">
		<a class="nav-tab<?php echo ( ADMIN_MENU_SLUG == $page )               ? ' nav-tab-active': ''; ?>" href="<?php menu_page_url( ADMIN_MENU_SLUG );               ?>"><?php _e( 'Global',   'scripts-n-styles' ); ?></a>
		<a class="nav-tab<?php echo ( ADMIN_MENU_SLUG . '_hoops' == $page )    ? ' nav-tab-active': ''; ?>" href="<?php menu_page_url( ADMIN_MENU_SLUG . '_hoops' );    ?>"><?php _e( 'Hoops',   'scripts-n-styles' ); ?></a>
		<?php if ( current_theme_supports( 'scripts-n-styles' ) ) { ?>
		<a class="nav-tab<?php echo ( ADMIN_MENU_SLUG . '_theme' == $page )    ? ' nav-tab-active': ''; ?>" href="<?php menu_page_url( ADMIN_MENU_SLUG . '_theme' );    ?>"><?php _e( 'Theme',    'scripts-n-styles' ); ?></a>
		<?php } ?>
		<a class="nav-tab<?php echo ( ADMIN_MENU_SLUG . '_settings' == $page ) ? ' nav-tab-active': ''; ?>" href="<?php menu_page_url( ADMIN_MENU_SLUG . '_settings' ); ?>"><?php _e( 'Settings', 'scripts-n-styles' ); ?></a>
		<a class="nav-tab<?php echo ( ADMIN_MENU_SLUG . '_usage' == $page )    ? ' nav-tab-active': ''; ?>" href="<?php menu_page_url( ADMIN_MENU_SLUG . '_usage' );    ?>"><?php _e( 'Usage',    'scripts-n-styles' ); ?></a>
	</h3>
	<?php
}