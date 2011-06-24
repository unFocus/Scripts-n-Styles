// Meta Box JavaScript

jQuery( document ).ready( function( $ ) {
	$( "#uFp_meta_box" ).tabs().find( ".wp-tab-bar" ).show();
	//var editor = CodeMirror.fromTextArea(document.getElementById("code"), { lineNumbers: true, readOnly: true });
	$( "textarea.js" ).each( function() {
		CodeMirror.fromTextArea( this, { lineNumbers: true, mode: "javascript" } );
	});
	$( "textarea.css" ).each( function() {
		CodeMirror.fromTextArea( this, { lineNumbers: true, mode: "css" } );
	});
});