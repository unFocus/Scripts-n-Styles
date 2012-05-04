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
		$cm_version = SnS_Admin::$cm_version;
		$cm_dir = plugins_url( 'libraries/CodeMirror2/', Scripts_n_Styles::$file);
		
		wp_enqueue_style( 'codemirror', $cm_dir . 'lib/codemirror.css', array(), $cm_version );
		if ( in_array( $cm_theme, SnS_Admin::$cm_themes ) && 'default' !== $cm_theme )
			wp_enqueue_style( "codemirror_$cm_theme", $cm_dir . "theme/$cm_theme.css", array( 'codemirror' ), $cm_version );
		
		wp_enqueue_style( 'sns-code-editor', plugins_url( 'css/code-editor.css', Scripts_n_Styles::$file), array( 'codemirror' ), Scripts_n_Styles::VERSION );
		
		wp_register_script( 'codemirror',            $cm_dir . 'lib/codemirror.js',             array(), $cm_version );
		
		wp_register_script( 'codemirror-css',        $cm_dir . 'mode/css/css.js',               array( 'codemirror' ), $cm_version );
		wp_register_script( 'codemirror-less',       $cm_dir . 'mode/less/less.js',             array( 'codemirror' ), $cm_version );
		wp_register_script( 'codemirror-javascript', $cm_dir . 'mode/javascript/javascript.js', array( 'codemirror' ), $cm_version );
		wp_register_script( 'codemirror-xml',        $cm_dir . 'mode/xml/xml.js',               array( 'codemirror' ), $cm_version );
		wp_register_script( 'codemirror-clike',      $cm_dir . 'mode/clike/clike.js',           array( 'codemirror' ), $cm_version );

		wp_register_script( 'codemirror-markdown',   $cm_dir . 'mode/markdown/markdown.js',     array( 'codemirror-xml' ), $cm_version );
		wp_register_script( 'codemirror-gfm',        $cm_dir . 'mode/gfm/gfm.js',               array( 'codemirror-php', 'codemirror-htmlmixed' ), $cm_version );
		wp_register_script( 'codemirror-htmlmixed',  $cm_dir . 'mode/htmlmixed/htmlmixed.js',   array( 'codemirror-xml', 'codemirror-css', 'codemirror-javascript' ), $cm_version );
		wp_register_script( 'codemirror-php',        $cm_dir . 'mode/php/php.js',               array( 'codemirror-xml', 'codemirror-css', 'codemirror-javascript', 'codemirror-clike' ), $cm_version );
		
		wp_enqueue_script( 'sns-code-editor', plugins_url( 'js/code-editor.js', Scripts_n_Styles::$file), array( 'editor', 'jquery-ui-tabs', 'codemirror-less', 'codemirror-htmlmixed', 'codemirror-php', 'codemirror-markdown' ), Scripts_n_Styles::VERSION, true );
			
		wp_localize_script( 'sns-code-editor', 'codemirror_options', array( 'theme' => $cm_theme ) );
	}
}
?>