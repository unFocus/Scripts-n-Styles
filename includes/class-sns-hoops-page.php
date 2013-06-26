<?php
/**
 * SnS_Hoops_Page
 *
 * Allows WordPress admin users the ability to add custom CSS
 * and JavaScript directly to individual Post, Pages or custom
 * post types.
 */

class SnS_Hoops_Page
{
	/**
	 * Constants
	 */
	const MENU_SLUG = 'sns_hoops';

	/**
	 * Initializing method.
	 * @static
	 */
	static function init() {
		$hook_suffix = add_submenu_page( SnS_Admin::$parent_slug, __( 'Scripts n Styles', 'scripts-n-styles' ), __( 'Hoops' ), 'unfiltered_html', self::MENU_SLUG, array( 'SnS_Form', 'page' ) );

		add_action( "load-$hook_suffix", array( __CLASS__, 'admin_load' ) );
		add_action( "load-$hook_suffix", array( 'SnS_Admin', 'help' ) );
		add_action( "load-$hook_suffix", array( 'SnS_Form', 'take_action' ), 49 );
		add_action( "admin_print_styles-$hook_suffix", array( __CLASS__, 'admin_enqueue_scripts' ) );

		// Make the page into a tab.
		if ( SnS_Admin::MENU_SLUG != SnS_Admin::$parent_slug ) {
			remove_submenu_page( SnS_Admin::$parent_slug, self::MENU_SLUG );
			add_filter( 'parent_file', array( __CLASS__, 'parent_file') );
		}
	}
	static function parent_file( $parent_file ) {
		global $plugin_page, $submenu_file;
		if ( self::MENU_SLUG == $plugin_page ) $submenu_file = SnS_Admin::MENU_SLUG;
		return $parent_file;
	}

	static function admin_enqueue_scripts() {
		$options = get_option( 'SnS_options' );
		$cm_theme = isset( $options[ 'cm_theme' ] ) ? $options[ 'cm_theme' ] : 'default';

		wp_enqueue_style( 'sns-options' );
		wp_enqueue_style( 'codemirror-theme' );

		wp_enqueue_script(  'sns-hoops-page' );
		wp_localize_script( 'sns-hoops-page', '_SnS_options', array( 'theme' => $cm_theme ) );
	}
	/**
	 * Settings Page
	 * Adds Admin Menu Item via WordPress' "Administration Menus" API. Also hook actions to register options via WordPress' Settings API.
	 */
	static function admin_load() {
		// added here to not effect other pages.
		add_filter( 'sns_options_pre_update_option', array( __CLASS__, 'new_hoops') );

		register_setting(
			SnS_Admin::OPTION_GROUP,
			'SnS_options' );

		add_settings_section(
			'hoops_section',
			__( 'The Hoops Shortcodes', 'scripts-n-styles' ),
			array( __CLASS__, 'hoops_section' ),
			SnS_Admin::MENU_SLUG );
	}
	static function new_hoops( $options ) {
		// Get Hoops. (Shouldn't be empty.)
		$hoops = $options[ 'hoops' ];

		/*
		add_settings_error( 'sns_hoops', 'settings_updated', '<pre>'
			. '$hoops '
			. print_r(
			$hoops, true ) . '</pre>', 'updated' );
		*/

		// take out new. (Also shouldn't be empty.)
		$new = $hoops[ 'new' ];
		unset( $hoops[ 'new' ] );

		// Get Shortcodes. (Could be empty.)
		$shortcodes = empty( $hoops[ 'shortcodes' ] ) ? array() : $hoops[ 'shortcodes' ];

		// prune shortcodes with blank values.
		foreach( $shortcodes as $key => $value ){
			if ( empty( $value ) )
				unset( $shortcodes[ $key ] );
		}

		// Add new (if not empty).
		if ( ! empty( $new[ 'code' ] ) ) {
			$name = empty( $new[ 'name' ] ) ? '' : $new[ 'name' ];

			if ( '' == $name ) {
				// If blank, find next index..
				$name = 0;
				while ( isset( $shortcodes[ $name ] ) )
					$name++;
			} else if ( isset( $shortcodes[ $name ] ) ) {
				// To make sure not to overwrite.
				$countr = 1;
				while ( isset( $shortcodes[ $name . '_' . $countr ] ) )
					$countr++;
				$name .= '_' . $countr;
			}

			// Add new to shortcodes.
			$shortcodes[ $name ] = $new[ 'code' ];
		}

		// Put in Shortcodes... if not empty.
		if ( empty( $shortcodes ) ) {
			if ( isset( $hoops[ 'shortcodes' ] ) )
				unset( $hoops[ 'shortcodes' ] );
		} else {
			$hoops[ 'shortcodes' ] = $shortcodes;
		}

		// Put in Hoops... if not empty.
		if ( empty( $hoops ) ) {
			if ( isset( $options[ 'hoops' ] ) )
				unset( $options[ 'hoops' ] );
		} else {
			$options[ 'hoops' ] = $hoops;
		}

		return $options; // Finish Filter.
	}

