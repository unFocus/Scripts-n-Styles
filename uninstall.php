<?php
if( ! defined( 'ABSPATH') && ! defined('WP_UNINSTALL_PLUGIN') ) exit();
$posts = get_posts( array(
	'numberposts' => -1,
	'post_type' => 'any',
	'post_status' => 'any',
	'orderby' => 'ID',
	'meta_query' => array(
		'relation' => 'OR',
		array( 'key' => '_SnS_scripts' ),
		array( 'key' => '_SnS_styles' )
	)
) );

foreach( $posts as $post) {
	delete_post_meta( $post->ID, '_SnS_scripts' );
	delete_post_meta( $post->ID, '_SnS_styles' );
}
delete_option( 'SnS_options' );
delete_option( 'sns_enqueue_scripts' );

$all_users = get_users( 'meta_key=current-sns-tab' );
foreach( $all_users as $user) delete_user_option( $user->ID, 'current-sns-tab', true );
?>