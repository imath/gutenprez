const path = require( 'path' );
const UglifyJsPlugin = require( 'uglifyjs-webpack-plugin' );

module.exports = {
	entry: './js/navigation.js',
	output: {
		filename: 'navigation.min.js',
		path: path.resolve( __dirname, 'js' ),
	},
	plugins: [
		new UglifyJsPlugin(),
	],
};
