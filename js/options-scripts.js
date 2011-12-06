// Options JavaScript

jQuery( document ).ready( function( $ ) {
	var theme = cm_theme || 'default';
	$( "textarea.js" ).each( function() {
		CodeMirror.fromTextArea( this, { lineNumbers: true, mode: "javascript", theme: theme } );
	});
	$( "textarea.css" ).each( function() {
		CodeMirror.fromTextArea( this, { lineNumbers: true, mode: "css", theme: theme } );
	});
});