import $ from 'jquery';
import '../css/options-styles.less';

$( function() {
	let sns = wp.codeEditor.initialize( $( '#codemirror_demo' ), $.extend({}, wp.codeEditor.defaultSettings ) );
	$( 'input[name="SnS_options[cm_theme]"]' ).change( function() {
		let theme = $( this ).val();
		let href = _SnSOptions.root + 'codemirror/theme/' + theme + '.css';

		$( '#sns-codemirror-theme-css' ).remove();

		let link = document.createElement( 'link' );
		link.setAttribute( 'rel', 'stylesheet' );
		link.setAttribute( 'type', 'text/css' );
		link.onload = function() {
			sns.codemirror.setOption( 'theme', theme );
		};
		link.id = 'sns-codemirror-theme-css';
		link.setAttribute( 'href', href );
		document.getElementsByTagName( 'head' )[0].appendChild( link );

		$( this ).focus();
	});
});
