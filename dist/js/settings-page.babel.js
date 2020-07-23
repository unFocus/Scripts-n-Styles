import $ from 'jquery';
import '../css/options-styles.less';

$( function() {
	let sns = wp.codeEditor.initialize( $( '#codemirror_demo' ), $.extend({}, wp.codeEditor.defaultSettings ) );

	function createLink( theme ) {
		$( '#sns-codemirror-theme-css' ).remove();

		if ( 'default' === theme ) {
			sns.codemirror.setOption( 'theme', 'default' );
			return;
		}

		let href = _SnSOptions.root + 'codemirror/theme/' + theme + '.css';
		let link = document.createElement( 'link' );

		link.setAttribute( 'rel', 'stylesheet' );
		link.setAttribute( 'type', 'text/css' );
		link.setAttribute( 'href', href );

		link.id = 'sns-codemirror-theme-css';

		link.onload = function() {
			sns.codemirror.setOption( 'theme', theme );
		};

		document.getElementsByTagName( 'head' )[0].appendChild( link );
	}

	$( 'input[name="SnS_options[cm_theme]"]' ).change( function() {
		let theme = $( this ).val();

		createLink( theme );

		$( this ).focus();
	});
});
