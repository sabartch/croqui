// webpack.config.js
var Encore = require('@symfony/webpack-encore');

Encore
// the project directory where all compiled assets will be stored
    .setOutputPath('public/build/')

    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')

    // will create public/build/app.js and public/build/app.css
    /*.addEntry('scripts', './assets/js/scripts.min.js')
    .addEntry('miniscripts', './assets/js/all-mini-scripts.js')
    .addEntry('jquery', './assets/js/jquery-2.1.4.min.js')
    .addEntry('paiement', './assets/js/paiement.js')
    .addEntry('panier', './assets/js/panier.js')*/

    .addEntry('bootstrap', './assets/css/bootstrap.css')
    .addEntry('theme', './assets/css/theme.css')
    .addEntry('custom', './assets/css/custom.css')

    // allow legacy applications to use $/jQuery as a global variable
    .autoProvidejQuery()

    // enable source maps during development
    .enableSourceMaps(!Encore.isProduction())

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()

    // show OS notifications when builds finish/fail
   // .enableBuildNotifications()

// create hashed filenames (e.g. app.abc123.css)
// .enableVersioning()

// allow sass/scss files to be processed
// .enableSassLoader()
;

// export the final configuration
module.exports = Encore.getWebpackConfig();