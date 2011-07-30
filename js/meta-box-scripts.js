// Meta Box JavaScript

jQuery( document ).ready( function( $ ) {
	
	var context = $( '#uFp_meta_box' );
	var CodeMirrors = [];
	var currentCodeMirror = [];
	
	// Refresh when panel becomes unhidden
	$( '#uFp_meta_box-hide, #uFp_meta_box .hndle, #uFp_meta_box .handlediv ' ).live( 'click', function(){
		$(currentCodeMirror).each(function (){ this.refresh(); });
	});
	
	// main handler
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
	
	// TinyMCE refresher.
	var original_body_class = tinyMCEPreInit.mceInit.body_class.split(' ');
	var sns_body_class = $('#uFp_classes_body').val().split;
	
	for ( var i = 0; i < original_body_class.length; i++ ) {
		if ( 0 != $.inArray( sns_body_class[i], original_body_class ) )
		original_body_class.splice( $.inArray( sns_body_class[i], original_body_class ), 1 );
	}
	original_body_class = original_body_class.join(' ');
		console.log( tinyMCEPreInit.mceInit );
	
	function refreshMCE( data ) {
		console.log( data );
		
		// update body_class
		tinyMCEPreInit.mceInit.body_class = original_body_class + ' ' + data.classes_body + ' ' + data.classes_post;
		var style_formats = [];
		for ( x in data.classes_mce ) {
			var format = {};
			format.title = x;
			if ( "inline" == data.classes_mce[x].type ) {
				format.inline = data.classes_mce[x].element;
			} else if ( "block" == data.classes_mce[x].type ) {
				format.block = data.classes_mce[x].element;
			} else {
				format.selector = data.classes_mce[x].element;
			}
			format.classes = data.classes_mce[x].name;
			style_formats.push( format );
		}
		tinyMCEPreInit.mceInit.style_formats = style_formats;
		console.log( tinyMCEPreInit.mceInit );

		// refresh.
		tinyMCE.editors["content"].save();
		tinyMCE.editors["content"].remove();
		tinyMCE.init(tinyMCEPreInit.mceInit);
	}
	$('#update-classes').click(function(e){
		e.preventDefault();
		$.post( ajaxurl,
			{
				action: 		'sns-classes-ajax',
				_ajax_nonce:	$( '#scripts_n_styles_noncename' ).val(),
				post_id:		$( '#post_ID' ).val(),
				uFp_classes_body:		 $( '#uFp_classes_body' ).val(),
				uFp_classes_post:		 $( '#uFp_classes_post' ).val(),
				uFp_classes_mce_label:	 $( '#uFp_classes_mce_label' ).val(),
				uFp_classes_mce_type:	 $( '#uFp_classes_mce_type' ).val(),
				uFp_classes_mce_element: $( '#uFp_classes_mce_element' ).val(),
				uFp_classes_mce_name:	 $( '#uFp_classes_mce_name' ).val(),
				uFp_classes_mce_wrap:	 $( '#uFp_classes_mce_wrap' ).val(),
			},
			function( data ) { refreshMCE( data ); }//, "json"
		);
	});
	
});