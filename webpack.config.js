const UglifyJsPlugin = require( 'uglifyjs-webpack-plugin' );
const ExtractTextPlugin = require( 'extract-text-webpack-plugin' );

// CSS loader for styles specific to block editing.
const blocksCSS = new ExtractTextPlugin( {
	filename: './assets/blocks.min.css',
} );

module.exports = {
	entry: [
		'./js/navigation.js',
		'./assets/blocks.css',
	],
	output: {
		filename: './js/navigation.min.js',
		path: __dirname,
	},
	module: {
		rules: [ {
			test: /\.css$/,
			use: blocksCSS.extract( {
				fallback: 'style-loader',
				use: {
					loader: 'css-loader',
					options: {
						url: false,
						minimize: true,
						sourceMap: true,
					},
				},
			} ),
		} ],
	},
	plugins: [
		blocksCSS,
		new UglifyJsPlugin(),
	],
};
