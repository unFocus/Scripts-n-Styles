// Options JavaScript

jQuery( document ).ready( function( $ ) {
	var theme = _SnS_options.theme ? _SnS_options.theme: 'default';
	$( "textarea.js" ).each( function() {
		CodeMirror.fromTextArea( this, { lineNumbers: true, mode: "javascript", theme: theme } );
	});
	$( "textarea.css" ).each( function() {
		CodeMirror.fromTextArea( this, { lineNumbers: true, mode: "css", theme: theme } );
	});
	$( "textarea.less" ).each( function() {
		CodeMirror.fromTextArea( this, { lineNumbers: true, mode: "less", theme: theme } );
	});
	$( "textarea#less" ).closest('form').submit(function() {
		//console.log( $( "textarea#less" ).val() );
		var parser = new( less.Parser );

		console.log( parser );
		parser.parse( $( "textarea#less" ).val(), function (err, tree) {
			if (err) { return console.error(err) }
			console.log( tree.toCSS() );
		});
	});
});