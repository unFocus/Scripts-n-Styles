<?php
namespace unFocus\SnS;

const VERSION = '4.0.0-alpha';
const OPTION_GROUP = 'scripts_n_styles';

add_action( 'wp_before_admin_bar_render', function() {
	global $wp_admin_bar;
	$wp_admin_bar->add_node( [
		'id'    => 'Scripts_n_Styles',
		'title' => 'Scripts n Styles',
		'href'  => '#',
		'meta'  => array( 'class' => 'Scripts_n_Styles' )
	] );
}, 11 );

require_once( "class-scripts-n-styles.php" );
Scripts_n_Styles::init();

require_once( "class-sns-widget.php" );
