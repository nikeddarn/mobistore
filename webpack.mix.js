let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/assets/js/app.js', 'public/js');
mix.sass('resources/assets/sass/app.scss', 'public/css');

mix.js('resources/assets/js/bootstrap-select.js', 'public/js');
mix.sass('resources/assets/sass/bootstrap-select.scss', 'public/css');

mix.js('resources/assets/js/mobistore.js', 'public/js');

mix.sass('resources/assets/sass/mobistore.scss', 'public/css');

mix.copyDirectory('resources/assets/font-awesome', 'public/font-awesome');

// mix.js('resources/assets/js/mobistore.js', 'public/js/mobistore.js');

mix.disableNotifications();