// Options JavaScript

jQuery( document ).ready( function( $ ) {
	$( "textarea.js" ).each( function() {
		CodeMirror.fromTextArea( this, { lineNumbers: true, mode: "javascript" } );
	});
	$( "textarea.css" ).each( function() {
		CodeMirror.fromTextArea( this, { lineNumbers: true, mode: "css" } );
	});
});