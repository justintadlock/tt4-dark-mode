
const [scriptConfig, moduleConfig] = require('@wordpress/scripts/config/webpack.config');

const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');

const path = require('path');

module.exports = (() => {
	return [
		{
			// Includes the default webpack config from WordPress.
			...scriptConfig,
			...{
				entry: {
					'js/editor': path.resolve(process.cwd(), 'resources/js',   'editor.js'),
					'css/editor': path.resolve(process.cwd(), 'resources/scss', 'editor.scss'),
					'css/core-button': path.resolve(process.cwd(), 'resources/scss', 'core-button.scss')
				},
				plugins: [
					...scriptConfig.plugins,
					new RemoveEmptyScriptsPlugin({
						stage: RemoveEmptyScriptsPlugin.STAGE_AFTER_PROCESS_PLUGINS
					})
				]
			}
		},
		{
			// Includes the default webpack config from WordPress.
			...moduleConfig,
			...{
				entry: {
					'js/color-scheme': path.resolve(process.cwd(), 'resources/js', 'color-scheme.js')
				}
			}
		}
	]
})();
