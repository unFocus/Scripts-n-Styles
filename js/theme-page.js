// Options JavaScript

jQuery( document ).ready( function( $ ) { "use strict"
	var collection = []
	  , context = "#less_area"
	  , theme = _SnS_options.theme ? _SnS_options.theme: 'default'
	  , loaded = false
	  , compiled, $codemirror, $error, $status,
	  timer, onChange, errorMarker, errorText, errorMirror;
	
	// Prevent keystoke compile buildup
	onChange = function onChange(){
		$status.show();
		clearTimeout( timer );
		timer = setTimeout( compile_all, 1000 );
	}
	
	// Each "IDE"
	$( ".sns-less-ide", context ).each( function() {
		var ide = {
			name : $('.code',this).data('file-name'),
			raw : $('.code',this).data('raw'),
			data : $('.code',this).val(),
			lines : 0,
			startLine : 0,
			endLine : 0,
			errorLine : null,
			errorText : null,
		};
		
		ide.cm = CodeMirror.fromTextArea(
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
	
	// Collapsable
	$( context ).on( "click", '.sns-collapsed-btn, .sns-collapsed-btn + label', function( event ){
		$( this ).parent().toggleClass( 'sns-collapsed' );
	});
	$( '.single-status' ).hide();
	$( '.sns-ajax-loading' ).hide();
	// Load
	$( context ).on( "click", ".sns-ajax-load", function( event ){
		event.preventDefault();
		$( this ).nextAll( '.sns-ajax-loading' ).show();
		var name = $( this ).parent().prevAll( '.code' ).data( 'file-name' );
		$( collection ).each( function( index, element ){
			if ( element.name == name ) {
				element.cm.setValue( element.raw );
				return;
			}
		});
		compile_all();
		$( '.sns-ajax-loading' ).hide();
		$( this ).nextAll( '.single-status' )
			.show().delay(3000).fadeOut()
			.children('.settings-error').text( 'Original Source File Loaded.' );
	});
	
	// Save
	$( context ).on( "click", ".sns-ajax-save", function( event ){
		event.preventDefault();
		$( this ).nextAll( '.sns-ajax-loading' ).show();
		$( "#less_area" ).closest( 'form' ).submit();
	});
	function saved( data ) {
		
		$(data).find('#setting-error-settings_updated').insertAfter( '#icon-sns + h2' ).delay(3000).fadeOut();
		$( '.sns-ajax-loading' ).hide();
	}
	
	// The CSS output side.
	compiled = CodeMirror.fromTextArea(
		$( '.css', "#css_area" ).get(0),
		{ lineNumbers: true, mode: "css", theme: theme, readOnly: true }
	);
	$codemirror = $( '.css', "#css_area" ).next( '.CodeMirror' );
	$error = $( "#compiled_error" );
	$status = $( "#compile_status" );
	
	// Start.
	compile_all();
	loaded = true;
	
	$( "#less_area" ).closest( 'form' ).submit( function( event ){
		event.preventDefault();
		compile_all();
		$.ajax({  
		  type: "POST",  
		  url: window.location,  
		  data: $(this).serialize(),
		  cache: false,
		  success: saved 
		});
	});
	
	function compile_all() {
		var lessValue = '';
		var totalLines = 0;
		$( collection ).each(function(){
			this.cm.save();
			lessValue += "\n" + this.cm.getValue();
			this.lines = this.cm.lineCount();
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
		clearTimeout( timer );
		$status.hide();
	}
	function doError( err ) {
		console.log( err );
		var pos, token, start, end, errLine, fileNum;
		errLine = err.line-1;
		
		errorMirror = null;
		$( collection ).each(function( i ){
			if ( this.startLine <= errLine && errLine < this.endLine ) {
				errorMirror = this.cm;
				errLine = errLine - this.startLine -1;
				fileNum = i+1;
				return;
			}
		});
		
		$codemirror
			.hide();
		
		if ( loaded ) {
			$error
				.removeClass( 'error' )
				.addClass( 'updated' )
				.show()
				.html( "<p><strong>Warning: &nbsp; </strong>LESS " + err.type + " Error on line " + (errLine+1) + " of the " + fileNum + " file.</p>" );
		} else {
			$error
				.show()
				.html( "<p><strong>Error: &nbsp; </strong>" + err.message + "</p>" );
		}
		
		clearCompileError();
		
		errorMarker = errorMirror.setMarker( errLine, '<strong>*%N%</strong>', "cm-error" );
		
		errorMirror.setLineClass( errorMarker, "cm-error" );
		
		pos = errorMirror.posFromIndex( err.index + 1 );
		token = errorMirror.getTokenAt( pos );
		start = errorMirror.posFromIndex( err.index );
		end = errorMirror.posFromIndex( err.index + token.string.length );
		
		errorText = errorMirror.markText( start, end, "cm-error" );
		
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
});