<?php
/**
 * SnS_Settings_Page
 * 
 * Allows WordPress admin users the ability to add custom CSS
 * and JavaScript directly to individual Post, Pages or custom
 * post types.
 */

// $hook_suffix = 'tools_page_Scripts-n-Styles'; // kept here for reference
// $plugin_file = 'scripts-n-styles/scripts-n-styles.php'; // kept here for reference
		
class SnS_Settings_Page
{
    /**
     * Constants
     */
	const OPTION_GROUP = 'scripts_n_styles';
	
    /**
	 * Initializing method.
     * @static
     */
	static function init() {
		/* NOTE: Even when Scripts n Styles is not restricted by 'manage_options', Editors still can't submit the option page */
		if ( self::check_strict_restriction() ) { // if they can't, they won't be able to save anyway.
			$hook_suffix = add_management_page(
					'Scripts n Styles Settings',	// $page_title (string) (required) The text to be displayed in the title tags of the page when the menu is selected
					'Scripts n Styles',	// $menu_title (string) (required) The text to be used for the menu
					'unfiltered_html',	// $capability (string) (required) The capability required for this menu to be displayed to the user.
					SnS_Admin::MENU_SLUG,	// $menu_slug (string) (required) The slug name to refer to this menu by (should be unique for this menu).
					array( __CLASS__, 'options_page' )	// $function (callback) (optional) The function to be called to output the content for this page. 
				);
			
			add_action( "load-$hook_suffix", array( __CLASS__, 'init_options_page' ) );
			add_action( "load-options.php", array( __CLASS__, 'init_options_page' ) );
			
			add_action( "admin_print_styles-$hook_suffix", array( __CLASS__, 'options_styles'));
			add_action( "admin_print_scripts-$hook_suffix", array( __CLASS__, 'options_scripts'));
		}
	}
	
    /**
	 * Settings Page
	 * Adds Admin Menu Item via WordPress' "Administration Menus" API. Also hook actions to register options via WordPress' Settings API.
     */
	static function init_options_page() {
		register_setting(
				self::OPTION_GROUP,	// $option_group (string) (required) A settings group name. Can be anything.
				Scripts_n_Styles::OPTION_PREFIX.'options',	// $option_name (string) (required) The name of an option to sanitize and save.
				array( __CLASS__, 'options_validate' )	// $sanitize_callback (string) (optional) A callback function that sanitizes the option's value.
			);
		register_setting(
				self::OPTION_GROUP, 
				Scripts_n_Styles::OPTION_PREFIX.'enqueue_scripts', 
				array( __CLASS__, 'enqueue_validate' )
			);
		add_settings_section(
				'general',	// $id (string) (required) String for use in the 'id' attribute of tags.
				'General Settings',	// $title (string) (required) Title of the section. 
				array( __CLASS__, 'general_section' ),	// $callback (string) (required) Function that fills the section with the desired content. The function should echo its output.
				SnS_Admin::MENU_SLUG	// $page (string) (required) The type of settings page on which to show the section (general, reading, writing, media etc.)
			);
		add_settings_field(
				'restrict', 
				'<label><strong>Restriction:</strong> </label>',
				array( __CLASS__, 'restrict_field' ),
				SnS_Admin::MENU_SLUG,
				'general'
			);
		add_settings_section(
				'global',
				'Global Scripts n Styles',
				array( __CLASS__, 'global_section' ),
				SnS_Admin::MENU_SLUG
			);
		add_settings_field(
				'scripts', 
				'<label for="scripts"><strong>Scripts:</strong> </label>',
				array( __CLASS__, 'scripts_field' ),
				SnS_Admin::MENU_SLUG,
				'global'
			);
		add_settings_field(
				'styles',
				'<label for="styles"><strong>Styles:</strong> </label>',
				array( __CLASS__, 'styles_field' ),
				SnS_Admin::MENU_SLUG,
				'global'
			);
		add_settings_field(
				'scripts_in_head',
				'<label for="scripts_in_head"><strong>Scripts</strong><br />(for the <code>head</code> element): </label>',
				array( __CLASS__, 'scripts_in_head_field' ),
				SnS_Admin::MENU_SLUG,
				'global'
			);
		add_settings_field(
				'enqueue_scripts',
				'<label for="enqueue_scripts"><strong>Enqueue Scripts</strong>: </label>',
				array( __CLASS__, 'enqueue_scripts_field' ),
				SnS_Admin::MENU_SLUG,
				'global'
			);
		add_settings_section(
				'usage',
				'Scripts n Styles Usage',
				array( __CLASS__, 'usage_section' ),
				SnS_Admin::MENU_SLUG
			);
		add_settings_field(
				'show_usage', 
				'<label><strong>Show Usage:</strong> </label>',
				array( __CLASS__, 'show_usage_field' ),
				SnS_Admin::MENU_SLUG,
				'usage'
			);
	}
	
