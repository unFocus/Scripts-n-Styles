// Options JavaScript

jQuery( function( $ ) {
	if ( 'plugin-editor' == pagenow ) {
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
					console.dir( data );
					if ( ! data.need_update ) return;

					// var warning = "<p><strong>Warning:</strong> Making changes to active plugins is not recommended. If your changes cause a fatal error, the plugin will be automatically deactivated.</p>";
					// if ( data.active ) {
					// 	$('p.submit').before(warning);
					// 	$('.fileedit-sub .alignleft big').html( 'Editing <strong>' + $('.fileedit-sub .alignleft big strong').html() + '</strong> (active)' );
					// }
					$('#plugin').val( data.plugin );
				}
			});
		} else if ( 'theme-editor' == pagenow ) {
			console.log('asdfasdf');
		}
});
jQuery( function( $ ) {
	var theme = codemirror_options.theme ? codemirror_options.theme: 'default',
		file = $( 'input[name="file"]' ).val(),
		$new = $( '#newcontent' ),
		fileType;

	fileType = file.slice( file.lastIndexOf(".")+1 );

	var config = {
		lineNumbers: true,
		theme: theme,
		viewportMargin: Infinity
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
});