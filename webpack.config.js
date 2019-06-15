let Encore = require('@symfony/webpack-encore');

// плагин для прямого копирования файлов, в нашем случае из asset в build
const CopyWebpackPlugin = require('copy-webpack-plugin');

Encore
    .setOutputPath('public/build/') // directory where compiled assets will be stored
    .setPublicPath('/build') // public path used by the web server to access the output path

    .addEntry('main', './assets/js/main.js')
    .addEntry('loaders', './assets/js/loaders.js')

    //.splitEntryChunks()

    // runtime.js file will be output and needs to be included in your pages
    // if the same module (e.g. jquery) is required by several entry files, they will require the same object
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild() //the output directory is emptied between each build (to remove old files)
    .enableBuildNotifications() //display build notifications using webpack-notifier
    .enableSourceMaps(!Encore.isProduction()) // В режиме разработки будем генерировать карту ресурсов
    .enableVersioning(Encore.isProduction()) // enables hashed filenames (e.g. app.abc123.css)
    .enableSassLoader() // enables Sass/SCSS support
    .autoProvidejQuery() // Makes jQuery available everywhere

    //PostCSS, используется тут только для автопрефиксов
    .enablePostCssLoader((options) => {
        options.config = {
            path: 'config/postcss.config.js'
        }
    })

    // эти папки/файлы копируем как есть
    .addPlugin(new CopyWebpackPlugin([
        { from: './assets/favicon.ico', to: '' },
        { from: './assets/images', to: 'images' },
        { from: './assets/icons', to: 'icons' }
    ]))
;

module.exports = Encore.getWebpackConfig();