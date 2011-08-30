<?php
if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') )
    exit();
$get_posts_args = array(
	'numberposts' => -1,
	'post_type' => 'any',
	'post_status' => 'any',
	'meta_query' => array(
		array( 'key' => 'uFp_scripts' ),
		array( 'key' => 'uFp_styles' )
	)
);
$all_posts = get_posts( $get_posts_args );
foreach( $all_posts as $postinfo) {
	delete_post_meta($postinfo->ID, 'uFp_scripts');
	delete_post_meta($postinfo->ID, 'uFp_styles');
}
delete_option('SnS_options');
delete_option('sns_enqueue_scripts');

$all_users = get_users( 'meta_key=current-sns-tab' );
foreach( $all_users as $user) {
	echo'<pre>';print_r(get_user_option( 'current-sns-tab', $user->ID ));echo'</pre>';
}		
?>