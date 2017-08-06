// Options JavaScript

jQuery( document ).ready( function( $ ) {
	var theme = codemirror_options.theme ? codemirror_options.theme: 'default';
	var editor = CodeMirror.fromTextArea(document.getElementById("codemirror_demo"), {
		lineNumbers: true,
		matchBrackets: true,
		mode: "application/x-httpd-php",
		indentUnit: 4,
		indentWithTabs: true,
		enterMode: "keep",
		tabMode: "shift",
		theme: theme
	});
	$('input[name="SnS_options[cm_theme]"]').change( function(){
		editor.setOption("theme", $(this).val());
	});
});