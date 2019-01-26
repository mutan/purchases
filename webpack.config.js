let Encore = require('@symfony/webpack-encore');

const CopyWebpackPlugin = require('copy-webpack-plugin');

Encore
    .setOutputPath('public/build/') // directory where compiled assets will be stored
    .setPublicPath('/build') // public path used by the web server to access the output path

    .createSharedEntry('app', './assets/js/app.js')
    //.addEntry('app', './assets/js/app.js')
    //.addEntry('page1', './assets/js/page1.js')
    //.addEntry('page2', './assets/js/page2.js')

    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction()) // В режиме разработки будем генерировать карту ресурсов
    .enableVersioning(Encore.isProduction()) // enables hashed filenames (e.g. app.abc123.css)
    .enableSassLoader() // enables Sass/SCSS support
    .autoProvidejQuery() // uncomment if you're having problems with a jQuery plugin

    .enablePostCssLoader((options) => {
        options.config = {
            path: 'config/postcss.config.js'
        }
    }) //PostCSS and autoprefixing

    .addPlugin(new CopyWebpackPlugin([
        { from: './assets/favicon.ico', to: '' },
        { from: './assets/images', to: 'images' },
        { from: './assets/icons', to: 'icons' }
    ]))
;

module.exports = Encore.getWebpackConfig();