    /**
	 * Settings Page
	 * Adds CSS styles to the Scripts n Styles Admin Page.
     */
	static function options_styles() {
		wp_enqueue_style( 'sns-options-styles', plugins_url('css/options-styles.css', Scripts_n_Styles::$file), array(), SnS_Admin::VERSION );
	}
	
    /**
	 * Settings Page
	 * Adds JavaScript to the Scripts n Styles Admin Page.
     */
	static function options_scripts() {
		wp_enqueue_script( 'sns-options-scripts', plugins_url('js/options-scripts.js', Scripts_n_Styles::$file), array( 'jquery' ), SnS_Admin::VERSION, true );
	}
	
    /**
	 * Settings Page
	 * Outputs Description text for the General Section.
     */
	static function general_section() {
		?>
		<div style="max-width: 55em;">
			<p>Notes about Capabilities: In default (non MultiSite) WordPress installs, Administrators and Editors have the 'unfiltered_html' capability. In MultiSite installs, only the super admin has this capabilty. In both types of install, Admin users have 'manage_options' but in MultiSite, you need to be a Super Admin to access the options.php file.</p>
			<p>The "Restriction" option will require users to have 'manage_options' in addition to 'unfiltered_html' capabilities in order to access Scripts n Styles. When this option is on, Editors will not have access to the Scripts n Styles box on Post and Page edit screens (unless another plugin grants them the 'unfiltered_html' capability). </p>
		</div>
		<?php
	}
	
    /**
	 * Settings Page
	 * Outputs Description text for the Global Section.
	 */
	static function global_section() {
		?>
		<div style="max-width: 55em;">
			<p>Code entered here will be included in <em>every page (and post) of your site</em>, including the homepage and archives. The code will appear <strong>before</strong> Scripts and Styles registered individually.</p>
		</div>
		<?php
	}
	
    /**
	 * Settings Page
	 * Outputs the Usage Section.
     */
	static function usage_section() {
		$options = Scripts_n_Styles::get_options();
		if ( $options['show_usage'] == 'no' ) {
			?>
			<div style="max-width: 55em;">
				<p>This Option, when active, will show a list here of all Content that has Scripts n Styles data.</p>
			</div>
			<?php
		} else {
			$all_posts = get_posts( array( 'numberposts' => -1, 'post_type' => 'any', 'post_status' => 'any' ) );
			$sns_posts = array();
			foreach( $all_posts as $post) {
				$temp_styles = get_post_meta( $post->ID, Scripts_n_Styles::PREFIX.'styles', true );
				$temp_scripts = get_post_meta( $post->ID, Scripts_n_Styles::PREFIX.'scripts', true );
				if ( ! empty( $temp_styles ) || ! empty( $temp_scripts ) )
					$sns_posts[] = $post;
			}
			
			if ( ! empty( $sns_posts ) ) {
				?>
				<table cellspacing="0" class="widefat">
					<thead>
						<tr>
							<th>Title</th>
							<th>ID</th>
							<th>Status</th>
							<th>Post Type</th>
						</tr>
					</thead>
					<tbody>
					<?php foreach( $sns_posts as $post) {
						$temp_styles = get_post_meta( $post->ID, Scripts_n_Styles::PREFIX.'styles', true );
						$temp_scripts = get_post_meta( $post->ID, Scripts_n_Styles::PREFIX.'scripts', true );
						if ( ! empty( $temp_styles ) || ! empty( $temp_scripts ) ) { ?>
							<tr>
								<td>
									<strong><a class="row-title" title="Edit &#8220;<?php echo esc_attr( $post->post_title ); ?>&#8221;" href="<?php echo esc_url( get_edit_post_link( $post->ID ) ); ?>"><?php echo $post->post_title; ?></a></strong>
									<div class="row-actions"><span class="edit"><a title="Edit this item" href="<?php echo esc_url( get_edit_post_link( $post->ID ) ); ?>">Edit</a></span></div>
								</td>
								<td><?php echo $post->ID; ?></td>
								<td><?php echo $post->post_status; ?></td>
								<td><?php echo $post->post_type; ?></td>
							</tr>
						<?php }
					} ?>
					</tbody>
					<tfoot>
						<tr>
							<th>Title</th>
							<th>ID</th>
							<th>Status</th>
							<th>Post Type</th>
						</tr>
					</tfoot>
				</table>
				<?php
			} else {
				?>
				<div style="max-width: 55em;">
					<p>No content items are currently using Scripts-n-Styles data.</p>
				</div>
				<?php
			}
		}
	}
	
