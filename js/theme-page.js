// Options JavaScript

jQuery( document ).ready( function( $ ) { "use strict"
	var collection = []
	  , context = "#less_area"
	  , theme = _SnS_options.theme ? _SnS_options.theme: 'default'
	  , loaded = false
	  , compiled, $codemirror, $error, timer, onChange, errorMarker, errorText, errorMirror;
	
	onChange = function onChange(){
		clearTimeout( timer );
		console.log( 'foo' );
		timer = setTimeout( 'compile_all()', 100 );
	}
	$( ".sns-less-ide", context ).each( function() {
		var ide = {
			raw : $('.raw',this).get(0),
			lines : 0,
			startLine : 0,
			endLine : 0,
			errorLine : null,
			errorText : null,
		};
		
		ide.less = CodeMirror.fromTextArea(
			$('.less',this).get(0),
			{
				lineNumbers: true,
				mode: "text/x-less",
				theme: theme,
				indentWithTabs: true,
				onChange: onChange
			}
		);
		
		collection.push( ide );
	});
	
	compiled = CodeMirror.fromTextArea(
		$('.css',"#css_area").get(0),
		{ lineNumbers: true, mode: "css", theme: theme, readOnly: true }
	);
	$codemirror = $('.css',"#css_area").next( '.CodeMirror' );
	$error = $("#compiled_error");
	
	compile_all();
	loaded = true;
	
	$( "#less_area" ).closest('form').submit( compile_all );
	
	function compile_all() {
		var lessValue = '';
		var totalLines = 0;
		$( collection ).each(function(){
			this.less.save();
			lessValue += "\n" + this.less.getValue();
			this.lines = this.less.lineCount();
			this.startLine = totalLines;
			totalLines += this.lines;
			this.endLine = totalLines;
		});
		var parser = new( less.Parser );
		parser.parse( lessValue, function ( err, tree ) {
			if ( err ){
				doError( err );
			} else {
				try {
					$error.hide();
					compiled.setValue( tree.toCSS() );
					compiled.save();
					$codemirror.show();
					compiled.refresh();
					clearCompileError();
				}
				catch ( err ) {
					doError( err );
				}
			}
		});
	}
	function doError( err ) {
		console.log( err );
		var pos, token, start, end, errLine, fileNum;
		errLine = err.line-1;
		
		errorMirror = null;
		$( collection ).each(function( i ){
			if ( this.startLine <= errLine && errLine < this.endLine ) {
				errorMirror = this.less;
				errLine = errLine - this.startLine -1;
				fileNum = i+1;
			}
		});
		
		$codemirror
			.hide();
		
		if ( loaded ) {
			$error
				.removeClass( 'error' )
				.addClass( 'updated' );
			$error
				.show()
				.html( "<p><strong>Warning: &nbsp; </strong>LESS " + err.type + " Error on line " + (errLine+1) + " of the " + fileNum + " file.</p>" );
		} else {
			$error
				.show()
				.html( "<p><strong>Error: &nbsp; </strong>" + err.message + "</p>" );
		}
		
		clearCompileError();
		
		errorMarker = errorMirror.setMarker( errLine, '<strong>*%N%</strong>', "cm-error");
		
		errorMirror.setLineClass( errorMarker, "cm-error");
		
		pos = errorMirror.posFromIndex( err.index + 1 );
		token = errorMirror.getTokenAt( pos );
		start = errorMirror.posFromIndex( err.index );
		end = errorMirror.posFromIndex( err.index + token.string.length );
		
		errorText = errorMirror.markText( start, end, "cm-error");
		
		compiled.setValue( "" );
		compiled.save();
	}
	function clearCompileError() {
		if ( errorMarker ) {
			errorMirror.clearMarker( errorMarker );
			errorMirror.setLineClass( errorMarker, null );
			errorMarker = false;
		}
		if ( errorText ) errorText.clear();
		errorText = false;
	}
	window.compile_all = compile_all;
});