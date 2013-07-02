// Options JavaScript

jQuery( document ).ready( function( $ ) {
	var theme = codemirror_options.theme ? codemirror_options.theme: 'default',
		file = $( 'input[name="file"]' ).val(),
		$new = $( '#newcontent' ),
		$template = $( '#template' ),
		$wpbody = $( '#wpbody-content' ),
		$documentation = $( '#documentation' ),
		$submit = $( 'p.submit' ).first(),
		$warning = $( '#documentation + p:not(.submit)' ).first(),
		$templateside = $( '#templateside' ),
		templateOffset, bottomPadding, docHeight, submitHeight, resizeTimer, fileType, cmheight;

	fileType = file.slice( file.lastIndexOf(".")+1 );

	templateOffset = parseInt( jQuery('#template').offset().top ),
	bottomPadding = parseInt( $('#wpbody-content').css('padding-bottom') );
	docHeight = ( $documentation.length ) ? parseInt( $documentation.height() )
			+ parseInt( $documentation.css('padding-top') )
			+ parseInt( $documentation.css('padding-bottom') )
			+ parseInt( $documentation.css('margin-top') )
			+ parseInt( $documentation.css('margin-bottom') )
			: 0;
	warningHeight = ( $warning.length ) ? parseInt( $warning.height() )
			+ parseInt( $warning.css('padding-top') )
			+ parseInt( $warning.css('padding-bottom') )
			+ parseInt( $warning.css('margin-top') )
			+ parseInt( $warning.css('margin-bottom') )
			: 0;
	submitHeight = parseInt( $submit.height() )
			+ parseInt( $submit.css('padding-top') )
			+ parseInt( $submit.css('padding-bottom') )
			+ parseInt( $submit.css('margin-top') )
			+ parseInt( $submit.css('margin-bottom') );
	templateside = parseInt( $templateside.height() );

	var config = {
		lineNumbers: true,
		theme: theme,
		//viewportMargin: Infinity
	};

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

	var cmeditor = CodeMirror.fromTextArea( $new.get(0), config );

	$(window).resize(function(){
		clearTimeout(resizeTimer);
    	resizeTimer = setTimeout( cmresizer, 100 );
	});
	function cmresizer() {
		cmheight = Math.max( 300, $(window).height() - ( templateOffset + bottomPadding + docHeight + warningHeight + submitHeight + 40 ) );
		if ( cmheight > templateside )
			cmeditor.setSize( null, cmheight );
		else
			cmeditor.setSize( null, $(window).height() - ( templateOffset + docHeight + warningHeight + submitHeight ) );
	}
	cmresizer();

});