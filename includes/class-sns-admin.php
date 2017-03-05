<?php
namespace unFocus\SnS;

add_action( 'wp_before_admin_bar_render', function() {
	global $wp_admin_bar;
	$wp_admin_bar->add_node( [
		'id'    => 'Scripts_n_Styles',
		'title' => 'Scripts n Styles',
		'href'  => '#',
		'meta'  => array( 'class' => 'Scripts_n_Styles' )
	] );
}, 11 );

/**
 * Scripts n Styles Admin Class
 *
 * Allows WordPress admin users the ability to add custom CSS
 * and JavaScript directly to individual Post, Pages or custom
 * post types.
 */

class Admin
{
	/**#@+
	 * Constants
	 */
	const MENU_SLUG = 'sns';
	static $parent_slug = '';
	/**#@-*/

	/**
	 * Initializing method.
	 * @static
	 */
	static function init() {
		add_action( 'admin_menu', array( '\unFocus\SnS\Admin_Meta_Box', 'init' ) );
		add_action( 'admin_menu', array( '\unFocus\SnS\Admin_Code_Editor', 'init' ) );
		add_action( 'network_admin_menu', array( '\unFocus\SnS\Admin_Code_Editor', 'init' ) );

		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );

		add_action( 'admin_init', array( '\unFocus\SnS\AJAX', 'init' ) );
		add_action( 'admin_init', array( __CLASS__, 'load_plugin_textdomain' ) );

		add_filter( 'plugin_action_links_'.BASENAME, array( __CLASS__, 'plugin_action_links') );

	}

	static function load_plugin_textdomain() {
		load_plugin_textdomain( 'scripts-n-styles', false, dirname( BASENAME ) . '/languages/' );
	}
	static function menu() {
		if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'unfiltered_html' ) ) return;

		$options = get_option( 'SnS_options' );
		$menu_spot = isset( $options[ 'menu_position' ] ) ? $options[ 'menu_position' ]: '';
		$top_spots = array( 'menu', 'object', 'utility' );
		$sub_spots = array( 'tools.php', 'options-general.php', 'themes.php' );

		if ( in_array( $menu_spot, $top_spots ) ) $parent_slug = ADMIN_MENU_SLUG;
		else if ( in_array( $menu_spot, $sub_spots ) ) $parent_slug = $menu_spot;
		else $parent_slug = 'tools.php';

		self::$parent_slug = $parent_slug;

		switch( $menu_spot ) {
			case 'menu':
				add_menu_page( __( 'Scripts n Styles', 'scripts-n-styles' ), __( 'Scripts n Styles', 'scripts-n-styles' ), 'unfiltered_html', $parent_slug, array( '\unFocus\SnS\Form', 'page' ), plugins_url( 'images/menu.png', BASENAME ), 200 );
				break;
			case 'object':
				add_menu_page( __( 'Scripts n Styles', 'scripts-n-styles' ), __( 'Scripts n Styles', 'scripts-n-styles' ), 'unfiltered_html', $parent_slug, array( '\unFocus\SnS\Form', 'page' ), plugins_url( 'images/menu.png', BASENAME ), 50 );
				break;
			case 'utility':
				add_menu_page( __( 'Scripts n Styles', 'scripts-n-styles' ), __( 'Scripts n Styles', 'scripts-n-styles' ), 'unfiltered_html', $parent_slug, array( '\unFocus\SnS\Form', 'page' ), plugins_url( 'images/menu.png', BASENAME ), 98 );
				break;
		}
		Plugin_Editor_Page::init();
		Theme_Editor_Page::init();
		Global_Page::init();
		Hoops_Page::init();
		if ( current_theme_supports( 'scripts-n-styles' ) )
			Theme_Page::init();
		Settings_Page::init();
		Usage_Page::init();
	}

	/**
	 * Nav Tabs
	 */
	static function nav() {
		$options = get_option( 'SnS_options' );
		$page = $_REQUEST[ 'page' ];
		?>
		<?php screen_icon(); ?>
		<h2>Scripts n Styles</h2>
		<?php if ( ! isset( $options[ 'menu_position' ] ) || 'options-general.php' != $options[ 'menu_position' ] ) settings_errors(); ?>
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

	/**
	 * Settings Page help
	 */
	static function help() {
		$help    = '<p>' . __( 'In default (non MultiSite) WordPress installs, both <em>Administrators</em> and <em>Editors</em> can access <em>Scripts-n-Styles</em> on individual edit screens. Only <em>Administrators</em> can access this Options Page. In MultiSite WordPress installs, only <em>"Super Admin"</em> users can access either <em>Scripts-n-Styles</em> on individual edit screens or this Options Page. If other plugins change capabilities (specifically "unfiltered_html"), other users can be granted access.', 'scripts-n-styles' ) . '</p>';
		$help   .= '<p><strong>' . __( 'Reference: jQuery Wrappers', 'scripts-n-styles' ) . '</strong></p>' .
				   '<pre><code>jQuery(document).ready(function($) {
	// $() will work as an alias for jQuery() inside of this function
	});</code></pre>';
		$help   .= '<pre><code>(function($) {
	// $() will work as an alias for jQuery() inside of this function
	})(jQuery);</code></pre>';
		$sidebar = '<p><strong>' . __( 'For more information:', 'scripts-n-styles' ) . '</strong></p>' .
					'<p>' . __( '<a href="http://wordpress.org/extend/plugins/scripts-n-styles/faq/" target="_blank">Frequently Asked Questions</a>', 'scripts-n-styles' ) . '</p>' .
					'<p>' . __( '<a href="https://github.com/unFocus/Scripts-n-Styles" target="_blank">Source on github</a>', 'scripts-n-styles' ) . '</p>' .
					'<p>' . __( '<a href="http://wordpress.org/tags/scripts-n-styles" target="_blank">Support Forums</a>', 'scripts-n-styles' ) . '</p>';
		$screen = get_current_screen();
		if ( method_exists( $screen, 'add_help_tab' ) ) {
			$screen->add_help_tab( array(
				'title' => __( 'Scripts n Styles', 'scripts-n-styles' ),
				'id' => 'scripts-n-styles',
				'content' => $help
				)
			);
			if ( 'post' != $screen->id )
				$screen->set_help_sidebar( $sidebar );
		} else {
			add_contextual_help( $screen, $help . $sidebar );
		}
	}

	/**
	 * Adds link to the Settings Page in the WordPress "Plugin Action Links" array.
	 * @param array $actions
	 * @return array
	 */
	static function plugin_action_links( $actions ) {
		$actions[ 'settings' ] = '<a href="' . menu_page_url( Settings_Page::MENU_SLUG, false ) . '"/>' . __( 'Settings' ) . '</a>';
		return $actions;
	}

}

?>