// Meta Box JavaScript

// Tabs code.
jQuery( document ).ready( function( $ ) {
	
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
	
});

// AJAX code.
jQuery( document ).ready( function( $ ) {
	
	// set up ajax ui.
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
	$('#delete-mce-dropdown-names input[type="checkbox"]').replaceWith(function(){
		return '<a class="sns-ajax-delete" id="' + $(this).attr('id') + '">X</a> ' + $(this).next().detach().html();
	});
	$('#sns-classes-ajax-loading').hide();
	$('#sns-dropdown-ajax-loading').hide();
	$('#sns-dropdown-delete-ajax-loading').hide();

	// TinyMCE refresher.
	var snsBaseBodyClass = tinyMCEPreInit.mceInit.body_class.split(' ');
	var sns_body_class = $('#uFp_classes_body').val().split;
	
	for ( var i = 0; i < snsBaseBodyClass.length; i++ ) { // loop over the base_body_class and remove sns classes
		var position = $.inArray( sns_body_class[i], snsBaseBodyClass )
		if ( 0 != position ) snsBaseBodyClass.splice( position, 1 );
	}
	snsBaseBodyClass = snsBaseBodyClass.join(' ');
	
	$('#sns-ajax-update-classes').click(function(e){
		e.preventDefault();
		$('#sns-classes-ajax-loading').show();
		$.post( ajaxurl,
			{
				action: 		'sns-classes-ajax',
				_ajax_nonce:	$( '#scripts_n_styles_noncename' ).val(),
				post_id:		$( '#post_ID' ).val(),
				uFp_classes_body:		 $( '#uFp_classes_body' ).val(),
				uFp_classes_post:		 $( '#uFp_classes_post' ).val(),
			},
			function( data ) { snsRefreshBodyClass( data ); }//, "json"
		);
	});
	
	$('#sns-ajax-update-dropdown').click(function(e){
		e.preventDefault();
		$('#sns-dropdown-ajax-loading').show();
		$.post( ajaxurl,
			{
				action: 		'sns-dropdown-ajax',
				_ajax_nonce:	$( '#scripts_n_styles_noncename' ).val(),
				post_id:		$( '#post_ID' ).val(),
				uFp_classes_mce_label:	 $( '#uFp_classes_mce_label' ).val(),
				uFp_classes_mce_type:	 $( '#uFp_classes_mce_type' ).val(),
				uFp_classes_mce_element: $( '#uFp_classes_mce_element' ).val(),
				uFp_classes_mce_name:	 $( '#uFp_classes_mce_name' ).val(),
				uFp_classes_mce_wrap:	 $( '#uFp_classes_mce_wrap' ).val(),
			},
			function( data ) { snsRefreshStyleFormats( data ); }//, "json"
		);
	});
	
	$('#delete-mce-dropdown-names .sns-ajax-delete').click(function(e){
		e.preventDefault();
		$('#sns-dropdown-ajax-loading').show();
		$.post( ajaxurl,
			{
				action: 		'sns-dropdown-delete-ajax',
				_ajax_nonce:	$( '#scripts_n_styles_noncename' ).val(),
				post_id:		$( '#post_ID' ).val(),
				uFp_delete:		 $( this ).class( 'id' ),
			},
			function( data ) { snsRefreshStyleFormats( data ); }//, "json"
		);
	});
	
	function snsRefreshDeleteNames( data ) {
		// update 'delete-mce-dropdown-names' section
		// Does nothing yet.
		
		snsRefreshMCE();
	}
	function snsRefreshBodyClass( data ) {
		tinyMCEPreInit.mceInit.body_class = snsBaseBodyClass + ' ' + data.classes_body + ' ' + data.classes_post;
		
		snsRefreshMCE();
		$('#sns-classes-ajax-loading').hide();
	}
	function snsRefreshStyleFormats( data ) {
		var style_formats = [];
		for ( x in data.classes_mce ) { // loop returned classes_mce
			var format = {};
			format.title = x;
			
			if ( "inline" == data.classes_mce[x].type ) 
				format.inline = data.classes_mce[x].element;
			else if ( "block" == data.classes_mce[x].type ) 
				format.block = data.classes_mce[x].element;
			else 
				format.selector = data.classes_mce[x].element;
			
			format.classes = data.classes_mce[x].name;
			style_formats.push( format );
		}
		tinyMCEPreInit.mceInit.style_formats = style_formats;
		
		snsRefreshMCE();
		$('#sns-dropdown-ajax-loading').hide();
	}
	
	function snsRefreshMCE() {
		tinyMCE.editors["content"].save();
		tinyMCE.editors["content"].remove();
		tinyMCE.init(tinyMCEPreInit.mceInit);
	}
	
});