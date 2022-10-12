const mix = require('laravel-mix');
const path = require('path');
require('laravel-mix-merge-manifest');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.webpackConfig({
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js/'),
        },
    },
});


/*
-----------------+
     Vue App     |
-----------------+
 */
mix
    .js('resources/js/app.js', 'public/js')
    .sourceMaps()
    .vue()
    .extract([
        'vue',
        // 'vuex',
        // 'vue-router',
        // 'vue-focus-lock',
        // 'vue-cookie',
        'axios',
    ])
    .sass('resources/sass/app.scss', 'public/css')
    // .svgSprite('resources/assets/icons/svg', 'assets/images/icons/sprite.svg', {
    //     extract: true,
    //     symbolId: filePath => 'icon-' + path.basename(filePath).split('.')[0],
    // }, {
    //     plainSprite: true,
    // })
    .copy('resources/fonts', 'public/fonts')
    .copy('resources/images', 'public/images')
    .version()
    .mergeManifest()
;



mix.disableNotifications();
