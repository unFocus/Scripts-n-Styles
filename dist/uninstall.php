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

$sposts = get_posts( [
	'numberposts' => -1,
	'post_type'   => 'any',
	'post_status' => 'any',
	'orderby'     => 'ID',
	'meta_key'    => '_SnS', // WPCS: slow query ok.
] );

foreach ( $sposts as $spost ) {
	delete_post_meta( $spost->ID, '_SnS' );
}
delete_option( 'SnS_options' );

$users = get_users( 'meta_key=current_sns_tab' ); // WPCS: slow query ok.
foreach ( $users as $user ) {
	delete_user_option( $user->ID, 'current_sns_tab', true );
}

$users = get_users( 'meta_key=scripts_n_styles_page_sns_usage_per_page' ); // WPCS: slow query ok.
foreach ( $users as $user ) {
	delete_user_option( $user->ID, 'scripts_n_styles_page_sns_usage_per_page', true );
}
