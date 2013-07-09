// Options JavaScript

jQuery( document ).ready( function( $ ) { "use strict"
	var collection = []
	  , context = "#less_area"
	  , theme = _SnS_options.theme ? _SnS_options.theme: 'default'
	  , timeout = _SnS_options.timeout || 1000
	  , loaded = false
	  , preview = false
	  , compiled
	  , $error, $status, $form, $css
	  , onChange
	  , errorMarker, errorText, errorMirror
	  , config;

	// Prevent keystoke compile buildup
	onChange = function onChange( cm ){
		$status.show();
		cm.save();
		if ( timeout ) {
			clearTimeout( _SnS_options.theme_compiler_timer );
			_SnS_options.theme_compiler_timer = setTimeout( _SnS_options.theme_compiler, timeout );
		} else {
			compile();
		}
	}
	config = {
		gutters: ["note-gutter", "CodeMirror-linenumbers"],
		lineNumbers: true,
		mode: "text/x-less",
		theme: theme,
		indentWithTabs: true,
		tabSize: 4,
		indentUnit: 4
	};

	CodeMirror.commands.save = function() {
		$form.submit();
	};

	// Each "IDE"
	$( ".sns-less-ide", context ).each( function() {
		var $text = $('.code',this);
		var ide = {
			name : $text.data('file-name'),
			raw : $text.data('raw'),
			data : $text.val(),
			$text : $text,
			lines : 0,
			startLine : 0,
			endLine : 0,
			startChars : 0,
			endChars : 0,
			errorLine : null,
			errorText : null,
			cm : CodeMirror.fromTextArea( $text.get(0), config )
		};
		ide.cm.on( "change", onChange );
		if ( $text.parent().hasClass( 'sns-collapsed' ) )
			ide.cm.toTextArea();
		collection.push( ide );
	});

	// Collapsable
	$( context ).on( "click", '.sns-collapsed-btn, .sns-collapsed-btn + label', function( event ){
		var $this = $( this )
		  , collapsed
		  , fileName
		  , thisIDE;
		$this.parent().toggleClass( 'sns-collapsed' );
		fileName = $this.siblings( '.code' ).data( 'file-name' );
		collapsed = $this.parent().hasClass( 'sns-collapsed' );
		$(collection).each(function(index, element) {
			if ( element.name == fileName )
				thisIDE = element;
		});
		if ( collapsed ) {
			thisIDE.cm.toTextArea();
		} else {
			thisIDE.cm = CodeMirror.fromTextArea( thisIDE.$text.get(0), config );
			thisIDE.cm.on( "change", onChange );
		}
		$.post( ajaxurl,
			{   action: 'sns_open_theme_panels'
			  , _ajax_nonce: $( '#_wpnonce' ).val()
			  , 'file-name':  fileName
			  , 'collapsed':  collapsed ? 'yes' : 'no'
			}
		);
	});
	$( '#css_area' ).on( "click", '.sns-collapsed-btn, .sns-collapsed-btn + label', function( event ){
		var $this = $( this ).parent();
		$this.toggleClass( 'sns-collapsed' );
		preview = ! $this.hasClass( 'sns-collapsed' );
		if ( preview )
			compiled = createCSSEditor();
		else
			compiled.toTextArea();
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
		compile();
		$( '.sns-ajax-loading' ).hide();
		$( this ).nextAll( '.single-status' )
			.show().delay(3000).fadeOut()
			.children('.settings-error').text( 'Original Source File Loaded.' );
	});

	// Save
	$( context ).on( "click", ".sns-ajax-save", function( event ){
		event.preventDefault();
		$( this ).nextAll( '.sns-ajax-loading' ).show();
		$form.submit();
	});
	function saved( data ) {
		$(data).insertAfter( '#icon-sns + h2' ).delay(3000).fadeOut();
		$( '.sns-ajax-loading' ).hide();
	}

	// The CSS output side.
	$css = $( '.css', "#css_area" );
	if ( preview ) {
		compiled = createCSSEditor();
	}

	$error = $( "#compiled_error" );
	$status = $( "#compile_status" );

	// Start.
	compile();
	loaded = true;

	$form = $( "#less_area" ).closest( 'form' );
	$form.submit( function( event ){
		event.preventDefault();
		compile();
		$.ajax({
			type: "POST",
			url: window.location,
			data: $(this).serialize()+'&ajaxsubmit=1',
			cache: false,
			success: saved
		});
	});
	function createCSSEditor() {
		return CodeMirror.fromTextArea(
			$css.get(0),
			{ lineNumbers: true, mode: "css", theme: theme, indentWithTabs: true, tabSize: 4, indentUnit: 4 }
		);
	}
	function compile() {
		var lessValue = '';
		var totalLines = 0;
		var totalChars = 0;
		var compiledValue;
		$( collection ).each(function(){
			//this.cm.save();
			lessValue += "\n" + this.$text.val();

			this.lines = this.cm.lineCount();
			this.startLine = totalLines;
			totalLines += this.lines;
			this.endLine = totalLines;

			this.chars = this.$text.val().length + 1;
			this.startChars = totalChars;
			totalChars += this.chars;
			this.endChars = totalChars;
		});

		var parser = new( less.Parser )({});
		parser.parse( lessValue, function ( err, tree ) {
			if ( err ){
				doError( err );
			} else {
				try {
					$error.hide();
					if ( preview ) {
						$( compiled.getWrapperElement() ).show();
						compiledValue = tree.toCSS();
						compiled.setValue( compiledValue );
						compiled.save();
						compiled.refresh();
					} else {
						compiledValue = tree.toCSS({ compress: true });
						$css.val( compiledValue );
					}
					clearCompileError();
				}
				catch ( err ) {
					doError( err );
				}
			}
		});
		clearTimeout( _SnS_options.theme_compiler_timer );
		$status.hide();
	}
	function doError( err ) {
		var pos, token, start, end, errLine, fileName, errMessage, errIndex;
		errLine = err.line-1;

		errorMirror = null;
		$( collection ).each(function( i ){
			if ( this.startLine <= errLine && errLine < this.endLine ) {
				errorMirror = this.cm;
				errLine = errLine - this.startLine -1;
				fileName = this.name;
				errIndex = err.index - this.startChars;
				return;
			}
		});
		if ( preview )
			$( compiled.getWrapperElement()).hide();
		var errMessage = '';

		errMessage = " &nbsp; <em>LESS " + err.type +" Error</em> on line " + ( errLine + 1 ) + " of " + fileName + ". <br />" + err.message + "</p>";

		if ( loaded ) {
			$error
				.removeClass( 'error' )
				.addClass( 'updated' )
				.show()
				.html( "<p><strong>Warning:</strong>" + errMessage + "</p>" );
		} else {
			$error
				.show()
				.html( "<p><strong>Error: &nbsp; </strong>" + errMessage + "</p>" );
		}

		clearCompileError();

		if (!errorMirror) return;

		errorMarker = errorMirror.setGutterMarker( errLine, 'note-gutter', $('<span></span>').addClass('cm-error').css('marginLeft','4px').text('✖').get(0) );

		//errorMirror.addLineClass( errLine, "wrap", cm-error" );

		pos = errorMirror.posFromIndex( errIndex );
		token = errorMirror.getTokenAt( pos );
		start = errorMirror.posFromIndex( errIndex - 1 );
		end = errorMirror.posFromIndex( errIndex + token.string.length - 1 );
		errorText = errorMirror.markText( start, end, { className: "cm-error" } );
		if ( preview ) {
			//compiled.setValue( "" );
			//compiled.save();
			//compiled.refresh();
		}
	}
	function clearCompileError() {
		if ( errorMarker ) {
			$( collection ).each(function( i ){
				this.cm.clearGutter( 'note-gutter' );
			});
			//errorMirror.removeLineClass( errLine, "wrap", "cm-error" );
			errorMarker = false;
		}
		if ( errorText ) {
			errorText.clear();
			errorText = false;
		}
	}
	_SnS_options.theme_compiler = compile;
});