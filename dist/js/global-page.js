// Options JavaScript

jQuery( function( $ ) {
	var compiled, source;
	var theme = _SnSOptions.theme ? _SnSOptions.theme : 'default';
	var lessMirror, lessOutput, errorLine, errorText, errors, loaded,
		coffeeMirror, coffeeOutput, coffeeLoaded,
		lessMirrorConfig = { gutters: [ 'note-gutter', 'CodeMirror-linenumbers' ],
			lineNumbers: true, mode: 'text/x-less', theme: theme, indentWithTabs: true },
		coffeeMirrorConfig = { lineNumbers: true, mode: 'text/x-coffeescript', theme: theme };

	$( '#enqueue_scripts' ).data( 'placeholder', 'Enqueue Registered Scripts...' ).width( 350 ).chosen();
	$( '.chosen-container-multi .chosen-choices .search-field input' ).height( '26px' );
	$( '.chosen-container .chosen-results' ).css( 'max-height', '176px' );

	//CodeMirror.commands.save = saveLessMirror;

	$( 'textarea.js' ).not( '#coffee_compiled' ).each( function() {
		CodeMirror.fromTextArea( this, { lineNumbers: true, mode: 'javascript', theme: theme });
	});

	$( 'textarea.css' ).not( '#compiled' ).each( function() {
		CodeMirror.fromTextArea( this, { lineNumbers: true, mode: 'css', theme: theme });
	});

	lessOutput = CodeMirror.fromTextArea( $( '#compiled' ).get( 0 ), { lineNumbers: true, mode: 'css', theme: theme, readOnly: true });
	coffeeOutput = CodeMirror.fromTextArea( $( '#coffee_compiled' ).get( 0 ), { lineNumbers: true, mode: 'javascript', theme: theme, readOnly: true });

	$( 'textarea.less' ).each( function() {
		lessMirror = CodeMirror.fromTextArea( this, lessMirrorConfig );
		lessMirror.on( 'change', compile );
	});
	$( 'textarea.coffee' ).each( function() {
		coffeeMirror = CodeMirror.fromTextArea( this, coffeeMirrorConfig );
		coffeeMirror.on( 'change', coffeeCompile );
	});
	$( '#coffee' ).parent().append( '<label><input type="checkbox" id="coffee_spacing"> Double Spaced</label>' );
	$( '#coffee_spacing' ).change( coffeeCompile );
	compile();
	coffeeCompile();
	loaded = true;
	coffeeLoaded = true;
	$( '#less' ).closest( 'form' ).submit( compile );
	$( '#coffee' ).closest( 'form' ).submit( coffeeCompile );

	//function saveLessMirror(){
		// Ajax Save.
	//}

	function compile() {
		lessMirror.save();

		less.render( lessMirror.getValue(), {}, function( error, output ) {
			if ( error  ) {
				doError( error );
			} else {
				try {
					$( '#compiled_error' ).hide();
					lessOutput.setValue( output.css );
					lessOutput.save();
					$( '#compiled' ).next( '.CodeMirror' ).show();
					lessOutput.refresh();
					clearCompileError();
				} catch ( error ) {
					doError( error );
				}
			}
		});
	}
	function coffeeCompile() {
		coffeeMirror.save();
		try {
			$( '#coffee_compiled_error' ).hide();
			source = $( '#coffee' ).val();
			if ( '' == source || ' ' == source ) {
				coffeeOutput.setValue( '' );
			} else {
				compiled = CoffeeScript.compile( source );
				trimmed = $( '#coffee_spacing' ).is( ':checked' ) ? compiled : compiled.replace( /(\n\n)/gm, '\n' );
				coffeeOutput.setValue( trimmed );
			}
			coffeeOutput.save();

			$( '#coffee_compiled' ).next( '.CodeMirror' ).show();
		} catch ( err ) {
			console.dir( err );
			$( '#coffee_compiled' ).next( '.CodeMirror' ).hide();
			if ( coffeeLoaded ) {
				$( '#coffee_compiled_error' ).removeClass( 'error' ).addClass( 'updated' );
				$( '#coffee_compiled_error' ).show().html( '<p><strong>Warning: &nbsp; </strong>' + err.message + '</p>' );
			} else {
				$( '#coffee_compiled_error' ).show().html( '<p><strong>Error: &nbsp; </strong>' + err.message + '</p>' );
			}
		}
	}
	function doError( err ) {

		//console.dir( err );
		$( '#compiled' ).next( '.CodeMirror' ).hide();
		if ( loaded ) {
			$( '#compiled_error' ).removeClass( 'error' ).addClass( 'updated' );
			$( '#compiled_error' ).show().html( '<p><strong>Warning: &nbsp; </strong>' + err.message + '</p>' );
		} else {
			$( '#compiled_error' ).show().html( '<p><strong>Error: &nbsp; </strong>' + err.message + '</p>' );
		}
		clearCompileError();

		errorLine = lessMirror.setGutterMarker( err.line - 1, 'note-gutter', document.createTextNode( '*' ) );

		//lessMirror.setLineClass( errorLine, "cm-error");

		let pos = lessMirror.posFromIndex( err.index + 1 );
		let token = lessMirror.getTokenAt( pos );
		let start = lessMirror.posFromIndex( err.index );
		let end = lessMirror.posFromIndex( err.index + token.string.length );
		errorText = lessMirror.markText( start, end, { className: 'cm-error' });

		lessOutput.setValue( '' );
		lessOutput.save();
	}
	function clearCompileError() {
		if ( errorLine ) {
			lessMirror.clearGutter( 'note-gutter' );

			//lessMirror.setLineClass( errorLine, null );
			errorLine = false;
		}
		if ( errorText ) {
			errorText.clear();
		}
		errorText = false;
	}
});
