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
		if ( current_user_can( 'manage_options' ) ) { // if they can't, they won't be able to save anyway.
			$hook_suffix = add_management_page(
					'Scripts n Styles Settings',	// $page_title (string) (required) The text to be displayed in the title tags of the page when the menu is selected
					'Scripts n Styles',	// $menu_title (string) (required) The text to be used for the menu
					'unfiltered_html',	// $capability (string) (required) The capability required for this menu to be displayed to the user.
					SnS_Admin::MENU_SLUG,	// $menu_slug (string) (required) The slug name to refer to this menu by (should be unique for this menu).
					array( __CLASS__, 'options_page' )	// $function (callback) (optional) The function to be called to output the content for this page. 
				);
			Scripts_n_Styles::$hook_suffix = $hook_suffix;
			add_action( "load-$hook_suffix", array( __CLASS__, 'init_options_page' ) );
			add_action( "load-options.php", array( __CLASS__, 'init_options_page' ) );
			
			add_action( "admin_print_styles-$hook_suffix", array( __CLASS__, 'options_styles'));
			add_action( "admin_print_scripts-$hook_suffix", array( __CLASS__, 'options_scripts'));
			
			add_contextual_help( $hook_suffix, self::contextual_help() );
		}
	}
	
    /**
	 * Settings Page help
     */
	function contextual_help() {
		$contextual_help = '<p>In default (non MultiSite) WordPress installs, both <em>Administrators</em> and 
			<em>Editors</em> can access <em>Scripts-n-Styles</em> on individual edit screens. 
			Only <em>Administrators</em> can access this Options Page. In MultiSite WordPress installs, only <em>"Super Admin"</em> users can access either
			<em>Scripts-n-Styles</em> on individual edit screens or this Options Page. If other plugins change capabilities (specifically "unfiltered_html"), 
			other users can be granted access.</p>';
		return $contextual_help;
	}
	
    /**
	 * Settings Page
	 * Adds Admin Menu Item via WordPress' "Administration Menus" API. Also hook actions to register options via WordPress' Settings API.
     */
	static function init_options_page() {
		register_setting(
				self::OPTION_GROUP,	// $option_group (string) (required) A settings group name. Can be anything.
				'SnS_options'	// $option_name (string) (required) The name of an option to sanitize and save.
			);
		register_setting(
				self::OPTION_GROUP, 
				'SnS_enqueue_scripts'
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
		/*add_settings_field(
				'enqueue_scripts',
				'<label for="enqueue_scripts"><strong>Enqueue Scripts</strong>: </label>',
				array( __CLASS__, 'enqueue_scripts_field' ),
				SnS_Admin::MENU_SLUG,
				'global'
			);*/
		add_settings_section(
				'usage',
				'Scripts n Styles Usage',
				array( __CLASS__, 'usage_section' ),
				SnS_Admin::MENU_SLUG
			);
	}
	
    /**
	 * Settings Page
	 * Adds CSS styles to the Scripts n Styles Admin Page.
     */
	static function options_styles() {
		wp_enqueue_style( 'sns-options-styles', plugins_url('css/options-styles.css', Scripts_n_Styles::$file), array( 'codemirror-default' ), SnS_Admin::VERSION );
		wp_enqueue_style( 'codemirror', plugins_url( 'libraries/codemirror/lib/codemirror.css', Scripts_n_Styles::$file), array(), '2.1' );
		wp_enqueue_style( 'codemirror-default', plugins_url( 'libraries/codemirror/theme/default.css', Scripts_n_Styles::$file), array( 'codemirror' ), '2.1' );
	}
	
    /**
	 * Settings Page
	 * Adds JavaScript to the Scripts n Styles Admin Page.
     */
	static function options_scripts() {
		wp_enqueue_script( 'sns-options-scripts', plugins_url('js/options-scripts.js', Scripts_n_Styles::$file), array( 'jquery', 'codemirror-css', 'codemirror-javascript' ), SnS_Admin::VERSION, true );
		wp_enqueue_script( 'codemirror', plugins_url( 'libraries/codemirror/lib/codemirror.js', Scripts_n_Styles::$file), array(), '2.1' );
		wp_enqueue_script( 'codemirror-css', plugins_url( 'libraries/codemirror/mode/css.js', Scripts_n_Styles::$file), array( 'codemirror' ), '2.1' );
		wp_enqueue_script( 'codemirror-javascript', plugins_url( 'libraries/codemirror/mode/javascript.js', Scripts_n_Styles::$file), array( 'codemirror' ), '2.1' );
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
		$options = get_option( 'SnS_options' );
		
		$all_posts = get_posts( array( 'numberposts' => -1, 'post_type' => 'any', 'post_status' => 'any' ) );
		$sns_posts = array();
		foreach( $all_posts as $post) {
			$temp_styles = get_post_meta( $post->ID, 'uFp_styles', true );
			$temp_scripts = get_post_meta( $post->ID, 'uFp_scripts', true );
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
					$temp_styles = get_post_meta( $post->ID, 'uFp_styles', true );
					$temp_scripts = get_post_meta( $post->ID, 'uFp_scripts', true );
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
	
    /**
	 * Settings Page
	 * Outputs a textarea for setting 'scripts'.
     */
	static function scripts_field() {
		$options = get_option( 'SnS_options' );
		?><textarea style="min-width: 500px; width:97%;" class="code js" rows="5" cols="40" name="SnS_options[scripts]" id="scripts"><?php echo isset( $options[ 'scripts' ] ) ? $options[ 'scripts' ] : ''; ?></textarea><br />
		<span class="description" style="max-width: 500px; display: inline-block;">The "Scripts" will be included <strong>verbatim</strong> in <code>&lt;script></code> tags at the bottom of the <code>&lt;body></code> element of your html.</span>
		<?php
	}
	
    /**
	 * Settings Page
	 * Outputs a textarea for setting 'styles'.
     */
	static function styles_field() {
		$options = get_option( 'SnS_options' );
		?><textarea style="min-width: 500px; width:97%;" class="code css" rows="5" cols="40" name="SnS_options[styles]" id="styles"><?php echo isset( $options[ 'styles' ] ) ? $options[ 'styles' ] : ''; ?></textarea><br />
		<span class="description" style="max-width: 500px; display: inline-block;">The "Styles" will be included <strong>verbatim</strong> in <code>&lt;style></code> tags in the <code>&lt;head></code> element of your html.</span><?php
	}
	
    /**
	 * Settings Page
	 * Outputs a textarea for setting 'scripts_in_head'.
     */
	static function scripts_in_head_field() {
		$options = get_option( 'SnS_options' );
		?><textarea style="min-width: 500px; width:97%;" class="code js" rows="5" cols="40" name="SnS_options[scripts_in_head]" id="scripts_in_head"><?php echo isset( $options[ 'scripts_in_head' ] ) ? $options[ 'scripts_in_head' ] : ''; ?></textarea><br />
		<span class="description" style="max-width: 500px; display: inline-block;">The "Scripts (in head)" will be included <strong>verbatim</strong> in <code>&lt;script></code> tags in the <code>&lt;head></code> element of your html.</span>
		<?php
	}
	
    /**
	 * Settings Page
	 * Outputs a select element for selecting options to set $sns_enqueue_scripts.
     */
	static function enqueue_scripts_field() {
		$registered_handles = Scripts_n_Styles::get_wp_registered();
		$sns_enqueue_scripts = get_option( 'SnS_enqueue_scripts' );
		if ( ! is_array( $sns_enqueue_scripts ) ) $sns_enqueue_scripts = array();
		?>
		<select name="SnS_enqueue_scripts[]" id="enqueue_scripts" size="5" multiple="multiple" style="height: auto;">
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
}
?>