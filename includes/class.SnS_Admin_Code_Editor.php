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
	
	}
	
	function styles() {
		$options = get_option( 'SnS_options' );
		$cm_theme = isset( $options[ 'cm_theme' ] ) ? $options[ 'cm_theme' ] : 'default';
		
		wp_enqueue_style( 'codemirror', plugins_url( 'libraries/CodeMirror2/lib/codemirror.css', Scripts_n_Styles::$file), array(), '2.2' );
		
		if ( in_array( $cm_theme, array( 'cobalt', 'eclipse', 'elegant', 'monokai', 'neat', 'night', 'rubyblue' ) ) )
			wp_enqueue_style( "codemirror-$cm_theme", plugins_url( "libraries/CodeMirror2/theme/$cm_theme.css", Scripts_n_Styles::$file), array( 'codemirror' ), '2.2' );
		
		wp_enqueue_style( 'sns-code-editor', plugins_url( 'css/code-editor.css', Scripts_n_Styles::$file), array( 'codemirror' ), Scripts_n_Styles::VERSION );
		
		wp_enqueue_script(
			'codemirror',
			plugins_url( 'libraries/CodeMirror2/lib/codemirror.js', Scripts_n_Styles::$file),
			array(),
			'2.2' );
		wp_enqueue_script(
			'codemirror-css',
			plugins_url( 'libraries/CodeMirror2/mode/css/css.js', Scripts_n_Styles::$file),
			array(  'codemirror' ),
			'2.2' );
		wp_enqueue_script(
			'codemirror-less',
			plugins_url( 'libraries/CodeMirror2/mode/less/less.js', Scripts_n_Styles::$file),
			array(  'codemirror' ),
			'2.2' );
		wp_enqueue_script(
			'codemirror-javascript',
			plugins_url( 'libraries/CodeMirror2/mode/javascript/javascript.js', Scripts_n_Styles::$file),
			array(  'codemirror' ),
			'2.2' );
		wp_register_script(
			'codemirror-xml',
			plugins_url( 'libraries/CodeMirror2/mode/xml/xml.js', Scripts_n_Styles::$file),
			array(  'codemirror' ),
			'2.2' );
		wp_register_script(
			'codemirror-htmlmixed',
			plugins_url( 'libraries/CodeMirror2/mode/htmlmixed/htmlmixed.js', Scripts_n_Styles::$file),
			array( 	'codemirror-xml',
					'codemirror-css',
					'codemirror-javascript'
				),
			'2.2' );
		wp_register_script(
			'codemirror-clike',
			plugins_url( 'libraries/CodeMirror2/mode/clike/clike.js', Scripts_n_Styles::$file),
			array(  'codemirror' ),
			'2.2' );
		wp_register_script(
			'codemirror-php',
			plugins_url( 'libraries/CodeMirror2/mode/php/php.js', Scripts_n_Styles::$file),
			array( 	'codemirror-xml',
					'codemirror-css',
					'codemirror-javascript',
					'codemirror-clike'
				),
			'2.2' );
		wp_enqueue_script(
			'sns-code-editor',
			plugins_url( 'js/code-editor.js', Scripts_n_Styles::$file),
			array( 	'editor',
					'jquery-ui-tabs',
					'codemirror-javascript',
					'codemirror-css',
					'codemirror-htmlmixed',
					'codemirror-php'
				),
			Scripts_n_Styles::VERSION, true );
			
		wp_localize_script( 'sns-code-editor', 'codemirror_options', array( 'theme' => $cm_theme ) );
	}
}
?>