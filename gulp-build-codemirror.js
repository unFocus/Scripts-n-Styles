import gulp from 'gulp';
import postcss from 'gulp-postcss';
import header from 'gulp-header';
import footer from 'gulp-footer';
import concat from 'gulp-concat';
import uglify from 'gulp-uglify-es';

const dir = 'node_modules/codemirror/';

// A List of each relevant JS file.
// See https://github.com/WordPress/better-code-editing/pull/92/files#diff-d624ea966e142693d2568ef0395f9aeb
let js = [
	'lib/codemirror.js',

	'keymap/emacs.js',
	'keymap/sublime.js',
	'keymap/vim.js',

	'addon/hint/show-hint.js',
	'addon/hint/anyword-hint.js',
	'addon/hint/css-hint.js',
	'addon/hint/html-hint.js',
	'addon/hint/javascript-hint.js',
	'addon/hint/sql-hint.js',
	'addon/hint/xml-hint.js',

	'addon/lint/lint.js',
	'addon/lint/css-lint.js',
	'addon/lint/html-lint.js',
	'addon/lint/javascript-lint.js',
	'addon/lint/json-lint.js',
	'addon/lint/coffeescript-lint.js', // Added.
	'addon/lint/yaml-lint.js', // Added.

	'addon/comment/comment.js',
	'addon/comment/continuecomment.js',

	'addon/fold/xml-fold.js',

	'addon/mode/overlay.js',

	'addon/edit/closebrackets.js',
	'addon/edit/closetag.js',
	'addon/edit/continuelist.js',
	'addon/edit/matchbrackets.js',
	'addon/edit/matchtags.js',
	'addon/edit/trailingspace.js',

	'addon/dialog/dialog.js',

	'addon/display/autorefresh.js',
	'addon/display/fullscreen.js',
	'addon/display/panel.js',
	'addon/display/placeholder.js',
	'addon/display/rulers.js',

	'addon/fold/brace-fold.js',
	'addon/fold/comment-fold.js',
	'addon/fold/foldcode.js',
	'addon/fold/foldgutter.js',
	'addon/fold/indent-fold.js',
	'addon/fold/markdown-fold.js',

	'addon/merge/merge.js',

	'addon/mode/loadmode.js',
	'addon/mode/multiplex.js',
	'addon/mode/simple.js',

	// Should be included separately.
	// 'addon/runmode/runmode-standalone.js',
	'addon/runmode/runmode.js',
	'addon/runmode/colorize.js',

	'addon/scroll/annotatescrollbar.js',
	'addon/scroll/scrollpastend.js',
	'addon/scroll/simplescrollbars.js',

	'addon/search/search.js',
	'addon/search/jump-to-line.js',
	'addon/search/match-highlighter.js',
	'addon/search/matchesonscrollbar.js',
	'addon/search/searchcursor.js',

	// Shouldn't be included.
	// 'addon/tern/worker.js',
	// 'addon/tern/tern.js',

	'addon/wrap/hardwrap.js',

	'addon/selection/active-line.js',
	'addon/selection/mark-selection.js',
	'addon/selection/selection-pointer.js',

	'mode/meta.js',
	'mode/clike/clike.js',
	'mode/css/css.js',
	'mode/diff/diff.js',
	'mode/htmlmixed/htmlmixed.js',
	'mode/http/http.js',
	'mode/javascript/javascript.js',
	'mode/jsx/jsx.js',
	'mode/markdown/markdown.js',
	'mode/gfm/gfm.js',
	'mode/nginx/nginx.js',
	'mode/php/php.js',
	'mode/sass/sass.js',
	'mode/shell/shell.js',
	'mode/sql/sql.js',
	'mode/wast/wast.js',
	'mode/xml/xml.js',
	'mode/yaml/yaml.js'
];

// Prepend the source dir.
js = js.map( i => dir + i );

function copyCodeMirrorJS() {
	var top = '(function () {',
		bottom = [
				'if ( ! window.wp ) {',
					'window.wp = {};',
				'}',
				'window.wp.CodeMirror = this.CodeMirror;',
			'})();'
		].join( '\n' );

	return gulp.src( js, { base: './node_modules' })
		.pipe( concat( 'codemirror.min.js' ) )
		.pipe( header( top ) )
		.pipe( footer( bottom ) )
		.pipe( uglify() )
		.pipe( gulp.dest( 'dist/codemirror' ) );
}
function copyCodeMirrorModes() {
	return gulp.src([
		dir + 'mode/**/*.js'
	], { base: './node_modules' })
		.pipe( uglify() )
		.pipe( gulp.dest( 'dist' ) );
}
function copyCodeMirrorThemes() {
	return gulp.src([
		dir + 'theme/**/*.css'
	], { base: './node_modules' })
		.pipe( gulp.dest( 'dist' ) );
}
function copyCodeMirrorStandalone() {
	return gulp.src([
		dir + 'addon/runmode/runmode-standalone.js'
	])
		.pipe( uglify() )
		.pipe( gulp.dest( 'dist/codemirror' ) );
}
function copyCodeMirrorCSS() {
	return gulp.src([
		dir + 'lib/codemirror.css',
		dir + 'mode/tiddlywiki/tiddlywiki.css',
		dir + 'mode/tiki/tiki.css',
		dir + 'addon/hint/show-hint.css',
		dir + 'addon/lint/lint.css',
		dir + 'addon/dialog/dialog.css',
		dir + 'addon/display/fullscreen.css',
		dir + 'addon/fold/foldgutter.css',
		dir + 'addon/merge/merge.css',
		dir + 'addon/scroll/simplescrollbars.css',
		dir + 'addon/search/matchesonscrollbar.css',
		dir + 'addon/tern/tern.css'
	], { base: './node_modules' })
		.pipe( concat( 'codemirror.min.css' ) )
		.pipe( postcss() )
		.pipe( gulp.dest( 'dist/codemirror' ) );
}

const copyCodeMirror = gulp.series(
	copyCodeMirrorModes,
	copyCodeMirrorJS,
	copyCodeMirrorCSS,
	copyCodeMirrorStandalone,
	copyCodeMirrorThemes
);

export {
	copyCodeMirror
};
export default copyCodeMirror;
