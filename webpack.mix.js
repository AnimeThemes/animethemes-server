const mix = require('laravel-mix');

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

mix
    .copy('node_modules/jquery/dist/jquery.min.js', 'public/js/vendor/jquery.min.js')
    .js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        require('postcss-import'),
        require('tailwindcss'),
        require('autoprefixer'),
    ]);

var modernizr = require("modernizr");

modernizr.build({
    "options": [
        "setClasses"
    ],
    "feature-detects": [
        "video"
    ]
}, function (result) {
    var modernizrJS = new File('public/js/vendor/modernizr.min.js');
    modernizrJS.write(result);
    modernizrJS.minify();
});

if (mix.inProduction()) {
    mix.version();
}