    /**
	 * Settings Page
	 * Outputs a Yes/No Radio option group for setting 'restrict'.
     */
	static function restrict_field() {
		$options = Scripts_n_Styles::get_options();
		?><label><strong>Restict access to Scripts n Styles on Edit screens</strong></label><br />
		<fieldset>
			<label>
				<input type="radio" name="<?php echo Scripts_n_Styles::OPTION_PREFIX ?>options[restrict]" value="yes" id="restrict_0" <?php checked( $options['restrict'], 'yes' ); ?>/>
				<span>Yes</span></label>
			<br />
			<label>
				<input type="radio" name="<?php echo Scripts_n_Styles::OPTION_PREFIX ?>options[restrict]" value="no" id="restrict_1" <?php checked( $options['restrict'], 'no' ); ?>/>
				<span>No</span></label>
		</fieldset>
		<span class="description" style="max-width: 500px; display: inline-block;">Apply a 'manage_options' check in addition to the 'unfiltered_html' check.</span><?php
	}
	
    /**
	 * Settings Page
	 * Outputs a textarea for setting 'scripts'.
     */
	static function scripts_field() {
		$options = Scripts_n_Styles::get_options();
		?><textarea style="min-width: 500px; width:97%;" class="code" rows="5" cols="40" name="<?php echo Scripts_n_Styles::OPTION_PREFIX ?>options[scripts]" id="scripts"><?php echo isset( $options[ 'scripts' ] ) ? $options[ 'scripts' ] : ''; ?></textarea><br />
		<span class="description" style="max-width: 500px; display: inline-block;">The "Scripts" will be included <strong>verbatim</strong> in <code>&lt;script></code> tags at the bottom of the <code>&lt;body></code> element of your html.</span>
		<?php
	}
	
    /**
	 * Settings Page
	 * Outputs a textarea for setting 'styles'.
     */
	static function styles_field() {
		$options = Scripts_n_Styles::get_options();
		?><textarea style="min-width: 500px; width:97%;" class="code" rows="5" cols="40" name="<?php echo Scripts_n_Styles::OPTION_PREFIX ?>options[styles]" id="styles"><?php echo isset( $options[ 'styles' ] ) ? $options[ 'styles' ] : ''; ?></textarea><br />
		<span class="description" style="max-width: 500px; display: inline-block;">The "Styles" will be included <strong>verbatim</strong> in <code>&lt;style></code> tags in the <code>&lt;head></code> element of your html.</span><?php
	}
	
    /**
	 * Settings Page
	 * Outputs a textarea for setting 'scripts_in_head'.
     */
	static function scripts_in_head_field() {
		$options = Scripts_n_Styles::get_options();
		?><textarea style="min-width: 500px; width:97%;" class="code" rows="5" cols="40" name="<?php echo Scripts_n_Styles::OPTION_PREFIX ?>options[scripts_in_head]" id="scripts_in_head"><?php echo isset( $options[ 'scripts_in_head' ] ) ? $options[ 'scripts_in_head' ] : ''; ?></textarea><br />
		<span class="description" style="max-width: 500px; display: inline-block;">The "Scripts (in head)" will be included <strong>verbatim</strong> in <code>&lt;script></code> tags in the <code>&lt;head></code> element of your html.</span>
		<?php
	}
	
