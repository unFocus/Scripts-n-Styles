// Options JavaScript

jQuery( document ).ready( function( $ ) {
	var compiled, source;
	var theme = _SnS_options.theme ? _SnS_options.theme: 'default';
	var lessMirror, lessOutput, errorLine, errorText, errors, loaded,
		coffeeMirror, coffeeOutput, coffee_errorLine, coffee_errorText, coffee_errors, coffee_loaded,
		lessMirrorConfig = { gutters: ["note-gutter", "CodeMirror-linenumbers"],
			lineNumbers: true, mode: "text/x-less", theme: theme, indentWithTabs: true },
		coffeeMirrorConfig = { lineNumbers: true, mode: "text/x-coffeescript", theme: theme };

	var parser = new( less.Parser )({});
	$("#enqueue_scripts").data( 'placeholder', 'Enqueue Registered Scripts...' ).width(350).chosen();
	$(".chosen-container-multi .chosen-choices .search-field input").height('26px');
	$(".chosen-container .chosen-results").css( 'max-height', '176px');

	//CodeMirror.commands.save = saveLessMirror;

	$( "textarea.js" ).not( '#coffee_compiled' ).each( function() {
		CodeMirror.fromTextArea( this, { lineNumbers: true, mode: "javascript", theme: theme } );
	});

	$( "textarea.css" ).not( '#compiled' ).each( function() {
		CodeMirror.fromTextArea( this, { lineNumbers: true, mode: "css", theme: theme } );
	});

	lessOutput = CodeMirror.fromTextArea( $( '#compiled' ).get(0), { lineNumbers: true, mode: "css", theme: theme, readOnly: true } );
	coffeeOutput = CodeMirror.fromTextArea( $( '#coffee_compiled' ).get(0), { lineNumbers: true, mode: "javascript", theme: theme, readOnly: true } );

	$( "textarea.less" ).each( function() {
		lessMirror = CodeMirror.fromTextArea( this, lessMirrorConfig );
		lessMirror.on( "change", compile );
	});
	$( "textarea.coffee" ).each( function() {
		coffeeMirror = CodeMirror.fromTextArea( this, coffeeMirrorConfig );
		coffeeMirror.on( "change", coffee_compile );
	});
	$('#coffee').parent().append('<label><input type="checkbox" id="coffee_spacing"> Double Spaced</label>');
	$('#coffee_spacing').change( coffee_compile );
	compile();
	coffee_compile();
	loaded = true;
	coffee_loaded = true;
	$( "#less" ).closest('form').submit( compile );
	$( "#coffee" ).closest('form').submit( coffee_compile );

	//function saveLessMirror(){
		// Ajax Save.
	//}

	function compile() {
		lessMirror.save();
		parser.parse( lessMirror.getValue(), function ( err, tree ) {
			if ( err  ){
				doError( err );
			} else {
				try {
					$( '#compiled_error' ).hide();
					lessOutput.setValue( tree.toCSS() );
					lessOutput.save();
					$( '#compiled' ).next( '.CodeMirror' ).show();
					lessOutput.refresh();
					clearCompileError();
				}
				catch ( err ) {
					doError( err );
				}
			}
		});
	}
	function coffee_compile() {
		coffeeMirror.save();
		try {
			$( '#coffee_compiled_error' ).hide();
			source = $('#coffee').val();
			if ( '' == source || ' ' == source ) {
				coffeeOutput.setValue( '' );
			} else {
				compiled = CoffeeScript.compile( source );
				trimmed = $('#coffee_spacing').is(':checked') ? compiled : compiled.replace(/(\n\n)/gm,"\n");
				coffeeOutput.setValue( trimmed );
			}
			coffeeOutput.save();

			$( '#coffee_compiled' ).next( '.CodeMirror' ).show();
		}
		catch ( err ) {
			console.dir( err );
			$( '#coffee_compiled' ).next( '.CodeMirror' ).hide();
			if ( coffee_loaded ) {
				$( '#coffee_compiled_error' ).removeClass( 'error' ).addClass( 'updated' );
				$( '#coffee_compiled_error' ).show().html( "<p><strong>Warning: &nbsp; </strong>" + err.message + "</p>" );
			} else {
				$( '#coffee_compiled_error' ).show().html( "<p><strong>Error: &nbsp; </strong>" + err.message + "</p>" );
			}
		}
	}
	function doError( err ) {
		//console.dir( err );
		$( '#compiled' ).next( '.CodeMirror' ).hide();
		if ( loaded ) {
			$( '#compiled_error' ).removeClass( 'error' ).addClass( 'updated' );
			$( '#compiled_error' ).show().html( "<p><strong>Warning: &nbsp; </strong>" + err.message + "</p>" );
		} else {
			$( '#compiled_error' ).show().html( "<p><strong>Error: &nbsp; </strong>" + err.message + "</p>" );
		}
		clearCompileError();

		errorLine = lessMirror.setGutterMarker( err.line - 1, 'note-gutter', document.createTextNode("*") );
		//lessMirror.setLineClass( errorLine, "cm-error");

		var pos = lessMirror.posFromIndex( err.index + 1 );
		var token = lessMirror.getTokenAt( pos );
		var start = lessMirror.posFromIndex( err.index );
		var end = lessMirror.posFromIndex( err.index + token.string.length )
		errorText = lessMirror.markText( start, end, { className: "cm-error" } );

		lessOutput.setValue( "" );
		lessOutput.save();
	}
	function clearCompileError() {
		if ( errorLine ) {
			lessMirror.clearGutter( 'note-gutter' );
			//lessMirror.setLineClass( errorLine, null );
			errorLine = false;
		}
		if ( errorText ) errorText.clear();
		errorText = false;
	}
});
