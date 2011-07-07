// Meta Box JavaScript

jQuery( document ).ready( function( $ ) {
	
	context = $( '#uFp_meta_box' );
	
	$( '.wp-tab-bar', context ).show();
	$( '.wp-tab-bar li:first-child', context ).addClass( 'wp-tab-active' );
	$( '.wp-panel-heading', context ).remove();
	$( '.wp-tab-panel', context ).addClass( 'wp-tabs-panel-inactive ' );
	$( '.wp-tab-panel', context ).first().removeClass( 'wp-tabs-panel-inactive ' ).addClass( 'wp-tabs-panel-active ' );
	$( '.wp-tab-bar a', context ).click( function( event ){
		event.preventDefault();
		$( '.wp-tab-active', context ).removeClass( 'wp-tab-active' );
		$( this ).parent().addClass( 'wp-tab-active' );
		$( '.wp-tab-panel', context ).removeClass( 'wp-tabs-panel-active ' ).addClass( 'wp-tabs-panel-inactive ' );
		$( $( this ).attr( 'href' ) ).removeClass( 'wp-tabs-panel-inactive ' ).addClass( 'wp-tabs-panel-active ' );
	});
	
	$( "textarea.htmlmixed" ).each( function() {
		CodeMirror.fromTextArea( this, { mode: "text/html", tabMode: "indent" } );
	});
	
	$( "textarea.js" ).each( function() {
		CodeMirror.fromTextArea( this, { lineNumbers: true, mode: "javascript" } );
	});
	$( "textarea.css" ).each( function() {
		CodeMirror.fromTextArea( this, { lineNumbers: true, mode: "css" } );
	});
});