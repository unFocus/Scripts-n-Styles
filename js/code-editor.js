// Options JavaScript

jQuery( function( $ ) {
	if ( 'plugin-editor' == pagenow || 'plugins_page_sns_plugin_editor' == pagenow ) {

		$('#templateside > ul').addClass('sns-file-list');
		nestPluginFolders();

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

	var modes = {
		"js"     : "javascript",
		"jsx"    : "text/jsx",
		"ts"     : "text/typescript",
		"tsx"    : "text/typescript-jsx",
		"coffee" : "coffeescript",

		"css"    : "css",
		"less"   : "text/x-less",
		"scss"   : "text/x-scss",
		"sass"   : "text/x-sass",
		"styl"   : "text/x-styl",

		"html"   : "htmlmixed",
		"htm"    : "htmlmixed",
		"include": "php",
		"inc"    : "php",
		"php"    : "php",

		"xml"    : "xml",
		"json"   : "application/ld+json",
		"md"     : "gfm",
		"txt"    : "text/plain",
		"text"   : "text/x-markdown",
	};
	var config = {
		lineNumbers: true,
		theme: theme,
		// viewportMargin: Infinity,
		mode: modes[fileType]
	};

	CodeMirror.commands.save = function (){ $('#submit').click(); };

	var cmeditor = CodeMirror.fromTextArea( $new.get(0), config );
});