// Meta Box JavaScript

jQuery( document ).ready( function( $ ) {
	
	context = $( '#uFp_meta_box' );
	
	$( '.wp-tab-bar li:first-child', context ).addClass( 'wp-tab-active' );
	
	$( '.wp-tab-panel', context ).first().addClass( 'wp-tabs-panel-active' );
	$( '.wp-tab-panel', context ).not( '.wp-tabs-panel-active' ).addClass( 'wp-tabs-panel-inactive' );
	
	$( '.wp-tab-bar a', context ).click( function( event ){
		event.preventDefault();
		$( '.wp-tab-active', context ).removeClass( 'wp-tab-active' );
		$( this ).parent().addClass( 'wp-tab-active' );
		$( '.wp-tabs-panel-active', context ).removeClass( 'wp-tabs-panel-active' ).addClass( 'wp-tabs-panel-inactive' );
		$( $( this ).attr( 'href' ) ).removeClass( 'wp-tabs-panel-inactive' ).addClass( 'wp-tabs-panel-active' );
	});
	
	$( "textarea.php" ).each( function() {
		CodeMirror.fromTextArea( this, {
			mode: "application/x-httpd-php",
			lineNumbers: true,
			tabMode: "shift",
			indentUnit: 8,
			indentWithTabs: true,
			enterMode: "keep",
			matchBrackets: true
		});
	});
	
	$( "textarea.htmlmixed" ).each( function() {
		CodeMirror.fromTextArea( this, {
			mode: "text/html",
			lineNumbers: true,
			tabMode: "shift",
			indentUnit: 4,
			indentWithTabs: true
		});
	});
	
	$( "textarea.js" ).each( function() {
		CodeMirror.fromTextArea( this, {
			mode: "text/javascript",
			lineNumbers: true,
			tabMode: "shift",
			indentUnit: 4,
			indentWithTabs: true,
		});
	});
	
	$( "textarea.css" ).each( function() {
		CodeMirror.fromTextArea( this, {
			mode: "text/css",
			lineNumbers: true,
			tabMode: "shift",
			indentUnit: 4,
			indentWithTabs: true
		});
	});
	
});