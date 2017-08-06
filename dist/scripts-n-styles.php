<?php
/*
Plugin Name: Scripts n Styles
Plugin URI: http://www.unfocus.com/projects/scripts-n-styles/
Description: Allows WordPress admin users the ability to add custom CSS and JavaScript directly to individual Post, Pages or custom post types.
Author: unFocus Projects
Author URI: http://www.unfocus.com/
Version: 4.0.0-alpha
License: GPLv3 or later
Text Domain: scripts-n-styles
*/

/*  The Scripts n Styles WordPress Plugin
	Copyright (c) 2010-2017  Kenneth Newman  <http://www.unfocus.com/>
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
 * NOTE: No user except the "Super Admin" can use this plugin in MultiSite. I'll add features for MultiSite later, perhaps the ones below...
 * The "Super Admin" user has exclusive 'unfiltered_html' capabilities in MultiSite. Also, options.php checks for is_super_admin()
 * so the 'manage_options' capability for blog admins is insufficient to pass the check to manage options directly.
 *
 * The Tentative plan is for Super Admins to create Snippets or Shortcodes approved for use by users with certain capabilities
 * ('unfiltered_html' and/or 'manage_options'). The 'unfiltered_html' capability can be granted via another plugin. This plugin will
 * not deal with granting any capabilities.
 *
 * @package Scripts_n_Styles
 * @link http://www.unfocus.com/projects/scripts-n-styles/ Plugin URI
 * @author unFocus Projects
 * @link http://www.unfocus.com/ Author URI
 * @version 4.0.0-alpha
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Copyright (c) 2010 - 2017, Kenneth Newman
 * @copyright Copyright (c) 2012, Kevin Newman
 * @copyright Copyright (c) 2012 - 2013, adcSTUDIO LLC
 *
 * @todo Space to add arbitrary html to wp_head and wp_footer.
 * @todo Create ability to add and register scripts and styles for enqueueing (via Options page).
 * @todo Create selection on Option page of which to pick registered scripts to make available on edit screens.
 * @todo Create shortcode registration on Options page to make those snippets available on edit screens.
 * @todo Add Error messaging.
 * @todo Clean up tiny_mce_before_init in SnS_Admin_Meta_Box.
 */

if ( version_compare( PHP_VERSION, '5.4', '>=' ) ) :

	/**
	 * This utility function is location specific.
	 * Use in places where __FILE__ would otherwise be used.
	 */
	function _FILE_() {
		return __FILE__;
	}

	require_once( "includes/bootstrap.php" );

else :
	function SnS_disable_message() {
		?><div class="notice notice-success is-dismissible"><p><?php
		_e('Sorry, Scripts n Styles doesn\'t work with PHP versions below 5.4. Please contact your host.', 'scripts-n-styles');
		?></p></div><?php
	}
	add_action( 'admin_notices', 'SnS_disable_message' );
endif;