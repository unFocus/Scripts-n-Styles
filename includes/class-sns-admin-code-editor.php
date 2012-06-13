<?php
/**
 * SnS_Admin_Code_Editor
 */
		
class SnS_Admin_Code_Editor
{
	/**
	 * Initializing method. 
	 */
	function init() {
		add_action( 'admin_head-theme-editor.php', array( __CLASS__, 'styles' ) );
		add_action( 'admin_head-plugin-editor.php', array( __CLASS__, 'styles' ) );
		add_filter( 'editable_extensions', array( __CLASS__, 'extend' ) );
	}
	
	function extend( $editable_extensions ) {
		$editable_extensions[] = 'less';
		$editable_extensions[] = 'coffee';
		return $editable_extensions;
	}
	
	function styles() {
		$options = get_option( 'SnS_options' );
		$cm_theme = isset( $options[ 'cm_theme' ] ) ? $options[ 'cm_theme' ] : 'default';
		wp_enqueue_style(   'sns-code-editor' );
		wp_enqueue_script(  'sns-code-editor' );
		wp_localize_script( 'sns-code-editor', 'codemirror_options', array( 'theme' => $cm_theme ) );
	}
}
?>