    /**
	 * Settings Page
	 * Outputs a select element for selecting options to set $sns_enqueue_scripts.
     */
	static function enqueue_scripts_field() {
		$registered_handles = Scripts_n_Styles::get_wp_registered();
		$sns_enqueue_scripts = Scripts_n_Styles::get_enqueue(); ?>
		<select name="<?php echo Scripts_n_Styles::OPTION_PREFIX ?>enqueue_scripts[]" id="enqueue_scripts" size="5" multiple="multiple" style="height: auto;">
			<?php foreach ( $registered_handles as $value ) { ?>
				<option value="<?php echo $value ?>"<?php foreach ( $sns_enqueue_scripts as $handle ) selected( $handle, $value ); ?>><?php echo $value ?></option> 
			<?php } ?>
		</select>
		<?php if ( ! empty( $sns_enqueue_scripts ) && is_array( $sns_enqueue_scripts ) ) { ?>
			<p>Currently Enqueued Scripts: 
			<?php foreach ( $sns_enqueue_scripts as $handle )  echo '<code>' . $handle . '</code> '; ?>
			</p>
		<?php }
	}
	
    /**
	 * Settings Page
	 * Outputs a select element for selecting options to set $sns_enqueue_scripts.
     */
	static function show_usage_field() {
		$options = Scripts_n_Styles::get_options();
		?><label><strong>Show the list</strong></label><br />
		<fieldset>
			<label>
				<input type="radio" name="<?php echo Scripts_n_Styles::OPTION_PREFIX ?>options[show_usage]" value="yes" id="show_usage_0" <?php checked( $options['show_usage'], 'yes' ); ?>/>
				<span>Yes</span></label>
			<br />
			<label>
				<input type="radio" name="<?php echo Scripts_n_Styles::OPTION_PREFIX ?>options[show_usage]" value="no" id="show_usage_1" <?php checked( $options['show_usage'], 'no' ); ?>/>
				<span>No</span></label>
		</fieldset>
		<span class="description" style="max-width: 500px; display: inline-block;">"Yes" will show a list of Content that use Scripts n Styles</span><?php
	}
	
    /**
	 * Settings Page
	 * Outputs the Admin Page and calls the Settings registered with the Settings API in init_options_page().
     */
	static function options_page() {
		SnS_Admin::upgrade_check();
		global $title;
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2><?php echo esc_html($title); ?></h2>
			<form action="options.php" method="post" autocomplete="off">
			<?php settings_fields( self::OPTION_GROUP ); ?>
			<?php do_settings_sections( SnS_Admin::MENU_SLUG ); ?>
			<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
	
    /**
	 * Utility Method: Returns the value of $allow_strict if it is set, and if not, sets it according the current users capabilties, 'manage_options' and 'unfiltered_html'.
	 * @return bool Whether or not current user can set options. 
	 * @uses Scripts_n_Styles::$allow_strict
     */
	static function check_strict_restriction() {
		// ::TODO:: Add MultiSite checks?
		if ( ! isset( Scripts_n_Styles::$allow_strict ) )
			Scripts_n_Styles::$allow_strict = current_user_can( 'manage_options' ) && current_user_can( 'unfiltered_html' );
		return Scripts_n_Styles::$allow_strict;
	}
	
    /**
	 * Settings Page
	 * Filters: the register_setting() return value for the Scripts_n_Styles::OPTION_PREFIX.'options' setting
	 * Checks capabilities 'manage_options' and 'unfiltered_html', returns the updated values for the options if passed, returns original values if not.
	 * This isn't the typical use of this filter since no data validation is needed; 'unfiltered_html' implies a Trusted User.
	 * @param array the submitted array of values.
	 * @return array $value Either the array of new values or the originals if the check failed.
     */
	function options_validate( $value ) {
		// I'm not sure that users without the proper caps can get this far, but if they can...
		if ( self::check_strict_restriction() ) 
			return $value;
		return Scripts_n_Styles::get_options();
	}
	
    /**
	 * Settings Page
	 * Filters: the register_setting() return value for the Scripts_n_Styles::OPTION_PREFIX.'enqueue_scripts' setting
	 * Checks capabilities 'manage_options' and 'unfiltered_html', returns the updated values for the options if passed, returns original values if not.
	 * This isn't the typical use of this filter since no data validation is needed; 'unfiltered_html' implies a Trusted User.
	 * @param array the submitted array of values.
	 * @return array $value Either the array of new values or the originals if the check failed.
     */
	function enqueue_validate( $value ) {
		if ( self::check_strict_restriction() ) 
			return $value;
		return Scripts_n_Styles::get_enqueue();
	}
}
?>