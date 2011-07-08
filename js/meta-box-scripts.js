// Meta Box JavaScript

jQuery( document ).ready( function( $ ) {
	
	var context = $( '#uFp_meta_box' );
	var CodeMirrors = new Array();
	var currentCodeMirror = false;
	
	$( '.wp-tab-bar li:first-child', context ).addClass( 'wp-tab-active' );
	$( '.wp-tab-panel', context ).hide();
	
	// Refresh when panel becomes unhidden
	$( '#uFp_meta_box-hide, #uFp_meta_box .hndle, #uFp_meta_box .handlediv ' ).live( 'click', function( event ){
		if ( currentCodeMirror ) currentCodeMirror.refresh();
	});
	
	// main handler
	$( '.wp-tab-bar a', context ).live( 'click', function( event ){
		event.preventDefault();
		
		if ( currentCodeMirror ) currentCodeMirror.toTextArea();
		currentCodeMirror = false;
		
		$( '.wp-tab-active', context ).removeClass( 'wp-tab-active' );
		$( this ).parents( 'li' ).addClass( 'wp-tab-active' );
				
		$( '.wp-tabs-panel-active', context ).hide().removeClass( 'wp-tabs-panel-active' );
		$( $( this ).attr( 'href' ) ).show().addClass( 'wp-tabs-panel-active' );
		
		var targetCode = $( '.wp-tabs-panel-active textarea.codemirror', context );
		var targetSet;
		
		if ( targetCode.hasClass( 'js' ) )
			targetSet = {
				mode: "text/javascript",
				lineNumbers: true,
				tabMode: "shift",
				indentUnit: 4,
				indentWithTabs: true
			};
		else if ( targetCode.hasClass( 'css' ) )
			targetSet = {
				mode: "text/css",
				lineNumbers: true,
				tabMode: "shift",
				indentUnit: 4,
				indentWithTabs: true
			};
		/*else if ( targetCode.hasClass( 'htmlmixed' ) )
			targetSet = {
				mode: "text/html",
				lineNumbers: true,
				tabMode: "shift",
				indentUnit: 8,
				indentWithTabs: true,
				enterMode: "keep",
				matchBrackets: true
			};
		else if ( targetCode.hasClass( 'php' ) )
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
		
		currentCodeMirror = CodeMirror.fromTextArea( targetCode.get(0), targetSet );
	});
	
	// active
	$( '.wp-tab-active a', context ).trigger( 'click' );
	
});