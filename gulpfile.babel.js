import gulp from 'gulp';
import path from 'path';
import webpack from 'webpack';
import BrowserSync from 'browser-sync';
import webpackDevMiddleware from 'webpack-dev-middleware';
import webpackHotMiddleware from 'webpack-hot-middleware';
import postcss from 'gulp-postcss';
import autoprefixer from 'autoprefixer';
import cssnano from 'cssnano';
import sourcemaps from 'gulp-sourcemaps';
import gulpSass from 'gulp-sass';
import nodeSass from 'node-sass';
import gulpLess from 'gulp-less';
import header from 'gulp-header';
import footer from 'gulp-footer';
import concat from 'gulp-concat';
import uglify from 'gulp-uglify-es';

// Use package's node-sass instead of gulp-sass bundled (out-of-date) version.
gulpSass.compiler = nodeSass;

// let uglify = uglifyES.default;

// On 'webpack-hot-middleware/client', `?reload=true` tells client to reload if HMR fails.
const devServer = [ 'webpack/hot/dev-server', 'webpack-hot-middleware/client?reload=true' ];

let rules = [];
rules.push({
	test: /\.babel.js$/,

	exclude: /(node_modules|bower_components)/,
	use: {
		loader: 'babel-loader',
		options: {
			cacheDirectory: true
		}
	}
});

let config = {
	entry: {
		'js/global-page.min': [ './dist/js/global-page.babel.js' ],
		'js/hoops-page.min': [ './dist/js/hoops-page.babel.js' ],
		'js/meta-box.min': [ './dist/js/meta-box.babel.js' ],
		'js/theme-page.min': [ './dist/js/theme-page.babel.js' ]
	},
	output: {
		filename: '[name].js',
		path: path.resolve( __dirname, 'dist' ),
		publicPath: '/wp-content/plugins/scripts-n-styles'
	},
	context: path.resolve( __dirname ),
	module: {
		rules: rules
	},
	plugins: [
		new webpack.ProvidePlugin({
			$: 'jquery',
			jQuery: 'jquery',
			'window.jQuery': 'jquery',
			'window.$': 'jquery'
		})
	],
	mode: 'production'
};
let devConfig = {
	entry: {
		'js/global-page.min': [ ...devServer, './dist/js/global-page.babel.js' ],
		'js/hoops-page.min': [ ...devServer, './dist/js/hoops-page.babel.js' ],
		'js/meta-box.min': [ ...devServer, './dist/js/meta-box.babel.js' ],
		'js/theme-page.min': [ ...devServer, './dist/js/theme-page.babel.js' ]
	},
	output: config.output,
	context: config.context,
	module: config.module,
	mode: 'development',
	devtool: 'cheap-module-eval-source-map',
	plugins: [
		...config.plugins,
		new webpack.HotModuleReplacementPlugin()
	]
};

const server = BrowserSync.create();
const compiler = webpack( config );
const devCompiler = webpack( devConfig );

function reload( done ) {
	server.reload();
	done();
}

function compile( done ) {
	compiler.run( done );
}

function serve( done ) {
	server.init({

		proxy: 'scriptsnstyles.test',
		host: 'scriptsnstyles.test',
		open: 'external',

		middleware: [
			webpackDevMiddleware( devCompiler, {
				publicPath: devConfig.output.publicPath,
				stats: { colors: true }
			}),
			webpackHotMiddleware( devCompiler )
		]
	}, done );
}

function watch( done ) {
	gulp.watch([ 'dist/**/*.php' ], reload );
	gulp.watch( 'dist/css/**/*.less', less );
	done();
}

function less() {
	return gulp.src( 'dist/css/**/*.less', { base: '.' })
		.pipe( sourcemaps.init() )
		.pipe( gulpLess({
			paths: [ path.join( __dirname, 'node_modules' ) ]
		}) )
		.pipe( postcss() )
		.pipe( sourcemaps.write() )
		.pipe( gulp.dest( '.' ) )
		.pipe( server.stream() );
}

