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

// Use package's node-sass instead of gulp-sass bundled (out-of-date) version.
gulpSass.compiler = nodeSass;

// On 'webpack-hot-middleware/client', `?reload=true` tells client to reload if HMR fails.
const devServer = [ 'webpack/hot/dev-server', 'webpack-hot-middleware/client?reload=true' ];

let rules = [];
rules.push({
	test: /\.js$/,
	exclude: /(node_modules|bower_components)/,
	use: {
		loader: 'babel-loader'
	}
});

let config = {
	entry: {
		'js/global-page.min': [ './dist/js/global-page.js' ],
		'js/hoops-page.min': [ './dist/js/hoops-page.js' ],
		'js/meta-box.min': [ './dist/js/meta-box.js' ],
		'js/theme-page.min': [ './dist/js/theme-page.js' ]
	},
	output: {
		filename: '[name].js',
		path: path.resolve( __dirname, 'dist' ),
		publicPath: '/wp-content/plugins/scripts-n-styles'
	},
	context: path.resolve( __dirname ),
	module: { rules: rules },
	mode: 'production'
};
let devConfig = {
	entry: {
		'js/global-page.min': [ ...devServer, './dist/js/global-page.js' ],
		'js/hoops-page.min': [ ...devServer, './dist/js/hoops-page.js' ],
		'js/meta-box.min': [ ...devServer, './dist/js/meta-box.js' ],
		'js/theme-page.min': [ ...devServer, './dist/js/theme-page.js' ]
	},
	output: config.output,
	context: config.context,
	mode: 'development',
	plugins: [
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
	compiler.run( () => { }); // Requires a function...
	done();
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
	});
	done();
}

function watch( done ) {
	gulp.watch([ 'dist/**/*.php' ], reload );
	gulp.watch( 'dist/css/**/*.less', less );
	done();
}

function less() {
	return gulp.src( 'dist/css/**/*.less', { base: '.' })
		.pipe( sourcemaps.init() )
		.pipe( gulpLess() )
		.pipe( postcss() )
		.pipe( sourcemaps.write() )
		.pipe( gulp.dest( '.' ) )
		.pipe( server.stream() );
}

const build = gulp.parallel( compile, less );
const dev = gulp.series( serve, watch );

export {
	dev,
	build
};
export default dev;
