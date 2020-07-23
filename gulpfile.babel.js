import gulp from 'gulp';
import path from 'path';
import webpack from 'webpack';
import BrowserSync from 'browser-sync';
import webpackDevMiddleware from 'webpack-dev-middleware';
import webpackHotMiddleware from 'webpack-hot-middleware';
import VueLoaderPlugin from 'vue-loader/lib/plugin';
import copyCodeMirror from './gulp-build-codemirror';

// let uglify = uglifyES.default;

// On 'webpack-hot-middleware/client', `?reload=true` tells client to reload if HMR fails.
const devServer = [ 'webpack/hot/dev-server', 'webpack-hot-middleware/client?reload=true' ];

let rules = [];

rules.push({
	test: /\.vue$/,
	loader: 'vue-loader'
});

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

rules.push({
	test: /\.css$/,
	use: [
		'vue-style-loader',
		'css-loader'
	]
});

rules.push({
	test: /\.(jpe?g|png|ttf|eot|svg|woff(2)?)$/,
	use: 'base64-inline-loader'
});

rules.push({
	test: /\.less$/,
	use: [
		'vue-style-loader',
		'css-loader',
		'less-loader'
	]
});

rules.push({
	test: /\.scss$/,
	use: [
		'vue-style-loader',
		'css-loader',
		'sass-loader'
	]
});

let config = {
	entry: {
		'js/settings-page.min': [ './dist/js/settings-page.babel.js' ],
		'js/global-page.min': [ './dist/js/global-page.babel.js' ],
		'js/hoops-page.min': [ './dist/js/hoops-page.babel.js' ],
		'js/meta-box.min': [ './dist/js/meta-box.babel.js' ],
		'js/block.min': [ './dist/js/block.babel.js' ],
		'js/sidebar.min': [ './dist/js/sidebar.babel.js' ],
		'js/theme-page.min': [ './dist/js/theme-page.babel.js' ],
		'js/rest.min': [ './dist/js/rest.babel.js' ]
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
	resolve: {
		extensions: [ '*', '.js', '.vue', '.json' ]
	},
	externals: {
		wpjquery: 'jQuery'
	},
	plugins: [
		new webpack.ProvidePlugin({
			$: 'jquery',
			jQuery: 'jquery',
			'window.jQuery': 'wpjquery',
			'window.$': 'wpjquery'
		}),
		new VueLoaderPlugin()
	],
	mode: 'production'
};
let devConfig = {
	entry: {
		'js/settings-page.min': [ ...devServer, './dist/js/settings-page.babel.js' ],
		'js/global-page.min': [ ...devServer, './dist/js/global-page.babel.js' ],
		'js/hoops-page.min': [ ...devServer, './dist/js/hoops-page.babel.js' ],
		'js/meta-box.min': [ ...devServer, './dist/js/meta-box.babel.js' ],
		'js/block.min': [ ...devServer, './dist/js/block.babel.js' ],
		'js/sidebar.min': [ ...devServer, './dist/js/sidebar.babel.js' ],
		'js/theme-page.min': [ ...devServer, './dist/js/theme-page.babel.js' ],
		'js/rest.min': [ ...devServer, './dist/js/rest.babel.js' ]
	},
	output: config.output,
	context: config.context,
	externals: config.externals,
	resolve: config.resolve,
	module: config.module,
	mode: 'development',
	devtool: 'eval',
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

		proxy: 'https://scriptsnstyles.test',
		host: 'scriptsnstyles.test',
		open: 'external',
		https: {
			key: './ssl/localhost.key',
			cert: './ssl/localhost.crt'
		},

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
	done();
}

const build = gulp.series( compile );
const buildFull = gulp.series( copyCodeMirror, build );
const dev = gulp.series( build, serve, watch );

export {
	watch,
	copyCodeMirror,
	compile,
	serve,
	reload,
	dev,
	build,
	buildFull
};
export default dev;
