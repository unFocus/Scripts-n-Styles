<?php
/**
 * Scripts n Styles
 *
 * @package   Scripts_N_Styles
 * @author    Kenneth Newman <username@example.com>
 * @copyright 2010 - 2018, Kenneth Newman
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link      http://www.unfocus.com/projects/scripts-n-styles/ Plugin URI
 * @link      http://www.unfocus.com/ Author URI
 *
 * @todo Space to add arbitrary html to wp_head and wp_footer.
 * @todo Create ability to add and register scripts and styles for enqueueing (via Options page).
 * @todo Create selection on Option page of which to pick registered scripts to make available on edit screens.
 * @todo Create shortcode registration on Options page to make those snippets available on edit screens.
 * @todo Add Error messaging.
 * @todo Clean up tiny_mce_before_init in SnS_Admin_Meta_Box.
 *
 * @wordpress-plugin
 * Plugin Name: Scripts n Styles
 * Plugin URI: http://www.unfocus.com/projects/scripts-n-styles/
 * Description: Allows WordPress admin users the ability to add custom CSS and JavaScript directly to individual Post, Pages or custom post types.
 * Version: 4.0.0-alpha-3
 * Requires at least: 5.4
 * Requires PHP: 7.2
 * Author: unFocus Projects
 * Author URI: http://www.unfocus.com/
 * License: GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: scripts-n-styles
 */

/*
The Scripts n Styles WordPress Plugin
Copyright (c) 2010-2018  Kenneth Newman  <http://www.unfocus.com/>
Copyright (c) 2012  Kevin Newman  <http://www.unfocus.com/>
Copyright (c) 2012-2013  adcSTUDIO LLC <http://www.adcstudio.com/>

Scripts n Styles is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 3
of the License, or (at your option) any later version.

Scripts n Styles is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.

This file incorporates work covered by other licenses and permissions.
*/

/**
 * Scripts n Styles
 *
 * Allows WordPress admin users the ability to add custom CSS
 * and JavaScript directly to individual Post, Pages or custom
 * post types.
 *
 * NOTE: No user except the "Super Admin" can use this plugin in MultiSite. I'll add
 * features for MultiSite later, perhaps the ones below...
 * The "Super Admin" user has exclusive 'unfiltered_html' capabilities in MultiSite.
 * Also, options.php checks for is_super_admin() so the 'manage_options' capability
 * for blog admins is insufficient to pass the check to manage options directly.
 *
 * The Tentative plan is for Super Admins to create Snippets or Shortcodes approved for use by users with certain capabilities
 * ('unfiltered_html' and/or 'manage_options'). The 'unfiltered_html' capability can be granted via another plugin. This plugin will
 * not deal with granting any capabilities.
 */

if ( version_compare( PHP_VERSION, '7.2', '>=' ) ) :
	/**
	 * This utility function is location specific.
	 * Use in places where __FILE__ would otherwise be used.
	 */
	define( 'SNS_FILE', __FILE__ );

	require_once 'includes/bootstrap.php';

else :
	/**
	 * Disabled message.
	 */
	function sns_disable_message() {
		?>
		<div class="notice notice-success is-dismissible"><p>
		<?php esc_html_e( 'Sorry, Scripts n Styles doesn\'t work with PHP versions below 5.6. Please contact your host.', 'scripts-n-styles' ); ?>
		</p></div>
		<?php
	}
	add_action( 'admin_notices', 'sns_disable_message' );

endif;
