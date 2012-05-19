// Options JavaScript

jQuery( document ).ready( function( $ ) {
	var theme = codemirror_options.theme ? codemirror_options.theme: 'default';
	var file = $( 'input[name="file"]' ).val();
	var fileType = file.slice( file.lastIndexOf(".")+1 );
	
	var $submitWrap = $( '#submit' ).parent();
	var modHeight = $submitWrap.height()
		+ parseInt( $submitWrap.css( 'marginTop' ) ) 
		+ parseInt( $submitWrap.css( 'marginBottom' ) ) 
		+ parseInt( $submitWrap.css( 'paddingTop' ) ) 
		+ parseInt( $submitWrap.css( 'paddingBottom' ) );
	
	var $documentation = $( '#documentation:visible' );
	if ( $documentation.length ) {
		modHeight += $documentation.height() + parseInt( $documentation.css( 'marginTop' ) );
	}
	
	var height = $(window).height() - $( '#newcontent' ).offset().top - $( '#wpadminbar' ).height() - modHeight;
	
	var config = { lineNumbers: true, mode: "javascript", theme: theme };
	
	if ( "js" == fileType )
		config.mode = "javascript";
	else if ( "css" == fileType )
		config.mode = "css";
	else if ( "less" == fileType )
		config.mode = "less";
	else if ( "coffee" == fileType )
		config.mode = "coffeescript";
	else if ( "html" == fileType || "htm" == fileType )
		config.mode = "html";
	else if ( "php" == fileType ) 
		config.mode = "php";
	else 
		config.mode = "markdown";
	
	CodeMirror.commands.save = function (){ jQuery('#submit').click(); };
	
	CodeMirror.fromTextArea( $( '#newcontent' ).get(0), config );
	
	if ( height < $( '.CodeMirror-scroll ' ).height() ) $( '.CodeMirror-scroll ' ).height( height );
});