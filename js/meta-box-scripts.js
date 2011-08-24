// Meta Box JavaScript

// Tabs code.
jQuery( document ).ready( function( $ ) {
	
	// hack for WP 3.3 compat
	var sns_mceInit = tinyMCEPreInit.mceInit["content"] || tinyMCEPreInit.mceInit;
	
	var context = $( '#uFp_meta_box' );
	var CodeMirrors = [];
	var currentCodeMirror = [];
	
	// Refresh when panel becomes unhidden
	$( '#uFp_meta_box-hide, #uFp_meta_box .hndle, #uFp_meta_box .handlediv ' ).live( 'click', function(){
		$(currentCodeMirror).each(function (){ this.refresh(); });
	});
	
	// main tab handler
	$( '.wp-tab-bar a', context ).live( 'click', function( event ){
		event.preventDefault();
		
		// unset active codemirrors
		$(currentCodeMirror).each(function (){ this.toTextArea(); });
		currentCodeMirror = [];
		
		// switch active classes
		$( '.wp-tab-active', context ).removeClass( 'wp-tab-active' );
		$( this ).parent( 'li' ).addClass( 'wp-tab-active' );
				
		$( '.wp-tabs-panel-active', context ).hide().removeClass( 'wp-tabs-panel-active' );
		$( $( this ).attr( 'href' ) ).show().addClass( 'wp-tabs-panel-active' );
		
		// collect codemirrors
		var targetCode = $( '.wp-tabs-panel-active textarea.codemirror', context );
		var targetSet;
		
		// loop codemirrors
		$(targetCode).each(function (){
			if ( $(this).hasClass( 'js' ) )
				targetSet = {
					mode: "text/javascript",
					lineNumbers: true,
					tabMode: "shift",
					indentUnit: 4,
					indentWithTabs: true
				};
			else if ( $(this).hasClass( 'css' ) )
				targetSet = {
					mode: "text/css",
					lineNumbers: true,
					tabMode: "shift",
					indentUnit: 4,
					indentWithTabs: true
				};
			/*else if ( $(this).hasClass( 'htmlmixed' ) )
				targetSet = {
					mode: "text/html",
					lineNumbers: true,
					tabMode: "shift",
					indentUnit: 8,
					indentWithTabs: true,
					enterMode: "keep",
					matchBrackets: true
				};
			else if ( $(this).hasClass( 'php' ) )
				targetSet = {
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
			
			// store active codemirrors
			currentCodeMirror.push( CodeMirror.fromTextArea( this, targetSet ) );
		});
		
		$.post( ajaxurl, {
				action: 'update-current-sns-tab',
				_ajax_nonce: $('#scripts_n_styles_noncename').val(),
				active_tab:  $( '.wp-tab-bar li', context ).index( $( this ).parent( 'li' ).get(0) ),
				page: pagenow
			}
		);
	});
	
	// activate first run
	$( '.wp-tab-active a', context ).trigger( 'click' );
	
	
	// set up ajax ui. (need to come up with a better ID naming scheme.)
	$('#uFp_scripts-tab').append(
		'<div id="sns-scripts-update" class="sns-ajax-wrap">'
		 + '<a id="sns-ajax-update-scripts" href="#" class="button">Update Scripts</a>'
		 + ' '
		 + '<img id="sns-scripts-ajax-loading" class="sns-ajax-loading" src="/wp-admin/images/wpspin_light.gif">'
		 + '</div>'
		);
	
	$('#uFp_styles-tab').append(
		'<div id="sns-styles-update" class="sns-ajax-wrap">'
		 + '<a id="sns-ajax-update-styles" href="#" class="button">Update Styles</a>'
		 + ' '
		 + '<img id="sns-styles-ajax-loading" class="sns-ajax-loading" src="/wp-admin/images/wpspin_light.gif">'
		 + '</div>'
		);
	
	$('#sns-classes').append(
		'<div id="sns-classes-ajax" class="sns-ajax-wrap">'
		 + '<a id="sns-ajax-update-classes" href="#" class="button">Update Classes</a>'
		 + ' '
		 + '<img id="sns-classes-ajax-loading" class="sns-ajax-loading" src="/wp-admin/images/wpspin_light.gif">'
		 + '</div>'
		);
	
	$('#add-mce-dropdown-names').append(
		'<div id="sns-dropdown-ajax" class="sns-ajax-wrap">'
		 + '<a id="sns-ajax-update-dropdown" href="#" class="button">Add Class</a>'
		 + ' '
		 + '<img id="sns-dropdown-ajax-loading" class="sns-ajax-loading" src="/wp-admin/images/wpspin_light.gif">'
		 + '</div>'
		);
	
	// show mce-dropdown sections
	$( '#mce-dropdown-names', context ).show();
	
	snsRefreshDeleteBtns();
	
	function snsRefreshDeleteBtns() {
		
		if ( sns_mceInit.style_formats && sns_mceInit.style_formats.length ) {
			$( '#delete-mce-dropdown-names .sns-ajax-delete-p' ).remove();
			$( '#delete-mce-dropdown-names', context ).show();
			var formats = sns_mceInit.style_formats;
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
	
	$('.sns-ajax-loading').hide();

	// TinyMCE refresher set up.
	var snsBaseBodyClass = sns_mceInit.body_class.split(' ');
	var sns_body_class = $('#uFp_classes_body').val().split(' ');
	var sns_post_class = $('#uFp_classes_post').val().split(' ');
	
	for ( var i = 0; i < snsBaseBodyClass.length; i++ ) { // loop over the base_body_class and remove sns classes
		var position = $.inArray( sns_body_class[i], snsBaseBodyClass )
		if ( 0 != position ) snsBaseBodyClass.splice( position, 1 );
	}
	for ( var i = 0; i < snsBaseBodyClass.length; i++ ) { // loop over the base_post_class and remove sns classes
		var position = $.inArray( sns_post_class[i], snsBaseBodyClass )
		if ( 0 != position ) snsBaseBodyClass.splice( position, 1 );
	}
	snsBaseBodyClass = snsBaseBodyClass.join(' ');
	
	$('#sns-ajax-update-scripts').click(function(e){
		e.preventDefault();
		$('#sns-scripts-ajax-loading').show();
		$(currentCodeMirror).each(function (){ this.save(); });
		var args = { _ajax_nonce: $( '#scripts_n_styles_noncename' ).val(), post_id: $( '#post_ID' ).val(), };
		args.action = 'sns-update-scripts-ajax';
		
		args.uFp_scripts = $( '#uFp_scripts' ).val();
		args.uFp_scripts_in_head = $( '#uFp_scripts_in_head' ).val();
		
		$.post( ajaxurl, args, function() { snsRefreshMCE(); } );
	});
	
	$('#sns-ajax-update-styles').click(function(e){
		e.preventDefault();
		$(this).next().show();
		$(currentCodeMirror).each(function (){ this.save(); });
		var args = { _ajax_nonce: $( '#scripts_n_styles_noncename' ).val(), post_id: $( '#post_ID' ).val(), };
		args.action = 'sns-update-styles-ajax';
		
		args.uFp_styles = $( '#uFp_styles' ).val();
		
		$.post( ajaxurl, args, function() { snsRefreshMCE(); } );
	});
	
	$('#sns-ajax-update-classes').click(function(e){
		e.preventDefault();
		$(this).next().show();
		var args = { _ajax_nonce: $( '#scripts_n_styles_noncename' ).val(), post_id: $( '#post_ID' ).val(), };
		args.action = 'sns-classes-ajax';
		
		args.uFp_classes_body = $( '#uFp_classes_body' ).val();
		args.uFp_classes_post = $( '#uFp_classes_post' ).val();
		
		$.post( ajaxurl, args, function( data ) { snsRefreshBodyClass( data ); } );
	});
	
	$('#sns-ajax-update-dropdown').click(function(e){
		e.preventDefault();
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
		
		$.post( ajaxurl, args, function( data ) { snsRefreshStyleFormats( data ); } );
	});
	
	$('#delete-mce-dropdown-names .sns-ajax-delete').live( "click", function(e){
		e.preventDefault();
		$(this).next().show();
		var args = { _ajax_nonce: $( '#scripts_n_styles_noncename' ).val(), post_id: $( '#post_ID' ).val(), };
		args.action = 'sns-dropdown-delete-ajax';
		
		args.uFp_delete = $( this ).attr( 'id' );
		
		$.post( ajaxurl, args, function( data ) { snsRefreshStyleFormats( data ); } );
	});
	
	function snsRefreshBodyClass( data ) {
		sns_mceInit.body_class = snsBaseBodyClass + ' ' + data.classes_body + ' ' + data.classes_post;
		tinymce.settings.body_class = sns_mceInit.body_class;
		snsRefreshMCE();
	}
	function snsRefreshStyleFormats( data ) {
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
			sns_mceInit.style_formats = style_formats;
			tinymce.settings.style_formats = sns_mceInit.style_formats;
			if ( sns_mceInit.theme_advanced_buttons2.indexOf( "styleselect" ) == -1 ) {
				var tempString = "styleselect,";
				sns_mceInit.theme_advanced_buttons2 = tempString.concat(sns_mceInit.theme_advanced_buttons2);
			}
			tinymce.settings.theme_advanced_buttons2 = sns_mceInit.theme_advanced_buttons2;
			$( '#delete-mce-dropdown-names', context ).show();
		} else {
			delete sns_mceInit.style_formats;
			sns_mceInit.theme_advanced_buttons2 = sns_mceInit.theme_advanced_buttons2.replace("styleselect,", "");
			tinymce.settings.theme_advanced_buttons2 = sns_mceInit.theme_advanced_buttons2;
			$( '#delete-mce-dropdown-names', context ).hide();
		}
		
		snsRefreshDeleteBtns();
		snsRefreshMCE();
	}
	function snsRefreshMCE() {
		if ( tinyMCE.editors["content"] ) {
			// needed for pre WP 3.3 editor initialization.
			if ( ! $( '#content' ).hasClass( '.theEditor' ) ) $( '#content' ).addClass( 'theEditor' );
			
			if ( tinyMCE.editors["content"].isHidden() ) {
				tinyMCE.editors["content"].remove();
				tinyMCE.init( sns_mceInit );
				tinyMCE.editors["content"].hide();
			} else {
				// you've got to be kidding me.
				if ( 1 == $('#content-html').length )
					$('#content-html').click();
				else if( 1 == $('#edButtonHTML').length )
					switchEditors.go('content', 'html');
				
				tinyMCE.editors["content"].remove();
				tinyMCE.init( sns_mceInit );
				tinyMCE.editors["content"].hide();
				
				if ( 1 == $('#content-tmce').length )
					$('#content-tmce').click();
				else if( 1 == $('#edButtonPreview').length )
					switchEditors.go('content', 'tinymce');
			}
			
		}
		$('.sns-ajax-loading').hide();
	}
	
});