<?php
if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') )
    exit();
$get_posts_args = array('numberposts' => -1,
			  'post_type' => 'any',
			  'post_status' => 'any' );
$all_posts = get_posts( $get_posts_args );
foreach( $all_posts as $postinfo) {
	delete_post_meta($postinfo->ID, 'uFp_scripts');
	delete_post_meta($postinfo->ID, 'uFp_styles');
}
delete_option('sns_options');
?>