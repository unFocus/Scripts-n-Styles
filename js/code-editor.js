// Options JavaScript

jQuery( document ).ready( function( $ ) {
	if ( 'plugin-editor' == pagenow )
		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: {
				_ajax_nonce: sns_plugin_editor_options.nonce,
				action: sns_plugin_editor_options.action,
				file: $('input[name="file"]').val(),
				plugin: $('input[name="plugin"]').val()
			},
			success: function( data ) {
				$('#templateside > ul').html( data.ul );
				if ( ! data.need_update ) return;

				var warning = "<p><strong>Warning:</strong> Making changes to active plugins is not recommended. If your changes cause a fatal error, the plugin will be automatically deactivated.</p>";
				if ( data.active ) {
					$('p.submit').before(warning);
					$('.fileedit-sub .alignleft big').html( 'Editing <strong>' + $('.fileedit-sub .alignleft big strong').html() + '</strong> (active)' );
				}
				$('#plugin').val( data.plugin );
				console.dir( data );
			}
		});
});
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
		case "md":
			config.mode = "gfm";
		break;
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