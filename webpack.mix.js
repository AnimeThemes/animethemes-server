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

fs = require('fs');

modernizr.build({
    "options": [
        "setClasses"
    ],
    "feature-detects": [
        "video"
    ]
}, function (result) {
    fs.writeFile('public/js/vendor/modernizr.min.js', result, function (err, data) {
        if (err) {
            return console.log(err);
        }
    });
});

if (mix.inProduction()) {
    mix.version();
}
