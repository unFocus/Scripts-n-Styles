// Meta Box JavaScript

jQuery( document ).ready( function( $ ) {
	
	context = $( '#uFp_meta_box' );
	CodeMirrors = new Array();
	
	$( '.wp-tab-bar li:first-child', context ).addClass( 'wp-tab-active' );
	
	//$( '.wp-tab-panel', context ).first().addClass( 'wp-tabs-panel-active' );
	$( '.wp-tab-panel', context )
	//.not( '.wp-tabs-panel-active' )
	.addClass( 'wp-tabs-panel-inactive' );
	
	/*$( "textarea.php" ).each( function() {
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
	});*/
	
	$( "textarea.js" ).each( function() {
		this.codemirror = CodeMirror.fromTextArea( this, {
			mode: "text/javascript",
			lineNumbers: true,
			tabMode: "shift",
			indentUnit: 4,
			indentWithTabs: true
		});
		$(this).live( 'focus', function() {
			this.codemirror.refresh();
		});
	});
	
	$( "textarea.css" ).each( function() {
		this.codemirror = CodeMirror.fromTextArea( this, {
			mode: "text/css",
			lineNumbers: true,
			tabMode: "shift",
			indentUnit: 4,
			indentWithTabs: true
		});
		$(this).live( 'focus', function() {
			this.codemirror.refresh();
		});
	});
	
	$( '.wp-tab-bar a', context ).live( 'click', function( event ){
		event.preventDefault();
		
		$( '.wp-tab-active', context ).removeClass( 'wp-tab-active' );
		$( this ).parent().addClass( 'wp-tab-active' );
		
		$( '.wp-tabs-panel-active', context ).removeClass( 'wp-tabs-panel-active' ).addClass( 'wp-tabs-panel-inactive' );
		$( $( this ).attr( 'href' ) ).removeClass( 'wp-tabs-panel-inactive' ).addClass( 'wp-tabs-panel-active' )
			.children('textarea.js, textarea.css').first().focus();
	});
	
	$( '.wp-tab-bar a', context ).first().trigger( 'click' );
	
});