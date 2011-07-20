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
	
});