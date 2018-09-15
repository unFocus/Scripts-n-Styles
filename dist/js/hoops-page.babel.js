// Options JavaScript
import $ from 'jquery';
import '../css/options-styles.less';

let CodeMirror = wp.CodeMirror;

if ( CodeMirror ) {
	CodeMirror.modeURL = _SnSOptions.root + 'codemirror/mode/%N/%N.js';
}

$( function() {
	if ( ! CodeMirror ) {

		// Temp bailout.
		return;
	}
	let collection = [],
		context = '#sns-shortcodes',
		defaultSettings = $.extend({}, wp.codeEditor.defaultSettings ),
		$form,
		config;

	config = {
		mode: 'text/html',
		lineNumbers: true,
		tabMode: 'shift',
		indentUnit: 4,
		indentWithTabs: true,
		enterMode: 'keep',
		matchBrackets: true
	};

	CodeMirror.commands.save = function() {
		$form.submit();
	};

	// Each "IDE"
	$( '.sns-less-ide', context ).each( function() {
		var $text = $( '.code', this );
		var ide = {
			data: $text.val(),
			name: $text.data( 'sns-shortcode-key' ),
			$text: $text,
			cm: wp.codeEditor.initialize( $text.get( 0 ), $.extend({}, defaultSettings, {
				codemirror: $.extend({}, defaultSettings.codemirror, { mode: 'text/html' })
			}) ).codemirror
		};
		if ( $text.parent().hasClass( 'sns-collapsed' ) ) {
			ide.cm.toTextArea();
		}
		collection.push( ide );
	});

	// Collapsable
	$( context ).on( 'click', '.sns-collapsed-btn, .sns-collapsed-btn + label', function( event ) {
		var $this = $( this ),
			collapsed,
			fileName,
			thisIDE;
		$this.parent().toggleClass( 'sns-collapsed' );
		fileName = $this.siblings( '.code' ).data( 'sns-shortcode-key' );
		collapsed = $this.parent().hasClass( 'sns-collapsed' );
		$( collection ).each( function( index, element ) {
			if ( element.name == fileName ) {
				thisIDE = element;
			}
		});
		if ( collapsed ) {
			thisIDE.cm.toTextArea();
		} else {
			thisIDE.cm = wp.codeEditor.initialize( thisIDE.$text.get( 0 ), $.extend({}, defaultSettings, {
				codemirror: $.extend({}, defaultSettings.codemirror, { mode: 'text/html' })
			}) ).codemirror;
		}
	});
	$( '.sns-ajax-loading' ).hide();
});
