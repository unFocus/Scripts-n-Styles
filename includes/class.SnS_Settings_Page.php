<?php
/**
 * SnS_Settings_Page
 * 
 * Allows WordPress admin users the ability to add custom CSS
 * and JavaScript directly to individual Post, Pages or custom
 * post types.
 */
		
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
	function init() {
		if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'unfiltered_html' ) ) return;
		
		$hook_suffix = add_utility_page(
				'Scripts n Styles Settings',
				'Scripts n Styles',
				'unfiltered_html',
				SnS_Admin::MENU_SLUG,
				array( __CLASS__, 'admin_page' ),
				plugins_url( 'images/menu.png', Scripts_n_Styles::$file)
			);
		add_action( "load-$hook_suffix", array( __CLASS__, 'admin_load' ) );
		add_action( "load-$hook_suffix", array( __CLASS__, 'take_action'), 49 );
		
		add_contextual_help( $hook_suffix, self::contextual_help() );
	}
	
    /**
	 * Settings Page help
     */
	function contextual_help() {
		$contextual_help = '<p>In default (non MultiSite) WordPress installs, both <em>Administrators</em> and 
			<em>Editors</em> can access <em>Scripts-n-Styles</em> on individual edit screens. 
			Only <em>Administrators</em> can access this Options Page. In MultiSite WordPress installs, only 
			<em>"Super Admin"</em> users can access either
			<em>Scripts-n-Styles</em> on individual edit screens or this Options Page. If other plugins change 
			capabilities (specifically "unfiltered_html"), 
			other users can be granted access.</p>';
		return $contextual_help;
	}
	
    /**
	 * Settings Page
	 * Adds Admin Menu Item via WordPress' "Administration Menus" API. Also hook actions to register options via WordPress' Settings API.
     */
	function admin_load() {
		wp_enqueue_style( 'sns-options-styles', plugins_url('css/options-styles.css', Scripts_n_Styles::$file), array( 'codemirror-default' ), SnS_Admin::VERSION );
		wp_enqueue_style( 'codemirror', plugins_url( 'libraries/codemirror/lib/codemirror.css', Scripts_n_Styles::$file), array(), '2.13' );
		wp_enqueue_style( 'codemirror-default', plugins_url( 'libraries/codemirror/theme/default.css', Scripts_n_Styles::$file), array( 'codemirror' ), '2.13' );
		
		wp_enqueue_script( 'sns-options-scripts', plugins_url('js/options-scripts.js', Scripts_n_Styles::$file), array( 'jquery', 'codemirror-css', 'codemirror-javascript' ), SnS_Admin::VERSION, true );
		wp_enqueue_script( 'codemirror', plugins_url( 'libraries/codemirror/lib/codemirror.js', Scripts_n_Styles::$file), array(), '2.13' );
		wp_enqueue_script( 'codemirror-css', plugins_url( 'libraries/codemirror/mode/css.js', Scripts_n_Styles::$file), array( 'codemirror' ), '2.13' );
		wp_enqueue_script( 'codemirror-javascript', plugins_url( 'libraries/codemirror/mode/javascript.js', Scripts_n_Styles::$file), array( 'codemirror' ), '2.13' );
		
		register_setting(
				self::OPTION_GROUP,
				'SnS_options' );
		register_setting(
				self::OPTION_GROUP,
				'sns_enqueue_scripts' );
		
		add_settings_section(
				'global',
				'Global Scripts n Styles',
				array( __CLASS__, 'global_section' ),
				SnS_Admin::MENU_SLUG );
		
		add_settings_field(
				'scripts',
				'<strong>Scripts:</strong> ',
				array( __CLASS__, 'scripts_field' ),
				SnS_Admin::MENU_SLUG,
				'global',
				array(
					'label_for' => 'scripts',
					'setting' => 'SnS_options'
				) );
		add_settings_field(
				'styles',
				'<strong>Styles:</strong> ',
				array( __CLASS__, 'styles_field' ),
				SnS_Admin::MENU_SLUG,
				'global',
				array(
					'label_for' => 'styles',
					'setting' => 'SnS_options'
				) );
		add_settings_field(
				'scripts_in_head',
				'<strong>Scripts</strong><br />(for the <code>head</code> element): ',
				array( __CLASS__, 'scripts_in_head_field' ),
				SnS_Admin::MENU_SLUG,
				'global',
				array(
					'label_for' => 'scripts_in_head',
					'setting' => 'SnS_options'
				) );
		add_settings_field(
				'enqueue_scripts',
				'<strong>Enqueue Scripts</strong>: ',
				array( __CLASS__, 'enqueue_scripts_field' ),
				SnS_Admin::MENU_SLUG,
				'global',
				array(
					'label_for' => 'enqueue_scripts',
					'setting' => 'sns_enqueue_scripts'
				) );
		
		add_settings_section(
				'usage',
				'Scripts n Styles Usage',
				array( __CLASS__, 'usage_section' ),
				SnS_Admin::MENU_SLUG );
	}
	
    /**
	 * Settings Page
	 * Outputs Description text for the Global Section.
	 */
	function global_section() {
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
	function usage_section() {
		$script_posts = get_posts( array(
			'numberposts' => -1,
			'post_type' => 'any',
			'post_status' => 'any',
    		'orderby' => 'ID',
			'meta_query' => array( array( 'key' => '_SnS_scripts' ) )
		) );
		
		$exclude = array();
		foreach ( $script_posts as $post ) {$exclude[] =  $post->ID;}
		$exclude = implode( ', ', $exclude );
		
		$style_posts = get_posts( array(
			'numberposts' => -1,
			'exclude' => $exclude,
			'post_type' => 'any',
			'post_status' => 'any',
    		'orderby' => 'ID',
			'meta_query' => array( array( 'key' => '_SnS_styles' ) )
		) );
		
		$all_posts = array_merge( $style_posts, $script_posts );
		$sns_posts = array();
		foreach( $all_posts as $post) {
			$styles = get_post_meta( $post->ID, '_SnS_styles', true );
			$scripts = get_post_meta( $post->ID, '_SnS_scripts', true );
			if ( ! empty( $styles ) || ! empty( $scripts ) ) {
				$post->sns_styles = $styles;
				$post->sns_scripts = $scripts;
				$sns_posts[] = $post;
			}
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
						<th>Script Data</th>
						<th>Style Data</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>Title</th>
						<th>ID</th>
						<th>Status</th>
						<th>Post Type</th>
						<th>Script Data</th>
						<th>Style Data</th>
					</tr>
				</tfoot>
				<tbody>
				<?php foreach( $sns_posts as $post) { ?>
					<tr>
						<td>
							<strong><a class="row-title" title="Edit &#8220;<?php echo esc_attr( $post->post_title ); ?>&#8221;" href="<?php echo esc_url( get_edit_post_link( $post->ID ) ); ?>"><?php echo $post->post_title; ?></a></strong>
							<div class="row-actions"><span class="edit"><a title="Edit this item" href="<?php echo esc_url( get_edit_post_link( $post->ID ) ); ?>">Edit</a></span></div>
						</td>
						<td><?php echo $post->ID; ?></td>
						<td><?php echo $post->post_status; ?></td>
						<td><?php echo $post->post_type; ?></td>
						<td><?php 
							if ( isset( $post->sns_scripts[ 'scripts_in_head' ] ) ) { ?>
								<div>Scripts (head)</div>
							<?php }
							if ( isset( $post->sns_scripts[ 'scripts' ] ) ) { ?>
								<div>Scripts</div>
							<?php }
							if ( isset( $post->sns_scripts[ 'enqueue_scripts' ] ) ) { ?>
								<div>Enqueued Scripts</div>
							<?php }
						 ?></td>
						<td><?php
							if ( isset( $post->sns_styles[ 'classes_mce' ] ) ) { ?>
								<div>TinyMCE Formats</div>
							<?php }
							if ( isset( $post->sns_styles[ 'styles' ] ) ) { ?>
								<div>Styles</div>
							<?php }
							if ( isset( $post->sns_styles[ 'classes_post' ] ) ) { ?>
								<div>Post Classes</div>
							<?php }
							if ( isset( $post->sns_styles[ 'classes_body' ] ) ) { ?>
								<div>Body Classes</div>
							<?php }
						?></td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
			<?php
		} else {
			?>
			<div style="max-width: 55em;">
				<p>No items are currently using Scripts-n-Styles.</p>
			</div>
			<?php
		}
	}
	
    /**
	 * Settings Page
	 * Outputs a textarea for setting 'scripts'.
     */
	function scripts_field( $args ) {
		$options = get_option( 'SnS_options' );
		?><textarea style="min-width: 500px; width:97%;" class="code js" rows="5" cols="40" name="SnS_options[scripts]" id="scripts"><?php echo isset( $options[ 'scripts' ] ) ? $options[ 'scripts' ] : ''; ?></textarea>
		<span class="description" style="max-width: 500px; display: inline-block;">The "Scripts" will be included <strong>verbatim</strong> in <code>&lt;script></code> tags at the bottom of the <code>&lt;body></code> element of your html.</span>
		<?php
	}
	
    /**
	 * Settings Page
	 * Outputs a textarea for setting 'styles'.
     */
	function styles_field( $args ) {
		$options = get_option( 'SnS_options' );
		?><textarea style="min-width: 500px; width:97%;" class="code css" rows="5" cols="40" name="SnS_options[styles]" id="styles"><?php echo isset( $options[ 'styles' ] ) ? $options[ 'styles' ] : ''; ?></textarea>
		<span class="description" style="max-width: 500px; display: inline-block;">The "Styles" will be included <strong>verbatim</strong> in <code>&lt;style></code> tags in the <code>&lt;head></code> element of your html.</span><?php
	}
	
    /**
	 * Settings Page
	 * Outputs a textarea for setting 'scripts_in_head'.
     */
	function scripts_in_head_field( $args ) {
		$options = get_option( 'SnS_options' );
		?><textarea style="min-width: 500px; width:97%;" class="code js" rows="5" cols="40" name="SnS_options[scripts_in_head]" id="scripts_in_head"><?php echo isset( $options[ 'scripts_in_head' ] ) ? $options[ 'scripts_in_head' ] : ''; ?></textarea>
		<span class="description" style="max-width: 500px; display: inline-block;">The "Scripts (in head)" will be included <strong>verbatim</strong> in <code>&lt;script></code> tags in the <code>&lt;head></code> element of your html.</span>
		<?php
	}
	
    /**
	 * Settings Page
	 * Outputs a select element for selecting options to set $sns_enqueue_scripts.
     */
	function enqueue_scripts_field( $args ) {
		$registered_handles = Scripts_n_Styles::get_wp_registered();
		$sns_enqueue_scripts = get_option( 'sns_enqueue_scripts' );
		if ( ! is_array( $sns_enqueue_scripts ) ) $sns_enqueue_scripts = array();
		?>
		<select name="sns_enqueue_scripts[]" id="enqueue_scripts" size="5" multiple="multiple" style="height: auto;">
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
	function take_action() {
		global $action, $option_page, $page;
		
		if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'unfiltered_html' ) || ( is_multisite() && ! is_super_admin() ) )
			wp_die( __( 'Cheatin&#8217; uh?' ) );
		
		if ( empty( $_POST ) || ! isset( $_POST[ 'action' ] ) || ! isset( $_POST[ 'option_page' ] ) || ! isset( $_GET[ 'page' ] ) ) return;
		
		wp_reset_vars( array( 'action', 'option_page', 'page' ) );
		
		check_admin_referer(  $option_page  . '-options' );
		
		self::save( $option_page, $page, $action );
		
		return;
	}
	
	function save( $option_page, $page, $action ) {
		global $new_whitelist_options;
		
		if ( ! isset( $new_whitelist_options ) || ! isset( $new_whitelist_options[ $option_page ] ) )
			return;
		
		$options = $new_whitelist_options[ $option_page ];
		
		foreach ( (array) $options as $option ) {
			$option = trim($option);
			$value = null;
			if ( isset($_POST[$option]) )
				$value = $_POST[$option];
			if ( !is_array($value) )
				$value = trim($value);
			$value = stripslashes_deep($value);
			update_option($option, $value);
		}
		
		if ( ! count( get_settings_errors() ) )
			add_settings_error( $page, 'settings_updated', __( 'Settings saved.' ), 'updated' );
	}

    /**
	 * Settings Page
	 * Outputs the Admin Page and calls the Settings registered with the Settings API in init_options_page().
     */
	function admin_page() {
		SnS_Admin::upgrade_check();
		global $title;
		?>
		<div class="wrap">
			<style>#icon-<?php echo esc_html( $_REQUEST[ 'page' ] ); ?> { background: no-repeat center url('<?php echo plugins_url( 'images/icon32.png', Scripts_n_Styles::$file); ?>'); }</style>
			<?php screen_icon(); ?>
			<h2><?php echo esc_html($title); ?></h2>
			<?php settings_errors(); ?>
			<form action="" method="post" autocomplete="off">
			<?php settings_fields( self::OPTION_GROUP ); ?>
			<?php do_settings_sections( SnS_Admin::MENU_SLUG ); ?>
			<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
?>