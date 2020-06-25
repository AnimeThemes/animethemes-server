let mix = require('laravel-mix');

mix
    .sass('resources/sass/main.scss', 'public/css/animethemes.css')
    .copy('resources/js/app.js', 'public/js/animethemes.js')
    .copy('node_modules/jquery/dist/jquery.min.js', 'public/js/vendor/jquery.min.js');

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
