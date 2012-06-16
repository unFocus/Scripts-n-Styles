// Options JavaScript

jQuery( document ).ready( function( $ ) {
	var theme = codemirror_options.theme ? codemirror_options.theme: 'default';
	var file = $( 'input[name="file"]' ).val();
	var fileType = file.slice( file.lastIndexOf(".")+1 );
	var $new = $( '#newcontent' );
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
	
	var height = $(window).height() - $new.offset().top - $( '#wpadminbar' ).height() - modHeight;
	
	var config = { lineNumbers: true, mode: "javascript", theme: theme };
	
	switch ( fileType ) {
		case "js":
			config.mode = "javascript";
		break;
		case "css":
			config.mode = "css";
		break;
		case "less":
			config.mode = "less";
		break;
		case "coffee":
			config.mode = "coffeescript";
		break;
		case "html":
		case "htm":
			config.mode = "html";
		break;
		case "php":
			config.mode = "php";
		break;
		default:
			config.mode = "markdown";
		break;
	}
	
	CodeMirror.commands.save = function (){ jQuery('#submit').click(); };
	
	CodeMirror.fromTextArea( $new.get(0), config );
	
	if ( height < $( '.CodeMirror-scroll ' ).height() ) $( '.CodeMirror-scroll ' ).height( height );
});