	/**
	 * Settings Page
	 * Outputs Description text for the Global Section.
	 */
	static function hoops_section() {
		echo '<div style="max-width: 55em;">';
		_e( '<p>"Hoops" are shortcodes invented to get around some limitations of vanilla WordPress.</p>'
			. '<p> Normally, certain HTML is very problematic to use in the Post Editor, because it either gets '
			. 'jumbled during Switching between HTML and Visual Tabs, stripped out by WPAutoP (rare) or stripped '
			. 'out because the User doesn&#8217;t have the proper Permissions.</p>'
			. '<p>With Hoops, an Admin user (who has `unfiltered_html` and `manage_options` capablilities) can '
			. 'write and approve snippets of HTML for other users to use via Shortcodes.</p>', 'scripts-n-styles' );
		echo '</div>';

		$options = get_option( 'SnS_options' );

		$meta_name  = 'SnS_options[hoops]';
		$hoops      = isset( $options[ 'hoops' ] ) ? $options[ 'hoops' ] : array();
		$shortcodes = isset( $hoops[ 'shortcodes' ] ) ? $hoops[ 'shortcodes' ] : array();
		?>
		<div id="sns-shortcodes">
			<h4>Add New: </h4>
			<div class="sns-less-ide" style="overflow: hidden">
				<div class="widget sns-shortcodes"><div class="inside">
					<label style="display:inline" for="<?php echo $meta_name; ?>">Name: </label>
					<input id="<?php echo $meta_name; ?>" name="<?php echo $meta_name . '[new][name]'; ?>" type="text" />
						<?php /** / ?>
					<a class="button" href="#" id="sns-ajax-add-shortcode">Add New</a>
						<?php /**/ ?>
					<textarea id="<?php echo $meta_name; ?>_new" class="code htmlmixed" name="<?php echo $meta_name . '[new][code]'; ?>" rows="5" cols="40" style="width: 98%;"></textarea>
				</div></div>
			</div>

			<?php if ( ! empty( $shortcodes ) ) { ?>
			<h4>Existing Codes: </h4>
			<div id="sns-shortcodes-wrap">
				<?php if ( ! empty( $shortcodes ) ) { ?>
				<?php foreach ( $shortcodes as $key => $value ) { ?>

				<div class="sns-less-ide" style="overflow: hidden">
					<div class="widget sns-shortcodes"><div class="sns-collapsed inside">
						<span class="sns-collapsed-btn"></span>
						<p style="margin-bottom: 0;">[hoops name="<?php echo $key ?>"]</p>
						<textarea class="code htmlmixed" data-sns-shortcode-key="<?php echo $key ?>" name="<?php echo $meta_name . '[shortcodes][' . $key . ']'; ?>" rows="5" cols="40" style="width: 98%;"><?php echo esc_textarea( $value ); ?></textarea>
						<?php /** / ?>
						<div class="sns-ajax-wrap">
							<a class="sns-ajax-delete-shortcode button" href="#">Delete</a> &nbsp;
							<a class="sns-ajax-update-shortcode button" href="#">Update</a>
							<span class="sns-ajax-loading"><span class="spinner" style="display: inline-block;"></span></span>
						</div>
						<?php /**/ ?>
					</div></div>
				</div>
				<?php } ?>
				<?php } ?>
			</div>
			<?php } ?>
		</div>
		<?php
	}
}
?>