<?php
namespace unFocus\SnS;

const BASENAME = 'scripts-n-styles/scripts-n-styles.php';
const VERSION = '4.0.0-alpha';
const OPTION_GROUP = 'scripts_n_styles';
const ADMIN_MENU_SLUG = 'sns';
const NONCE_NAME = 'scripts_n_styles_noncename';

require_once( __DIR__.'/main.php' );
require_once( __DIR__.'/utility.php' );
require_once( __DIR__.'/widget.php' );


if ( is_admin() && ! ( defined('DISALLOW_UNFILTERED_HTML') && DISALLOW_UNFILTERED_HTML ) ) {
	/*	NOTE: Setting the DISALLOW_UNFILTERED_HTML constant to
		true in the wp-config.php would effectively disable this
		plugin's admin because no user would have the capability.
	*/

	require_once( __DIR__.'/admin.php' );
	require_once( __DIR__.'/meta-box.php' );
	require_once( __DIR__.'/class-sns-plugin-editor-page.php' );
	require_once( __DIR__.'/class-sns-theme-editor-page.php' );
	require_once( __DIR__.'/settings-page.php' );
	require_once( __DIR__.'/usage-page.php' );
	require_once( __DIR__.'/global-page.php' );
	require_once( __DIR__.'/hoops-page.php' );
	require_once( __DIR__.'/theme-page.php' );
	require_once( __DIR__.'/ajax.php' );
	require_once( __DIR__.'/form.php' );
}