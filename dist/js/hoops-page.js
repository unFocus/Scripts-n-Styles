// Options JavaScript

jQuery( document ).ready( function( $ ) { "use strict"
	var collection = []
	  , context = "#sns-shortcodes"
	  , theme = _SnS_options.theme ? _SnS_options.theme: 'default'
	  , $form
	  , config;

	config = {
		mode: "text/html",
		theme: theme,
		lineNumbers: true,
		tabMode: "shift",
		indentUnit: 4,
		indentWithTabs: true,
		enterMode: "keep",
		matchBrackets: true
	};

	CodeMirror.commands.save = function() {
		$form.submit();
	};

	// Each "IDE"
	$( ".sns-less-ide", context ).each( function() {
		var $text = $('.code',this);
		var ide = {
			data : $text.val(),
			name : $text.data('sns-shortcode-key'),
			$text : $text,
			cm : CodeMirror.fromTextArea( $text.get(0), config )
		};
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
		fileName = $this.siblings( '.code' ).data( 'sns-shortcode-key' );
		collapsed = $this.parent().hasClass( 'sns-collapsed' );
		$(collection).each(function(index, element) {
			if ( element.name == fileName )
				thisIDE = element;
		});
		if ( collapsed ) {
			thisIDE.cm.toTextArea();
		} else {
			thisIDE.cm = CodeMirror.fromTextArea( thisIDE.$text.get(0), config );
		}
	});
	$( '.sns-ajax-loading' ).hide();
	/*
	$form = $( context ).closest( 'form' );
	$form.submit( function( event ){
		event.preventDefault();
		$.ajax({
			type: "POST",
			url: window.location,
			data: $(this).serialize()+'&ajaxsubmit=1',
			cache: false,
			success: saved
		});
	});
	// Save
	$( context ).on( "click", ".sns-ajax-save", function( event ){
		event.preventDefault();
		$( this ).nextAll( '.sns-ajax-loading' ).show();
		$form.submit();
	});*/
	/*
	function saved( data ) {
		$(data).insertAfter( '#icon-sns + h2' ).delay(3000).fadeOut();
		$( '.sns-ajax-loading' ).hide();
	}

	 * Expects return data.
	$('#sns-ajax-add-shortcode').click(function( event ){
		event.preventDefault();
		$(this).next().show();
		$(collection).each(function (){ this.save(); });

		var args = { _ajax_nonce: nonce };

		args.action = 'sns_hoops';
		args.subaction = 'add';
		args.name = $( '#SnS_shortcodes' ).val();
		args.shortcode = $( '#SnS_shortcodes_new' ).val();

		$.post( ajaxurl, args, function( data ) { refreshShortcodes( data ); } );
	});
	$('#SnS_shortcodes').keypress(function( event ) {
		if ( event.which == 13 ) {
			event.preventDefault();
			$("#sns-ajax-add-shortcode").click();
		}
	});
	 */
});