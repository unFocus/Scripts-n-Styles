// Options JavaScript

jQuery( document ).ready( function( $ ) {
	var theme = _SnS_options.theme ? _SnS_options.theme: 'default';
	var lessMirror, lessOutput;
	
	$( "textarea.js" ).each( function() {
		CodeMirror.fromTextArea( this, { lineNumbers: true, mode: "javascript", theme: theme } );
	});
	
	$( "textarea.css" ).not( '#compiled' ).each( function() {
		CodeMirror.fromTextArea( this, { lineNumbers: true, mode: "css", theme: theme } );
	});
	
	lessOutput = CodeMirror.fromTextArea( $( '#compiled' ).get(0), { lineNumbers: true, mode: "css", theme: theme, readOnly: true } );
	
	$( "textarea.less" ).each( function() {
		lessMirror = CodeMirror.fromTextArea( this, { lineNumbers: true, mode: "less", theme: theme, onChange: compile } );
	});
	
	$( "textarea#less" ).closest('form').submit( compile );
	
	function compile() {
		lessMirror.save();
		var parser = new( less.Parser );
		parser.parse( lessMirror.getValue(), function ( err, tree ) {
			if ( err ) return console.error( err );
			lessOutput.setValue( tree.toCSS() );
			lessOutput.save();
		});
	}
});