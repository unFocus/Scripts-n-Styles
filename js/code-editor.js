// Options JavaScript

jQuery( function( $ ) {
	console.log(pagenow);
	if ( 'plugin-editor' == pagenow || 'plugins_page_sns_plugin_editor' == pagenow ) {
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
					$('#templateside > ul').addClass('sns-file-list').html( data.ul );
					console.dir( data );
					nestPluginFolders()
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
			console.log('theme-editor');
		}
	function nestPluginFolders() {

		var $list = $('.sns-file-list');
		$list.before('<ul class="sns-folders-list">');
		var $folders = $('.sns-folders-list');
		$('.sns-file-list > li:first-child').addClass('main-file');

		$('.sns-file-list > li').each(function(i,e){
			var link = $(e).find("a");
			var folderArray = link.text().split("/");
			if ( folderArray.length === 1 ) return true;

			var filename = folderArray.pop();
			link.text(filename);
			var depth = folderArray.length;
			var targetUL = $folders; // start at root.

			for (var i = 0; i < folderArray.length; i++) {
				var folder = folderArray[i];
				var $folder = targetUL.children('li.folder-'+folder);
				if ( ! $folder.length ) {
					if ( targetUL.children('.folder').length ) {
						targetUL.children('.folder').last().after('<li class="folder folder-'+folder+'"></li>');
					} else {
						targetUL.prepend('<li class="folder folder-'+folder+'"></li>');
					}
					targetUL.children('.folder-'+folder)
						.append('<a href="#" class="tog-folder">'+folder+'</a>')
						.append('<ul>');
				}
				targetUL = targetUL.children('.folder-'+folder).children('ul');
				$(this).appendTo(targetUL);
			}
		});

		$('#templateside .highlight').parents('.folder').addClass('open');

		$folders.on('click', '.tog-folder', function(e){
			e.preventDefault();
			$(this).parent().toggleClass('open');
		})
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