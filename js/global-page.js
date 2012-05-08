// Options JavaScript

jQuery( document ).ready( function( $ ) {
	var theme = _SnS_options.theme ? _SnS_options.theme: 'default';
	var lessMirror, lessOutput, errorLine, errorText, errors, loaded,
		lessMirrorConfig = { lineNumbers: true, mode: "text/x-less", theme: theme, indentWithTabs: true };
	
	$("#enqueue_scripts").data( 'placeholder', 'Enqueue Registered Scripts...' ).width(350).chosen();
	$(".chzn-container-multi .chzn-choices .search-field input").height('26px');
	
	CodeMirror.commands.save = saveLessMirror;
	lessMirrorConfig.onChange = compile;
	
	$( "textarea.js" ).each( function() {
		CodeMirror.fromTextArea( this, { lineNumbers: true, mode: "javascript", theme: theme } );
	});
	
	$( "textarea.css" ).not( '#compiled' ).each( function() {
		CodeMirror.fromTextArea( this, { lineNumbers: true, mode: "css", theme: theme } );
	});
	
	lessOutput = CodeMirror.fromTextArea( $( '#compiled' ).get(0), { lineNumbers: true, mode: "css", theme: theme, readOnly: true } );
	
	$( "textarea.less" ).each( function() {
		lessMirror = CodeMirror.fromTextArea( this, lessMirrorConfig );
	});
	
	compile();
	loaded = true;
	$( "#less" ).closest('form').submit( compile );
	
	function saveLessMirror(){
		// Ajax Save.
	}
	function compile() {
		lessMirror.save();
		var parser = new( less.Parser );
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
		
		errorLine = lessMirror.setMarker( err.line - 1, '<strong>*%N%</strong>', "cm-error");
		lessMirror.setLineClass( errorLine, "cm-error");
		
		var pos = lessMirror.posFromIndex( err.index + 1 );
		var token = lessMirror.getTokenAt( pos );
		var start = lessMirror.posFromIndex( err.index );
		var end = lessMirror.posFromIndex( err.index + token.string.length )
		errorText = lessMirror.markText( start, end, "cm-error");
		
		lessOutput.setValue( "" );
		lessOutput.save();
	}
	function clearCompileError() {
		if ( errorLine ) {
			lessMirror.clearMarker( errorLine );
			lessMirror.setLineClass( errorLine, null );
			errorLine = false;
		}
		if ( errorText ) errorText.clear();
		errorText = false;
	}
});