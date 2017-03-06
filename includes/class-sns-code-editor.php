<?php
namespace unFocus\SnS\Admin_Code_Editor;

/**
 * Admin_Code_Editor
 */

add_action( 'admin_head-theme-editor.php', 'unFocus\SnS\Admin_Code_Editor\styles' );
add_action( 'admin_head-plugin-editor.php', 'unFocus\SnS\Admin_Code_Editor\styles' );
add_filter( 'editable_extensions', function( $editable_extensions ) {
	$editable_extensions[] = 'less';
	$editable_extensions[] = 'coffee';
	$editable_extensions[] = 'md';
	return $editable_extensions;
} );

function styles() {
	$options = get_option( 'SnS_options' );
	$cm_theme = isset( $options[ 'cm_theme' ] ) ? $options[ 'cm_theme' ] : 'default';
	wp_enqueue_style(   'sns-code-editor' );
	wp_enqueue_script(  'sns-code-editor' );
	wp_localize_script( 'sns-code-editor', 'codemirror_options', array( 'theme' => $cm_theme ) );
	wp_localize_script( 'sns-code-editor', 'sns_plugin_editor_options', array(
		'action' => 'sns_plugin_editor',
		'nonce' => wp_create_nonce( 'sns_plugin_editor')
	) );
}
?>