// Options JavaScript
import less from 'less';
import $ from 'jquery';
import CoffeeScript from 'coffeescript';

let CodeMirror = wp.CodeMirror;

if ( CodeMirror ) {
	CodeMirror.modeURL = _SnSOptions.root + 'vendor/codemirror/mode/%N/%N.js';
}

$( function() {
	if ( ! CodeMirror ) {

		// Temp bailout.
		return;
	}
	let compiled, source,
		theme = _SnSOptions.theme ? _SnSOptions.theme : 'default',
		lessMirror,
		lessOutput,
		errorLine,
		errorText,
		errors,
		loaded,
		coffeeMirror,
		coffeeOutput,
		coffeeLoaded,
		defaultSettings = $.extend({}, wp.codeEditor.defaultSettings ),
		lessMirrorConfig = {
			gutters: [ 'note-gutter', 'CodeMirror-linenumbers' ],
			mode: 'text/x-less',
			indentWithTabs: true
		},
		coffeeMirrorConfig = {
			mode: 'text/coffeescript'
		};

	//CodeMirror.commands.save = saveLessMirror;

	$( 'textarea.js' ).not( '#coffee_compiled' ).each( function() {
		wp.codeEditor.initialize( this, $.extend({}, defaultSettings, {
			codemirror: $.extend({}, defaultSettings.codemirror, { mode: 'javascript' })
		}) );
	});

	$( 'textarea.css' ).not( '#compiled' ).each( function() {
		wp.codeEditor.initialize( this, $.extend({}, defaultSettings, {
			codemirror: $.extend({}, defaultSettings.codemirror, { mode: 'css' })
		}) );
	});


	lessOutput = wp.codeEditor.initialize( 'compiled', $.extend({}, defaultSettings, {
		codemirror: $.extend({}, defaultSettings.codemirror, { mode: 'css', readOnly: true })
	}) ).codemirror;

	coffeeOutput = wp.codeEditor.initialize( 'coffee_compiled', $.extend({}, defaultSettings, {
		codemirror: $.extend({}, defaultSettings.codemirror, { mode: 'javascript', readOnly: true })
	}) ).codemirror;

	$( 'textarea.less' ).each( function() {
		lessMirror = wp.codeEditor.initialize( this, $.extend({}, defaultSettings, {
			codemirror: $.extend({}, defaultSettings.codemirror, lessMirrorConfig )
		}) ).codemirror;
		lessMirror.on( 'change', compile );
	});
	$( 'textarea.coffee' ).each( function() {
		coffeeMirror = wp.codeEditor.initialize( this, $.extend({}, defaultSettings, {
			codemirror: $.extend({}, defaultSettings.codemirror, coffeeMirrorConfig )
		}) ).codemirror;
		coffeeMirror.on( 'change', coffeeCompile );
		let result = CodeMirror.autoLoadMode( coffeeMirror, 'coffeescript' );
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
				let trimmed = $( '#coffee_spacing' ).is( ':checked' ) ? compiled : compiled.replace( /(\n\n)/gm, '\n' );
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
