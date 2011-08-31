jQuery( document ).ready( function( $ ) {
	
	// For compat: 3.3 || 3.2 
	var initData = tinyMCEPreInit.mceInit["content"] || tinyMCEPreInit.mceInit,
		context = '#uFp_meta_box',
		currentCodeMirror = [],
		mceBodyClass = getMCEBodyClasses();
	
	//$('textarea', context).attr('autocomplete','off');
	
	// Refresh when panel becomes unhidden
	$( context + '-hide, '
		+ context + ' .hndle, '
		+ context + ' .handlediv ' )
		.live( 'click', refreshCodeMirrors );
	
	// add tab-switch handler
	$( '.wp-tab-bar a', context ).live( 'click', onTabSwitch );
	
	// activate first run
	$( '.wp-tab-active a', context ).trigger( 'click' );
	
	// must run before ajax click handlers are added.
	setupAjaxUI();
	
	refreshDeleteBtns();
	
	
	$('#sns-ajax-update-scripts').click(function( event ){
		event.preventDefault();
		$(this).next().show();
		$(currentCodeMirror).each(function (){ this.save(); });
		var args = { _ajax_nonce: $( '#scripts_n_styles_noncename' ).val(), post_id: $( '#post_ID' ).val(), };
		
		args.action = 'sns-update-scripts-ajax';
		args.uFp_scripts = $( '#uFp_scripts' ).val();
		args.uFp_scripts_in_head = $( '#uFp_scripts_in_head' ).val();
		
		$.post( ajaxurl, args, function() { refreshMCE(); } );
	});
	
	$('#sns-ajax-update-styles').click(function( event ){
		event.preventDefault();
		$(this).next().show();
		$(currentCodeMirror).each(function (){ this.save(); });
		var args = { _ajax_nonce: $( '#scripts_n_styles_noncename' ).val(), post_id: $( '#post_ID' ).val(), };
		
		args.action = 'sns-update-styles-ajax';
		args.uFp_styles = $( '#uFp_styles' ).val();
		
		$.post( ajaxurl, args, function() { refreshMCE(); } );
	});
	
	/*
	 * Expects return data.
	 */
	$('#sns-ajax-update-classes').click(function( event ){
		event.preventDefault();
		$(this).next().show();
		var args = { _ajax_nonce: $( '#scripts_n_styles_noncename' ).val(), post_id: $( '#post_ID' ).val(), };
		
		args.action = 'sns-classes-ajax';
		args.uFp_classes_body = $( '#uFp_classes_body' ).val();
		args.uFp_classes_post = $( '#uFp_classes_post' ).val();
		
		$.post( ajaxurl, args, function( data ) { refreshBodyClass( data ); } );
	});
	
	/*
	 * Expects return data.
	 */
	$('#sns-ajax-update-dropdown').click(function( event ){
		event.preventDefault();
		$(this).next().show();
		var args = { _ajax_nonce: $( '#scripts_n_styles_noncename' ).val(), post_id: $( '#post_ID' ).val(), };
		
		args.action = 'sns-dropdown-ajax';
		var format = {};
		format.title = $( '#uFp_classes_mce_title' ).val();
		format.classes = $( '#uFp_classes_mce_classes' ).val();
		switch ( $( '#uFp_classes_mce_type' ).val() ) {
			case 'inline':
				format.inline = $( '#uFp_classes_mce_element' ).val();
				break;
			case 'block':
				format.block = $( '#uFp_classes_mce_element' ).val();
				if ( $( '#uFp_classes_mce_wrapper' ).prop('checked') )
					format.wrapper = true;
				break;
			case 'selector':
				format.selector = $( '#uFp_classes_mce_element' ).val();
				break;
			default:
				return;
		}
		args.format = format;
		
		$.post( ajaxurl, args, function( data ) { refreshStyleFormats( data ); } );
	});
	
	/*
	 * Expects return data.
	 */
	$('#delete-mce-dropdown-names .sns-ajax-delete').live( "click", function( event ){
		event.preventDefault();
		$(this).next().show();
		var args = { _ajax_nonce: $( '#scripts_n_styles_noncename' ).val(), post_id: $( '#post_ID' ).val(), };
		
		args.action = 'sns-dropdown-delete-ajax';
		args.uFp_delete = $( this ).attr( 'id' );
		
		$.post( ajaxurl, args, function( data ) { refreshStyleFormats( data ); } );
	});
	
	/*
	 * Returns the body_class of TinyMCE minus the Scripts n Styles values.
	 */
	function getMCEBodyClasses() {
		var t = [];
		if ( initData.body_class )
			t = initData.body_class.split(' ');
		
		var bc = $('#uFp_classes_body').val().split(' ');
		var pc = $('#uFp_classes_post').val().split(' ');
		var p;
		for ( var i = 0; i < t.length; i++ ) {
			p = $.inArray( bc[i], t )
			if ( -1 != p )
				t.splice( p, 1 );
		}
		for ( var i = 0; i < t.length; i++ ) {
			p = $.inArray( pc[i], t )
			if ( -1 != p )
				t.splice( p, 1 );
		}
		t = t.join(' ');
		return t;
	}
	
	/*
	 * Builds and Adds the DOM for AJAX functionality.
	 */
	function setupAjaxUI() {
		// set up ajax ui. (need to come up with a better ID naming scheme.)
		$('#uFp_scripts-tab').append(
			'<div id="sns-scripts-update" class="sns-ajax-wrap">'
			 + '<a id="sns-ajax-update-scripts" href="#" class="button">Update Scripts</a>'
			 + ' '
			 + '<img class="sns-ajax-loading" src="/wp-admin/images/wpspin_light.gif">'
			 + '</div>'
			);
		
		$('#uFp_styles-tab').append(
			'<div id="sns-styles-update" class="sns-ajax-wrap">'
			 + '<a id="sns-ajax-update-styles" href="#" class="button">Update Styles</a>'
			 + ' '
			 + '<img class="sns-ajax-loading" src="/wp-admin/images/wpspin_light.gif">'
			 + '</div>'
			);
		
		$('#sns-classes').append(
			'<div id="sns-classes-ajax" class="sns-ajax-wrap">'
			 + '<a id="sns-ajax-update-classes" href="#" class="button">Update Classes</a>'
			 + ' '
			 + '<img class="sns-ajax-loading" src="/wp-admin/images/wpspin_light.gif">'
			 + '</div>'
			);
		
		$('#add-mce-dropdown-names').append(
			'<div id="sns-dropdown-ajax" class="sns-ajax-wrap">'
			 + '<a id="sns-ajax-update-dropdown" href="#" class="button">Add Class</a>'
			 + ' '
			 + '<img class="sns-ajax-loading" src="/wp-admin/images/wpspin_light.gif">'
			 + '</div>'
			);
	
		$('.sns-ajax-loading').hide();
		
		if ( $( '#uFp_classes_mce_type').val() == 'block' ) {
			$('#add-mce-dropdown-names .sns-mce-wrapper').show();
		} else {
			$('#add-mce-dropdown-names .sns-mce-wrapper').hide();
		}
			
		$( '#uFp_classes_mce_type' ).change(function() {
			if ( $(this).val() == 'block' ) {
				$('#add-mce-dropdown-names .sns-mce-wrapper').show();
			} else {
				$('#add-mce-dropdown-names .sns-mce-wrapper').hide();
			}
		});
		
		$( '#mce-dropdown-names', context ).show();
	}
	
	/*
	 * Main Tab Switch Handler.
	 */
	function onTabSwitch( event ) {
		event.preventDefault();
		
		clearCodeMirrors();
		
		/*
		 * There is a weird bug where if clearCodeMirrors() is called right before
		 * loadCodeMirrors(), loading the page with the Styles tab active, and
		 * then switching to the Script tab, you can lose data from the second
		 * CodeMirror if leaving and returning to that tab. I've no idea what's
		 * going on there. Leaving code inbetween them is a fraggle, but working,
		 * workaround. Maybe has to do with execution time? No idea.
		 */
		
		// switch active classes
		$( '.wp-tab-active', context ).removeClass( 'wp-tab-active' );
		$( this ).parent( 'li' ).addClass( 'wp-tab-active' );
				
		$( '.wp-tabs-panel-active', context ).hide().removeClass( 'wp-tabs-panel-active' );
		$( $( this ).attr( 'href' ) ).show().addClass( 'wp-tabs-panel-active' );
		
		loadCodeMirrors();
		
		$.post( ajaxurl, {
				action: 'update-current-sns-tab',
				_ajax_nonce: $('#scripts_n_styles_noncename').val(),
				active_tab:  $( '.wp-tab-bar li', context ).index( $( this ).parent( 'li' ).get(0) )
			}
		);
	}
	
	/*
	 * CodeMirror Utilities.
	 */
	function clearCodeMirrors() {
		$(currentCodeMirror).each(function (){
			this.toTextArea();
		});
		currentCodeMirror = [];
	}
	function refreshCodeMirrors() {
		$(currentCodeMirror).each( function(){
			this.refresh();
		});
	}
	function loadCodeMirrors() {
		// collect codemirrors
		var settings;
		
		// loop codemirrors
		$( '.wp-tabs-panel-active textarea.codemirror', context ).each(function (){
			if ( $(this).hasClass( 'js' ) )
				settings = {
					mode: "text/javascript",
					lineNumbers: true,
					tabMode: "shift",
					indentUnit: 4,
					indentWithTabs: true
				};
			else if ( $(this).hasClass( 'css' ) )
				settings = {
					mode: "text/css",
					lineNumbers: true,
					tabMode: "shift",
					indentUnit: 4,
					indentWithTabs: true
				};
			/*else if ( $(this).hasClass( 'htmlmixed' ) )
				settings = {
					mode: "text/html",
					lineNumbers: true,
					tabMode: "shift",
					indentUnit: 8,
					indentWithTabs: true,
					enterMode: "keep",
					matchBrackets: true
				};
			else if ( $(this).hasClass( 'php' ) )
				settings = {
					mode: "application/x-httpd-php",
					lineNumbers: true,
					tabMode: "shift",
					indentUnit: 8,
					indentWithTabs: true,
					enterMode: "keep",
					matchBrackets: true
				};*/
			else
				return;
			
			// initialize and store active codemirrors
			currentCodeMirror.push( CodeMirror.fromTextArea( this, settings ) );
		});
	}
	
	/*
	 * Refresh after AJAX.
	 */
	function refreshDeleteBtns() {
		// responsible for clearing out Delete Buttons, and Adding new ones.
		// initData should always contain the latest settings.
		if ( initData.style_formats && initData.style_formats.length ) {
			$( '#delete-mce-dropdown-names .sns-ajax-delete-p' ).remove();
			$( '#delete-mce-dropdown-names', context ).show();
			var formats = initData.style_formats;
			for ( var i = 0; i < formats.length; i++ ) {
				var deleteBtn = {};
				if ( formats[i].inline ) {
					deleteBtn.element =  formats[i].inline;
					deleteBtn.wrapper = '';
				} else if ( formats[i].block ) {
					deleteBtn.element =  formats[i].block;
					if ( formats[i].wrapper )
						deleteBtn.wrapper = ' (wrapper)';
					else
						deleteBtn.wrapper = '';
				} else if ( formats[i].selector ) {
					deleteBtn.element =  formats[i].selector;
					deleteBtn.wrapper = '';
				} else {
					alert( 'ERROR!' ); 
				}
				deleteBtn.title = formats[i].title;
				deleteBtn.classes = formats[i].classes;
				$( '#instructions-mce-dropdown-names', context ).after(
					'<p class="sns-ajax-delete-p"><a title="delete" class="sns-ajax-delete" id="'
					+ deleteBtn.title + '">X</a> "'
					+ deleteBtn.title + '" <code>&lt;'
					+ deleteBtn.element + ' class="'
					+ deleteBtn.classes + '"&gt;</code>'
					+ deleteBtn.wrapper + '</p>'
				);
			}
		} else {
			$( '#delete-mce-dropdown-names', context ).hide();
		}
	}
	function refreshBodyClass( data ) {
		initData.body_class = mceBodyClass + ' ' + data.classes_body + ' ' + data.classes_post;
		
		// needed for < 3.3
		if ( tinymce.settings ) tinymce.settings.body_class = initData.body_class;
		refreshMCE();
	}
	function refreshStyleFormats( data ) {
		// error check
		if ( typeof data.classes_mce === 'undefined' ) {
			alert( data );
			$('.sns-ajax-loading').hide();
			return;
		} else if ( data.classes_mce.length ) {
			var style_formats = [];
			
			for ( var i = 0; i < data.classes_mce.length; i++ ) { // loop returned classes_mce
				var format = {};
				format.title = data.classes_mce[i].title;
				
				if ( data.classes_mce[i].inline )
					format.inline = data.classes_mce[i].inline;
				else if ( data.classes_mce[i].block ) {
					format.block = data.classes_mce[i].block;
					if (data.classes_mce[i].wrapper)
						format.wrapper = true;
				} else if ( data.classes_mce[i].selector )
					format.selector = data.classes_mce[i].selector;
				else
					alert('dropdown format has bad type.');
				
				format.classes = data.classes_mce[i].classes;
				style_formats.push( format );
			}
			initData.style_formats = style_formats;
			
			// needed for < 3.3
			if ( tinymce.settings ) tinymce.settings.style_formats = initData.style_formats;
			if ( initData.theme_advanced_buttons2.indexOf( "styleselect" ) == -1 ) {
				var tempString = "styleselect,";
				initData.theme_advanced_buttons2 = tempString.concat(initData.theme_advanced_buttons2);
			}
			
			// needed for < 3.3
			if ( tinymce.settings ) tinymce.settings.theme_advanced_buttons2 = initData.theme_advanced_buttons2;
			$( '#delete-mce-dropdown-names', context ).show();
		} else {
			delete initData.style_formats;
			initData.theme_advanced_buttons2 = initData.theme_advanced_buttons2.replace("styleselect,", "");
			
			// needed for < 3.3
			if ( tinymce.settings ) tinymce.settings.theme_advanced_buttons2 = initData.theme_advanced_buttons2;
			$( '#delete-mce-dropdown-names', context ).hide();
		}
		
		refreshDeleteBtns();
		refreshMCE();
	}
	function refreshMCE() {
		if ( tinyMCE.editors["content"] ) {
			// needed for < 3.3 editor initialization.
			if ( ! $( '#content' ).hasClass( '.theEditor' ) ) $( '#content' ).addClass( 'theEditor' );
			
			if ( tinyMCE.editors["content"].isHidden() ) {
				tinyMCE.editors["content"].remove();
				tinyMCE.init( initData );
				tinyMCE.editors["content"].hide();
			} else {
				// you've got to be kidding me.
				if ( 1 == $('#content-html').length )
					$('#content-html').click(); // 3.3
				else if( 1 == $('#edButtonHTML').length )
					switchEditors.go('content', 'html'); // 3.2
				
				tinyMCE.editors["content"].remove();
				tinyMCE.init( initData );
				tinyMCE.editors["content"].hide();
				
				if ( 1 == $('#content-tmce').length )
					$('#content-tmce').click(); // 3.3
				else if( 1 == $('#edButtonPreview').length )
					switchEditors.go('content', 'tinymce'); // 3.2
			}
			
		}
		$('.sns-ajax-loading').hide();
	}
	
});
