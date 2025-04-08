const path = require('path');

module.exports = {
    publicPath: process.env.NODE_ENV === 'production' ? '/app/' : '/',
    outputDir: path.resolve(__dirname, './dist'),
    filenameHashing: false,
    css: {
        extract: false,
        sourceMap: process.env.NODE_ENV === 'development'
    },
    configureWebpack: {
        entry: path.resolve(__dirname, 'js/modules/settings/index.js'),
        output: {
            filename: 'settings.js',
        },
        optimization: {
            splitChunks: false
        },
        devtool: process.env.NODE_ENV === 'development' ? 'source-map' : false,
        watch: process.env.NODE_ENV === 'development',
        watchOptions: {
            poll: true,
            ignored: /node_modules/
        }
    },
    chainWebpack: config => {
        config.optimization.delete('splitChunks')
        config.plugins.delete('prefetch')
        config.plugins.delete('preload')
    }
}