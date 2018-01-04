<?php
/**
 * Uninstall file
 *
 * @package Scripts-N-Styles
 */

namespace unFocus\SnS;

if ( ! defined( 'ABSPATH' ) || ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

$options = get_option( 'SnS_options' );
if ( empty( $options['delete_data_uninstall'] ) || 'yes' !== $options['delete_data_uninstall'] ) {
	return;
}

$posts = get_posts( [
	'numberposts' => -1,
	'post_type' => 'any',
	'post_status' => 'any',
	'orderby' => 'ID',
	'meta_key' => '_SnS',
] );

foreach ( $posts as $post ) {
	delete_post_meta( $post->ID, '_SnS' );
}
delete_option( 'SnS_options' );

$users = get_users( 'meta_key=current_sns_tab' );
foreach ( $users as $user ) {
	delete_user_option( $user->ID, 'current_sns_tab', true );
}

$users = get_users( 'meta_key=scripts_n_styles_page_sns_usage_per_page' );
foreach ( $users as $user ) {
	delete_user_option( $user->ID, 'scripts_n_styles_page_sns_usage_per_page', true );
}
