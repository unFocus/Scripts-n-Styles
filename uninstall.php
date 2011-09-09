<?php
if( ! defined( 'ABSPATH') && ! defined('WP_UNINSTALL_PLUGIN') ) exit();
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
foreach( $all_posts as $post) {
	delete_post_meta( $post->ID, '_SnS_scripts' );
	delete_post_meta( $post->ID, '_SnS_styles' );
}
delete_option( 'SnS_options' );
delete_option( 'sns_enqueue_scripts' );

$all_users = get_users( 'meta_key=current-sns-tab' );
foreach( $all_users as $user) delete_user_option( $user->ID, 'current-sns-tab', true );
?>