<?php
namespace unFocus\SnS;

require_once( "constants.php" );
require_once( "main.php" );
require_once( "class-sns-widget.php" );


if ( is_admin() && ! ( defined('DISALLOW_UNFILTERED_HTML') && DISALLOW_UNFILTERED_HTML ) ) {
	/*	NOTE: Setting the DISALLOW_UNFILTERED_HTML constant to
		true in the wp-config.php would effectively disable this
		plugin's admin because no user would have the capability.
	*/
	require_once( 'class-sns-admin.php' );
	Admin::init();
}