let dir = 'node_modules/codemirror/';
function copyCodeMirrorJS() {
	var top = '(function () {',
		bottom = [
				'if ( ! window.wp ) {',
					'window.wp = {};',
				'}',
				'window.wp.CodeMirror = this.CodeMirror;',
			'})();'
		].join( '\n' );
	return gulp.src([
		dir + 'lib/codemirror.js',
		dir + 'keymap/emacs.js',
		dir + 'keymap/sublime.js',
		dir + 'keymap/vim.js',
		dir + 'addon/hint/show-hint.js',
		dir + 'addon/hint/anyword-hint.js',
		dir + 'addon/hint/css-hint.js',
		dir + 'addon/hint/html-hint.js',
		dir + 'addon/hint/javascript-hint.js',
		dir + 'addon/hint/sql-hint.js',
		dir + 'addon/hint/xml-hint.js',
		dir + 'addon/lint/lint.js',
		dir + 'addon/lint/css-lint.js',
		dir + 'addon/lint/html-lint.js',
		dir + 'addon/lint/javascript-lint.js',
		dir + 'addon/lint/json-lint.js',
		dir + 'addon/comment/comment.js',
		dir + 'addon/comment/continuecomment.js',
		dir + 'addon/fold/xml-fold.js',
		dir + 'addon/mode/overlay.js',
		dir + 'addon/edit/closebrackets.js',
		dir + 'addon/edit/closetag.js',
		dir + 'addon/edit/continuelist.js',
		dir + 'addon/edit/matchbrackets.js',
		dir + 'addon/edit/matchtags.js',
		dir + 'addon/edit/trailingspace.js',
		dir + 'addon/dialog/dialog.js',
		dir + 'addon/display/autorefresh.js',
		dir + 'addon/display/fullscreen.js',
		dir + 'addon/display/panel.js',
		dir + 'addon/display/placeholder.js',
		dir + 'addon/display/rulers.js',
		dir + 'addon/fold/brace-fold.js',
		dir + 'addon/fold/comment-fold.js',
		dir + 'addon/fold/foldcode.js',
		dir + 'addon/fold/foldgutter.js',
		dir + 'addon/fold/indent-fold.js',
		dir + 'addon/fold/markdown-fold.js',
		dir + 'addon/merge/merge.js',
		dir + 'addon/mode/loadmode.js',
		dir + 'addon/mode/multiplex.js',
		dir + 'addon/mode/simple.js',
		dir + 'addon/runmode/runmode.js',
		dir + 'addon/runmode/colorize.js',
		// dir + 'addon/runmode/runmode-standalone.js', // Should be included separately.
		dir + 'addon/scroll/annotatescrollbar.js',
		dir + 'addon/scroll/scrollpastend.js',
		dir + 'addon/scroll/simplescrollbars.js',
		dir + 'addon/search/search.js',
		dir + 'addon/search/jump-to-line.js',
		dir + 'addon/search/match-highlighter.js',
		dir + 'addon/search/matchesonscrollbar.js',
		dir + 'addon/search/searchcursor.js',
		dir + 'addon/tern/tern.js',
		// dir + 'addon/tern/tern/worker.js', // Shouldn't be included.
		dir + 'addon/wrap/hardwrap.js',
		dir + 'addon/selection/active-line.js',
		dir + 'addon/selection/mark-selection.js',
		dir + 'addon/selection/selection-pointer.js',
		dir + 'mode/meta.js',
		dir + 'mode/clike/clike.js',
		dir + 'mode/css/css.js',
		dir + 'mode/diff/diff.js',
		dir + 'mode/htmlmixed/htmlmixed.js',
		dir + 'mode/http/http.js',
		dir + 'mode/javascript/javascript.js',
		dir + 'mode/jsx/jsx.js',
		dir + 'mode/markdown/markdown.js',
		dir + 'mode/gfm/gfm.js',
		dir + 'mode/nginx/nginx.js',
		dir + 'mode/php/php.js',
		dir + 'mode/sass/sass.js',
		dir + 'mode/shell/shell.js',
		dir + 'mode/sql/sql.js',
		dir + 'mode/xml/xml.js',
		dir + 'mode/yaml/yaml.js'
	], { base: './node_modules' })
		.pipe( concat( 'codemirror.min.js' ) )
		.pipe( header( top ) )
		.pipe( footer( bottom ) )
		.pipe( uglify() )
		.pipe( gulp.dest( 'dist/vendor/codemirror' ) );
}
function copyCodeMirrorModes() {
	return gulp.src([
		dir + 'mode/**/*.js'
	], { base: './node_modules' })
		.pipe( uglify() )
		.pipe( gulp.dest( 'dist/vendor' ) );
}
function copyCodeMirrorStandalone() {
	return gulp.src([
		dir + 'addon/runmode/runmode-standalone.js'
	])
		.pipe( uglify() )
		.pipe( gulp.dest( 'dist/vendor/codemirror' ) );
}
function copyCodeMirrorCSS() {
	return gulp.src([
		dir + 'lib/**/*.css',
		dir + 'mode/**/*.css',
		dir + 'addon/**/*.css',
		dir + 'theme/**/*.css'
	], { base: './node_modules' })
		.pipe( concat( 'codemirror.min.css' ) )
		.pipe( postcss() )
		.pipe( gulp.dest( 'dist/vendor/codemirror' ) );
}

const copyCodeMirror = gulp.series( copyCodeMirrorModes, copyCodeMirrorJS, copyCodeMirrorCSS, copyCodeMirrorStandalone );
const build = gulp.series( compile, less );
const buildFull = gulp.series( copyCodeMirror, build );
const dev = gulp.series( build, serve, watch );

export {
	watch,
	copyCodeMirror,
	copyCodeMirrorModes,
	copyCodeMirrorJS,
	copyCodeMirrorCSS,
	copyCodeMirrorStandalone,
	less,
	compile,
	serve,
	reload,
	dev,
	build,
	buildFull
};
export default dev;
